package kela_im

import (
    //"reflect"
    "encoding/json"
    "log"
    //"fmt"
    "strconv"
)

type Message struct {
    add_time   string `json:"add_time"`
    chat_goods GoodsInfo `json:"chat_goods"`
    f_id       string `json:"f_id"`
    f_ip       string `json:"f_ip"`
    f_name     string `json:"f_name"`
    goods_id   string `json:"goods_id"`
    m_id       string `json:"m_id"`
    msg_type   string `json:"msg_type"`
    t_id       string `json:"t_id"`
    t_msg      string `json:"t_msg"`
    t_name     string `json:"t_name"`
}

func JsonToMessage(data interface{}) *Message {

    dd, err := json.Marshal(data)
    if err != nil {
        log.Println("interface map data transfer to json string failed:", err.Error())
        return nil
    } else {
        log.Println("encoded json string:", string(dd[:]))
        var mm Message
        var aa map[string]interface{}
        err1 := json.Unmarshal(dd, &aa)
        if err1 != nil {
            log.Println("json string transfer to Message struct failed:", err1.Error())
            return nil
        } else {
            //fmt.Println("transfer json string to Message struct success:", aa["chat_goods"])
            mm = NewMessage(aa)
            log.Println("chat_goods:", mm.chat_goods)
            return  &mm
        }
    }
}

func NewMessage(msg map[string]interface{}) Message {
    mm := Message{}
    if add_time, ok := msg["add_time"]; ok {
        mm.add_time = add_time.(string)
    }

    if chat_goods, ok := msg["chat_goods"]; ok {
        /*ft := reflect.TypeOf(chat_goods)
        if ft.String() == reflect.Map.String() {
            for k,v := range map[string]string(chat_goods) {
                mm.chat_goods[k] = v
            }
        }*/
        gd, err := json.Marshal(chat_goods)

        if err != nil {
            log.Println("json decode chat_goods data failed: ", err.Error())
            mm.chat_goods = NewGoodsInfo(nil)
        } else {
            var dd map[string]interface{}
            if err2 := json.Unmarshal(gd, &dd); err2 != nil {
                log.Println(err2.Error())
            } else {
                mm.chat_goods = NewGoodsInfo(dd)
            }
        }
    }

    if f_id, ok := msg["f_id"]; ok {
        mm.f_id = f_id.(string)
    }

    if f_ip, ok := msg["f_ip"]; ok {
        mm.f_ip = f_ip.(string)
    }

    if f_name, ok := msg["f_name"]; ok {
        mm.f_name = f_name.(string)
    }

    if goods_id, ok := msg["goods_id"]; ok {
        mm.goods_id = strconv.FormatFloat(goods_id.(float64), 'f', -1, 64)
    }

    if m_id, ok := msg["m_id"]; ok {
        mm.m_id = m_id.(string)
    }

    if msg_type, ok := msg["msg_type"]; ok {
        mm.msg_type = msg_type.(string)
    }

    if t_id, ok := msg["t_id"]; ok {
        mm.t_id = strconv.FormatFloat(t_id.(float64), 'f', -1, 64)
    }
    if t_msg, ok := msg["t_msg"]; ok {
        mm.t_msg = t_msg.(string)
    }
    if t_name, ok := msg["t_name"]; ok {
        mm.t_name = t_name.(string)
    }

    return mm
}

func (m *Message) ToJsonMap() map[string]interface{} {
    mp := make(map[string]interface{})
    mp["m_id"] = m.m_id


    return mp
}
