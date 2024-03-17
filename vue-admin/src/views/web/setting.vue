<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="PC版设置" name="web">
        <el-form
          v-if="form"
          ref="webForm"
          :model="form"
          :rules="formRules"
          label-width="140px"
          style="padding: 30px 0;"
        >
          <el-form-item label="web站点开关" prop="is_open">
            <el-switch
              v-model="form['is_open']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
          <el-form-item label="自动合并小程序用户" prop="bind_wxapp_user">
            <el-switch
              v-model="form['bind_wxapp_user']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
          <el-form-item v-if="pcurl" label="PC站点链接">
            <a :href="pcurl" target="_blank" style="color: #409EFF">{{pcurl}}</a>
          </el-form-item>
          <el-form-item label="页面标题" prop="page_title">
            <el-input v-model="form['page_title']" size="small" style="width: 240px;" />
          </el-form-item>
          <el-form-item label="底部声明" prop="copyright">
            <el-input v-model="form['copyright']" type="textarea" :rows="3" placeholder="请输入内容" size="small" style="width: 500px;" />
          </el-form-item>
          <el-form-item label="声明链接" prop="copyright_link">
            <el-input v-model="form['copyright_link']" size="small" style="width: 300px;" />
          </el-form-item>
          <el-form-item label="登录方式" prop="login_way">
            扫码关注公众号
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit('webForm')">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
      <el-tab-pane label="H5版设置" name="h5">
        <el-form
          v-if="form"
          ref="h5Form"
          :model="form"
          :rules="formRules"
          label-width="140px"
          style="padding: 30px 0;"
        >
          <el-form-item v-if="h5url" label="H5站点链接">
            <a :href="h5url" target="_blank" style="color: #409EFF">{{h5url}}</a>
          </el-form-item>
          <el-form-item label="H5首页标题" prop="page_title">
            <el-input v-model="form['page_title']" placeholder="请输入首页标题" size="small" />
            <p style="margin: 0; color: #999;">默认：AI创作助手</p>
          </el-form-item>
          <el-form-item label="分享标题" prop="share_title">
            <el-input v-model="form['share_title']" placeholder="默认页面标题" size="small" />
          </el-form-item>
          <el-form-item label="分享描述" prop="share_desc">
            <el-input v-model="form['share_desc']" placeholder="" size="small" />
          </el-form-item>
          <el-form-item label="分享图片" prop="share_image">
            <el-input v-model="form['share_image']" placeholder="输入图片地址或上传图片" size="small" />
            <el-upload
              class="avatar-uploader"
              action=""
              :data="{type: 'share_image'}"
              :http-request="uploadImage"
              :show-file-list="false"
              :multiple="false"
            >
              <img v-if="form['share_image']" :src="form['share_image']" class="avatar" style="height: 80px; width: auto;">
              <i v-else class="el-icon-plus avatar-uploader-icon" style="width: 80px; height: 80px; line-height:80px;" />
            </el-upload>
            <span>建议300x300像素，正方形图片</span>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit('h5Form')">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
      <el-tab-pane label="公众号设置" name="wxmp">
        <el-form
          v-if="form"
          ref="wxmpForm"
          :model="form"
          :rules="formRules"
          label-width="160px"
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
          <div style="padding-left: 160px; margin: 50px 0 20px 0;">以下配置在公众号后台->设置与开发->基本配置->服务器配置->修改配置，修改保存并启用</div>
          <el-form-item label="令牌(Token)" prop="token">
            <el-input v-model="form['token']" placeholder="公众号令牌(Token)" size="small" style="width: 350px;" />
          </el-form-item>
          <el-form-item label="消息加解密密钥(EncodingAESKey)" prop="aes_key">
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
import { getSetting, setSetting } from '@/api/setting'
import { getWebSiteUrl } from '@/api/user'
import { uploadImage } from '@/api/upload'

export default {
  data() {
    return {
      tabName: 'web',
      form: null,
      pcurl: '',
      h5url: '',
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
        ],
        'page_title': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'share_title': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'share_desc': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'share_image': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]

      }
    }
  },
  mounted() {
    this.getSetting()
    this.getWebSiteUrl()
  },
  methods: {
    getSetting() {
      getSetting({ name: this.tabName }).then(res => {
        this.form = res.data
      })
    },
    getWebSiteUrl() {
      getWebSiteUrl().then(res => {
        this.pcurl = res.data.pcurl
        this.h5url = res.data.h5url
      })
    },
    switchTab() {
      this.getSetting()
    },
    doSubmit(formName) {
      this.$refs[formName].validate((valid) => {
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
