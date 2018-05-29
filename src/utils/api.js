import request from './request'

/* jshint camelcase: true */
const STOREID = 2

request.config.headers = {
  'content-type': 'application/x-www-form-urlencoded'
}
request.config.baseURL = 'https://mall.wsxitong.cn'

const api = {
  getStore: () => request.post('/api/store/get_store', {
    store_id: STOREID
  }),
  getOpenId: (id, secret, code) => request.get(`/sns/jscode2session?appid=${id}&secret=${secret}&grant_type=authorization_code&js_code=${code}`),
  getTemplet: (type, page, active) => request.post('/api/store/get_templet', {
    store_id: STOREID,
    active: active,
    type: type,
    page: page
  }),
  checkLogin: (code, nickname, headimgurl, province, city, county) => request.post('/api/user/login', {
    store_id: STOREID,
    code: code,
    nickname: nickname,
    headimgurl: headimgurl,
    province: province,
    city: city,
    area: county
  }),
  getCoupon: (openid, page) => request.get('/api/coupons/get_coupons', {
    openid: openid,
    page: page,
    store_id: STOREID
  }),
  recCoupon: (openid, couponid) => request.post('/api/coupons/add_coupons', {
    store_id: STOREID,
    openid: openid,
    coupons_id: couponid
  }),
  getProductDetail: (id) => request.get(`/api/store/get_templet_detail?store_id=${STOREID}&id=${id}`),
  getIndexBanner: () => request.post('/api/store/get_topmap', {store_id: STOREID})
}

export default api
