package kela_im

import (
	"database/sql"
	"github.com/go-redis/redis"
	_ "github.com/go-sql-driver/mysql"
	"log"
	"reflect"
)

const DB_TYPE string = "mysql"
const DB_DNS string = "root:klpassword@tcp(192.168.0.133:3306)/share_rent?charset=utf8"
const REDIS_DNS string = "192.168.0.94:6379"
const CACHE_KEY_COMMON_PREFIX = "share_rent"

func GetDb() *sql.DB {
	log.Println("---start connect db----------")
	db, err := sql.Open(DB_TYPE, DB_DNS)

	if err != nil {
		log.Fatalln(db, err.Error())
		return nil
	}
	db.Ping()
	return db
}

//save data to cache
func CacheStringSet(key string, val string) bool {
	err := GetRedis().Set(CACHE_KEY_COMMON_PREFIX+key, val, 0).Err()
	if err != nil {
		log.Println(err.Error())
		return false
	}
	return true
}

//get data from cache
func CacheStringGet(key string) string {
	return GetRedis().Get(CACHE_KEY_COMMON_PREFIX + key).String()
}

func CachePush(key string, data interface{}) bool {
	err := GetRedis().Set(CACHE_KEY_COMMON_PREFIX+key, data, 0).Err()
	if err != nil {
		log.Println(err.Error())
		return false
	}
	return true
}

func CachePop(key string) interface{} {
	var rs interface{}
	err := GetRedis().Get(CACHE_KEY_COMMON_PREFIX + key).Scan(rs)
	if err != nil {
		log.Println(err.Error())
		return nil
	}
	return rs
}

func CacheJsonSet(key string, data map[string]string) bool {

	return true
}

func CacheJsonGet(key string) map[string]string {
	data := GetRedis().HGetAll(CACHE_KEY_COMMON_PREFIX + key)

	err := data.Err()
	if err != nil {
		log.Println(err.Error())
		return nil
	}

	rs, err2 := data.Result()
	if err2 != nil {
		log.Println(err2.Error())
		return nil
	}

	return rs
}

func CacheJsonGetSub(key string, sub_key string) string {
	data := GetRedis().HGet(CACHE_KEY_COMMON_PREFIX+key, sub_key)
	if data.Err() != nil {
		log.Println(data.Err().Error())
		return ""
	}
	return data.String()
}

func GetRedis() *redis.Client {
	client := redis.NewClient(&redis.Options{
		Addr:     REDIS_DNS,
		Password: "", // no password set
		DB:       0,  // use default DB
	})

	pong, err := client.Ping().Result()
	if err != nil {
		log.Fatalln(pong, err)
	}
	return client
}

func SetStructData(m interface{}, data map[string]string) error {
	t := reflect.TypeOf(m)
	v := reflect.ValueOf(m)
	var fieldName string
	for k := 0; k < t.NumField(); k++ {
		fieldName = t.Field(k).Name
		if _, ok := data[fieldName]; ok {
			v.Field(k).Set(reflect.ValueOf(data[fieldName]))
		}
		log.Printf("%s -- %v \n", fieldName, v.Field(k).Interface())
	}
	return nil
}


func Struct2Map(obj interface{}) map[string]interface{} {
	t := reflect.TypeOf(obj)
	v := reflect.ValueOf(obj)

	var data = make(map[string]interface{})
	for i := 0; i < t.NumField(); i++ {
		data[t.Field(i).Name] = v.Field(i).Interface()
	}
	return data
}
