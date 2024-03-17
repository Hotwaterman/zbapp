<template>
  <div v-if="detail">
    <el-dialog
      title="订单详情"
      width="500px"
      custom-class="my-dialog"
      :visible="true"
      :modal="true"
      :modal-append-to-body="true"
      :append-to-body="true"
      :close-on-click-modal="true"
      :close-on-press-escape="true"
      :before-close="closeDetail"
    >
      <el-descriptions class="box" title="" :column="1" border size="medium">
        <el-descriptions-item label="商户单号">
          {{ detail.out_trade_no }}
          <el-button
            v-clipboard:copy="detail.out_trade_no"
            v-clipboard:success="onCopySuccess"
            v-clipboard:error="onCopyError"
            type="text"
            size="mini"
          >
            复制
          </el-button>
        </el-descriptions-item>
        <el-descriptions-item label="流水单号">
          {{ detail.transaction_id }}
          <el-button
            v-clipboard:copy="detail.transaction_id"
            v-clipboard:success="onCopySuccess"
            v-clipboard:error="onCopyError"
            type="text"
            size="mini"
          >
            复制
          </el-button>
        </el-descriptions-item>
        <el-descriptions-item label="订单金额">
          <span class="total-fee text-danger">
            ￥{{ detail.total_fee }}
          </span>
        </el-descriptions-item>
        <el-descriptions-item label="支付方式">
          <span v-if="detail.pay_type === 'wxpay'">
            <svg-icon icon-class="wxpay" style="color:#04BE02;" /> 微信支付
          </span>
          <span v-if="detail.pay_type === 'alipay'">
            <svg-icon icon-class="alipay" style="color:#027aff;" /> 支付宝
          </span>
        </el-descriptions-item>
        <el-descriptions-item label="状态">
          <span class="text-green">
            支付成功
          </span>
          <span
            v-if="detail.is_refund === 1"
            class="text-danger"
            style="font-size: 12px;"
          >
            [已全额退款]
          </span>
        </el-descriptions-item>
        <el-descriptions-item label="数量/时长">
          <span v-if="detail.goods_id">{{ detail.num }} 条</span>
          <span v-if="detail.draw_id">{{ detail.num }} 张</span>
          <span v-if="detail.gpt4_id">{{ detail.num }} 万字</span>
          <span v-if="detail.vip_id">{{ detail.num }} 个月</span>
        </el-descriptions-item>
        <el-descriptions-item :label="detail.is_refund === 1 ? '退款时间' : '支付时间'">
          {{ detail.pay_time }}
        </el-descriptions-item>
        <el-descriptions-item label="用户ID">
          {{ detail.user_id }}
        </el-descriptions-item>
      </el-descriptions>
    </el-dialog>
  </div>
</template>

<script>
import { getOrderDetail } from '@/api/order'

export default {
  props: {
    tradeNo: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      detail: null
    }
  },
  watch: {
    tradeNo: function() {
      this.getOrderDetail()
    }
  },
  methods: {
    getOrderDetail() {
      if (!this.tradeNo) {
        return
      }
      getOrderDetail({
        order_no: this.tradeNo
      }).then(res => {
        this.detail = res.data
      }).catch(res => {
        this.errMsg = res.message
      })
    },
    closeDetail() {
      this.detail = null
      this.$emit('close')
    },
    onCopySuccess: function() {
      this.$message.success('已复制')
    },
    onCopyError: function() {
      this.$message.error('复制失败')
    }
  }
}
</script>
