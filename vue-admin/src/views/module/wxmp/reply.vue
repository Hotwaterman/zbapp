<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="关注时回复" name="welcomeReply">
        <el-form
          v-if="form"
          ref="welcomeReply"
          :model="form"
          label-width="120px"
          style="padding: 30px 0;"
        >
          <el-form-item label="回复内容">
            <replyEditor ref="welcomeReplyEditor" :type="form.type" :content="form.content" :image="form.image" />
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
      <el-tab-pane label="扫码登录回复" name="loginReply">
        <el-form
          v-if="form"
          ref="loginReply"
          :model="form"
          label-width="120px"
        >
          <el-alert
            type="warning"
            size="small"
            title="当PC端扫码登录时，回复的内容"
            :closable="false"
            style="width: 600px; margin: 10px 0 20px 20px;"
          ></el-alert>
          <el-form-item label="回复内容">
            <replyEditor ref="loginReplyEditor" :type="form.type" :content="form.content" :image="form.image" />
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
      <el-tab-pane label="默认回复" name="defaultReply">
        <el-form
          v-if="form"
          ref="defaultReply"
          :model="form"
          label-width="120px"
        >
          <el-alert
            type="warning"
            size="small"
            title="当系统不知道该如何回复时，默认发送的内容"
            :closable="false"
            style="width: 600px; margin: 10px 0 20px 20px;"
          ></el-alert>
          <el-form-item label="回复内容">
            <replyEditor ref="defaultReplyEditor" :type="form.type" :content="form.content" :image="form.image" />
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
import { getWxmpReply, setWxmpReply } from '@/api/wxmp'
import replyEditor from './replyEditor';

export default {
  components: { replyEditor },
  data() {
    return {
      tabName: 'welcomeReply',
      form: null
    }
  },
  mounted() {
    this.getWxmpReply()
  },
  methods: {
    getWxmpReply() {
      getWxmpReply({ type: this.tabName }).then(res => {
        this.form = res.data
      })
    },
    switchTab() {
      this.getWxmpReply()
    },
    doSubmit() {
      this.$refs[this.tabName].validate((valid) => {
        if (!valid) {
          this.$message.error('请填写必填项')
          return
        }
        var { type, content, image } = this.$refs[this.tabName + 'Editor'].getReply();
        this.form.type = type
        this.form.content = content
        this.form.image = image
        setWxmpReply({
          type: this.tabName,
          data: JSON.stringify(this.form)
        }).then(res => {
          this.getWxmpReply()
          this.$message.success(res.message)
        });
      })
    },
    uploadImage(item) {
      var form = new FormData()
      form.append('file', item.file)
      if (item.data) {
        form.append('data', JSON.stringify(item.data))
      }
      uploadImage(form).then(res => {
        this.$set(this.form, item.data.type, res.data.path)
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
