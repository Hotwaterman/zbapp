import request from '@/utils/request'

export function getSetting(data) {
  return request({
    url: '/setting/getSetting',
    method: 'post',
    data
  })
}
export function getEngines(data) {
  return request({
    url: '/setting/getEngines',
    method: 'post',
    data
  })
}
export function setSetting(data) {
  return request({
    url: '/setting/setSetting',
    method: 'post',
    data
  })
}
export function getBalance(data) {
  return request({
    url: '/setting/getBalance',
    method: 'post',
    data
  })
}
export function getLxaiBalance(data) {
  return request({
    url: '/setting/getLxaiBalance',
    method: 'post',
    data
  })
}
