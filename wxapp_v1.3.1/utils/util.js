var util = {};
util.request = function (option) {
    var app = getApp()
    var option = option ? option : {}

    // 组装url
    var url = option.url;
    if (url.indexOf('http://') == -1 && url.indexOf('https://') == -1) {
        url = app.globalData.siteroot + option.url
    }
    if (!url) {
        return false;
    }

    // 是否显示loading
    option.loading = typeof option.loading != 'undefined' ? option.loading : true
    if (option.loading) {
        wx.showNavigationBarLoading();
        wx.showLoading({
            title: '加载中'
        });
    }

    var data = option.data ? option.data : {}
    if (app.globalData.site_id) {
        data.site_id = app.globalData.site_id
    }
    return new Promise(function (resolve, reject) {
        wx.request({
            url: url,
            data: data,
            method: option.method ? option.method : 'POST',
            dataType: 'json',
            header: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Token': wx.getStorageSync('token')
            },
            timeout: option.timeout ? option.timeout : 60000,
            success: function (res) {
                if (res.statusCode != 200) {
                    reject(res)
                    return
                }
                if (res.data.errno > 0) {
                    if (res.data.errno === 403) {
                        // 需要登录后才可以操作
                        if(option.url == '/wxapp/checkLogin') {
                            reject(res)
                        } else {
                            // 登录后重新发起请求
                            util.login().then(() => {
                                util.request(option).then((res) => {
                                    resolve(res)
                                })
                            })
                        }
                        return
                    } else {
                        if (res.data.message) {
                            util.message(res.data.message, 'error')
                        }
                        reject(res.data)
                    }
                } else {
                    resolve(res.data)
                }
            },
            fail: function (err) {
                util.message('网络错误', 'error')
                reject(err)
            },
            complete: function (res) {
                if (option.loading) {
                    wx.hideLoading();
                    wx.hideNavigationBarLoading();
                }
            }
        })
    })
}

util.upload = function (option = null) {
    var app = getApp()
    var option = option ? option : {}

    // 组装url
    var url = option.url;
    if (url.indexOf('http://') == -1 && url.indexOf('https://') == -1) {
        url = app.globalData.siteroot + option.url
    }
    if (!url) {
        return false;
    }

    // 是否显示loading
    option.loading = typeof option.loading != 'undefined' ? option.loading : true
    if (option.loading) {
        wx.showNavigationBarLoading();
        wx.showLoading({
            title: '上传中'
        });
    }

    var data = option.data ? option.data : {}
    if (app.globalData.site_id) {
        data.site_id = app.globalData.site_id
    }

    return new Promise(function (resolve, reject) {
        wx.uploadFile({
            url: url,
            data: option.data ? option.data : {},
            filePath: option.filePath,
            name: option.name ? option.name : 'image',
            header: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Token': wx.getStorageSync('token')
            },
            dataType: 'json',
            success: function (response) {
                const res = JSON.parse(response.data)
                if (res.errno > 0) {
                    if (res.errno === 403) {
                        // 需要登录后才可以操作
                        wx.navigateTo({
                            url: '/pages/login/login'
                        });
                    } else {
                        if (res.message) {
                            util.message(res.message, 'error')
                        }
                        reject(res)
                    }
                } else {
                    resolve(res)
                }
            },
            fail: function (err) {
                util.message('网络错误', 'error')
                reject(err)
            },
            complete: function (res) {
                if (option.loading) {
                    wx.hideLoading();
                    wx.hideNavigationBarLoading();
                }
            }
        })
    })
}

util.login = function () {
    return new Promise(function (resolve, reject) {
        var sid = wx.getStorageSync('sid')
        var tuid = wx.getStorageSync('tuid')
        wx.login({
            success: function (e) {
                var code = e.code
                util.request({
                    url: '/wxapp/login',
                    data: {
                        code: code,
                        sid: sid ? parseInt(sid) : 0,
                        tuid: tuid ? parseInt(tuid) : 0
                    }
                }).then(res => {
                    wx.setStorageSync('token', res.data.token)
                    if (sid) {
                        wx.removeStorageSync('sid')
                    }
                    if (tuid) {
                        wx.removeStorageSync('tuid')
                    }
                    resolve()
                }).catch(res => {
                    util.message(res.message, 'error', function () {
                        setTimeout(() => {
                            util.login()
                        }, 1000)
                    })
                })
            }
        })
    })
}

/**
 * 检查是否登录
 */
util.checkLogin = function () {
    return new Promise(function (resolve, reject) {
        util.request({
            url: '/wxapp/checkLogin',
            loading: false
        }).then((res) => {
            resolve(res)
        }).catch((res) => {
            util.login().then((res) => {
                resolve(res)
            }).catch((res) => {
                reject(res)
            })
        })
    })
}
/*
    * 提示信息
    * type 为 success, error 当为 success,  时，为toast方式，否则为模态框的方式
    */
util.message = function (title, type = 'none', callback = null) {
    if (!title) {
        return true;
    }
    if (type == 'success') {
        wx.showToast({
            title: title,
            icon: 'success',
            duration: 2000,
            mask: false,
            complete: function () {
                if (callback && typeof callback == 'function') {
                    setTimeout(function () {
                        callback()
                    }, 1800);
                }
            }
        });
    } else if (type == 'none') {
        wx.showToast({
            title: title,
            icon: 'none',
            duration: 2000,
            mask: false,
            complete: function () {
                if (callback && typeof callback == 'function') {
                    setTimeout(function () {
                        callback()
                    }, 1800);
                }
            }
        });
    } else if (type == 'error') {
        wx.showModal({
            title: '系统提示',
            content: title,
            showCancel: false,
            complete: function () {
                if (callback && typeof callback == 'function') {
                    callback()
                }
            }
        });
    }
}

util.queryToArr = function (str) {
    let result = [];
    let kv = [];
    let paramArr = str.split('&');
    for (let i in paramArr) {
        kv = paramArr[i].split('=');
        result[kv[0]] = kv[1];
    }
    return result;
}

module.exports = util