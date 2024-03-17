<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="系统配置" name="system">
        <el-form v-if="form" ref="systemForm" :model="form" :rules="formRules" label-width="120px" style="padding: 30px 0;">
          <el-form-item label="系统名称" prop="system_title">
            <el-input v-model="form['system_title']" placeholder="系统标题" size="small" />
          </el-form-item>
          <el-form-item label="后台logo" prop="system_logo">
            <el-input v-model="form['system_logo']" placeholder="输入图片地址或上传图片" size="small" />
            <el-upload
              class="avatar-uploader"
              action=""
              :data="{type: 'system_logo'}"
              :http-request="uploadImage"
              :show-file-list="false"
              :multiple="false"
            >
              <img v-if="form['system_logo']" :src="form['system_logo']" class="avatar" style="height: 50px; width: auto;">
              <i v-else class="el-icon-plus avatar-uploader-icon" style="width: 210px; height: 50px; line-height:50px;" />
            </el-upload>
            <span>210x50像素</span>
          </el-form-item>
          <el-form-item label="ICP备案号" prop="system_icp">
            <el-input v-model="form['system_icp']" placeholder="ICP备案号" size="small" />
          </el-form-item>
          <el-form-item label="公网安备号" prop="system_gongan">
            <el-input v-model="form['system_gongan']" placeholder="公网安备号" size="small" />
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
      <el-tab-pane label="接口设置" name="api">
        <el-form v-if="form" ref="apiForm" :model="form" :rules="formRules" label-width="120px" style="padding: 30px 0;">
<!--          <el-form-item label="内容输出方式" prop="outtype">
            <el-radio-group v-model="form['outtype']">
              <el-radio label="nostream" size="mini" style="margin: 0 20px 5px 0;">一次性完整输出</el-radio>
              <el-radio label="stream" size="mini">流式回复</el-radio>
            </el-radio-group>
          </el-form-item>-->
          <el-form-item label="接口通道" prop="channel">
            <el-radio-group v-model="form['channel']">
              <el-radio label="gpt" size="mini" style="margin: 0 20px 5px 0;">直连</el-radio>
<!--              <el-radio label="diy" size="mini">使用接口文件转发</el-radio>-->
              <el-radio label="agent" size="mini">反向代理</el-radio>
            </el-radio-group>
          </el-form-item>
<!--          <div v-if="form['channel'] === 'diy'">
            <el-alert
              type="warning"
              title="用于流式回复时，效果略差，承载能力低"
              style="width: 400px; margin-left: 50px; margin-bottom: 30px;"
              :closable="false"
            ></el-alert>
            <el-form-item label="接口域名" prop="host">
              <el-input v-model="form['host']" placeholder="http:// 或 https://域名 ，结尾不带/" size="small" style="width: 300px;" />
            </el-form-item>
            <el-form-item label="接口密钥">
              <el-input v-model="form['key']" placeholder="自定义" size="small" style="width: 160px;" />
            </el-form-item>
          </div>-->
          <div v-if="form['channel'] === 'agent'">
            <el-alert
              type="warning"
              title="此方式速度快、稳定、承载能力强，推荐使用"
              style="width: 400px; margin-left: 50px; margin-bottom: 30px;"
              :closable="false"
            ></el-alert>
            <el-form-item label="反代域名" prop="agent_host">
              <el-input v-model="form['agent_host']" placeholder="http:// 或 https://域名 ，结尾不带/" size="small" style="width: 300px;" />
              <p><a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/fandai" target="_blank">《反向代理配置教程》</a></p>
            </el-form-item>
          </div>
          <div v-else>
            <el-alert
              type="warning"
              title="主程序运行在国外服务器时，使用直连"
              style="width: 400px; margin-left: 50px; margin-bottom: 30px;"
              :closable="false"
            ></el-alert>
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
import { uploadImage } from '@/api/upload'

export default {
  data() {
    return {
      tabName: 'system',
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
      if (this.tabName === 'system') {
        formObj = this.$refs.systemForm
      } else if (this.tabName === 'api') {
        formObj = this.$refs.apiForm
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
    },
    uploadImage(item) {
      var form = new FormData()
      form.append('file', item.file)
      if (item.data) {
        form.append('data', JSON.stringify(item.data))
      }
      uploadImage(form).then(res => {
        if (item.data.type === 'system_logo') {
          this.$set(this.form, 'system_logo', res.data.path)
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
