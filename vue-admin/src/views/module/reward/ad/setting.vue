<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="激励广告 - 参数配置" name="reward_ad">
        <el-form v-if="form" ref="form" :model="form" :rules="formRules" label-width="150px" style="width: 600px; padding: 20px 0;">
          <el-form-item label="功能开关" prop="is_open">
            <el-switch
              v-model="form['is_open']"
              :active-value="1"
              :inactive-value="0"
            />
            <p>此任务仅支持小程序端</p>
          </el-form-item>
          <el-form-item label="观看一次奖励" prop="num">
            <el-input v-model="form['num']" placeholder="正整数" size="small" style="width: 100px;" /> <span style="color:#666; margin-left: 10px;">条</span>
          </el-form-item>
          <el-form-item label="每天最多观看" prop="max">
            <el-input v-model="form['max']" placeholder="正整数" size="small" style="width: 100px;" /> <span style="color:#666; margin-left: 10px;">次有奖励</span>
          </el-form-item>
          <el-form-item label="广告位ID" prop="ad_unit_id">
            <el-input v-model="form['ad_unit_id']" size="small" />
            <p style="color: #666; margin: 0;">流量主激励视频广告的广告位ID</p>
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
import { getSetting, setSetting } from '@/api/setting'
import { uploadImage } from '@/api/upload'

export default {
  data() {
    return {
      tabName: 'reward_ad',
      form: {},
      host: '',
      formRules: {

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
    doSubmit() {
      this.$refs['form'].validate((valid) => {
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
        if (item.data.type === 'share_image') {
          this.$set(this.form, 'share_image', res.data.path)
        }
        this.$message.success('上传成功')
      })
    }
  }
}
</script>
