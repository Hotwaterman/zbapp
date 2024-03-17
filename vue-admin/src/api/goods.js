import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/goods/getList',
    method: 'post',
    data
  })
}
export function getInfo(data) {
  return request({
    url: '/goods/getInfo',
    method: 'post',
    data
  })
}
export function saveInfo(data) {
  return request({
    url: '/goods/saveInfo',
    method: 'post',
    data
  })
}
export function del(data) {
  return request({
    url: '/goods/del',
    method: 'post',
    data
  })
}
export function setStatus(data) {
  return request({
    url: '/goods/setStatus',
    method: 'post',
    data
  })
}
export function setDefault(data) {
  return request({
    url: '/goods/setDefault',
    method: 'post',
    data
  })
}
