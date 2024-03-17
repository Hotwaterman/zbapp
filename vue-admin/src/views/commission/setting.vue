<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="分销设置" name="commission">
        <el-form
          v-if="form"
          ref="commissionForm"
          :model="form"
          :rules="formRules"
          label-width="140px"
          style="padding: 30px 0;"
        >
          <el-form-item label="分销开关" prop="is_open">
            <el-switch
              v-model="form['is_open']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
          <el-form-item label="自动通过审核" prop="auto_audit">
            <el-switch
              v-model="form['auto_audit']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
          <el-form-item label="分销层级" prop="deep">
            <el-radio v-model="form['deep']" :label="2">2级</el-radio>
          </el-form-item>
          <el-form-item label="直推分佣比例" prop="bili_1">
            <el-input v-model="form['bili_1']" size="small" style="width: 60px;" /> %
            <p style="color: #666; margin: 0;">填0~100之间的数字</p>
          </el-form-item>
          <el-form-item label="间推分佣比例" prop="bili_2">
            <el-input v-model="form['bili_2']" size="small" style="width: 60px;" /> %
            <p style="color: #666; margin: 0;">填0~100之间的数字</p>
          </el-form-item>
          <el-form-item label="分销协议" prop="agreement">
            <el-input v-model="form['agreement']" type="textarea" :rows="10" placeholder="请输入内容" size="small" style="width: 500px;" />
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
    </el-tabs>

  </div>
</template>

<script>
import { getSetting, setSetting } from '@/api/setting'

export default {
  data() {
    return {
      tabName: 'commission',
      form: null,
      engines: [],
      balance: null,
      formRules: {}
    }
  },
  mounted() {
    this.getSetting()
  },
  methods: {
    getSetting() {
      getSetting({ name: this.tabName }).then(res => {
        this.form = res.data
      })
    },
    switchTab() {
      this.getSetting()
    },
    doSubmit() {
      this.$refs.commissionForm.validate((valid) => {
        if (valid) {
          setSetting({
            name: this.tabName,
            data: JSON.stringify(this.form)
          }).then(res => {
            this.getSetting()
            this.$message.success(res.message)
          })
        } else {
          this.$message.error('请填写必填项')
        }
      })
    }

  }
}
</script>
<style>
  .number .el-input__inner {
    padding-right: 0;
    padding-left: 8px;
  }
</style>
<style scoped>
  .el-input {
    width: 240px;
  }

  .number {
    width: 66px;
    margin-right: 5px;
  }

  .el-select {
    width: 240px;
  }

  .el-switch {
    transform: scale(0.80);
  }

  .textarea {
    width: 400px;
    max-width: 100%;
  }

  .form-title {
    width: 660px;
    border-top: 1px solid #e2e2e2;
    height: 44px;
    line-height: 44px;
    margin: 15px;
    background: #f8f8f8;
    padding-left: 20px;
    font-size: 16px;
    font-weight: 500;
  }

  p {
    margin: 0;
    padding: 0;
    line-height: 24px;
    color: #666;
    font-size: 14px;
  }
</style>
