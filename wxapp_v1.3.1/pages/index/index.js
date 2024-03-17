const app = getApp()
var interAd = null
Page({
    data: {
        pageIsLoad: false,
        shareTitle: '',
        shareImage: '',
        timeline_share_id: 0,
        siteroot: '',
        index_type: 'chat',
        content: null,
        token: ''
    },
    onLoad(options) {
        if (options.sid) {
            // 分享id
            wx.setStorageSync('sid', options.sid)
        }
        if (options.scene) {
            let scene = decodeURIComponent(options.scene)
            let param = app.util.queryToArr(scene)
            if(param.tuid) {
                wx.setStorageSync('tuid', param.tuid)
            }
        }
        

        this.getWxappInfo().then(() => {
            app.util.checkLogin().then((res) => {
                if(this.data.index_type == 'chat') {
                    wx.showLoading({
                        title: '加载中'
                    })
                    this.setData({
                        siteroot: app.globalData.siteroot,
                        token: wx.getStorageSync('token')
                    })
                }
                this.setData({
                    pageIsLoad: true
                })
            })
        })
        
    },
    webOnload() {
        wx.hideLoading()
    },
    getAd() {
        app.util.request({
            url: '/wxapp/getIndexAd',
            loading: false
        }).then(res => {
            this.setData({
                ad: res.data
            })
            this.initInterAd()
        })
    },
    initInterAd() {
        const ad = this.data.ad
        if (interAd || (!ad.inter_is_open || !ad.inter_unit_id)) {
            return
        }
        // 插屏广告每0.5小时显示一次
        var now = (new Date()).getTime()
        var insertAdLastShow = wx.getStorageSync('insertAdLastShow')
        if (insertAdLastShow && now - insertAdLastShow < 0.5 * 3600 * 1000) {
            return
        }

        if (wx.createInterstitialAd) {
            interAd = wx.createInterstitialAd({
                adUnitId: ad.inter_unit_id
            })
            interAd.onLoad(() => {
                wx.setStorageSync('insertAdLastShow', now)
                interAd.show()
            })
        }
    },
    getWxappInfo() {
        const _this = this
        return new Promise(function (resolve, reject) {
            app.util.request({
                url: '/wxapp/getWxappInfo',
                loading: false
            }).then(res => {
                // 设置页面标题
                let page_title = res.data.page_title
                if (page_title) {
                    wx.setNavigationBarTitle({
                        title: page_title
                    })
                }
                _this.setData({
                    shareTitle: res.data.share_title,
                    shareImage: res.data.share_image,
                    index_type: res.data.index_type,
                    content: res.data.content ? JSON.parse(res.data.content) : ''
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
            path: '/pages/index/index?sid=' + share_id
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
            query: 'sid=' + share_id
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