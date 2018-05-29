import request from './request'

/* jshint camelcase: true */
const STOREID = 1

const baseUrlApi = 'https://mall.wsxitong.cn'
const baseWeChat = 'https://api.weixin.qq.com'

const api = {
  getStore: () => request.post('/api/store/get_store', {store_id: STOREID}, {
    baseURL: baseUrlApi,
    headers: {'content-type': 'application/x-www-form-urlencoded'}
  }),
  getOpenId: (id, secret, code) => request.get(`/sns/jscode2session?appid=${id}&secret=${secret}&grant_type=authorization_code&js_code=${code}`, null, {
    baseURL: baseWeChat,
    headers: {'content-type': 'application/x-www-form-urlencoded'}
  }),
  getTemplet: (type, page, active) => request.post('/api/store/get_templet', {store_id: STOREID, active: active, type: type, page: page}, {
    baseURL: baseUrlApi,
    headers: {'content-type': 'application/x-www-form-urlencoded'}
  }),
  checkLogin: (code, nickname, headimgurl, province, city, county) => request.post('/api/user/login', {store_id: STOREID, code: code, nickname: nickname, headimgurl: headimgurl, province: province, city: city, area: county}, {
    baseURL: baseUrlApi,
    headers: {'content-type': 'application/x-www-form-urlencoded'}
  }),
  getCoupon: (openid, page) => request.get('/api/coupons/get_coupons', {
    openid: openid,
    page: page,
    store_id: STOREID
  }, {
    baseURL: baseUrlApi,
    headers: {'content-type': 'application/x-www-form-urlencoded'}
  }),
  recCoupon: (openid, couponid) => request.post('/api/coupons/add_coupons', {store_id: STOREID, openid: openid, coupons_id: couponid}, {
    baseURL: baseUrlApi,
    headers: {'content-type': 'application/x-www-form-urlencoded'}
  }),
  getProductDetail: (id) => request.get(`/api/store/get_templet_detail?store_id=${STOREID}&id=${id}`, null, {
    baseURL: baseUrlApi,
    headers: {'content-type': 'application/x-www-form-urlencoded'}
  })
}

export default api
