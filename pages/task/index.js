const app = getApp();
var rewardVideoAd = null;
Page({
    data: {
        pageIsLoad: false,
        is_check: -1,
        is_ios_pay: 0,
        system: '',
        balance: 0,
        vip_expire_time: '',
        tasks: null
    },

    onLoad: function (options) {
        this.setData({
            system: app.globalData.system
        })
        app.util.checkLogin().then(() => {
            this.getWxappInfo()
            this.getBalance()
            this.getTasks()
            this.setData({
                pageIsLoad: true
            })
        })
    },

    onShow() {
        if(this.data.pageIsLoad) {
            this.getBalance()
        }
    },

    getTasks() {
        app.util.request({
            url: '/wxapp/getTasks',
            loading: false
        }).then(res => {
            this.setData({
                tasks: res.data
            })
            if(res.data && res.data.ad && !rewardVideoAd) {
                this.initRewardAd()
            }
        })
    },
    
    getBalance() {
        app.util.request({
            url: '/wxapp/getBalance',
            loading: false
        }).then(res => {
            this.setData({
                balance: res.data.balance,
                vip_expire_time: res.data.vip_expire_time
            })
        })
    },
    
    getWxappInfo() {
        const _this = this
        app.util.request({
            url: '/wxapp/getWxappInfo',
            loading: false
        }).then(res => {
            _this.setData({
                is_check: res.data.is_check,
                is_ios_pay: res.data.is_ios_pay
            })
        })
    },

    toShare() {
        app.util.message('请回到首页，点右上角“...”，转发给朋友', 'error', function() {
            wx.switchTab({
                url: '/pages/index/index'
            })
        })
    },
    initRewardAd() {
        rewardVideoAd = null
        const ad_unit_id = this.data.tasks.ad.ad_unit_id
        if(wx.createRewardedVideoAd && !rewardVideoAd){
            rewardVideoAd = wx.createRewardedVideoAd({ adUnitId: ad_unit_id })
            rewardVideoAd.onLoad(() => {
                console.log('reward ad onload')
            })
            rewardVideoAd.onError((err) => {
                console.log('reward ad error')
                this.initRewardAd()
            })
            rewardVideoAd.onClose((res) => {
                console.log('reward ad onclose', res)
                if (res && res.isEnded) {
                    app.util.request({
                        url: '/wxapp/doAd',
                        data: {
                            ad_unit_id: ad_unit_id
                        },
                        loading: false
                    }).then(res => {
                        app.util.message(res.message)
                        this.getTasks()
                        this.getBalance()
                    })
                }
            })
        }
    },
    showTaskAd() {
        wx.showLoading({
            title: '加载中'
        })
        if(!rewardVideoAd) {
            this.initRewardAd()
        }
        if (!rewardVideoAd) {
            wx.hideLoading()
            app.util.message('广告加载失败，请重试', 'error')
            return
        }
        rewardVideoAd.load().then(() => {
            rewardVideoAd.show()
            setTimeout(() => {
                wx.hideLoading()
            }, 1500)
        }).catch(() => {
            wx.hideLoading()
            app.util.message('广告加载失败，请重试', 'error')
        })
    }
})