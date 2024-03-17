<template>
  <div class="app-container" style="padding-top: 20px;">
    <el-form ref="form" :rules="formRules" label-width="100px" style="width: 700px;">
      <el-steps :active="activeStep" align-center simple process-status="wait">
        <el-step title="设置参数" icon="el-icon-edit" />
        <el-step title="上传代码" icon="el-icon-upload" />
        <el-step title="上传结果" icon="el-icon-info" />
      </el-steps>
      <div v-if="activeStep === 1">
        <el-form-item label="上传密钥" prop="upload_secret" style="margin-top: 40px;">
          <el-input v-model="upload_secret" type="textarea" :rows="10" placeholder="小程序代码上传密钥" size="small" />
          <p style="color: #888;">请将IP白名单关闭，或将<span style="color:#666; text-decoration: underline; margin: 0 3px;">47.95.209.62</span>加入IP白名单</p>
        </el-form-item>
        <el-form-item label="接口域名" prop="host">
          <el-input v-model="host" placeholder="本站域名，不带https://" size="small" style="width: 240px;" />
        </el-form-item>
        <el-form-item label="" style="margin: 50px 0;">
          <el-button type="primary" icon="el-icon-upload" size="small" @click="uploadCode">开始上传代码</el-button>
          <p style="color: #888;">点击上传按钮后，请耐心等待，需要半分钟左右。</p>
        </el-form-item>
      </div>
      <div v-if="activeStep === 3">
        <div style="text-align: center; padding: 100px 0 100px 0;">
          <p class="text-green" style="font-size: 48px; margin: 20px 0;">
            <i class="el-icon-success" />
          </p>
          <p style="font-size: 16px;">上传成功</p>
          <p v-if="upload_time" style="font-size: 14px; color: #888;">最后上传时间：{{ upload_time }}</p>
          <p style="text-align: left;">
            <el-alert v-if="has_update === 1" type="warning" show-icon :closable="false" :title="'发现更新（' + update_time + '），请重新上传代码'" style="width: 320px; margin: 0 auto;" />
            <el-alert v-if="has_update === 0" type="success" show-icon title="已是最新版" style="width: 160px; margin: 0 auto;" />
          </p>
          <p><el-button type="primary" plain size="small" style="margin-top: 50px;" @click="toEdit">返回重新上传</el-button></p>
        </div>

      </div>
    </el-form>
  </div>
</template>

<script>
import { getSetting } from '@/api/setting'
import { checkUpdate, uploadCode } from '@/api/wxapp'

export default {
  data() {
    return {
      wxapp_name: 'wxapp',
      host: '',
      upload_secret: '',
      upload_time: '',
      has_update: -1,
      update_time: '',
      activeStep: 1,
      formRules: {
        'upload_secret': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  mounted() {
    this.getSetting()
  },
  methods: {
    getSetting() {
      getSetting({
        name: this.wxapp_name + '_upload'
      }).then(res => {
        this.has_update = -1
        this.update_time = ''
        this.upload_secret = res.data.upload_secret
        if (res.data.host) {
          this.host = res.data.host
        } else {
          this.host = window.location.host
        }

        this.upload_time = res.data.upload_time
        if (this.upload_time) {
          this.activeStep = 3
          setTimeout(() => {
            this.checkUpdate()
          }, 1000)
        }
      })
    },
    uploadCode() {
      if (!this.upload_secret) {
        this.$message({
          message: '请填写上传密钥',
          type: 'error',
          duration: 1500
        })
        return
      }
      uploadCode({
        wxapp_name: this.wxapp_name,
        upload_secret: this.upload_secret,
        host: this.host
      }).then(res => {
        this.getSetting()
        this.$message({
          message: res.message,
          type: 'success',
          duration: 1500
        })
      })
    },
    checkUpdate() {
      checkUpdate({
        wxapp_name: this.wxapp_name
      }).then(res => {
        this.has_update = res.data.has_update
        if (this.has_update) {
          this.update_time = res.data.update_time
        }
      })
    },
    toEdit() {
      this.activeStep = 1
    }
  }
}
</script>
