import request from '@/utils/request'

export function getMsgList(data) {
  return request({
    url: '/msg/getMsgList',
    method: 'post',
    data
  })
}
export function getMsgTongji(data) {
  return request({
    url: '/msg/getMsgTongji',
    method: 'post',
    data
  })
}
export function delMsg(data) {
  return request({
    url: '/msg/delMsg',
    method: 'post',
    data
  })
}

export function getOldMsgList(data) {
  return request({
    url: '/msg/getOldMsgList',
    method: 'post',
    data
  })
}
export function getOldMsgTongji(data) {
  return request({
    url: '/msg/getOldMsgTongji',
    method: 'post',
    data
  })
}
