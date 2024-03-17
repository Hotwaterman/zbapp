<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="登录设置" name="login">
        <el-form
          v-if="form"
          ref="form"
          :model="form"
          :rules="formRules"
          label-width="140px"
          style="padding: 30px 0;"
        >
          <el-form-item label="微信登录" prop="login_wechat">
            <el-switch
              v-model="form['login_wechat']"
              :active-value="1"
              :inactive-value="0"
            />
            <p>需配置公众号参数</p>
          </el-form-item>
          <el-form-item label="手机账号登录" prop="login_phone">
            <el-switch
              v-model="form['login_phone']"
              :active-value="1"
              :inactive-value="0"
            />
            <p>需配置短信参数</p>
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
      tabName: 'login',
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
      this.$refs.form.validate((valid) => {
        if (valid) {
          if (!this.form.login_wechat && !this.form.login_phone) {
            this.$message.error('至少启用一种登录方式')
            return
          }
          setSetting({
            name: this.tabName,
            data: JSON.stringify(this.form)
          }).then(res => {
            this.getSetting()
            this.$message.success(res.message)
          });
        } else {
          this.$message.error('请填写必填项')
        }
      })
    }
  }
}
</script>

<style scoped>
  .el-switch {
    transform: scale(0.80);
  }
  p {
    margin: 0;
    padding: 0;
    line-height: 24px;
    color: #666;
    font-size: 14px;
  }
</style>
