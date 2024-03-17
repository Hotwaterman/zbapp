import request from '@/utils/request'

export function getList() {
  return request({
    url: '/upgrade/getList',
    method: 'get'
  })
}
export function doUpgrade(data) {
  return request({
    url: '/upgrade/doUpgrade',
    method: 'post',
    data
  })
}
export function getHistory(data) {
  return request({
    url: '/upgrade/getHistory',
    method: 'post',
    data
  })
}
