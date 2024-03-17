const app = getApp();
Page({
    data: {
        is_check: 1,
        userinfo: {},
        system: '',
    },
    onLoad: function (options) {
        this.setData({
            system: app.globalData.system
        })
    },

    onShow() {
        this.getUserInfo()
    },

    getUserInfo() {
        app.util.request({
            url: '/user/getInfo'
        }).then(res => {
            this.setData({
                userinfo: res.data,
                is_check: res.data.is_check
            })
        })
    },

    getUserProfile() {
        wx.getUserProfile({
            desc: '使用微信登录'
        }).then(e => {
            if (!(e.errMsg.indexOf('fail') > 0)) {
                app.util.request({
                    url: '/user/setUserAvatar',
                    data: {
                        encryptedData: e.encryptedData,
                        iv: e.iv
                    }
                }).then(res => {
                    this.getUserInfo()
                })
            } else {
                app.util.message('授权失败', 'error')
            }
        }).catch(() => {
            
        })
    },
    getUserPhone(e) {
        if (e.detail.errMsg != 'getPhoneNumber:ok') {
            var message = e.detail.errMsg
            if (message.indexOf('user deny') !== -1) {
                message = '已取消授权'
            }
            app.util.message(message, 'error')
            return
        }
        app.util.request({
            url: '/user/setUserPhone',
            data: {
                encryptedData: e.detail.encryptedData,
                iv: e.detail.iv
            }
        }).then(res => {
            this.getUserInfo()
        })
    },

    linkto(e) {
        const url = e.currentTarget.dataset.url
        wx.navigateTo({
            url: url
        })
    },
    toPayVip() {
        wx.navigateTo({
            url: '/pages/pay/vip'
        })
    },
    toTask() {
        wx.switchTab({
            url: '/pages/task/index'
        })
    },
    toInputNickname() {
        wx.navigateTo({
            url: '/pages/user/nickname'
        })
    }
})