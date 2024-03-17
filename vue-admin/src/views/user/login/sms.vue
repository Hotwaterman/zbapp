<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="短信参数配置" name="sms">
        <el-form
          v-if="form"
          ref="form"
          :model="form"
          :rules="formRules"
          label-width="180px"
          style="padding: 30px 0;"
        >
          <el-form-item label="选择短信通道" prop="channel">
            <el-radio-group v-model="form['channel']">
              <el-radio label="" size="mini">关闭</el-radio>
              <el-radio label="aliyun" size="mini">阿里云短信</el-radio>
              <el-radio label="tencent" size="mini">腾讯云短信</el-radio>
            </el-radio-group>
          </el-form-item>
          <div v-if="form['channel'] === 'aliyun'">
            <el-form-item label="AccessKey ID" prop="access_key_id">
              <el-input v-model="form['aliyun_access_key_id']" size="small" />
            </el-form-item>
            <el-form-item label="AccessKey Secret" prop="access_key_secret">
              <el-input v-model="form['aliyun_access_key_secret']" size="small" />
            </el-form-item>
            <el-form-item label="【注册】模板CODE" prop="reg_tpl">
              <el-input v-model="form['aliyun_reg_tpl']" size="small" />
              <p>注册时使用的短信验证码模板CODE</p>
            </el-form-item>
            <el-form-item label="【找回密码】模板CODE" prop="reset_tpl">
              <el-input v-model="form['aliyun_reset_tpl']" size="small" />
              <p>找回密码时使用的短信验证码模板CODE</p>
            </el-form-item>
            <el-form-item label="【绑定手机】模板CODE" prop="bind_tpl">
              <el-input v-model="form['aliyun_bind_tpl']" size="small" />
              <p>绑定手机号时使用的短信验证码模板CODE</p>
            </el-form-item>
            <el-form-item label="签名名称" prop="signname">
              <el-input v-model="form['aliyun_signname']" size="small" />
              <p>短信的前缀【xxx】</p>
            </el-form-item>
          </div>
          <div v-if="form['channel'] === 'tencent'">
            <el-form-item label="AppID" prop="appid">
              <el-input v-model="form['tencent_appid']" size="small" />
            </el-form-item>
            <el-form-item label="AppKey" prop="appkey">
              <el-input v-model="form['tencent_appkey']" size="small" />
            </el-form-item>
            <el-form-item label="【注册】模板ID" prop="reg_tpl">
              <el-input v-model="form['tencent_reg_tpl']" size="small" />
              <p>注册时使用的短信验证码模板ID</p>
            </el-form-item>
            <el-form-item label="【找回密码】模板ID" prop="reset_tpl">
              <el-input v-model="form['tencent_reset_tpl']" size="small" />
              <p>找回密码时使用的短信验证码模板ID</p>
            </el-form-item>
            <el-form-item label="【绑定手机】模板ID" prop="bind_tpl">
              <el-input v-model="form['tencent_bind_tpl']" size="small" />
              <p>绑定手机号时使用的短信验证码模板ID</p>
            </el-form-item>
            <el-form-item label="签名名称" prop="signname">
              <el-input v-model="form['tencent_signname']" size="small" />
              <p>短信的前缀【xxx】</p>
            </el-form-item>
          </div>

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
      tabName: 'sms',
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
  .el-input {
    width: 240px;
  }
  p {
    margin: 0;
    padding: 0;
    line-height: 24px;
    color: #666;
    font-size: 14px;
  }
</style>
