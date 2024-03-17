<template>
  <div class="app-container" style="padding: 0;">
    <div class="search">
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
        <span class="label">昵称：</span>
        <el-input v-model="search.keyword" type="text" size="mini" clearable placeholder="请输入昵称" style="width: 160px;" />
      </p>
      <p>
        <span class="label">类型：</span>
        <el-radio-group v-model="search.is_vip" size="mini">
          <el-radio-button label="all">全部</el-radio-button>
          <el-radio-button label="no">普通用户</el-radio-button>
          <el-radio-button label="yes">VIP会员</el-radio-button>
        </el-radio-group>
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
<!--        <el-table-column prop="openid" label="openid" width="300" />-->
        <el-table-column label="头像昵称" width="200">
          <template slot-scope="scope">
            <div style="display:flex; align-items:center;">
              <img :src="scope.row.avatar" style="height: 24px; margin-right: 5px;">
              {{ scope.row.nickname }}
            </div>
          </template>
        </el-table-column>
        <!--<el-table-column prop="realname" label="姓名" width="120" />-->
        <el-table-column prop="phone" label="手机号" width="180" />
        <el-table-column label="对话余额" width="110">
          <template slot-scope="scope">
            <el-button type="text" size="mini" @click.native.prevent="showRecharge(scope.row.id, 'chat')">{{ scope.row.balance }} 条</el-button>
          </template>
        </el-table-column>
        <el-table-column label="绘画余额" width="110">
          <template slot-scope="scope">
            <el-button type="text" size="mini" @click.native.prevent="showRecharge(scope.row.id, 'draw')">{{ scope.row.balance_draw }} 张</el-button>
          </template>
        </el-table-column>
        <el-table-column label="GPT4余额" width="110">
          <template slot-scope="scope">
            <el-button type="text" size="mini" @click.native.prevent="showRecharge(scope.row.id, 'gpt4')">{{ scope.row.balance_gpt4 }} 万字</el-button>
          </template>
        </el-table-column>
        <el-table-column label="会员" width="140">
          <template slot-scope="scope">
            <el-button type="text" size="mini" @click.native.prevent="showVipForm(scope.row.id, scope.row.vip_expire_time)">{{ scope.row.vip_expire_time || '未开通' }}</el-button>
          </template>
        </el-table-column>
        <el-table-column label="共消费" width="100">
          <template slot-scope="scope">
            {{ scope.row.order_total }} 元
          </template>
        </el-table-column>
        <el-table-column label="共提问" width="100">
          <template slot-scope="scope">
            {{ scope.row.msg_count }} 条
          </template>
        </el-table-column>
        <el-table-column prop="commission1" label="推荐人">
          <template slot-scope="scope">
            <div v-if="scope.row.tuser" style="display:flex; align-items:center;">
              <img :src="scope.row.tuser.avatar" style="height: 24px; margin-right: 5px;">
              {{ scope.row.tuser.nickname }} [ID: {{scope.row.tuid}}]
            </div>
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
          用户，对话余额
          <span class="text-danger">
            {{ tongji.userBalance }}
          </span>条，绘画余额
          <span class="text-danger">
            {{ tongji.userBalanceDraw }}
          </span>张，GPT4余额
          <span class="text-danger">
            {{ tongji.userBalanceGpt4 }}
          </span>万字
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
    <div v-if="rechargeForm">
      <el-dialog
        custom-class="my-dialog"
        title="余额充值"
        width="480px"
        :visible="true"
        :close-on-click-modal="false"
        :append-to-body="true"
        :before-close="closeRecharge"
      >
        <el-form ref="form" :model="rechargeForm" label-width="120px">
          <el-form-item label="充值类型" prop="type">
            <el-radio-group v-model="rechargeForm['type']">
              <el-radio label="chat" size="mini" style="margin: 0 20px 5px 0;">对话余额</el-radio>
              <el-radio label="draw" size="mini">绘画余额</el-radio>
              <el-radio label="gpt4" size="mini">GPT4余额</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item v-if="rechargeForm.type === 'chat'" label="充值条数" prop="num">
            <el-input v-model="rechargeForm.num" placeholder="填入整数" size="small" style="width: 130px;" />
            <span style="color:#666;margin-left:10px;"> 条</span>
            <p style="color:#999;">正数为增加，负数为减少</p>
          </el-form-item>
          <el-form-item v-if="rechargeForm.type === 'draw'" label="充值数量" prop="num">
            <el-input v-model="rechargeForm.num" placeholder="填入整数" size="small" style="width: 130px;" />
            <span style="color:#666;margin-left:10px;"> 张</span>
            <p style="color:#999;">正数为增加，负数为减少</p>
          </el-form-item>
          <el-form-item v-if="rechargeForm.type === 'gpt4'" label="充值字数" prop="num">
            <el-input v-model="rechargeForm.num" placeholder="填入整数" size="small" style="width: 130px;" />
            <span style="color:#666;margin-left:10px;"> 万字</span>
            <p style="color:#999;">正数为增加，负数为减少</p>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="default" icon="el-icon-close" size="small" @click="closeRecharge">取 消</el-button>
          <el-button type="primary" icon="el-icon-check" size="small" @click="doRecharge">提 交</el-button>
        </span>
      </el-dialog>
    </div>

    <div v-if="vipForm">
      <el-dialog
        custom-class="my-dialog"
        title="设置vip会员时间"
        width="480px"
        :visible="true"
        :close-on-click-modal="false"
        :append-to-body="true"
        :before-close="closeVipForm"
      >
        <el-form ref="form" :model="vipForm" label-width="120px">
          <el-form-item label="到期时间" prop="vip_expire_time">
            <el-date-picker
              v-model="vipForm.vip_expire_time"
              type="date"
              placeholder="留空则表示关闭会员"
              value-format="yyyy-MM-dd"
              editable
              clearable
            >
            </el-date-picker>
            <p>留空表示关闭会员</p>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="default" icon="el-icon-close" size="small" @click="closeVipForm">取 消</el-button>
          <el-button type="primary" icon="el-icon-check" size="small" @click="setVipTime">提 交</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { getUserList, getTongji, doRecharge, setVipTime } from '@/api/user'

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
        user_id: '',
        is_vip: 'all',
        keyword: ''
      },
      tongji: {
        userCount: 0,
        userBalance: 0
      },
      rechargeForm: null,
      vipForm: null
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
        user_id: '',
        is_vip: 'all',
        keyword: ''
      }
      this.doSearch()
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
    },
    showRecharge(user_id, type = 'chat') {
      this.rechargeForm = {
        type: type,
        user_id: user_id
      }
    },
    closeRecharge() {
      this.rechargeForm = null
    },
    doRecharge() {
      doRecharge(this.rechargeForm).then(res => {
        this.getList()
        this.$message({
          message: res.message,
          type: 'success',
          duration: 1500
        })
        this.closeRecharge()
      })
    },
    showVipForm(user_id, vip_expire_time = '') {
      this.vipForm = {
        user_id: user_id,
        vip_expire_time: vip_expire_time
      }
    },
    closeVipForm() {
      this.vipForm = null
    },
    setVipTime() {
      setVipTime(this.vipForm).then(res => {
        this.getList()
        this.$message.success(res.message)
        this.closeVipForm()
      })
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
