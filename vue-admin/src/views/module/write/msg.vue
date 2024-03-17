<template>
  <div class="app-container" style="padding: 0;">
    <div class="search">
      <p>
        <span class="label">提问时间：</span>
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
        <span class="label">关键词：</span>
        <el-input v-model="search.keyword" type="text" size="mini" clearable placeholder="请输入关键词" style="width: 200px;" />
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
        <el-table-column prop="id" label="ID" width="60" />
        <el-table-column prop="create_time" label="时间" width="170" />
        <el-table-column prop="user_id" label="用户ID" width="80" />
        <el-table-column label="使用模型">
          <template slot-scope="scope">
            <span>【{{scope.row.topic_title}}】{{scope.row.prompt_title}}</span>
          </template>
        </el-table-column>
        <el-table-column label="用户输入">
          <template slot-scope="scope">
            <span v-html="scope.row.message"></span>
            <el-button v-if="scope.row.message_input" type="text" size="small" @click="showMsgDialog(scope.row.message_input)" style="margin-left: 10px;">查看原文</el-button>
          </template>
        </el-table-column>
        <el-table-column label="AI回复" width="120">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="showMsgDialog(scope.row.response)" style="margin-left: 10px;">查看回复</el-button>
          </template>
        </el-table-column>
        <el-table-column prop="total_tokens" label="消耗tokens" width="100" />
        <el-table-column label="操作" width="120">
          <template slot-scope="scope">
              <el-button type="text text-danger" size="mini" icon="el-icon-delete" @click.native.prevent="doDel(scope.row.id)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="footer">
        <div class="tongji">
          <i class="el-icon-s-data text-green" style="font-size: 20px;" />
          共
          <span class="text-green">
            {{ tongji.msgCount }}
          </span>
          条问题，
          <span class="text-danger">
            {{ tongji.msgTokens }}
          </span>
          tokens
        </div>
        <el-pagination
          :current-page="page"
          :page-size="pagesize"
          layout="total, prev, pager, next"
          :total="dataTotal"
          @current-change="pageChange"
        />
      </div>
      <div v-if="dialogMessage">
        <el-dialog
          custom-class="my-dialog"
          title="查看原文"
          width="600px"
          :visible="true"
          :close-on-click-modal="true"
          :before-close="closeMsgDialog"
        >
          <p style="padding: 0; font-size: 14px; line-height: 24px; color: #333;" v-html="dialogMessage" />
          <span
            slot="footer"
            class="dialog-footer"
          >
            <el-button type="default" icon="el-icon-close" size="small" @click="closeMsgDialog">关 闭</el-button>
          </span>
        </el-dialog>
      </div>
    </div>
  </div>
</template>

<script>
import { getMsgList, getMsgTongji, delMsg } from '@/api/write'

export default {
  data() {
    return {
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 20,
      dateType: '',
      search: {
        date: [],
        user_id: '',
        keyword: ''
      },
      tongji: {
        msgCount: 0,
        msgTokens: 0
      },
      dialogMessage: ''
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
        keyword: ''
      }
      this.doSearch()
    },
    getList() {
      const param = Object.assign(this.search, {
        page: this.page,
        pagesize: this.pagesize
      })
      getMsgList(param).then(res => {
        this.dataList = res.data.list
        this.dataTotal = res.data.count
      })
      if (this.page === 1) {
        getMsgTongji(param).then(res => {
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
    showMsgDialog(msg) {
      this.dialogMessage = msg
    },
    closeMsgDialog() {
      this.dialogMessage = ''
    },
    doDel(id) {
      this.$confirm('确定删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delMsg({ id: id }).then(res => {
          this.getList()
          this.$message.success(res.message)
        })
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
      line-height: 24px;
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
