const app = getApp()
Page({
    data: {
        share: {},
        currentHaibao: 0,
        currentText: ''
    },
    onLoad() {
        this.getShare()
    },

    getShare: function() {
        app.util.request({
            url: '/commission/getShare'
        }).then(res => {
            this.setData({
                share: res.data,
                currentText: res.data.defaultText,
                currentHaibao: res.data.defaultHaibao
            })
        })
    },
    copyText() {
        var text = this.data.currentText
        if(!text) {
            return false
        }
        text = text.replaceAll("<br>", "\n");
        wx.setClipboardData({
            data: text,
            success: function() {
                app.util.message('复制成功')
            }
        })
    },
    nextText() {
        var currentText = this.data.currentText
        var textArr = this.data.share.textArr
        if(!textArr || textArr.length <= 1) {
            app.util.message('没有更多了')
            return false
        }
        textArr.forEach((item, index) => {
            if(item.content == currentText) {
                // 切换到下一个
                if(index === textArr.length -1) {
                    this.setData({
                        currentText: textArr[0]['content']
                    })
                } else {
                    this.setData({
                        currentText: textArr[index+1]['content']
                    })
                }
            }
        })
    },
    swiperChange: function(e) {
        this.setData({
            currentHaibao: e.detail.current
        })
    },
    saveToAlbum: function() {
        const context = wx.createCanvasContext('poster')
        const haibaoArr = this.data.share.haibaoArr
        const currentHaibao = this.data.currentHaibao
        const haibao = haibaoArr[currentHaibao]
        const qrcode = this.data.share.qrcode
        var bgOk = false
        var qrcodeOk = false
        wx.showLoading({
            title: '保存中'
        })
        // 下载背景图
        wx.downloadFile({
            url: haibao.bg,
            success: function(res) {
                context.drawImage(res.tempFilePath, 0, 0, haibao.bg_w, haibao.bg_h)
                bgOk = true;
                // 下载二维码
                wx.downloadFile({
                    url: qrcode,
                    success: function(res) {
                        context.drawImage(res.tempFilePath, haibao.hole_x, haibao.hole_y, haibao.hole_w, haibao.hole_h)
                        qrcodeOk = true;
                    }
                })
            }
        })
        var si = setInterval(() => {
            if(bgOk && qrcodeOk) {
                clearInterval(si)
                context.draw()
                wx.hideLoading()
                setTimeout(() => {
                    wx.canvasToTempFilePath({
                        canvasId: 'poster',
                        width: haibao.bg_w,
                        height: haibao.bg_h,
                        destWidth: haibao.bg_w,
                        destHeight: haibao.bg_h,
                        quality: 1,
                        fileType: 'jpg',
                        success: (res) => {
                            wx.saveImageToPhotosAlbum({
                                filePath: res.tempFilePath,
                                success: function() {
                                    app.util.message('已保存到相册', 'success')
                                },
                                fail: function(res) {
                                    console.log('error', res)
                                    app.util.message('保存失败，请检查是否有保存到相册权限', 'error')
                                }
                            })
                        }
                    })
                }, 300)
            }
        }, 500)
    }
})