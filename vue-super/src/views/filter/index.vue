<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="敏感内容过滤" name="filter">
        <el-form v-if="form" ref="filterForm" :model="form" :rules="formRules" label-width="120px" style="padding: 30px 0;">
          <el-form-item label="处理方式" prop="handle_type">
            <el-radio-group v-model="form.handle_type">
              <el-radio :label="1" size="mini">关键词替换成**（默认）</el-radio>
              <el-radio :label="2" size="mini" style="margin-left: 10px;">阻止提交</el-radio>
            </el-radio-group>
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
      tabName: 'filter',
      form: null,
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
      var formObj = null
      if (this.tabName === 'filter') {
        formObj = this.$refs.filterForm
      }
      if (!formObj) {
        return
      }
      formObj.validate((valid) => {
        if (valid) {
          setSetting({
            name: this.tabName,
            data: JSON.stringify(this.form)
          }).then(res => {
            this.getSetting()
            this.$message({
              message: res.message,
              type: 'success',
              duration: 1500
            })
          })
        } else {
          this.$message({
            message: '请填写必填项',
            type: 'error',
            duration: 1500
          })
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
    line-height:44px;
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
