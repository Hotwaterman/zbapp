import request from '@/utils/request'

export function getTongji() {
  return request({
    url: '/index/getTongji',
    method: 'get'
  })
}
export function getOrderChartData() {
  return request({
    url: '/index/getOrderChartData',
    method: 'get'
  })
}
export function getMsgChartData() {
  return request({
    url: '/index/getMsgChartData',
    method: 'get'
  })
}
