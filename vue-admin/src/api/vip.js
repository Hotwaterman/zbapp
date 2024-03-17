import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/vip/getList',
    method: 'post',
    data
  })
}
export function getInfo(data) {
  return request({
    url: '/vip/getInfo',
    method: 'post',
    data
  })
}
export function saveInfo(data) {
  return request({
    url: '/vip/saveInfo',
    method: 'post',
    data
  })
}
export function del(data) {
  return request({
    url: '/vip/del',
    method: 'post',
    data
  })
}
export function setStatus(data) {
  return request({
    url: '/vip/setStatus',
    method: 'post',
    data
  })
}
export function setDefault(data) {
  return request({
    url: '/vip/setDefault',
    method: 'post',
    data
  })
}
