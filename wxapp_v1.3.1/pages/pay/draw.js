const app = getApp();
Page({
    data: {
        pageIsLoad: false,
        is_check: -1,
        is_ios_pay: 0,
        system: '',
        balance: 0,
        balance_draw: 0,
        vip_expire_time: '',
        drawList: [],
        draw_id: 0,
        paying: false
    },

    onLoad: function (options) {
        this.setData({
            system: app.globalData.system
        })
        app.util.checkLogin().then(() => {
            this.getWxappInfo()
            this.getBalance()
            this.getDrawList()
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

    getDrawList() {
        app.util.request({
            url: '/wxapp/getDrawList'
        }).then(res => {
            const drawList = res.data
            drawList.forEach(item => {
                if(item.is_default) {
                    this.setData({
                        draw_id: item.id
                    })
                }
            })
            this.setData({
                drawList: drawList
            })
        })
    },
    getBalance() {
        app.util.request({
            url: '/wxapp/getBalance',
            loading: false
        }).then(res => {
            this.setData({
                balance: res.data.balance,
                balance_draw: res.data.balance_draw,
                vip_expire_time: res.data.vip_expire_time
            })
        })
    },

    chooseDraw(e) {
        const id = e.currentTarget.dataset.id
        this.setData({
            draw_id: id
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

    doPayDraw: function () {
		if (this.data.paying) {
			return false;
        }
        var draw_id = this.data.draw_id;

        if (draw_id == 0) {
            app.util.message('请选择充值套餐');
            return false;
        }

        this.setData({
            paying: true
        })

        setTimeout(() => {
            this.setData({
                paying: false
            }, 2000)
        })

        app.util.request({
            url: '/wxapp/createOrder',
            data: {
                draw_id: draw_id
            }
        }).then(res => {
            wx.requestPayment({
                timeStamp: res.data.timeStamp,
                nonceStr: res.data.nonceStr,
                package: res.data.package,
                signType: 'MD5',
                paySign: res.data.paySign,
                success: function (res) {
                    app.util.message('支付成功', 'error', function() {
                        wx.switchTab({
                           url: '/pages/draw/index',
                        })
                    })
                },
                fail: function (res) {
                    app.util.message('支付失败，请重试', 'error');
                }
            })
        })
    },
    toKefu() {
        wx.redirectTo({
          url: '/pages/article/article?type=kefu'
        })
    }
})