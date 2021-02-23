package kela_im

import (
	"log"
	"net/http"
	"strings"
	"time"

	"encoding/json"
	"errors"
	"github.com/go-redis/redis"
	"github.com/googollee/go-socket.io"
	"strconv"
)

const MEMBER_CACHE_KEY_PREFIX = "_mi_"
const SOCKET_MEMBER_PAIR_PREFIX = "_smp_"
const MEMBER_ONLINE_STATUS_PREFIX = "online_status_"

var socketList = make(map[string]*socketio.Socket)
var memberOnlineList = make(map[string]bool)

type SocketIoServer struct {
	useTls       bool
	port         int
	readTimeOut  time.Duration
	writeTimeOut time.Duration
	cacheServer  *redis.Client
	socketList   map[string]*socketio.Socket
}

func NewSocketIoServer(port int, useTls bool) *SocketIoServer {
	return &SocketIoServer{
		useTls:       useTls,
		port:         port,
		readTimeOut:  time.Second * 5,
		writeTimeOut: time.Second * 10,
		cacheServer:  GetRedis(),
	}
}

func (si *SocketIoServer) Run() {
	http.Handle("/socket.io/", si.createSocketHandler())
	http.Handle("/", http.FileServer(http.Dir("./asset")))
	log.Println("Serving at localhost:" + strconv.Itoa(si.port))
	log.Fatal(http.ListenAndServe(":" + strconv.Itoa(si.port), nil))
}

func (si *SocketIoServer) createSocketHandler() http.Handler {
	socket, err := socketio.NewServer(nil)
	if nil != err {
		log.Fatalln(err.Error())
	}

	socket.On("connection", func(so socketio.Socket) {
		log.Printf("socket object:", so)
		log.Println("on connection")

		so.On("disconnect", func() {
			log.Println("on disconnect")
			currentMember := GetMemberInfoBySocket(so.Id())
			if currentMember != nil {
				chatWith := currentMember.GetChatWith()
				roomId := CalcuRoomId(currentMember.GetMemberId(), currentMember.GetChatWith())
				if len(chatWith) > 0 {
					//通知对方自己已经下线，如果正在视频通话，将断开通话连接
					toMemSocket, err := GetSocketByMemberId(chatWith)
					if err != nil {
						log.Println(err.Error())
					} else {
						toMemSocket.Emit("close video chat", nil)
						toMemSocket.Leave(roomId)
					}
				}
				//离开房间
				so.Leave(roomId)
				SetOffline(currentMember.member_id)
			}
			so.Disconnect()
		})

		//用户登陆时连接系统时上传自己的信息，并请求连接具体的对象进行会话
		so.On("update_user", func(user map[string]string) { // 更新用户信息
			log.Println("update_user event:", user)
			var u_id = user["u_id"]
			var t_id = user["t_id"]
			log.Printf("update user for user: %s", u_id)
			
			//更新用户信息
			currentMember := GetMemberById(u_id)
			log.Print("current member info:")
			log.Println(currentMember)

			var roomId = CalcuRoomId(t_id, u_id)

			oldSocket, _ := GetSocketByMemberId(u_id);
			if oldSocket != nil {
				oldSocket.Emit("multiple connected", nil)
				oldSocket.Leave(roomId);
			}

			SetOnline(u_id, so)
			//缓存当前登录人员的socket对象
			//加入
			so.Join(roomId)

			//获取对方的用户信息
			//toMember := si.chooseCustomerService(s_id)
			toMember := GetMemberById(t_id)
			log.Print("get customer server member info:")
			log.Println(toMember)

			if toMember != nil {
				//如果对方在线，则自动给对方发送信息，双方建立了p2p连接
				toMemSocket, err := GetSocketByMemberId(toMember.GetMemberId())
				if err != nil {
					log.Println("can't get chat with client socket", err.Error())
					so.Emit("chat_with_client_not_online", nil)
				} else {
					//反馈通知当前用户已经p2p连接成功
					var cuSoNotice = make(map[string]interface{})
					cuSoNotice["chat_with"] = toMember
					cuSoNotice["room_id"] = roomId
					so.Emit("p2p_connected", cuSoNotice)
					log.Println("notice chat with client we both connected A:", so.Id(), " B:", toMemSocket.Id())

					var toSoNotice = make(map[string]interface{})
					toSoNotice["chat_with"] = currentMember
					toSoNotice["room_id"] = roomId
					toMemSocket.Join(roomId)
					toMemSocket.Emit("p2p_connected", toSoNotice)
				}
			} else {
				so.Emit("chat_with_client_not_online", nil)
			}

		})

		so.On("send_msg", func(msg map[string]interface{}) {
			log.Println("received a message:", msg)

			//msg := JsonToMessage(data)
			var t_id = msg["t_id"].(string)
			log.Println("t_id", t_id)
			var u_id = msg["f_id"].(string)
			// sort member id asc
			var msg_key = CalcuRoomId(t_id, u_id)
			var m_id = msg["m_id"].(string)

			if CheckOnline(t_id) {
				log.Println("chat with member is noline")
				var msg_list = make(map[string]map[string]interface{})

				msg_list[m_id] = msg
				log.Println("send message to another member: ", msg)

				toMemSocket, err := GetSocketByMemberId(t_id)
				if err != nil {
					log.Println(err.Error())
					err1 := so.BroadcastTo(CalcuRoomId(t_id, u_id), "get_msg", msg_list)
					if err1 != nil {
						log.Println("broadcat message to room failed: ", err1.Error())
					}
				} else {
					err2 := toMemSocket.Emit("get_msg", msg_list)
					if err2 != nil {
						log.Println("Emit message to specific client failed: ", err2.Error())
					}
				}
			} else {
				log.Println("chat with member is noline")
			}
			//缓存在线客户的聊天记录
			SetChatMessage(msg_key, msg)
		})

		//处理请求查询用户在线状态
		so.On("get_state", func(memberIds []string) {
			var returnData map[string]interface{}
			returnData["u_state"] = memberIds
			returnData["user"] = si.GetMemberOnlineStatusList(memberIds)
			so.Emit("get_state", returnData)
		})

		so.On("del_msg", func(msg map[string]interface{}) { //删除消息
			log.Println("del_msg:", msg)
			//var max_id = strconv.FormatFloat(msg["max_id"].(float64), 'f', -1, 64) //获取最大的消息ＩＤ
			//var f_id = strconv.FormatFloat(msg["f_id"].(float64), 'f', -1, 64)     //获取要删除的消息ID
			//var u_id = strconv.FormatFloat(msg["m_id"].(float64), 'f', -1, 64)

			var max_id = msg["max_id"].(string) //获取最大的消息ＩＤ
			var f_id = msg["f_id"].(string)     //获取要删除的消息ID
			var u_id = msg["t_id"].(string)

			if CheckOnline(f_id) {
				// sort member id asc
				var msg_key = CalcuRoomId(f_id, u_id)
				if CheckOnline(u_id) { //如果已经删除成功，需要通知客户端同步删除
					toMemSocket, err := GetSocketByMemberId(u_id)
					if err == nil {
						toMemSocket.Emit("del_msg", msg)
					}
				}

				RemoveMaxIdMsg(msg_key, max_id)

				//db.update_msg(' t_id = ' + u_id + ' AND f_id = ' + f_id + ' AND m_id = ' + max_id, v);
			}
		})

		// video chat handler
		so.On("send offer", func(data map[string]interface{}) {
			log.Println("send offer:", data)
			var t_id = data["t_id"].(string) //strconv.FormatFloat(data["t_id"].(float64), 'f', -1, 64)
			var attached = false
			if CheckOnline(t_id) {
				var toMemberInfo = GetMemberById(t_id)
				if toMemberInfo != nil { //如果找到该客户，则给其发送消息
					attached = true
					toMemberSocket, err := GetSocketByMemberId(t_id)
					if err != nil {
						log.Println(err.Error())
					} else {
						toMemberSocket.Emit("receive offer", data["sdp"])
					}
					//                    so.set('video_chat_cp', t_id);
				}
			}
			if !attached {
				so.Emit("offer failed", nil)
			}
		})

		so.On("refused offer", func(data map[string]interface{}) {
			var t_id = data["t_id"].(string) //strconv.FormatFloat(data["t_id"].(float64), 'f', -1, 64)
			var attached = false
			if CheckOnline(t_id) {
				var toMemberInfo = GetMemberById(t_id)
				if toMemberInfo != nil { //如果找到该客户，则给其发送消息
					attached = true
					toMemSocket, err := GetSocketByMemberId(t_id)
					if err != nil {
						log.Println(err.Error())
					} else {
						toMemSocket.Emit("refused offer", nil)
					}
				}
			}
			if !attached {
				//nothing to do
				so.Emit("refuse offer failed", nil)
			}
		})

		so.On("send answer", func(data map[string]interface{}) {
			var t_id = data["t_id"].(string) //strconv.FormatFloat(data["t_id"].(float64), 'f', -1, 64)
			var attached = false
			if CheckOnline(t_id) {
				var toMemberInfo = GetMemberById(t_id)
				if toMemberInfo != nil { //如果找到该客户，则给其发送消息
					attached = true
					toMemSocket, err := GetSocketByMemberId(t_id)
					if err != nil {
						log.Println(err.Error())
					} else {
						toMemSocket.Emit("receive answer", data["sdp"])
					}
				}
			}
			if !attached {
				so.Emit("answer failed", nil)
			}
		})

		so.On("send candidate", func(data map[string]interface{}) {
			log.Print("send candidate event")
			log.Println(data)
			var t_id = data["t_id"].(string) //strconv.FormatFloat(data["t_id"].(float64), 'f', -1, 64)
			var attached = false
			if CheckOnline(t_id) {
				var toMemberInfo = GetMemberById(t_id)
				if toMemberInfo != nil { //如果找到该客户，则给其发送消息
					attached = true
					toMemSocket, err := GetSocketByMemberId(t_id)
					if err != nil {
						log.Println(err.Error())
					} else {
						log.Println("send candidate data to receiver")
						toMemSocket.Emit("receive candidate", data["candidate"])
					}
				}
			}
			if !attached {
				log.Println("cause receiver doesn't online, send candidate data failed.")
				so.Emit("candidate transfer failed", nil)
			}
		})

		so.On("close video chat", func(data map[string]string) {
			var t_id = data["t_id"]
			var attached = false
			if CheckOnline(t_id) {
				var toMemberInfo = GetMemberById(t_id)
				if toMemberInfo != nil { //如果找到该客户，则给其发送消息
					attached = true
					toMemSocket, err := GetSocketByMemberId(t_id)
					if err != nil {
						log.Println(err.Error())
					} else {
						toMemSocket.Emit("close video chat", nil)
					}
				}
			}
			if !attached {
				so.Emit("close video chat failed", nil)
			}
		})
	})

	socket.On("error", func(so socketio.Socket, err error) {
		log.Println("error:", err)
	})
	return socket
}

func RemoveMaxIdMsg(msgKey string, msgId string) bool {
	//从缓存中删除
	msgList := GetChatMessages(msgKey)
	delete(msgList, msgId)
	newList := make(map[string]interface{})
	for k, v := range msgList {
		newList[k] = v
	}
	GetRedis().HMSet(msgKey, newList)

	//从数据库中删除
	_, err := GetDb().Exec("DELETE FROM chat_msg WHERE m_id = '"+msgId+"'", nil)
	if err != nil {
		log.Println(err.Error())
		return false
	}
	return true
}

func GetChatMessages(msgKey string) map[string]Message {
	ml := GetRedis().HGetAll(msgKey)
	if ml.Err() != nil {
		log.Println(ml.Err().Error())
		return nil
	}
	rs, err := ml.Result()
	if err != nil {
		log.Println(err.Error())
		return nil
	}

	msgList := make(map[string]Message)
	for k, v := range rs {
		var newMessage Message
		if err2 := json.Unmarshal([]byte(v), newMessage); err2 == nil {
			msgList[k] = newMessage
		}
	}
	return msgList
}

func SetChatMessage(msgKey string, msg map[string]interface{}) bool {
	b, err := json.Marshal(msg)
	if err == nil {
		GetRedis().HSet(msgKey, msg["m_id"].(string), string(b))
		return true
	} else {
		log.Println(err.Error())
		return false
	}
}

func (si *SocketIoServer) updateMemberInfo(uid string, data map[string]interface{}) bool {

	return true
}

func GetSocketByMemberId(memberId string) (socketio.Socket, error) {
	socket := socketList[memberId]
	if socket == nil {
		return nil, errors.New("socket not found yet for member id: " + memberId)
	}
	return *socket, nil
}

func SetMemberSocket(memberId string, socket socketio.Socket) bool {
	socketList[memberId] = &socket
	return CacheStringSet(SOCKET_MEMBER_PAIR_PREFIX+memberId, socket.Id())
}

func (si *SocketIoServer) GetMemberOnlineStatusList(memberIds []string) map[string]int {

	var list = make(map[string]int)
	for i := 0; i < len(memberIds); i++ {
		ok := memberIds[i]
		if CheckOnline(ok) {
			list[ok] = 1
		} else {
			list[ok] = 0
		}
	}
	return list
}

func CalcuRoomId(memberIdA string, memberIdB string) string {
	var roomId string
	if strings.Compare(memberIdA, memberIdA) > 0 {
		roomId = "chat_room_" + memberIdA + "_" + memberIdB
	} else {
		roomId = "chat_room_" + memberIdB + "_" + memberIdA
	}
	return roomId
}

//从数据库获取用户信息:
//默认先从缓存读取，如果没有则从数据库读取然后写入到缓存，数据的变更不更新到数据库
func GetMemberById(memberId string) *MemberInfo {
	if len(memberId) == 0 {
		return nil
	}
	rs := CacheJsonGet(MEMBER_CACHE_KEY_PREFIX + memberId)
	log.Print("get member data from cache:")
	log.Println(rs)
	var (
		mi  *MemberInfo
		err error
	)
	if rs == nil || len(rs) == 0 {
		mi, err = GetMemberInfoFromDb(memberId)
		if err != nil {
			log.Println(err.Error())
			return nil
		}
		//缓存数据
		cacheMap := make(map[string]string)
		json.Unmarshal(mi.ToByteArray(), &cacheMap)
		CacheJsonSet(MEMBER_CACHE_KEY_PREFIX+memberId, cacheMap)
		return mi
	}

	mi = NewMemberInfo(memberId)
	SetStructData(mi, rs)
	return mi
}

func SetMemberInfo(memberInfo *MemberInfo) bool {
	//缓存数据
	cacheMap := make(map[string]string)
	json.Unmarshal(memberInfo.ToByteArray(), &cacheMap)
	return CacheJsonSet(MEMBER_CACHE_KEY_PREFIX+memberInfo.GetMemberId(), cacheMap)
}

func SetOnline(uid string, socket socketio.Socket) bool {
	socketList[uid] = &socket
	memberOnlineList[uid] = true
	return CacheStringSet(SOCKET_MEMBER_PAIR_PREFIX+uid, socket.Id())
	return CacheStringSet(MEMBER_ONLINE_STATUS_PREFIX+uid, "1")
}

func SetOffline(uid string) bool {
	delete(socketList, uid)
	memberOnlineList[uid] = false
	return CacheStringSet(MEMBER_ONLINE_STATUS_PREFIX+uid, "0")
}

func CheckOnline(uid string) bool {
	if stat, ok := memberOnlineList[uid]; ok {
		return stat
	} else {
		return false
	}
	//return CacheStringGet(MEMBER_ONLINE_STATUS_PREFIX+uid) == "1"
}

//从缓存获取用户数据，如果用户没有上线，则获取不到用户信息
func GetMemberInfoBySocket(socketId string) *MemberInfo {
	member_id := CacheStringGet(SOCKET_MEMBER_PAIR_PREFIX + socketId)
	return GetMemberById(member_id)
}

//给终端用户自动匹配一个商家客服
//@todo
func ChooseCustomerService(store_id string) *MemberInfo {

	return nil
}
