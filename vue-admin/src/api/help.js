import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/help/getList',
    method: 'post',
    data
  })
}
export function getInfo(data) {
  return request({
    url: '/help/getInfo',
    method: 'post',
    data
  })
}
export function saveInfo(data) {
  return request({
    url: '/help/saveInfo',
    method: 'post',
    data
  })
}
export function del(data) {
  return request({
    url: '/help/del',
    method: 'post',
    data
  })
}
