import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/draw/getList',
    method: 'post',
    data
  })
}
export function getInfo(data) {
  return request({
    url: '/draw/getInfo',
    method: 'post',
    data
  })
}
export function saveInfo(data) {
  return request({
    url: '/draw/saveInfo',
    method: 'post',
    data
  })
}
export function del(data) {
  return request({
    url: '/draw/del',
    method: 'post',
    data
  })
}
export function setStatus(data) {
  return request({
    url: '/draw/setStatus',
    method: 'post',
    data
  })
}
export function setDefault(data) {
  return request({
    url: '/draw/setDefault',
    method: 'post',
    data
  })
}

export function getMsgList(data) {
  return request({
    url: '/draw/getMsgList',
    method: 'post',
    data
  })
}
export function delMsg(data) {
  return request({
    url: '/draw/delMsg',
    method: 'post',
    data
  })
}
