<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="H5版设置" name="h5">
        <el-form
          v-if="form"
          ref="h5Form"
          :model="form"
          :rules="formRules"
          label-width="140px"
          style="padding: 30px 0;"
        >
          <el-form-item label="H5版站点开关" prop="is_open">
            <el-switch
              v-model="form['is_open']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
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
      tabName: 'h5',
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
