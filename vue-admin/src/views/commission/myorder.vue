<template>
  <div>
    <el-dialog
      custom-class="my-dialog"
      title="查看分销商订单"
      width="1400px"
      :visible="true"
      :close-on-click-modal="true"
      :before-close="handleClose"
    >
      <el-table
        :data="dataList"
        stripe
        size="medium"
        header-cell-class-name="bg-gray"
      >
        <el-table-column prop="create_time" label="下单时间" width="160" />
        <el-table-column prop="user_id" label="客户ID" width="80" />
        <el-table-column prop="nickname" label="头像昵称" width="160">
          <template slot-scope="scope">
            <div style="display:flex; align-items:center;">
              <img :src="scope.row.avatar" style="height: 24px; margin-right: 5px;">
              {{ scope.row.nickname }}
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="out_trade_no" label="订单号" width="200" />

        <el-table-column label="订单金额" width="160">
          <template slot-scope="scope">
            ￥{{ scope.row.total_fee }}
          </template>
        </el-table-column>

        <el-table-column prop="status" label="支付状态" width="120">
          <template slot-scope="scope">
            <span v-if="scope.row.status === 0">未付款</span>
            <span v-if="scope.row.status === 1">已付款</span>
          </template>
        </el-table-column>

        <el-table-column prop="commission1" label="直接推荐人" width="160">
          <template slot-scope="scope">
            <div v-if="scope.row.commission1" style="display:flex; align-items:center;">
              <img :src="scope.row.commission1_avatar" style="height: 24px; margin-right: 5px;">
              {{ scope.row.commission1_nickname || '无昵称' }}（￥{{ scope.row.commission1_fee }}）
            </div>
          </template>
        </el-table-column>

        <el-table-column prop="commission2" label="间接推荐人">
          <template slot-scope="scope">
            <div v-if="scope.row.commission2" style="display:flex; align-items:center;">
              <img :src="scope.row.commission2_avatar" style="height: 24px; margin-right: 5px;">
              {{ scope.row.commission2_nickname || '无昵称' }}（￥{{ scope.row.commission2_fee }}）
            </div>
          </template>
        </el-table-column>

      </el-table>

      <el-pagination
        :current-page="page"
        :page-size="pagesize"
        layout="total, prev, pager, next"
        :total="dataTotal"
        @current-change="pageChange"
      />
    </el-dialog>
  </div>
</template>

<script>
import { getOrderList } from '@/api/commission'

export default {
  props: {
    pid: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      dataTotal: 0,
      page: 1,
      pagesize: 10,
      dataList: []
    }
  },
  created() {
    this.getOrderList()
  },
  methods: {
    handleClose() {
      this.dataList = []
      this.$emit('close')
    },
    getOrderList() {
      getOrderList({
        page: this.page,
        pagesize: this.pagesize,
        pid: this.pid
      }).then(res => {
        const data = res.data
        this.dataList = data.list
        this.dataTotal = res.data.count
      })
    },
    pageChange(page) {
      this.page = page
      this.getOrderList()
    }
  }
}
</script>
<style scoped>
  .el-input {
    width: 240px;
  }

  .el-select {
    width: 240px;
  }

  .el-switch {
    transform: scale(0.80);
  }

  .form-title {
    line-height: 40px;
    border-top: 1px solid #e6e6e6;
    padding-left: 20px;
    font-size: 18px;
    margin: 10px 0;
    background: #fafafa;
  }

  .upload_status {
    color: #149314;
    font-weight: bold;
  }

  .el-table p {
    margin: 0;
  }

  .el-table p.desc {
    font-size: 12px;
    color: #999;
  }

  .input-number {
    width: 80px;
    margin-right: 5px;
  }

  .avatar {
    background: #666;
  }
</style>

