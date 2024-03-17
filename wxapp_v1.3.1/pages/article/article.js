const WxParse = require('../../utils/wxParse/wxParse.js')
const app = getApp();
Page({
  data: {
      contents: ''
  },
  onLoad(options) {
      var type = options.type
      var title = '文章内容'
      switch(options.type) {
        case 'about':
            title = '关于我们';
            break;
        case 'kefu':
            title = '联系客服';
            break;
        case 'privacy':
            title = '隐私政策';
            break;
        case 'service':
            title = '服务协议';
            break;
        case 'notice':
            title = '通知公告';
            break;
        case 'legal':
            title = '免责声明';
            break;
        case 'commission':
            title = '推广协议';
            break;
      }
      wx.setNavigationBarTitle({
        title: title
      })
      var data = {
          type: type
      }
      if(type == 'help' && options.id) {
        data.id = options.id
      }
     
      app.util.request({
          url: '/article/getArticle',
          data: data
      }).then(res => {
          this.setData({
              content: res.data.content
          })
          if(res.data.title) {
              wx.setNavigationBarTitle({
                title: res.data.title
              })
          }
          WxParse.wxParse('article', 'html', res.data.content, this, 5);
      })
  }
})
