import Vue from 'vue'
import Vuex from 'vuex'
import api from '@/utils/api'

Vue.use(Vuex)

const store = new Vuex.Store({
  state: {
    AppID: 'wx676cc1d8509846aa',
    AppSecret: '7076aeb26f564bf47c4690738f57eaa6',
    appName: 'miniProgram',
    openID: '',
    avatar: '',
    nickName: '123',
    companyPhone: '400-080-5922',
    companyAddress: '广东省广州市高新技术产业开发区科学城科学大道182号创新大厦C2栋209',
    companyLatitude: '23.1665600000',
    companyLongitude: '113.4559900000'
  },
  mutations: {
    appName (state, name) {
      state.appName = name
    },
    avatar (state, avatar) {
      state.avatar = avatar
    },
    nickName (state, name) {
      state.nickName = name
    },
    openID (state, openid) {
      state.openID = openid
    }
  },
  actions: {
    async getStore ({commit}) {
      await api.getStore()
    // commit('appName', formatedSlides)
    },
    changeNickName ({commit}, param) {
      commit('nickName', param)
    }
  }
})

export default store
