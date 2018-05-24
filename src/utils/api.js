import request from './request'

/* jshint camelcase: true */
const STOREID = 1

const baseUrlApi = 'https://mall.wsxitong.cn'
const baseWeChat = 'https://api.weixin.qq.com'
const baseUrlQuan = 'https://apiquan.ithome.com'

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
  getTopics: (r) => request.get('/api/post', {
    categoryid: 0,
    type: 0,
    orderTime: r,
    visistCount: '',
    pageLength: ''
  }, {
    baseURL: baseUrlQuan
  }),
  getTopic: (id) => request.get(`/api/post/${id}`, null, {
    baseURL: baseUrlQuan
  }),
  getTopicComments: (id, last) => request.get('/api/reply', {
    postid: id,
    replyidlessthan: last
  }, {
    baseURL: baseUrlQuan
  })
}

export default api
