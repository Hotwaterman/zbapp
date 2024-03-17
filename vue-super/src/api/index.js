import request from '@/utils/request'

export function getTongji(data) {
  return request({
    url: '/index/getTongji',
    method: 'post',
    data
  })
}
export function getOrderChartData(data) {
  return request({
    url: '/index/getOrderChartData',
    method: 'post',
    data
  })
}
export function getMsgChartData(data) {
  return request({
    url: '/index/getMsgChartData',
    method: 'post',
    data
  })
}
