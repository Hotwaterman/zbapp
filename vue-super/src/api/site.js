import request from '@/utils/request'

export function getList(data) {
  return request({
    url: '/site/getList',
    method: 'post',
    data
  })
}
export function getInfo(data) {
  return request({
    url: '/site/getInfo',
    method: 'post',
    data
  })
}
export function saveInfo(data) {
  return request({
    url: '/site/saveInfo',
    method: 'post',
    data
  })
}
export function del(data) {
  return request({
    url: '/site/del',
    method: 'post',
    data
  })
}
export function setStatus(data) {
  return request({
    url: '/site/setStatus',
    method: 'post',
    data
  })
}
export function getLoginToken(data) {
  return request({
    url: '/site/getLoginToken',
    method: 'post',
    data
  })
}

export function getSelectSiteList(data) {
  return request({
    url: '/site/getSelectSiteList',
    method: 'post',
    data
  })
}
