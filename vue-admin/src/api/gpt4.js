import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/gpt4/getList',
    method: 'post',
    data
  })
}
export function getInfo(data) {
  return request({
    url: '/gpt4/getInfo',
    method: 'post',
    data
  })
}
export function saveInfo(data) {
  return request({
    url: '/gpt4/saveInfo',
    method: 'post',
    data
  })
}
export function del(data) {
  return request({
    url: '/gpt4/del',
    method: 'post',
    data
  })
}
export function setStatus(data) {
  return request({
    url: '/gpt4/setStatus',
    method: 'post',
    data
  })
}
export function setDefault(data) {
  return request({
    url: '/gpt4/setDefault',
    method: 'post',
    data
  })
}

export function getMsgList(data) {
  return request({
    url: '/gpt4/getMsgList',
    method: 'post',
    data
  })
}
export function delMsg(data) {
  return request({
    url: '/gpt4/delMsg',
    method: 'post',
    data
  })
}
