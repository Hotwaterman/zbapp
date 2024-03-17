const app = getApp();
Page({
    data: {
        siteroot: '',
        lists: [],
        message: '',
        page: 1,
        pagesize: 10,
        scrollTop: 0,
        inputShow: true
    },
    onLoad(options) {
        if (options.sid) {
            // 分享id
            wx.setStorageSync('sid', options.sid);
        }
        
        this.setData({
            system: app.globalData.system,
            siteroot: app.globalData.siteroot.replace('/api.php', '')
        });
        app.util.checkLogin().then(() => {
            this.getHistoryMsg();
        });
    },

    submitDraw() {
        var message = this.trim(this.data.message)
        if (!message) {
            app.util.message('请输入要求和场景描述')
            this.setData({
                message: ''
            })
            return
        }
        this.setData({
            message: ''
        })
        this.scrollToBottom()
        
        app.util.request({
            url: '/draw/draw',
            data: {
                message: message
            }
        })
        .then(res => {
            var lists = this.data.lists
            lists.push({
                state: 0,
                draw_id: res.data.draw_id,
                message: message
            });
            this.setData({
                lists: lists
            })
            
            this.startLoopResult(res.data.draw_id);
            
            setTimeout(() => {
                this.scrollToBottom()
            }, 500)
        })
        .catch(res => {
            if(res.message.indexOf('请充值') !== -1) {
                app.util.message(res.message, 'error', function() {
                    wx.navigateTo({
                        url: '/pages/pay/draw'
                    })
                });
            }
        })
    },

    sendConfirm() {
        setTimeout(() => {
          this.submitDraw()
        }, 50)
    },
    
    startLoopResult(draw_id) {
        var si = setInterval(() => {
            app.util.request({
                url: '/draw/getDrawResult',
                data: {
                    draw_id: draw_id
                },
                loading: false
            })
            .then(res => {
                var lists = this.data.lists
                lists.forEach((item, index) => {
                    if(item.draw_id == draw_id) {
                        var state = res.data.state
                        lists[index].state = state
                        if(state != 0) {
                            clearInterval(si)
                            if(state == 1) {
                                lists[index].response = res.data.images
                            }
                            else if(state == 2) {
                                lists[index].errmsg = res.data.message
                            }
                            this.setData({
                                lists: lists
                            })
                            setTimeout(() => {
                                this.scrollToBottom()
                            }, 300)
                        }
                    }
                })
            });
        }, 5000)
    },

    retryDraw(e) {
        const draw_id = e.detail
        app.util.request({
            url: '/draw/draw',
            data: {
                draw_id: draw_id
            }
        })
        .then((res) => {
            var lists = this.data.lists
            lists.forEach((item, index) => {
                if(item.draw_id == draw_id) {
                    lists[index].state = 0
                    lists[index].errmsg = ''
                    lists[index].response = ''
                    this.startLoopResult(draw_id)
                }
            })
            this.setData({
                lists: lists
            })
        })
    },

    getHistoryMsg() {
        app.util
            .request({
                url: '/draw/getHistoryMsg',
                data: {
                    page: this.data.page,
                    pagesize: this.data.pagesize
                }
            })
            .then((res) => {
                if (res.data.length > 0) {
                    var lists = res.data
                    this.setData({
                        lists: lists
                    });
                    lists.forEach((item, index) => {
                        if(item.state == 0) {
                            this.startLoopResult(item.draw_id);
                        }
                    })
                }
                setTimeout(() => {
                    this.scrollToBottom();
                }, 500)
            });
    },

    scrollToBottom() {
        setTimeout(() => {
            let query = wx.createSelectorQuery().in(this);
            query.select('.list').boundingClientRect((res) => {
                this.setData({
                    scrollTop: parseInt(res.height)
                });
            });
            query.exec((res) => {});
        }, 100);
    },

    copyText(e) {
        const index = e.currentTarget.dataset.index
        const lists = this.data.lists
        let message = lists[index].message
        wx.setClipboardData({
            data: message
        })
    },
    
    previewImage(e) {
        var src = e.detail
        var urls = []
        this.data.lists.forEach((item, index) => {
            if(item.state == 1) {
                item.response.forEach((img, idx) => {
                    urls.push(img)
                })
            }
        })
        wx.previewImage({
            urls: urls,
            current: src
        })
    },

    trim(str) {
        if (str) {
            return str.replace(/(^\s*)|(\s*$)/g, '');
        } else {
            return ''
        }
    },
    
    toEdit(e) {
        const index = e.currentTarget.dataset.index
        this.setData({
            message: this.data.lists[index].message
        })
    }
})