import request from '@/utils/request'

export function getMsgList(data) {
  return request({
    url: '/cosplay/getMsgList',
    method: 'post',
    data
  })
}
export function getMsgTongji(data) {
  return request({
    url: '/cosplay/getMsgTongji',
    method: 'post',
    data
  })
}
export function delMsg(data) {
  return request({
    url: '/cosplay/delMsg',
    method: 'post',
    data
  })
}
export function getTypeList(data) {
  return request({
    url: '/cosplay/getTypeList',
    method: 'post',
    data
  })
}
export function getType(data) {
  return request({
    url: '/cosplay/getType',
    method: 'post',
    data
  })
}
export function saveType(data) {
  return request({
    url: '/cosplay/saveType',
    method: 'post',
    data
  })
}
export function delType(data) {
  return request({
    url: '/cosplay/delType',
    method: 'post',
    data
  })
}
export function setTypeState(data) {
  return request({
    url: '/cosplay/setTypeState',
    method: 'post',
    data
  })
}
export function getRoleList(data) {
  return request({
    url: '/cosplay/getRoleList',
    method: 'post',
    data
  })
}
export function getRole(data) {
  return request({
    url: '/cosplay/getRole',
    method: 'post',
    data
  })
}
export function saveRole(data) {
  return request({
    url: '/cosplay/saveRole',
    method: 'post',
    data
  })
}
export function delRole(data) {
  return request({
    url: '/cosplay/delRole',
    method: 'post',
    data
  })
}
export function setRoleState(data) {
  return request({
    url: '/cosplay/setRoleState',
    method: 'post',
    data
  })
}
