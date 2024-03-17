import axios from 'axios'
import { MessageBox, Message, Loading } from 'element-ui'
import store from '@/store'
import { getToken } from '@/utils/auth'

let loadingInstance = null
// create an axios instance
const service = axios.create({
  baseURL: process.env.VUE_APP_BASE_API, // url = base url + request url
  // withCredentials: true, // send cookies when cross-domain requests
  timeout: 120000 // request timeout
})

// request interceptor
service.interceptors.request.use(
  config => {
    // do something before request is sent

    if (store.getters.token) {
      // let each request carry token
      // ['X-Token'] is a custom headers key
      // please modify it according to the actual situation
      config.headers['S-Token'] = getToken()
    }

    if (!config.headers['hideLoading'] && !config.hideLoading) {
      loadingInstance = Loading.service({
        fullscreen: true,
        lock: true,
        text: '加载中',
        background: 'rgba(0,0,0,0.3)',
        customClass: 'my-loading'
      })
    }

    return config
  },
  error => {
    // do something with request error
    console.log(error) // for debug
    return Promise.reject(error)
  }
)

// response interceptor
service.interceptors.response.use(
  /**
   * If you want to get http information such as headers or status
   * Please return  response => response
   */

  /**
   * Determine the request status by custom code
   * Here is just an example
   * You can also judge the status by HTTP Status Code
   */
  response => {
    if (loadingInstance) {
      loadingInstance.close()
    }
    const res = response.data

    if (res.errno) {
      if (res.errno === 403) {
        // 提示重新登录
        MessageBox.confirm('登录过期，请重新登录', '系统提示', {
          showCancelButton: false,
          cancelButtonText: '去登录',
          type: 'warning'
        }).then(() => {
            window.location.href = '/super'
        }).catch(() => {
          location.reload()
        })
      } else {
        Message({
          message: res.message || res.msg || 'Error',
          type: 'error',
          duration: 3 * 1000
        })
      }
      return Promise.reject(new Error(res.message || 'Error'))
    } else {
      return res
    }
  },
  error => {
    if (loadingInstance) {
      loadingInstance.close()
    }
    Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

export default service
