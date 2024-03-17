import request from '@/utils/request'

export function getTextList(data) {
  return request({
    url: '/share/getTextList',
    method: 'post',
    data
  })
}
export function getTextInfo(data) {
  return request({
    url: '/share/getTextInfo',
    method: 'post',
    data
  })
}
export function saveTextInfo(data) {
  return request({
    url: '/share/saveTextInfo',
    method: 'post',
    data
  })
}
export function delText(data) {
  return request({
    url: '/share/delText',
    method: 'post',
    data
  })
}
export function setTextState(data) {
  return request({
    url: '/share/setTextState',
    method: 'post',
    data
  })
}
export function setTextDefault(data) {
  return request({
    url: '/share/setTextDefault',
    method: 'post',
    data
  })
}
export function getHaibaoList(data) {
  return request({
    url: '/share/getHaibaoList',
    method: 'post',
    data
  })
}
export function getHaibaoInfo(data) {
  return request({
    url: '/share/getHaibaoInfo',
    method: 'post',
    data
  })
}
export function saveHaibaoInfo(data) {
  return request({
    url: '/share/saveHaibaoInfo',
    method: 'post',
    data
  })
}
export function delHaibao(data) {
  return request({
    url: '/share/delHaibao',
    method: 'post',
    data
  })
}
export function setHaibaoState(data) {
  return request({
    url: '/share/setHaibaoState',
    method: 'post',
    data
  })
}
export function setHaibaoDefault(data) {
  return request({
    url: '/share/setHaibaoDefault',
    method: 'post',
    data
  })
}
