import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/commission/getList',
    method: 'post',
    data
  })
}

export function getSonList(data) {
  return request({
    url: '/commission/getSonList',
    method: 'post',
    data
  })
}

export function getTuserList(data) {
  return request({
    url: '/commission/getTuserList',
    method: 'post',
    data
  })
}

export function getOrderList(data) {
  return request({
    url: '/commission/getOrderList',
    method: 'post',
    data
  })
}

export function getApplyList(data) {
  return request({
    url: '/commission/getApplyList',
    method: 'post',
    data
  })
}

export function getApplyInfo(data) {
  return request({
    url: '/commission/getApplyInfo',
    method: 'post',
    data
  })
}

export function delApply(data) {
  return request({
    url: '/commission/delApply',
    method: 'post',
    data
  })
}

export function setApplyStatus(data) {
  return request({
    url: '/commission/setApplyStatus',
    method: 'post',
    data
  })
}

export function getWithdrawList(data) {
  return request({
    url: '/commission/getWithdrawList',
    method: 'post',
    data
  })
}

export function delWithdraw(data) {
  return request({
    url: '/commission/delWithdraw',
    method: 'post',
    data
  })
}

export function getWithdrawInfo(data) {
  return request({
    url: '/commission/getWithdrawInfo',
    method: 'post',
    data
  })
}

export function saveWithdrawInfo(data) {
  return request({
    url: '/commission/saveWithdrawInfo',
    method: 'post',
    data
  })
}

