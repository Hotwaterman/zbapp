<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="参数配置" name="wxapp">
        <el-alert
          type="warning"
          size="small"
          title="："
          :closable="false"
          style="width: 600px; margin: 10px 0 20px 20px;"
        >
          <template slot="title">
            <div><strong>注意事项：</strong></div>
            <div>1、小程序类目选择：工具 -> 办公</div>
            <div>2、小程序后台request合法域名、uploadFile合法域名、downloadFile合法域名，均为：https://{{host}} ，必须设置</div>
          </template>
        </el-alert>
        <el-form v-if="form" ref="form_wxapp" :model="form" :rules="formRules" label-width="150px" style="width: 600px;">
          <el-form-item label="小程序名称" prop="title">
            <el-input v-model="form['title']" placeholder="小程序名称" size="small" />
          </el-form-item>
          <el-form-item label="小程序appid" prop="appid">
            <el-input v-model="form['appid']" placeholder="小程序appid" size="small" />
          </el-form-item>
          <el-form-item label="小程序appsecret" prop="appsecret">
            <el-input v-model="form['appsecret']" placeholder="小程序密钥appsecret" size="small" />
          </el-form-item>
          <el-form-item label="小程序首页标题" prop="page_title">
            <el-input v-model="form['page_title']" placeholder="请输入小程序首页标题" size="small" />
            <p style="margin: 0; color: #999;">默认：AI创作助手</p>
          </el-form-item>
          <el-form-item label="小程序欢迎语" prop="share_title">
            <el-input v-model="form['welcome']" placeholder="请输入小程序欢迎语" size="small" />
            <p style="margin: 0; color: #999;">默认：你好，我是AI创作助手！你现在可以向我提问了！</p>
          </el-form-item>
          <el-form-item label="分享标题" prop="share_title">
            <el-input v-model="form['share_title']" placeholder="默认小程序名称" size="small" />
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
          <el-form-item label="IOS支付开关" prop="is_ios_pay">
            <el-switch
              v-model="form['is_ios_pay']"
              :active-value="1"
              :inactive-value="0"
            />
            <p>微信IOS端不允许有虚拟商品支付</p>
          </el-form-item>
          <el-form-item label="小程序审核模式" prop="is_check">
            <el-switch
              v-model="form['is_check']"
              :active-value="1"
              :inactive-value="0"
            />
            <p>打开以后会隐藏一些不好过审的功能</p>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit('form_wxapp')">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
      <el-tab-pane label="小程序首页" name="wxapp_index">
        <el-form v-if="form" ref="form_wxapp_index" :model="form" :rules="formRules" label-width="150px" style="width: 600px;">
          <el-form-item label="首页类型" prop="type">
            <el-radio v-model="form['type']" label="chat">聊天</el-radio>
            <el-radio v-model="form['type']" label="diy">自定义内容</el-radio>
          </el-form-item>

          <el-form-item label="自定义内容" prop="content" class="describe">
            <fox-editor ref="foxEditor" :content="form.content"></fox-editor>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit('form_wxapp_index')">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import { getSetting, setSetting } from '@/api/setting'
import { uploadImage } from '@/api/upload'
import FoxEditor from '@/components/FoxEditor'

export default {
  components: { FoxEditor },
  data() {
    return {
      tabName: 'wxapp',
      form: {},
      host: '',
      formRules: {
        'title': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'qrcode': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'appid': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'appsecret': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  mounted() {
    this.host = window.location.host
    this.getSetting()
  },
  methods: {
    getSetting() {
      getSetting({
        name: this.tabName
      }).then(res => {
        this.form = res.data
      })
    },
    switchTab() {
      this.getSetting()
    },
    doSubmit(formName) {
      this.$refs[formName].validate((valid) => {
        var form = Object.assign({}, this.form);
        if (this.tabName === 'wxapp_index') {
          form.content = this.$refs.foxEditor.getContent()
        }

        if (valid) {
          setSetting({
            name: this.tabName,
            data: JSON.stringify(form)
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
      });
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
