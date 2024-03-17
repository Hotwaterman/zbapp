<template>
  <div v-if="form">
    <el-dialog
      custom-class="my-dialog"
      :title="form.id ? '编辑' : '添加海报'"
      width="950px"
      :visible="true"
      :close-on-click-modal="false"
      :append-to-body="true"
      :before-close="closeForm"
    >
      <div style="position: relative;width: 100%; height: 100%;">
        <el-form v-if="form" ref="form" :model="form" label-width="120px" style="width: 460px; background:#fff; min-height: 550px;">
          <el-form-item label="权重" prop="weight">
            <el-input v-model="form['weight']" placeholder="越大越靠前" size="small" style="width: 110px;" />
            <span class="tips">越大越靠前</span>
          </el-form-item>
          <el-form-item label="背景图片" prop="bg">
            <el-input v-model="form['bg']" placeholder="输入图片地址或上传图片" size="small" @blur="getBgSize" />
            <el-upload
              class="avatar-uploader"
              action=""
              :data="{type: 'bg'}"
              :http-request="uploadImage"
              :show-file-list="false"
              :multiple="false"
            >
              <img v-if="form['bg']" :src="form['bg']" class="avatar" style="height: 130px; width: auto;">
              <i v-else class="el-icon-plus avatar-uploader-icon" style="width: 100px; height: 130px; line-height:130px;" />
            </el-upload>
            <span>支持png/jpg格式</span>
          </el-form-item>
          <el-form-item label="图片尺寸" prop="bg_w">
            <el-input v-model="form['bg_w']" type="number" placeholder="宽" size="small" class="number" style="width:100px;" />
            <el-input v-model="form['bg_h']" type="number" placeholder="高" size="small" class="number" style="width:100px;" />
            <span>px</span>
          </el-form-item>
          <el-form-item label="二维码孔位" prop="hole">
            <el-input v-model="form['hole_w']" type="number" placeholder="宽" size="small" class="number" style="width:60px;" />
            <el-input v-model="form['hole_h']" type="number" placeholder="高" size="small" class="number" style="width:60px;" />
            <el-input v-model="form['hole_x']" type="number" placeholder="距左" size="small" class="number" style="width:60px;" />
            <el-input v-model="form['hole_y']" type="number" placeholder="距上" size="small" class="number" style="width:60px;" />
            <span>px</span>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
          </el-form-item>
        </el-form>
        <div style="position: absolute; top:0; left: 460px; right:0; bottom:0;background: #d7d7d7; overflow: hidden;">
          <div v-if="form && form['bg'] && form['bg_w'] && form['bg_h']" :style="'position: absolute;transform: scale(' + (400/form['bg_h']) + ');left:-' + ((form['bg_w'] - form['bg_w']*400/form['bg_h'])/2 - 30) + 'px;top:-' + ((form['bg_h'] - 400)/2 - 30) + 'px;'">
            <img :src="form['bg']">
            <img v-if="form['hole_w'] && form['hole_h']" src="@/assets/qrcode.png" :style="'position:absolute; width:' + form['hole_w'] + 'px; height:' + form['hole_h'] + 'px;left:' + (form['hole_x']?form['hole_x']:0) + 'px; top:' + (form['hole_y']?form['hole_y']:0) + 'px;'">
          </div>
        </div>

      </div>
    </el-dialog>
  </div>
</template>

<script>
import { uploadImage } from '@/api/upload'

export default {
  props: {
    values: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      form: null,
      formRules: {
        weight: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        bg: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  created() {
    this.form = this.values
    if (!this.form.weight) {
      this.$set(this.form, 'weight', 100)
    }
  },
  methods: {
    closeForm() {
      this.$emit('close')
    },
    doSubmit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.$emit('submit', this.form)
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
        this.getBgSize()
        this.$message.success('上传成功')
      })
    },
    getBgSize() {
      if (!this.form.bg) {
        return
      }
      var _this = this;
      var img = new Image()
      img.src = this.form.bg
      img.onload = function() {
        _this.$set(_this.form, 'bg_w', img.width)
        _this.$set(_this.form, 'bg_h', img.height)
      }
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
.tips {
  margin-left: 10px;
  color: #999;
}
.el-input {
  width: 240px;
}
.number {
  width: 66px;
  margin-right: 5px;
}
</style>
