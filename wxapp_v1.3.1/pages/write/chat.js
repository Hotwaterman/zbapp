const app = getApp()
Page({
    data: {
        pageIsLoad: false,
        prompt_id: 0,
        shareTitle: '',
        shareImage: '',
        siteroot: '',
        token: ''
    },
    onLoad(options) {
        if (options.sid) {
            wx.setStorageSync('sid', options.sid)
        }
        if (options.prompt_id) {
            this.setData({
                prompt_id: options.prompt_id
            })
        }
        this.getWxappInfo().then(() => {
            app.util.checkLogin().then((res) => {
                wx.showLoading({
                    title: '加载中'
                })
                this.setData({
                    siteroot: app.globalData.siteroot,
                    token: wx.getStorageSync('token'),
                    pageIsLoad: true
                })
            })
        })
    },
    webOnload() {
        wx.hideLoading()
    },
    getWxappInfo() {
        const _this = this
        return new Promise(function (resolve, reject) {
            app.util.request({
                url: '/wxapp/getWxappInfo',
                loading: false
            }).then(res => {
                _this.setData({
                    shareTitle: res.data.share_title,
                    shareImage: res.data.share_image
                })
                resolve()
            })
        })
    },
    async onShareAppMessage() {
        var share_id = await this.getShareId('wechat')
        console.log('sh', share_id)
        return {
            title: this.data.shareTitle,
            imageUrl: this.data.shareImage,
            path: '/pages/write/chat?prompt_id=' + this.data.prompt_id + '&sid=' + share_id
        }
    },
    onShareTimeline() {
        var share_id = this.data.timeline_share_id
        if (!share_id) {
            this.getShareId('timeline')
        }
        console.log('share_id', share_id)
        return {
            title: this.data.shareTitle,
            imageUrl: this.data.shareImage,
            query: 'prompt_id=' + this.data.prompt_id + '&sid=' + share_id
        }
    },
    async getShareId(way) {
        var share_id = 0
        await app.util.request({
            url: '/wxapp/doShare',
            data: {
                way: way
            }
        }).then(res => {
            share_id = res.data.share_id
            if (way == 'timeline') {
                this.setData({
                    timeline_share_id: share_id
                })
            }
        })
        return share_id
    },
    onAddToFavorites() {
        return {
            title: this.data.shareTitle,
            imageUrl: this.data.shareImage
        }
    }
})