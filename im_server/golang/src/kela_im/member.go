package kela_im

import (
	// "database/sql"
	"encoding/json"
	"errors"
	"log"
)

type MemberInfo struct {
	member_id     string
	store_id      string
	member_name   string
	member_avatar string
	seller_id     string
	is_seller     string
	is_online     string
	chat_with     string
	store_info    *StoreInfo
	goods_info    *GoodsInfo
}

func NewMemberInfo(member_id string) *MemberInfo {
	return &MemberInfo{
		member_id: member_id,
	}
}

func GetMemberInfoFromDb(member_id string) (*MemberInfo, error) {
	if len(member_id) == 0 {
		return nil, errors.New("member id can't be empty")
	}
	sql_str := "SELECT member_id, member_name, IFNULL(member_avatar, '') AS member_avatar FROM member WHERE member_id = '" + member_id + "'"
	log.Print("execute sql:")
	log.Println(sql_str)
	db := GetDb()
	defer db.Close()
	log.Print("db connnect data:")
	log.Println(db)
	row := db.QueryRow(sql_str)
	log.Print("sql executed row result:")
	log.Println(row)

	member := NewMemberInfo(member_id)
	err := row.Scan(&member.member_id, &member.member_name, &member.member_avatar)
	if err != nil {
		log.Println(err.Error())
		return nil, err
	}
	return member, nil
}

func (m *MemberInfo) ToString() string {

	str, err := json.Marshal(m)
	if err != nil {
		log.Println(err.Error())
		return "[]"
	}

	return string(str)
}

func (m *MemberInfo) GetChatWith() string {
	return m.chat_with
}

func (m *MemberInfo) ToByteArray() []byte {

	str, err := json.Marshal(m)
	if err != nil {
		log.Println(err.Error())
		return nil
	}
	return str
}

func (m *MemberInfo) GetMemberId() string {
	return m.member_id
}

func (m *MemberInfo) SetMemberId(m_id string) *MemberInfo {
	m.member_id = m_id
	return m
}

func (m *MemberInfo) GetAvatar() string {
	return m.member_avatar
}

func (m *MemberInfo) SetAvatar(ava string) *MemberInfo {
	m.member_avatar = ava
	return m
}

func (m *MemberInfo) IsOnline() bool {
	return m.is_online == "1"
}

func (m *MemberInfo) SetIsOnline(is_online bool) *MemberInfo {
	if is_online {
		m.is_online = "1"
	} else {
		m.is_online = "0"
	}
	return m
}

func (m *MemberInfo) GetStoreId() string {
	return m.store_id
}

func (m *MemberInfo) SetStoreId(s_id string) *MemberInfo {
	m.store_id = s_id
	return m
}

func (m *MemberInfo) GetMemberName() string {
	return m.member_name
}

func (m *MemberInfo) SetMemberName(m_name string) *MemberInfo {
	m.member_name = m_name
	return m
}

func (m *MemberInfo) GetSellerId() string {
	return m.seller_id
}

func (m *MemberInfo) SetSellerId(sl_id string) *MemberInfo {
	m.seller_id = sl_id
	return m
}

func (m *MemberInfo) IsSeller() bool {
	return m.is_seller != "1"
}

func (m *MemberInfo) SetIsSeller(is_seller bool) *MemberInfo {
	if is_seller {
		m.is_seller = "1"
	} else {
		m.is_seller = "0"
	}
	return m
}

func (m *MemberInfo) GetStoreInfo() *StoreInfo {
	return m.store_info
}

func (m *MemberInfo) SetStoreInfo(s_info *StoreInfo) *MemberInfo {
	m.store_info = s_info
	return m
}

func (m *MemberInfo) GetGoodsInfo() *GoodsInfo {
	return m.goods_info
}

func (m *MemberInfo) SetGoodsInfo(g_info *GoodsInfo) *MemberInfo {
	m.goods_info = g_info
	return m
}
