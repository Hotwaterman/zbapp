import request from '@/utils/request'

export function getWxmpSetting(data) {
  return request({
    url: '/wxmp/getWxmpSetting',
    method: 'post',
    data
  })
}

export function setWxmpSetting(data) {
  return request({
    url: '/wxmp/setWxmpSetting',
    method: 'post',
    data
  })
}

export function getWxmpKeywords(data) {
  return request({
    url: '/wxmp/getWxmpKeywords',
    method: 'post',
    data
  })
}

export function getWxmpReply(data) {
  return request({
    url: '/wxmp/getWxmpReply',
    method: 'post',
    data
  })
}

export function setWxmpReply(data) {
  return request({
    url: '/wxmp/setWxmpReply',
    method: 'post',
    data
  })
}

export function getKeywordList(data) {
  return request({
    url: '/wxmp/getKeywordList',
    method: 'post',
    data
  })
}

export function getKeyword(data) {
  return request({
    url: '/wxmp/getKeyword',
    method: 'post',
    data
  })
}

export function saveKeyword(data) {
  return request({
    url: '/wxmp/saveKeyword',
    method: 'post',
    data
  })
}

export function delKeyword(data) {
  return request({
    url: '/wxmp/delKeyword',
    method: 'post',
    data
  })
}

export function setKeywordState(data) {
  return request({
    url: '/wxmp/setKeywordState',
    method: 'post',
    data
  })
}
