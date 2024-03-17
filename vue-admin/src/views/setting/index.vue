<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="系统配置" name="system">
        <el-form
          v-if="form"
          ref="systemForm"
          :model="form"
          :rules="formRules"
          label-width="120px"
          style="padding: 30px 0;"
        >
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
              <img
                v-if="form['system_logo']"
                :src="form['system_logo']"
                class="avatar"
                style="height: 50px; width: auto;"
              >
              <i
                v-else
                class="el-icon-plus avatar-uploader-icon"
                style="width: 210px; height: 50px; line-height:50px;"
              />
            </el-upload>
            <span>210x50像素</span>
          </el-form-item>
          <!--<el-form-item label="ICP备案号" prop="system_icp">
            <el-input v-model="form['system_icp']" placeholder="ICP备案号" size="small" />
          </el-form-item>
          <el-form-item label="公网安备号" prop="system_gongan">
            <el-input v-model="form['system_gongan']" placeholder="公网安备号" size="small" />
          </el-form-item>-->
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>

      <el-tab-pane label="AI参数配置" name="chatgpt">
        <el-alert type="warning" title="Key统一在key池配置" style="width: 800px; margin-top: 20px;"></el-alert>
        <el-form
          v-if="form"
          ref="chatgptForm"
          :model="form"
          :rules="formRules"
          label-width="160px"
          style="padding: 30px 0;"
        >
          <el-form-item label="AI接口" prop="channel">
            <el-radio-group v-model="form['channel']">
              <el-radio label="baidu" size="mini">百度文心一言</el-radio>
              <el-radio label="lxai" size="mini">灵犀AI</el-radio>
              <el-radio label="api2d" size="mini">Api2d</el-radio>
              <el-radio label="openai" size="mini">自定义</el-radio>
              <el-radio label="azure" size="mini" disabled title="即将上线">Azure</el-radio>
              <el-radio label="xunfei" size="mini" disabled title="静待开放">讯飞星火</el-radio>
<!--              <el-radio label="aliyun" size="mini" disabled title="静待开放">阿里通义千问</el-radio>-->
            </el-radio-group>
            <p v-if="form['channel'] === 'openai'">请根据您当地的法规使用，禁止非法运营</p>
            <p v-if="form['channel'] === 'api2d'">这是一个提供openai接口的第三方平台，<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/api2d" target="_blank">注册地址</a></p>
            <p v-if="form['channel'] === 'lxai'">这是一家国内的AI接口，与openai计费规则一样，<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/lxai" target="_blank">key下单地址</a></p>
            <p v-if="form['channel'] === 'baidu'">内测阶段，需先<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/baidu_apply" target="_blank">申请文心接口</a>，然后<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/baidu" target="_blank">创建应用</a></p>
            <p v-if="form['channel'] === 'azure'"><a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/azure" target="_blank">申请教程</a></p>
          </el-form-item>

          <div v-if="form['channel'] === 'openai'">
            <el-form-item label="接口地址" prop="diyhost">
              <el-input v-model="form['diyhost']" placeholder="https://或http://，结尾不带/" size="small" />
              <p>https://或http://的接口地址，结尾不带/</p>
            </el-form-item>
          </div>
          <div v-if="form['channel'] === 'baidu'">
            <el-form-item label="温度(temperature)" prop="temperature">
              <el-input v-model="form['temperature']" placeholder="0-1，默认0.9" size="small" />
              <p>取值0-1之间，默认0.9。较高的数值会使输出更加随机，而较低的数值会使其更加集中和确定。</p>
            </el-form-item>
            <el-form-item label="模型(model)" prop="model">
              <el-radio checked>ERNIE-Bot</el-radio>
            </el-form-item>
          </div>
          <div v-else-if="form['channel'] === 'xunfei'">
            <el-form-item label="温度(temperature)" prop="temperature">
              <el-input v-model="form['temperature']" placeholder="0-1，默认0.9" size="small" />
              <p>取值0-1之间，默认0.9。较高的数值会使输出更加随机，而较低的数值会使其更加集中和确定。</p>
            </el-form-item>
            <el-form-item label="最大长度(max_tokens)" prop="max_tokens">
              <el-input v-model="form['max_tokens']" placeholder="默认2048" size="small" />
              <p>每次回答的最大字符长度，如果设置过小，回答中间可能被切断，如果设置过大，则可能消耗更多的成本</p>
              <p>最大4096，默认2048</p>
            </el-form-item>
          </div>
          <div v-else>
            <el-form-item label="温度(temperature)" prop="temperature">
              <el-input v-model="form['temperature']" placeholder="0-1，默认0.9" size="small" />
              <p>取值0-1之间，默认0.9。较高的数值会使输出更加随机，而较低的数值会使其更加集中和确定。</p>
            </el-form-item>
            <el-form-item label="最大长度(max_tokens)" prop="max_tokens">
              <el-input v-model="form['max_tokens']" placeholder="默认1500" size="small" />
              <p>每次回答的最大字符长度，如果设置过小，回答中间可能被切断，如果设置过大，则可能消耗更多的成本</p>
              <p>4K模型：最大3500，建议2000以内</p>
              <p>16K模型：最大15000，建议8000以内</p>
            </el-form-item>
            <el-form-item label="模型(model)" prop="model">
              <el-select v-model="form['model']" size="small">
                <el-option
                  v-for="(item,index) in engines"
                  :key="index"
                  :label="item.name"
                  :value="item.name"
                >{{ item.name }}
                </el-option>
              </el-select>
              <p>默认：gpt-3.5-turbo ，<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/model" target="_blank">查看各模型区别</a></p>
            </el-form-item>
          </div>

          <el-form-item label="新用户免费送" prop="free_num">
            <p style="padding: 5px 0;">
              对话次数
              <el-input v-model="form['free_num']" size="small" style="width: 100px;" />
              条
            </p>
            <p style="padding: 5px 0;">
              绘画次数
              <el-input v-model="form['free_num_draw']" size="small" style="width: 100px;" />
              张
            </p>
            <p style="padding: 5px 0;">
              GPT4字数
              <el-input v-model="form['free_num_gpt4']" size="small" style="width: 100px;" />
              万字
            </p>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>

      <el-tab-pane label="GPT4配置" name="gpt4">
        <el-alert type="warning" title="Key统一在key池配置" style="width: 800px; margin-top: 20px;"></el-alert>
        <el-form
          v-if="form"
          ref="gpt4Form"
          :model="form"
          :rules="formRules"
          label-width="160px"
          style="padding: 30px 0;"
        >
          <el-form-item label="GPT4开关" prop="is_open">
            <el-switch
              v-model="form['is_open']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
          <el-form-item label="GPT4接口" prop="channel">
            <el-radio-group v-model="form['channel']">
              <el-radio label="openai" size="mini" style="margin: 0 20px 5px 0;">openai</el-radio>
              <el-radio label="api2d" size="mini">api2d</el-radio>
            </el-radio-group>
            <p v-if="form['channel'] === 'openai'">需自备支持GPT4接口的账号，请根据您当地的法规使用，禁止非法运营</p>
            <p v-if="form['channel'] === 'api2d'">这是一个提供openai接口的第三方平台，<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/api2d" target="_blank">注册地址</a></p>
          </el-form-item>
          <el-form-item label="温度(temperature)" prop="temperature">
            <el-input v-model="form['temperature']" placeholder="0-1，默认0.9" size="small" />
            <p>取值0-1之间，默认0.9。较高的数值会使输出更加随机，而较低的数值会使其更加集中和确定。</p>
          </el-form-item>
          <el-form-item label="最大长度(max_tokens)" prop="max_tokens">
            <el-input v-model="form['max_tokens']" placeholder="默认3000" size="small" />
            <p>每次回答的最大字符长度，最大8000</p>
          </el-form-item>
          <el-form-item label="模型(model)" prop="model">
            <el-select v-model="form['model']" size="small">
              <el-option
                v-for="(item,index) in engines"
                :key="index"
                :label="item.name"
                :value="item.name"
              >{{ item.name }}
              </el-option>
            </el-select>
            <p>默认：gpt-4</p>
            <p><a href="https://console.ttk.ink/api.php/link/ai/name/model">各模型区别</a></p>
          </el-form-item>
          <el-form-item label="GPT4别名">
            <p style="padding: 5px 0;">
              小程序端
              <el-input v-model="form['alias_wxapp']" size="small" style="width: 100px; margin-left: 5px;" />
            </p>
            <p style="padding: 5px 0;">
              web端
              <el-input v-model="form['alias_web']" size="small" style="width: 100px; margin-left: 5px;" />
            </p>
            <p style="margin-top: 10px; color: #999;">留空默认显示GPT4</p>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>

      <el-tab-pane label="支付配置" name="pay">
        <el-form
          v-if="form"
          ref="payForm"
          :model="form"
          :rules="formRules"
          label-width="120px"
          style="padding: 30px 0;"
        >
          <el-form-item label="商户号" prop="mch_id">
            <el-input v-model="form['mch_id']" placeholder="微信商户号" size="small" />
          </el-form-item>
          <el-form-item label="Api_v2密钥" prop="key">
            <el-input v-model="form['key']" placeholder="微信商户号v2密钥" size="small" />
          </el-form-item>

          <el-form-item label="cert证书">
            <el-upload
              action=""
              :data="{type: 'cert'}"
              :before-upload="uploadPemCheck"
              :http-request="uploadPem"
              :show-file-list="false"
              :multiple="false"
            >
              <el-button size="small" type="primary">选择文件</el-button>
            </el-upload>
            <span v-if="form.apiclient_cert" class="upload_status">已上传</span>
          </el-form-item>
          <el-form-item label="key证书">
            <el-upload
              action=""
              :data="{type: 'key'}"
              :before-upload="uploadPemCheck"
              :http-request="uploadPem"
              :show-file-list="false"
              :multiple="false"
            >
              <el-button size="small" type="primary">选择文件</el-button>
            </el-upload>
            <span v-if="form.apiclient_key" class="upload_status">已上传</span>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
      <el-tab-pane label="流量主广告配置" name="ad">
        <el-form
          v-if="form"
          ref="adForm"
          :model="form"
          :rules="formRules"
          label-width="180px"
          style="padding: 30px 0;"
        >
          <el-form-item label="首页插屏广告" prop="inter_is_open">
            <el-switch
              v-model="form['inter_is_open']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
          <el-form-item label="插屏广告位ID" prop="inter_unit_id">
            <el-input v-model="form['inter_unit_id']" size="small" />
            <p style="color: #666; margin: 0;">首页插屏广告的广告位ID，每隔6小时显示一次</p>
          </el-form-item>
          <el-form-item label="首页底部banner广告" prop="banner_is_open">
            <el-switch
              v-model="form['banner_is_open']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
          <el-form-item label="banner广告位ID" prop="banner_unit_id">
            <el-input v-model="form['banner_unit_id']" size="small" />
            <p style="color: #666; margin: 0;">首页底部banner广告的广告位ID</p>
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
import { getSetting, setSetting, getEngines } from '@/api/setting'
import { uploadImage, uploadPem } from '@/api/upload'

export default {
  data() {
    return {
      tabName: 'system',
      form: null,
      engines: [],
      balance: null,
      lxaiBalance: null,
      formRules: {
        'channel': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'is_open': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'mch_id': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'key': [
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
    getEngines(type) {
      getEngines({
        type: type
      }).then(res => {
        this.engines = res.data
      })
    },
    switchTab() {
      this.getSetting()
      if (this.tabName === 'chatgpt') {
        this.getEngines('GPT3.5')
      } else if (this.tabName === 'gpt4') {
        this.getEngines('GPT4')
      }
    },
    doSubmit() {
      var formObj = null
      if (this.tabName === 'system') {
        formObj = this.$refs.systemForm
      } else if (this.tabName === 'pay') {
        formObj = this.$refs.payForm
      } else if (this.tabName === 'chatgpt') {
        formObj = this.$refs.chatgptForm
      }  else if (this.tabName === 'gpt4') {
        formObj = this.$refs.gpt4Form
      } else if (this.tabName === 'ad') {
        formObj = this.$refs.adForm
      } else if (this.tabName === 'about') {
        formObj = this.$refs.aboutForm
      } else if (this.tabName === 'kefu') {
        formObj = this.$refs.kefuForm
      } else if (this.tabName === 'sms') {
        formObj = this.$refs.smsForm
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
        this.$set(this.form, item.data.type, res.data.path)
        this.$message.success('上传成功')
      })
    },
    uploadPemCheck(field) {
      if (field.name.substr(-3) !== 'pem') {
        this.$message({
          showClose: true,
          message: '请上传后缀为.pem的证书文件',
          type: 'warning'
        })
        return false
      }
    },
    uploadPem(item) {
      var form = new FormData()
      form.append('file', item.file)
      if (item.data) {
        form.append('data', JSON.stringify(item.data))
      }
      uploadPem(form).then(res => {
        if (item.data.type === 'cert') {
          this.$set(this.form, 'apiclient_cert', res.data)
        } else if (item.data.type === 'key') {
          this.$set(this.form, 'apiclient_key', res.data)
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
