const app = getApp()
Page({
    data: {
        code: ''
    },
    onLoad(options) {
        if(options.scene) {
            let scene = decodeURIComponent(options.scene)
            let param = app.util.queryToArr(scene)
            if(param.code) {
                this.setData({
                    code: param.code
                })
            }
        }
        app.util.login()
    },
    doLogin() {
        if(!this.data.code) {
            app.util.message('参数错误', 'error');
            return;
        }
        app.util.request({
            url: '/wxapp/pclogin',
            data: {
                code: this.data.code
            }
        }).then(res => {
            app.util.message(res.message)
            setTimeout(() => {
                wx.switchTab({
                    url: '/pages/user/index'
                })
            }, 800)
            
        })
    }
})