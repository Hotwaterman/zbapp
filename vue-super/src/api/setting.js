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
