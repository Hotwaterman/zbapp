import request from '@/utils/request'

export function getShareList(data) {
  return request({
    url: '/reward/getShareList',
    method: 'post',
    data
  })
}
export function getShareTongji(data) {
  return request({
    url: '/reward/getShareTongji',
    method: 'post',
    data
  })
}
export function getInviteList(data) {
  return request({
    url: '/reward/getInviteList',
    method: 'post',
    data
  })
}
export function getInviteTongji(data) {
  return request({
    url: '/reward/getInviteTongji',
    method: 'post',
    data
  })
}
export function getAdList(data) {
  return request({
    url: '/reward/getAdList',
    method: 'post',
    data
  })
}
export function getAdTongji(data) {
  return request({
    url: '/reward/getAdTongji',
    method: 'post',
    data
  })
}
