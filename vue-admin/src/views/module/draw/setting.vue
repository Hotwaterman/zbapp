<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="AI绘画 - 参数配置" name="draw">
        <el-alert type="warning" title="Key统一在key池配置" style="width: 500px; margin-top: 20px;"></el-alert>
        <el-form
          v-if="form"
          ref="drawForm"
          :model="form"
          :rules="formRules"
          label-width="120px"
          style="padding: 30px 0;"
        >
          <el-form-item label="绘画接口类型" prop="platform">
            <el-radio-group v-model="form['platform']">
<!--              <el-radio label="openai" size="mini" style="margin: 0 20px 5px 0;">OpenAI</el-radio>-->
              <el-radio label="mj" size="mini" style="margin: 0 20px 5px 0;">Midjourney</el-radio>
              <el-radio label="other" size="mini" style="margin: 0 20px 5px 0;">其他绘画接口</el-radio>
<!--              <el-radio label="sd" size="mini" style="margin: 0 20px 5px 0;">Stable-Diffusion</el-radio>-->
            </el-radio-group>
          </el-form-item>
          <el-form-item label="接口通道" prop="channel">
            <el-radio-group v-if="form['platform'] === 'openai'" v-model="form['channel']">
              <el-radio label="openai" size="mini" style="margin: 0 20px 5px 0;">OpenAI官方接口</el-radio>
              <el-radio label="api2d" size="mini" style="margin: 0 20px 5px 0;">Api2d</el-radio>
            </el-radio-group>
            <el-radio-group v-if="form['platform'] === 'mj'" v-model="form['channel']">
              <el-radio label="lxai" size="mini" style="margin: 0 20px 5px 0;">灵犀AI</el-radio>
<!--              <el-radio label="replicate" size="mini" style="margin: 0 20px 5px 0;">Replicate</el-radio>-->
            </el-radio-group>
            <el-radio-group v-if="form['platform'] === 'sd'" v-model="form['channel']">
              <el-radio label="lxai" size="mini" style="margin: 0 20px 5px 0;">灵犀AI</el-radio>
            </el-radio-group>
            <el-radio-group v-if="form['platform'] === 'other'" v-model="form['channel']">
              <el-radio label="yijian" size="mini" style="margin: 0 20px 5px 0;">意间AI</el-radio>
            </el-radio-group>
            <p v-if="form['platform'] === 'openai' && form['channel'] === 'openai'">可以使用普通的openai key，请根据您当地的法规酌情使用</p>
            <p v-if="form['platform'] === 'openai' && form['channel'] === 'api2d'">这是一个提供openai接口的第三方平台，消耗90P/张，<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/api2d" target="_blank">注册地址</a></p>
            <p v-if="form['platform'] === 'mj' && form['channel'] === 'lxai'">
              注：MJ接口服务免费，但需要购买灵犀key和MJ账号<br>
              1、购买灵犀key，填到key池。<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/lxai" target="_blank">灵犀key下单地址</a><br>
              2、自备MJ账号，并绑定到灵犀AI。<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/lxmj" target="_blank">MJ绑定教程</a>
            </p>
            <p v-if="form['platform'] === 'mj' && form['channel'] === 'replicate'">由Replicate提供的Midjourney接口，<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/replicate" target="_blank">注册地址</a></p>
            <p v-if="form['platform'] === 'other' && form['channel'] === 'yijian'">由意间AI提供的绘画接口，￥0.05/张，<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/yijian" target="_blank">注册&充值地址</a></p>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit('drawForm')">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
    </el-tabs>

  </div>
</template>

<script>
import { getSetting, setSetting, getBalance, getLxaiBalance } from '@/api/setting'

export default {
  data() {
    return {
      tabName: 'draw',
      form: null,
      balance: null,
      lxaiBalance: null,
      formRules: {
        'platform': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'channel': [
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
      getSetting({ name: this.tabName }).then(res => {
        this.form = res.data
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
    getBalance() {
      getBalance({
        apikey: this.form.apikey
      }).then(res => {
        this.balance = res.data
      })
    },
    getLxaiBalance() {
      getLxaiBalance({
        apikey: this.form.lxai_key
      }).then(res => {
        this.lxaiBalance = res.data
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
    width: 320px;
  }

  .el-select {
    width: 320px;
  }

  .el-switch {
    transform: scale(0.80);
  }

  .textarea {
    width: 400px;
    max-width: 100%;
  }

  p {
    margin: 0;
    padding: 0;
    line-height: 24px;
    color: #666;
    font-size: 14px;
  }
</style>
