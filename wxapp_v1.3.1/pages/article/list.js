const app = getApp()
Page({
    data: {
        type: '',
        list: []
    },
    onLoad(options) {
        const type = options.type
        this.setData({
            type: type
        })
        if(type == 'help') {
            wx.setNavigationBarTitle({
              title: '使用教程'
            })
        }
        app.util.request({
            url: '/article/getList',
            data: {
                type: type
            }
        }).then(res => {
            this.setData({
                list: res.data.list
            })
        })
    },
    toArticle(e) {
        const id = e.currentTarget.dataset.id
        wx.navigateTo({
          url: '/pages/article/article?type=' + this.data.type + '&id=' + id
        })
    }
})