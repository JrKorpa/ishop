package kela_im


type GoodsInfo struct {
    goods_commonid        string  `json:"goods_commonid"`
    goods_id              string  `json:"goods_id"`
    goods_image           string  `json:"goods_image"`
    goods_marketprice     string `json:"goods_marketprice"`
    goods_name            string  `json:"goods_name"`
    goods_price           string `json:"goods_price"`
    goods_promotion_price string `json:"goods_promotion_price"`
    pic                   string  `json:"pic"`
    pic24                 string  `json:"pic24"`
    pic36                 string  `json:"pic36"`
    store_id              string  `json:"store_id"`
    url                   string  `json:"url"`
}

func NewGoodsInfo(data map[string]interface{}) GoodsInfo {
    gi := GoodsInfo{}
    if goods_commonid, ok := data["goods_commonid"]; ok {
        gi.goods_commonid = goods_commonid.(string)
    }

    if goods_id, ok := data["goods_id"]; ok {
        gi.goods_id = goods_id.(string)
    }

    if goods_image, ok := data["goods_image"]; ok {
        gi.goods_image = goods_image.(string)
    }

    if goods_marketprice, ok := data["goods_marketprice"]; ok {
        gi.goods_marketprice = goods_marketprice.(string)
    }

    if goods_name, ok := data["goods_name"]; ok {
        gi.goods_name = goods_name.(string)
    }

    if goods_price, ok := data["goods_price"]; ok {
        gi.goods_price = goods_price.(string)
    }

    if goods_promotion_price, ok := data["goods_promotion_price"]; ok {
        gi.goods_promotion_price = goods_promotion_price.(string)
    }

    if pic, ok := data["pic"]; ok {
        gi.pic = pic.(string)
    }

    if pic24, ok := data["pic24"]; ok {
        gi.pic24 = pic24.(string)
    }

    if pic36, ok := data["pic36"]; ok {
        gi.pic36 = pic36.(string)
    }

    if store_id, ok := data["store_id"]; ok {
        gi.store_id = store_id.(string)
    }

    if url, ok := data["url"]; ok {
        gi.url = url.(string)
    }

    return gi
}
