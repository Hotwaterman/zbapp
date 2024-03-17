import request from '@/utils/request'

export function login(data) {
  return request({
    url: '/login/login',
    method: 'post',
    data
  })
}
export function getSystemSetting(data) {
  return request({
    url: '/login/system',
    method: 'post',
    data
  })
}

export function getInfo() {
  return request({
    url: '/user/info',
    method: 'get'
  })
}

export function logout() {
  return request({
    url: '/user/logout',
    method: 'get'
  })
}

export function changePassword(data) {
  return request({
    url: '/user/changePassword',
    method: 'post',
    data
  })
}

export function getTongji(data) {
  return request({
    url: '/user/getTongji',
    method: 'post',
    data
  })
}

export function getUserList(data) {
  return request({
    url: '/user/getUserList',
    method: 'post',
    data
  })
}

export function doRecharge(data) {
  return request({
    url: '/user/doRecharge',
    method: 'post',
    data
  })
}

export function setVipTime(data) {
  return request({
    url: '/user/setVipTime',
    method: 'post',
    data
  })
}

export function doFreeze(data) {
  return request({
    url: '/user/freeze',
    method: 'post',
    data
  })
}

export function doUnfreeze(data) {
  return request({
    url: '/user/unfreeze',
    method: 'post',
    data
  })
}

export function getWebSiteUrl(data) {
  return request({
    url: '/user/getWebSiteUrl',
    method: 'post',
    data
  })
}
