<template>
  <div class="app-container" style="padding: 0;">
    <div class="search">
      <p>
        <span class="label">选择站点：</span>
        <el-select v-model="search.site_id" size="small" clearable placeholder="请选择站点">
          <el-option
            v-for="item in siteList"
            :key="item.id"
            :label="item.title"
            :value="item.id"
          />
        </el-select>
      </p>
      <p>
        <span class="label">注册时间：</span>
        <el-date-picker
          v-model="search.date"
          align="right"
          type="datetimerange"
          format="yyyy-MM-dd HH:mm:ss"
          :default-time="['00:00:00', '23:59:59']"
          range-separator="至"
          start-placeholder="时间开始"
          end-placeholder="时间结束"
          size="mini"
          style="width: 340px;"
          @change="dateChange"
        />
        <el-radio-group v-model="dateType" size="mini" style="margin-left: 10px;" @change="dateTypeChange">
          <el-radio-button label="day0">今天</el-radio-button>
          <el-radio-button label="day1">昨天</el-radio-button>
          <el-radio-button label="day2">前天</el-radio-button>
        </el-radio-group>
      </p>
      <p>
        <span class="label">用户ID：</span>
        <el-input v-model="search.user_id" type="text" size="mini" clearable placeholder="请输入用户id" style="width: 120px;" />
      </p>
      <p>
        <span class="label">昵称：</span>
        <el-input v-model="search.keyword" type="text" size="mini" clearable placeholder="请输入昵称" style="width: 160px;" />
      </p>
      <p style="padding-top: 10px;">
        <span class="label" />
        <el-button
          type="primary"
          icon="el-icon-check"
          size="mini"
          @click="doSearch"
        >筛选</el-button>
        <el-button
          type="default"
          plain
          size="mini"
          style="margin-left: 10px;"
          @click="doInit"
        >重置</el-button>
      </p>
    </div>
    <div class="box-panel" style="padding-bottom: 10px; margin-bottom: 0;">
      <el-table
        :data="dataList"
        stripe
        size="medium"
        header-cell-class-name="bg-gray"
      >
        <el-table-column prop="id" label="用户ID" width="80" />
        <el-table-column prop="create_time" label="注册时间" width="160" />
        <el-table-column prop="avatar" label="头像" width="100" align="center">
          <template slot-scope="scope">
            <a :href="scope.row.avatar" target="_blank"><img :src="scope.row.avatar" style="height:36px;"></a>
          </template>
        </el-table-column>
        <el-table-column prop="nickname" label="昵称" width="180" />
        <!--<el-table-column prop="realname" label="姓名" width="120" />-->
        <el-table-column prop="phone" label="手机号" width="180" />
        <el-table-column label="余额" width="100">
          <template slot-scope="scope">
            {{ scope.row.balance }} 条
          </template>
        </el-table-column>
        <el-table-column label="会员" width="200">
          <template slot-scope="scope">
            {{ scope.row.vip_expire_time }}
          </template>
        </el-table-column>
        <el-table-column label="共消费" width="100">
          <template slot-scope="scope">
            {{ scope.row.order_total }} 元
          </template>
        </el-table-column>
        <el-table-column label="共提问">
          <template slot-scope="scope">
            {{ scope.row.msg_count }} 条
          </template>
        </el-table-column>
      </el-table>
      <div class="footer">
        <div class="tongji">
          <i class="el-icon-s-data text-green" style="font-size: 20px;" />
          共
          <span class="text-green">
            {{ tongji.userCount }}
          </span>
          用户，余额
          <span class="text-danger">
            {{ tongji.userBalance }}
          </span>
          条
        </div>
        <el-pagination
          :current-page="page"
          :page-size="pagesize"
          layout="total, prev, pager, next"
          :total="dataTotal"
          @current-change="pageChange"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { getUserList, getTongji } from '@/api/user'
import { getSelectSiteList } from '@/api/site'

export default {
  data() {
    return {
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 15,
      dateType: '',
      search: {
        date: [],
        site_id: '',
        user_id: '',
        keyword: ''
      },
      tongji: {
        userCount: 0,
        userBalance: 0
      },
      siteList: []
    }
  },
  created() {
    this.doInit()
  },
  methods: {
    doInit() {
      this.dateType = ''
      this.search = {
        date: [],
        site_id: '',
        user_id: '',
        keyword: ''
      }
      this.doSearch()
      this.getSiteList()
    },
    getList() {
      const param = Object.assign(this.search, {
        page: this.page,
        pagesize: this.pagesize
      })
      getUserList(param).then(res => {
        this.dataList = res.data.list
        this.dataTotal = res.data.count
      })
      if (this.page === 1) {
        getTongji(param).then(res => {
          this.tongji = res.data
        })
      }
    },
    getSiteList() {
      getSelectSiteList().then(res => {
        this.siteList = res.data
      })
    },
    tableIndex(index) {
      return this.pagesize * (this.page - 1) + index + 1
    },
    pageChange(page) {
      this.page = page
      this.getList()
    },
    doSearch() {
      this.page = 1
      this.getList()
    },
    dateChange() {
      this.dateType = ''
    },
    dateTypeChange() {
      var day = ''
      // 今天
      var day0 = this.dateFormat(new Date(), 'yyyy-MM-dd')
      // 昨天
      var day1 = this.dateFormat(new Date(new Date().setDate(new Date().getDate() - 1)), 'yyyy-MM-dd')
      // 前天
      var day2 = this.dateFormat(new Date(new Date().setDate(new Date().getDate() - 2)), 'yyyy-MM-dd')

      if (this.dateType === 'day0') {
        day = day0
      } else if (this.dateType === 'day1') {
        day = day1
      } else if (this.dateType === 'day2') {
        day = day2
      }
      this.search.date = [day + ' 00:00:00', day + ' 23:59:59']
    },
    dateFormat(date, format) {
      var o = {
        'M+': date.getMonth() + 1, // month
        'd+': date.getDate(), // day
        'h+': date.getHours(), // hour
        'm+': date.getMinutes(), // minute
        's+': date.getSeconds(), // second
        'q+': Math.floor((date.getMonth() + 3) / 3), // quarter
        'S': date.getMilliseconds() // millisecond
      }
      if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length))
      }
      for (var k in o) {
        if (new RegExp('(' + k + ')').test(format)) {
          format = format.replace(RegExp.$1, RegExp.$1.length === 1 ? o[k] : ('00' + o[k]).substr(('' + o[k]).length))
        }
      }
      return format
    }
  }
}
</script>
<style lang="scss" scoped>
  .search {
    padding: 10px 0;
    background: #f8fafc;

    .label {
      font-weight: normal;
      font-size: 13px;
      color: #666;
      display: inline-block;
      width: 100px;
      text-align: right;
      padding-right: 5px;
    }

    .el-input {
      width: 180px;
    }

    p {
      margin: 10px 0;
    }
  }
  .footer {
    width: 100%;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;

    .tongji {
      height: 32px;
      line-height: 32px;
      font-size: 14px;
      color: #666;
      min-width: 240px;
    }

    .tongji span {
      margin: 0 4px;
      font-weight: bold;
      font-size: 16px;
    }

    .tongji span i {
      font-size: 18px;
      margin-right: 5px;
    }
  }
</style>
