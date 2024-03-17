<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="图片存储配置" name="storage">
        <el-form
          v-if="form"
          ref="storageForm"
          :model="form"
          :rules="formRules"
          label-width="180px"
          style="padding: 30px 0;"
        >
          <el-form-item label="存储引擎" prop="engine">
            <el-radio-group v-model="form['engine']">
              <el-radio label="local" size="mini" style="margin: 0 20px 5px 0;">本地服务器</el-radio>
              <el-radio label="alioss" size="mini" style="margin: 0 20px 5px 0;">阿里云OSS</el-radio>
              <el-radio label="txcos" size="mini">腾讯云COS</el-radio>
              <el-radio label="qiniu" size="mini">七牛云对象存储</el-radio>
            </el-radio-group>
          </el-form-item>
          <div v-if="form['engine'] === 'alioss'">
            <el-form-item label="存储区域" prop="alioss_region">
              <el-select v-model="form['alioss_region']" size="small">
                <el-option
                  v-for="(item, key) in aliossRegions"
                  :key="key"
                  :label="item"
                  :value="key"
                >{{ item }} ( {{ key }} )
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="空间名称 Bucket" prop="alioss_bucket">
              <el-input v-model="form['alioss_bucket']" placeholder="空间名称 Bucket" size="small" />
            </el-form-item>
            <el-form-item label="AccessKey ID" prop="alioss_access_key_id">
              <el-input v-model="form['alioss_access_key_id']" placeholder="AccessKey ID" size="small" />
            </el-form-item>
            <el-form-item label="AccessKey Secret" prop="alioss_access_key_secret">
              <el-input v-model="form['alioss_access_key_secret']" placeholder="AccessKey Secret" size="small" />
            </el-form-item>
            <el-form-item label="访问域名 Domain" prop="alioss_domain">
              <el-input v-model="form['alioss_domain']" placeholder="访问域名 Domain，没绑定请留空" size="small" />
              <p>空间没绑定域名请留空，开头带http://或https://，结尾不带/</p>
            </el-form-item>
          </div>
          <div v-if="form['engine'] === 'txcos'">
            <el-form-item label="存储区域" prop="txcos_region">
              <el-select v-model="form['txcos_region']" size="small">
                <el-option
                  v-for="(item, key) in txcosRegions"
                  :key="key"
                  :label="item"
                  :value="key"
                >{{ item }} ( {{ key }} )
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="存储桶名称 Bucket" prop="txcos_bucket">
              <el-input v-model="form['txcos_bucket']" placeholder="存储桶名称 Bucket" size="small" />
            </el-form-item>
            <el-form-item label="访问密钥 AccessID" prop="txcos_secret_id">
              <el-input v-model="form['txcos_secret_id']" placeholder="访问密钥 AccessID" size="small" />
            </el-form-item>
            <el-form-item label="安全密钥 SecretKey" prop="txcos_secret_key">
              <el-input v-model="form['txcos_secret_key']" placeholder="安全密钥 SecretKey" size="small" />
            </el-form-item>
            <el-form-item label="访问域名 Domain" prop="txcos_domain">
              <el-input v-model="form['txcos_domain']" placeholder="访问域名 Domain，没绑定请留空" size="small" />
              <p>空间没绑定域名请留空，开头带http://或https://，结尾不带/</p>
            </el-form-item>
          </div>
          <div v-if="form['engine'] === 'qiniu'">
            <el-form-item label="存储区域" prop="qiniu_region">
              <el-select v-model="form['qiniu_region']" size="small">
                <el-option
                  v-for="(item, key) in qiniuRegions"
                  :key="key"
                  :label="item"
                  :value="key"
                >{{ item }} ( {{ key }} )
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="空间名称 Bucket" prop="qiniu_bucket">
              <el-input v-model="form['qiniu_bucket']" placeholder="空间名称 Bucket" size="small" />
            </el-form-item>
            <el-form-item label="访问密钥 AccessID" prop="qiniu_access_key">
              <el-input v-model="form['qiniu_access_key']" placeholder="访问密钥 AccessKey" size="small" />
            </el-form-item>
            <el-form-item label="安全密钥 SecretKey" prop="qiniu_secret_key">
              <el-input v-model="form['qiniu_secret_key']" placeholder="安全密钥 SecretKey" size="small" />
            </el-form-item>
            <el-form-item label="访问域名 Domain" prop="qiniu_domain">
              <el-input v-model="form['qiniu_domain']" placeholder="访问域名 Domain，没绑定请留空" size="small" />
              <p>开头带http://或https://，结尾不带/</p>
            </el-form-item>
          </div>

          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit('storageForm')">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
    </el-tabs>

  </div>
</template>

<script>
import { getSetting, setSetting } from '@/api/setting'

export default {
  data() {
    return {
      tabName: 'storage',
      form: null,
      aliossRegions: {
        'oss-cn-hangzhou.aliyuncs.com': '华东1（杭州）',
        'oss-cn-shanghai.aliyuncs.com': '华东2（上海）',
        'oss-cn-nanjing.aliyuncs.com': '华东5（南京本地地域）',
        'oss-cn-fuzhou.aliyuncs.com': '华东6（福州本地地域）',
        'oss-cn-qingdao.aliyuncs.com': '华北1（青岛）',
        'oss-cn-beijing.aliyuncs.com': '华北2（北京）',
        'oss-cn-zhangjiakou.aliyuncs.com': '华北3（张家口）',
        'oss-cn-huhehaote.aliyuncs.com': '华北5（呼和浩特）',
        'oss-cn-wulanchabu.aliyuncs.com': '华北6（乌兰察布）',
        'oss-cn-shenzhen.aliyuncs.com': '华南1（深圳）',
        'oss-cn-heyuan.aliyuncs.com': '华南2（河源）',
        'oss-cn-guangzhou.aliyuncs.com': '华南3（广州）',
        'oss-cn-chengdu.aliyuncs.com': '西南1（成都）',
        'oss-cn-hongkong.aliyuncs.com': '中国（香港）',
        'oss-us-west-1.aliyuncs.com': '美国（硅谷）',
        'oss-us-east-1.aliyuncs.com': '美国（弗吉尼亚）',
        'oss-ap-northeast-1.aliyuncs.com': '日本（东京）',
        'oss-ap-northeast-2.aliyuncs.com': '韩国（首尔）',
        'oss-ap-southeast-1.aliyuncs.com': '新加坡',
        'oss-ap-southeast-2.aliyuncs.com': '澳大利亚（悉尼）',
        'oss-ap-southeast-3.aliyuncs.com': '马来西亚（吉隆坡）',
        'oss-ap-southeast-5.aliyuncs.com': '印度尼西亚（雅加达）',
        'oss-ap-southeast-6.aliyuncs.com': '菲律宾（马尼拉）',
        'oss-ap-southeast-7.aliyuncs.com': '泰国（曼谷）',
        'oss-ap-south-1.aliyuncs.com': '印度（孟买）',
        'oss-eu-central-1.aliyuncs.com': '德国（法兰克福）',
        'oss-eu-west-1.aliyuncs.com': '英国（伦敦）',
        'oss-me-east-1.aliyuncs.com': '阿联酋（迪拜）',
        'oss-rg-china-mainland.aliyuncs.com': '无地域属性（中国内地）'
      },
      txcosRegions: {
        'ap-beijing-1': '中国大陆 北京一区',
        'ap-beijing': '中国大陆 北京',
        'ap-nanjing': '中国大陆 南京',
        'ap-shanghai': '中国大陆 上海',
        'ap-guangzhou': '中国大陆 广州',
        'ap-chengdu': '中国大陆 成都',
        'ap-chongqing': '中国大陆 重庆',
        'ap-shenzhen-fsi': '中国大陆 金融云 深圳金融',
        'ap-shanghai-fsi': '中国大陆 金融云 上海金融',
        'ap-beijing-fsi': '中国大陆 金融云 北京金融',
        'ap-hongkong': '亚太地区 中国香港',
        'ap-singapore': '亚太地区 新加坡',
        'ap-mumbai': '亚太地区 孟买',
        'ap-jakarta': '亚太地区 雅加达',
        'ap-seoul': '亚太地区 首尔',
        'ap-bangkok': '亚太地区 曼谷',
        'ap-tokyo': '亚太地区 东京',
        'na-siliconvalley': '北美地区 硅谷',
        'na-ashburn': '北美地区 弗吉尼亚',
        'na-toronto': '北美地区 多伦多',
        'sa-saopaulo': '北美地区 圣保罗',
        'eu-frankfurt': '欧洲地区 法兰克福',
        'eu-moscow': '欧洲地区 莫斯科'
      },
      qiniuRegions: {
        'up.qiniup.com': '华东-浙江',
        'up-cn-east-2.qiniup.com': '华东-浙江2',
        'up-z1.qiniup.com': '华北-河北',
        'up-z2.qiniup.com': '华南-广东',
        'up-na0.qiniup.com': '北美-洛杉矶',
        'up-as0.qiniup.com': '亚太-新加坡',
        'up-ap-northeast-1.qiniup.com': '亚太-首尔'
      },
      formRules: {
        engine: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        alioss_region: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        alioss_bucket: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        alioss_access_key_id: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        alioss_access_key_secret: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        txcos_region: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        txcos_bucket: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        txcos_secret_id: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        txcos_secret_key: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        qiniu_region: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        qiniu_bucket: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        qiniu_access_key: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        qiniu_secret_key: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        qiniu_domain: [
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

p {
  margin: 0;
  padding: 0;
  line-height: 24px;
  color: #666;
  font-size: 14px;
}
</style>
