const app = getApp();
Page({
    data: {
        code: '',
        card: null,
        hasModel4: false,
        model4Name: ''
    },
    onLoad() {
        this.checkModel4()
    },
    getCardInfo() {
        app.util.request({
            url: '/user/getCardInfo',
            data: {
                code: this.data.code
            }
        }).then(res => {
            this.setData({
                card: res.data
            })
       })
    },
    doInput() {
        this.setData({
            card: null
        })
    },
    bindCard() {
        app.util.request({
            url: '/user/bindCard',
            data: {
                code: this.data.code
            }
        }).then(res => {
            app.util.message(res.message, 'error', () => {
                wx.navigateBack()
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
    }

})