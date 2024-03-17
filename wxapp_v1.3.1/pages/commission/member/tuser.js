
const app = getApp()
Page({
    data: {
        list: [],
        page: 1,
        noMore: 0,
        curTab: 1
    },

    onLoad() {
        this.getList();
    },
    switchTab(e) {
        const index = e.currentTarget.dataset.index
        this.setData({
            curTab: index,
			page: 1,
			list: [],
			noMore: 0
        })
        this.getList()
    },
    onReachBottom() {
        this.getList();
    },
    onPullDownRefresh() {
        this.setData({
			page: 1,
			list: [],
			noMore: 0
		})
		this.getList();
    },
    getList() {
        let page = this.data.page
        let curTab = this.data.curTab
        app.util.request({
            url: '/commission/tuserList',
            data: {
                type: curTab,
                page: page
            }
        }).then((res) => {
            if (res.data.length > 0) {
                this.setData({
                    list: this.data.list.concat(res.data),
                    noMore: 0,
                    page: (page + 1)
                });
            } else {
                this.setData({
                    noMore: 1
                });
            }
        });
    }
})