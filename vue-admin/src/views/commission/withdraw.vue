<template>
  <div class="app-container" style="padding-bottom: 20px;">
    <div class="toolbar">

      <el-button-group>
        <el-button v-if="status === 'all'" type="primary" size="mini" @click="switchTab('all')">全部</el-button>
        <el-button v-else size="mini" @click="switchTab('all')">全部</el-button>

        <el-button v-if="status === '0'" type="primary" size="mini" @click="switchTab('0')">未审核</el-button>
        <el-button v-else size="mini" @click="switchTab('0')">未审核</el-button>

        <el-button v-if="status === '1'" type="primary" size="mini" @click="switchTab('1')">已打款</el-button>
        <el-button v-else size="mini" @click="switchTab('1')">已打款</el-button>

        <el-button v-if="status === '2'" type="primary" size="mini" @click="switchTab('2')">已驳回</el-button>
        <el-button v-else size="mini" @click="switchTab('2')">已驳回</el-button>
      </el-button-group>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="user_id" label="用户ID" width="80" />
      <el-table-column prop="create_time" label="申请时间" width="160" />

      <el-table-column prop="nickname" label="分销商微信" width="200">
        <template slot-scope="scope">
          <div style="display:flex; align-items:center;">
            <img :src="scope.row.avatar" style="height: 24px; margin-right: 5px;">
            {{ scope.row.nickname }}
          </div>
        </template>
      </el-table-column>

      <el-table-column prop="money" label="提现金额" width="160">
        <template slot-scope="scope">
          <p>{{ scope.row.money }}元</p>
        </template>
      </el-table-column>

      <el-table-column prop="bank_name" label="提现方式 / 姓名 / 账号" width="300">
        <template slot-scope="scope">
          <p v-if="scope.row.bank_name === '微信零钱'">
            {{ scope.row.bank_name }} / {{ scope.row.account_name }}
          </p>
          <p v-else>
            {{ scope.row.bank_name }} / {{ scope.row.account_name }} / {{ scope.row.account_number }}
          </p>
        </template>
      </el-table-column>
      <el-table-column label="收款码" width="160">
        <template slot-scope="scope">
          <a v-if="scope.row.bank_name === '微信零钱'" :href="scope.row.qrcode" target="_blank">
            <img :src="scope.row.qrcode" style="height: 40px; cursor: pointer;" />
          </a>
        </template>
      </el-table-column>

      <el-table-column prop="status" label="审核状态" width="120">
        <template slot-scope="scope">
          <el-button v-if="scope.row.status===0" type="primary" size="mini" plain disabled>未审核</el-button>
          <el-button v-if="scope.row.status===1" type="success" size="mini" plain disabled>已打款</el-button>
          <el-button v-if="scope.row.status===2" type="danger" size="mini" plain disabled>已驳回</el-button>
        </template>
      </el-table-column>

      <el-table-column label="操作">
        <template slot-scope="scope">
          <span v-if="scope.row.status===2" style="color:red;">理由：{{ scope.row.reject_reason }}</span>
          <el-button-group>
            <el-button v-if="scope.row.status === 0" type="text" size="mini" icon="el-icon-edit" @click.native.prevent="clickEdit(scope.row.id)">审核</el-button>
          </el-button-group>
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

    <div v-if="form">
      <el-dialog
        custom-class="my-dialog"
        title="审核"
        width="720px"
        :visible="true"
        :close-on-click-modal="false"
        :before-close="formClose"
      >
        <el-form ref="form" :model="form" :rules="formRules" label-width="120px">

          <el-form-item label="状态" prop="status">
            <el-radio v-model="form.status" :label="0">未审核</el-radio>
            <el-radio v-model="form.status" :label="1">已打款</el-radio>
            <el-radio v-model="form.status" :label="2">驳回</el-radio>
          </el-form-item>

          <el-form-item v-if="form.status===2" label="驳回原因" prop="reject_reason">
            <el-input v-model="form.reject_reason" type="textarea" :row="3" placeholder="请输入驳回原因" size="small" style="width: 400px;" />
          </el-form-item>

        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="default" icon="el-icon-close" size="small" @click="formClose">取 消</el-button>
          <el-button type="primary" icon="el-icon-check" size="small" @click="clickSubmit">提 交</el-button>
        </span>
      </el-dialog>
    </div>

  </div>

</template>

<script>
import { getWithdrawList, delWithdraw, getWithdrawInfo, saveWithdrawInfo } from '@/api/commission'

export default {

  data() {
    return {
      search: {},
      dataList: [],
      dataTotal: 0,
      form: null,
      page: 1,
      pagesize: 10,
      detailShow: false,
      formRules: {
        status: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        reject_reason: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      },
      status: 'all'
    }
  },
  created() {
    this.getWithdrawList()
  },
  methods: {
    getWithdrawList() {
      getWithdrawList({
        page: this.page,
        pagesize: this.pagesize,
        keyword: this.search.keyword,
        status: this.status
      }).then(res => {
        const data = res.data
        this.dataList = data.list
        this.dataTotal = res.data.count
      })
    },
    // 删除信息
    clickDel(id) {
      this.$confirm('确认删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delWithdraw({ id: id }).then(res => {
          this.getWithdrawList()
          this.$message({
            message: res.message,
            type: 'success',
            duration: 1500
          })
        })
      })
    },
    // 显示修改内容
    clickEdit(id) {
      getWithdrawInfo({ id: id }).then(res => {
        this.formType = 'edit'
        this.form = res.data
      })
    },
    // 提交表单
    clickSubmit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          saveWithdrawInfo(this.form).then(res => {
            this.getWithdrawList()
            this.$message({
              message: res.message,
              type: 'success',
              duration: 1500
            })
            this.formClose()
          })
        } else {
          console.log('请按照规则填写表单')
        }
      })
    },
    // 关闭模态框
    formClose() {
      this.form = null
    },
    switchTab(status) {
      this.status = status
      this.page = 1
      this.dataList = []
      this.dataTotal = 0
      this.getWithdrawList()
    },
    pageChange(page) {
      this.page = page
      this.getWithdrawList()
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
