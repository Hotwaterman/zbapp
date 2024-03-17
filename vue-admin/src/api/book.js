import request from '@/utils/request'

export function getBookList(data) {
  return request({
    url: '/book/getBookList',
    method: 'post',
    data
  })
}
export function getBook(data) {
  return request({
    url: '/book/getBook',
    method: 'post',
    data
  })
}
export function saveBook(data) {
  return request({
    url: '/book/saveBook',
    method: 'post',
    data
  })
}
export function delBook(data) {
  return request({
    url: '/book/delBook',
    method: 'post',
    data
  })
}
export function setBookState(data) {
  return request({
    url: '/book/setBookState',
    method: 'post',
    data
  })
}
export function getDataList(data) {
  return request({
    url: '/book/getDataList',
    method: 'post',
    data
  })
}
export function getData(data) {
  return request({
    url: '/book/getData',
    method: 'post',
    data
  })
}
export function saveData(data) {
  return request({
    url: '/book/saveData',
    method: 'post',
    data
  })
}
export function setDataState(data) {
  return request({
    url: '/book/setDataState',
    method: 'post',
    data
  })
}
export function delData(data) {
  return request({
    url: '/book/delData',
    method: 'post',
    data
  })
}
export function exportData(data) {
  return request({
    url: '/book/exportData',
    method: 'post',
    data
  })
}
export function startTrain(data) {
  return request({
    url: '/book/startTrain',
    method: 'post',
    data,
    hideLoading: true
  })
}
