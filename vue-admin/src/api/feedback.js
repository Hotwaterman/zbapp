import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/feedback/getList',
    method: 'post',
    data
  })
}
export function setState(data) {
  return request({
    url: '/feedback/setState',
    method: 'post',
    data
  })
}
export function del(data) {
  return request({
    url: '/feedback/del',
    method: 'post',
    data
  })
}
