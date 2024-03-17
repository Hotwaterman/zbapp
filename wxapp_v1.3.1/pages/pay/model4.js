const app = getApp();
Page({
    data: {
        pageIsLoad: false,
        is_check: -1,
        is_ios_pay: 0,
        system: '',
        balance_model4: 0,
        hasModel4: false,
        model4Name: '',
        model4List: [],
        model4_id: 0,
        paying: false
    },

    onLoad: function (options) {
        this.setData({
            system: app.globalData.system
        })
        app.util.checkLogin().then(() => {
            this.getWxappInfo()
            this.checkModel4()
            this.getBalance()
            this.getModel4List()
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

    getModel4List() {
        app.util.request({
            url: '/wxapp/getModel4List'
        }).then(res => {
            const model4List = res.data
            model4List.forEach(item => {
                if(item.is_default) {
                    this.setData({
                        model4_id: item.id
                    })
                }
            })
            this.setData({
                model4List: model4List
            })
        })
    },
    getBalance() {
        app.util.request({
            url: '/wxapp/getBalance',
            loading: false
        }).then(res => {
            this.setData({
                balance_model4: res.data.balance_model4
            })
        })
    },

    checkModel4() {
        app.util.request({
            url: '/wxapp/hasModel4',
            loading: false
        })
        .then((res) => {
            this.setData({
                hasModel4: res.data.hasModel4,
                model4Name: res.data.model4Name
            })
        });
    },

    chooseModel4(e) {
        const id = e.currentTarget.dataset.id
        this.setData({
            model4_id: id
        })
    },
    
    getWxappInfo() {
        const _this = this
        app.util.request({
            url: '/wxapp/getWxappInfo',
            loading: false
        }).then(res => {
            if(res.data.is_check == 1) {
                wx.setNavigationBarTitle({
                  title: '功能尚未上线'
                })
            } else {
                wx.setNavigationBarTitle({
                    title: '充值'
                })
            }
            _this.setData({
                is_check: res.data.is_check,
                is_ios_pay: res.data.is_ios_pay
            })
        })
    },

    doPayModel4: function () {
		if (this.data.paying) {
			return false;
        }
        var model4_id = this.data.model4_id;

        if (model4_id == 0) {
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
                model4_id: model4_id
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
                           url: '/pages/index/index',
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