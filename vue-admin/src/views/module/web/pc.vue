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
          <el-form-item label="PC版站点开关" prop="is_open">
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
          <el-form-item label="登录方式" prop="login_way">
            扫码关注公众号
          </el-form-item>
          <div v-if="site_id === 1">
            <el-form-item label="SEO关键词" prop="keywords">
              <el-input v-model="form['keywords']" size="small" placeholder="每个词用英文逗号,隔开" style="width: 500px;" />
            </el-form-item>
            <el-form-item label="SEO描述" prop="description">
              <el-input v-model="form['description']" type="textarea" :rows="3" placeholder="请输入内容" size="small" style="width: 500px;" />
            </el-form-item>
            <el-form-item label="JS统计代码" prop="tongji">
              <el-input v-model="form['tongji']" type="textarea" :rows="3" placeholder="请输入js统计代码" size="small" style="width: 500px;" />
            </el-form-item>
          </div>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit('webForm')">保 存</el-button>
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
  computed: {
    site_id() {
      return this.$store.getters.site_id
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
