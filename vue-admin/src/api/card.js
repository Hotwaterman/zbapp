import request from '@/utils/request'

export function getBatchList(data) {
  return request({
    url: '/card/getBatchList',
    method: 'post',
    data
  })
}
export function createBatch(data) {
  return request({
    url: '/card/createBatch',
    method: 'post',
    data
  })
}
export function getBatchCard(data) {
  return request({
    url: '/card/getBatchCard',
    method: 'post',
    data
  })
}
export function batchDel(data) {
  return request({
    url: '/card/batchDel',
    method: 'post',
    data
  })
}

export function unbind(data) {
  return request({
    url: '/card/unbind',
    method: 'post',
    data
  })
}

export function getCardLogs(data) {
  return request({
    url: '/card/getCardLogs',
    method: 'post',
    data
  })
}

export function getBatchExport(data) {
  return request({
    url: '/card/getBatchExport',
    method: 'post',
    data
  })
}
