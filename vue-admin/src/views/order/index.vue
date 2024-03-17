<template>
  <div class="app-container" style="padding: 0;">
    <div class="search">
      <!--<p>
          <span class="label">支付方式：</span>
          <el-radio-group v-model="search.pay_type" size="mini">
            <el-radio-button plain label="">全部</el-radio-button>
            <el-radio-button plain label="wxpay">微信</el-radio-button>
            <el-radio-button plain label="alipay">支付宝</el-radio-button>
          </el-radio-group>
        </p>-->
      <p>
        <span class="label">支付时间：</span>
        <el-date-picker
          v-model="search.date"
          align="right"
          type="datetimerange"
          format="yyyy-MM-dd HH:mm:ss"
          :default-time="['00:00:00', '23:59:59']"
          range-separator="至"
          start-placeholder="支付时间-开始"
          end-placeholder="支付时间-结束"
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
        <span class="label">订单号：</span>
        <el-input v-model="search.trade_no" type="text" size="mini" clearable placeholder="请输入单号" style="width: 200px;" />
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
        <el-button
          :loading="exportLoading"
          type="text"
          icon="el-icon-download"
          size="mini"
          style="margin-left: 10px;"
          @click="doExport"
        >导出</el-button>
      </p>
    </div>
    <div class="box-panel" style="padding-bottom: 10px; margin-bottom: 0;">
      <el-table
        :data="dataList"
        element-loading-text="加载中..."
        stripe
        size="medium"
        header-cell-class-name="bg-gray"
        @row-dblclick="rowDblclick"
      >
        <el-table-column prop="id" label="订单ID" width="120" />
        <el-table-column prop="user_id" label="用户ID" width="120" />
        <el-table-column prop="pay_time" label="支付时间" width="180" />
        <el-table-column prop="total_fee" label="订单金额" width="120">
          <template slot-scope="scope">
            <span>￥{{ scope.row.total_fee }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="out_trade_no" label="订单号" width="200" />
        <el-table-column prop="agent_title" label="数量 / 时长" width="120">
          <template slot-scope="scope">
            <span v-if="scope.row.goods_id">{{ scope.row.num }} 条</span>
            <span v-if="scope.row.draw_id">{{ scope.row.num }} 张</span>
            <span v-if="scope.row.gpt4_id">{{ scope.row.num }} 万字</span>
            <span v-if="scope.row.vip_id">{{ scope.row.num }} 个月</span>
          </template>
        </el-table-column>
        <el-table-column label="订单状态" width="140">
          <template slot-scope="scope">
            <span class="text-green">
              支付成功
            </span>
            <span v-if="scope.row.is_refund === 1" class="text-danger">
              [已退]
            </span>
          </template>
        </el-table-column>
        <el-table-column label="操作">
          <template slot-scope="scope">
            <el-button
              type="text"
              plain
              size="mini"
              @click.native.prevent="showOrderDetail(scope.row.out_trade_no)"
            >查看详情</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="footer">
        <div class="tongji">
          <i class="el-icon-s-data text-green" style="font-size: 20px;" />
          共
          <span class="text-green">
            {{ tongji.orderCount }}
          </span>
          笔，
          <span class="text-danger">
            {{ tongji.orderAmount }}
          </span>
          元
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

    <order-detail :trade-no="out_trade_no" @close="hideOrderDetail" />
  </div>
</template>

<script>
import { getList, getExportList, getTongji } from '@/api/order'
import orderDetail from './detail'

export default {
  components: { orderDetail },
  data() {
    return {
      today: {
        amount: 0,
        wxpay_amount: 0,
        alipay_amount: 0,
        user_count: 0
      },
      dateType: '',
      search: {
        pay_type: '',
        trade_no: '',
        date: []
      },
      tongji: {
        orderCount: 0,
        orderAmount: 0
      },
      payTypes: [
        { name: '', title: '请选择' },
        { name: 'wxpay', title: '微信' },
        { name: 'alipay', title: '支付宝' }
      ],
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 10,
      out_trade_no: '',
      exportLoading: false
    }
  },
  mounted() {
    this.doInit()
  },
  methods: {
    doInit() {
      this.dateType = ''
      this.search = {
        pay_type: '',
        trade_no: '',
        date: []
      }
      this.doSearch()
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
    getList() {
      const param = Object.assign(this.search, {
        page: this.page,
        pagesize: this.pagesize
      })
      getList(param).then(res => {
        this.dataList = res.data.list
        this.dataTotal = res.data.count
      })
      if (this.page === 1) {
        getTongji(param).then(res => {
          this.tongji = res.data
        })
      }
    },
    pageChange(page) {
      this.page = page
      this.getList()
    },
    toRefund(order_no) {
      this.$router.replace({
        path: '/refund',
        query: {
          order_no: order_no
        }
      })
    },
    showOrderDetail(out_trade_no) {
      this.out_trade_no = out_trade_no
    },
    hideOrderDetail() {
      this.out_trade_no = ''
    },
    rowDblclick(row) {
      this.showOrderDetail(row.out_trade_no)
    },
    closeDetail() {
      this.detail = null
    },
    onCopySuccess: function() {
      this.$message.success('已复制')
    },
    onCopyError: function() {
      this.$message.error('复制失败')
    },
    doSearch() {
      this.page = 1
      this.getList()
    },
    doExport() {
      this.exportLoading = true
      getExportList(this.search).then(res => {
        import('@/vendor/Export2Excel').then(excel => {
          const filename = '订单'
          const tHeader = ['支付方式', '支付时间', '商户订单号', '渠道流水单号', '订单金额', '数量 / 时长', '状态']
          const filterVal = ['pay_type', 'pay_time', 'out_trade_no', 'transaction_id', 'total_fee', 'num', 'status']
          const list = res.data
          const data = this.formatJson(filterVal, list)
          excel.export_json_to_excel({
            header: tHeader,
            data,
            filename: filename,
            autoWidth: true,
            bookType: 'xlsx'
          })
          this.exportLoading = false
        })
      })
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        return v[j]
      }))
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

  .total-fee {
    font-weight: bold;
    font-size: 16px;
  }

  .el-table p.small {
    font-size: 12px;
    color: #999;
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
