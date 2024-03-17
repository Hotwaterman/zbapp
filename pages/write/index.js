const app = getApp()
Page({
    data: {
        topicList: [],
        tabWidth: 160,
        promptList: [],
        promptCount: 0,
        page: 1,
        pagesize: 10,
        scrollTop: 0,
        shareTitle: '',
        shareImage: '',
        timeline_share_id: 0,
        topic_id: 'all'
    },
    onLoad(options) {
        if (options.sid) {
            // 分享id
            wx.setStorageSync('sid', options.sid)
        }

        this.setData({
            system: app.globalData.system
        })
        app.util.login().then(() => {
            this.getTopicList()
            this.getPrompts()
            this.getWxappInfo()
        })
    },
    switchTopic: function (e) {
        let topic_id = e.currentTarget.dataset.topic
        this.setData({
            topic_id: topic_id
        });
        if(topic_id == 'vote') {
            this.setData({
                page: 1,
                promptList: []
            })
            this.getVotePrompts()
        } else {
            this.setData({
                page: 1
            })
            this.getPrompts();
        }
    },
    getTopicList() {
        app.util.request({
            url: '/write/getTopicList'
        }).then(res => {
            this.setData({
                topicList: res.data
            })
            this.calcTabWidth()
        })
    },
    calcTabWidth() {
        const topicList = this.data.topicList
        var tabWidth = 160, len = 0;
        topicList.forEach((item) => {
            len = this.strLen(item.title)
            tabWidth += len * 18
        })
        this.setData({
            tabWidth: tabWidth
        })
    },
    strLen(str) {
        var len = 0;
        for (var i = 0; i < str.length; i++) {
            if (str.charCodeAt(i) >= 0 && str.charCodeAt(i) <= 128){
                len += 1
            } else {
                len += 2
            }
        }
        return len
    },
    getPrompts() {
        const topic_id = this.data.topic_id
        const page = this.data.page
        const pagesize = this.data.pagesize
        app.util.request({
            url: '/write/getPrompts',
            data: {
                topic_id: topic_id,
                page: page,
                pagesize: pagesize
            }
        }).then(res => {
            this.setData({
                promptList: res.data.list,
                promptCount: res.data.count
            })
            this.scrollToTop()
        })
    },
    getVotePrompts() {
        const page = this.data.page
        const pagesize = this.data.pagesize
        app.util.request({
            url: '/write/getVotePrompts',
            data: {
                page: page,
                pagesize: pagesize
            }
        }).then(res => {
            this.setData({
                promptList: res.data.list,
                promptCount: res.data.count
            })
            this.scrollToTop()
        })
    },
    pageChange(e) {
        var page = e.detail
        this.setData({
            page: page
        })
        if(this.data.topic_id == 'vote') {
            this.getVotePrompts()
        } else {
            this.getPrompts();
        }
    },
    doVote(e) {
        const prompt_id = e.currentTarget.dataset.id
        app.util.request({
            url: '/write/votePrompt',
            data: {
                prompt_id: prompt_id
            }
        }).then(res => {
           app.util.message(res.message)
           var promptList = this.data.promptList
           for(var i in promptList) {
               if(promptList[i].id == prompt_id) {
                   promptList[i].isVote = promptList[i].isVote ? 0 : 1
               }
           }
           this.setData({
               promptList: promptList
           })
        })
    },
    scrollToTop() {
        setTimeout(() => {
            this.setData({
                scrollTop: 0
            })
        }, 100)
    },
    toPay() {
        wx.navigateTo({
            url: '/pages/pay/pay'
        })
    },
    toWriteChat(e) {
        const id = e.currentTarget.dataset.id
        wx.navigateTo({
            url: '/pages/write/chat?prompt_id=' + id
        })
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
        console.log('sh', share_id)
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