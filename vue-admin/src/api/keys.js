import request from '@/utils/request'

export function getKeyList(data) {
  return request({
    url: '/keys/getKeyList',
    method: 'post',
    data
  })
}
export function saveKey(data) {
  return request({
    url: '/keys/saveKey',
    method: 'post',
    data
  })
}
export function delKey(data) {
  return request({
    url: '/keys/delKey',
    method: 'post',
    data
  })
}
export function setKeyState(data) {
  return request({
    url: '/keys/setKeyState',
    method: 'post',
    data
  })
}
