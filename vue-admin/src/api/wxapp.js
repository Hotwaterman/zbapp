import request from '@/utils/request'

export function uploadCode(data) {
  return request({
    url: '/wxapp/uploadCode',
    method: 'post',
    data
  })
}

export function checkUpdate(data) {
  return request({
    url: '/wxapp/checkUpdate',
    method: 'post',
    data
  })
}
