const app = getApp()
Page({
    data: {
        allRoles: []
    },
    onLoad(options) {
        if (options.sid) {
            // 分享id
            wx.setStorageSync('sid', options.sid)
        }

        this.setData({
            system: app.globalData.system
        })

        app.util.checkLogin().then(() => {
            this.getAllRoles();
            this.getWxappInfo()
        });
    },
    getAllRoles() {
        app.util.request({
                url: '/cosplay/getAllRoles'
            })
            .then((res) => {
                this.setData({
                    allRoles: res.data
                });
            });
    },
    toCosplayChat(e) {
        const id = e.currentTarget.dataset.id;
        wx.navigateTo({
            url: '/pages/cosplay/chat?role_id=' + id
        });
    },
    getWxappInfo() {
        const _this = this
        app.util.request({
            url: '/wxapp/getWxappInfo',
            loading: false
        }).then(res => {
            _this.setData({
                shareTitle: res.data.share_title,
                shareImage: res.data.share_image
            })
        })
    },
    async onShareAppMessage() {
        var share_id = await this.getShareId('wechat')
        return {
            title: this.data.shareTitle,
            imageUrl: this.data.shareImage,
            path: '/pages/write/index?sid=' + share_id
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