import request from '@/utils/request'

export function getHelpList(data) {
  return request({
    url: '/article/getHelpList',
    method: 'post',
    data
  })
}
export function getHelpArticle(data) {
  return request({
    url: '/article/getHelpArticle',
    method: 'post',
    data
  })
}
export function saveHelpArticle(data) {
  return request({
    url: '/article/saveHelpArticle',
    method: 'post',
    data
  })
}
export function delHelpArticle(data) {
  return request({
    url: '/article/delHelpArticle',
    method: 'post',
    data
  })
}
export function getArticle(data) {
  return request({
    url: '/article/getArticle',
    method: 'post',
    data
  })
}
export function saveArticle(data) {
  return request({
    url: '/article/saveArticle',
    method: 'post',
    data
  })
}
