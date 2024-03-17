import request from '@/utils/request'

export function getMsgList(data) {
  return request({
    url: '/write/getMsgList',
    method: 'post',
    data
  })
}
export function getMsgTongji(data) {
  return request({
    url: '/write/getMsgTongji',
    method: 'post',
    data
  })
}
export function delMsg(data) {
  return request({
    url: '/write/delMsg',
    method: 'post',
    data
  })
}
export function getTopicList(data) {
  return request({
    url: '/write/getTopicList',
    method: 'post',
    data
  })
}
export function getTopic(data) {
  return request({
    url: '/write/getTopic',
    method: 'post',
    data
  })
}
export function saveTopic(data) {
  return request({
    url: '/write/saveTopic',
    method: 'post',
    data
  })
}
export function delTopic(data) {
  return request({
    url: '/write/delTopic',
    method: 'post',
    data
  })
}
export function setTopicState(data) {
  return request({
    url: '/write/setTopicState',
    method: 'post',
    data
  })
}
export function getPromptList(data) {
  return request({
    url: '/write/getPromptList',
    method: 'post',
    data
  })
}
export function getPrompt(data) {
  return request({
    url: '/write/getPrompt',
    method: 'post',
    data
  })
}
export function savePrompt(data) {
  return request({
    url: '/write/savePrompt',
    method: 'post',
    data
  })
}
export function delPrompt(data) {
  return request({
    url: '/write/delPrompt',
    method: 'post',
    data
  })
}
export function setPromptState(data) {
  return request({
    url: '/write/setPromptState',
    method: 'post',
    data
  })
}
export function exportPrompt(data) {
  return request({
    url: '/write/exportPrompt',
    method: 'post',
    data
  })
}
