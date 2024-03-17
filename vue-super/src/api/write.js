import request from '@/utils/request'

export function getMsgList(data) {
  return request({
    url: '/write/getMsgList',
    method: 'post',
    data
  })
}
export function getMsgTongji(data) {
  return request({
    url: '/write/getMsgTongji',
    method: 'post',
    data
  })
}
export function delMsg(data) {
  return request({
    url: '/write/delMsg',
    method: 'post',
    data
  })
}
