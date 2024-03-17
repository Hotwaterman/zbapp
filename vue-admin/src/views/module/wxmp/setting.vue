<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="公众号设置" name="wxmp">
        <el-form
          v-if="form"
          ref="wxmpForm"
          :model="form"
          :rules="formRules"
          label-width="180px"
          style="padding: 30px 0;"
        >
          <el-form-item label="公众号名称" prop="title">
            <el-input v-model="form['title']" placeholder="公众号名称" size="small" style="width: 300px;" />
          </el-form-item>
          <el-form-item label="公众号appid" prop="appid">
            <el-input v-model="form['appid']" placeholder="公众号appid" size="small" style="width: 300px;" />
          </el-form-item>
          <el-form-item label="公众号appsecret" prop="appsecret">
            <el-input v-model="form['appsecret']" placeholder="公众号密钥appsecret" size="small" style="width: 300px;" />
          </el-form-item>
          <el-alert
            type="warning"
            size="small"
            title="以下配置在公众号后台->设置与开发->基本配置->服务器配置->修改配置，修改保存并启用"
            :closable="false"
            style="width: 600px; margin: 10px 0 20px 20px;"
          >
          </el-alert>
          <el-form-item label="令牌(Token)" prop="token">
            <el-input v-model="form['token']" placeholder="公众号令牌(Token)" size="small" style="width: 350px;" />
          </el-form-item>
          <el-form-item label="消息加解密(AESKey)" prop="aes_key">
            <el-input v-model="form['aes_key']" placeholder="公众号消息加解密密钥(EncodingAESKey)" size="small" style="width: 350px;" />
          </el-form-item>
          <el-form-item label="消息加解密模式">
            明文模式
          </el-form-item>
          <el-form-item label="服务器地址" prop="server_url">
            <el-input v-model="form['server_url']" readonly size="small" style="width: 400px;" />
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit('wxmpForm')">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
    </el-tabs>

  </div>
</template>

<script>
import { getWxmpSetting, setWxmpSetting } from '@/api/wxmp'
import { uploadImage } from '@/api/upload'

export default {
  data() {
    return {
      tabName: 'wxmp',
      form: null,
      formRules: {
        'title': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'appid': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'appsecret': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'token': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'aes_key': [
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
      getWxmpSetting().then(res => {
        this.form = res.data
      })
    },
    switchTab() {
      this.getSetting()
    },
    doSubmit(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          setWxmpSetting({
            data: JSON.stringify(this.form)
          }).then(res => {
            this.getSetting()
            this.$message.success(res.message)
          })
        } else {
          this.$message.error('请填写必填项')
        }
      })
    },
    uploadImage(item) {
      var form = new FormData()
      form.append('file', item.file)
      if (item.data) {
        form.append('data', JSON.stringify(item.data))
      }
      uploadImage(form).then(res => {
        if (item.data.type === 'share_image') {
          this.$set(this.form, 'share_image', res.data.path)
        }
        this.$message.success('上传成功')
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
