<template>
  <div>
    <div>
      <el-radio-group v-model="form['type']">
        <el-radio label="text" size="mini">文字</el-radio>
        <el-radio label="image" size="mini">图片</el-radio>
      </el-radio-group>
    </div>

    <div v-if="form['type'] === 'text'">
      <el-input type="textarea" v-model="form['content']" :rows="6" placeholder="输入文字内容" size="small" style="width: 500px;" />
      <p><el-button type="text" size="small" @click="showLinkForm">插入链接</el-button></p>
    </div>

    <div v-if="form['type'] === 'image'">
      <el-input type="text" v-model="form['image']" placeholder="输入图片地址或上传图片" size="small" style="width: 400px;" />
      <el-upload
        class="avatar-uploader"
        action=""
        :data="{type: 'image'}"
        :http-request="uploadImage"
        :show-file-list="false"
        :multiple="false"
      >
        <img v-if="form['image']" :src="form['image']" class="avatar" style="height: 100px; width: auto;">
        <i v-else class="el-icon-plus avatar-uploader-icon" style="width: 100px; height: 100px; line-height:100px;"/>
      </el-upload>
      <span>10MB以内，支持bmp/png/jpeg/jpg/gif格式</span>
    </div>
    <div v-if="linkForm">
      <el-dialog
        custom-class="my-dialog"
        title="插入链接"
        width="460px"
        :visible="true"
        :append-to-body="true"
        :close-on-click-modal="false"
        :before-close="closeLinkForm"
      >
        <el-form ref="form" :model="linkForm" :rules="linkFormRules" label-width="80px">
          <el-form-item label="标题" prop="title">
            <el-input v-model="linkForm.title" placeholder="链接标题" size="small" style="width: 200px;" />
          </el-form-item>
          <el-form-item label="链接" prop="url">
            <el-input v-model="linkForm.url" placeholder="http:// 或 https://" size="small" style="width: 300px;" />
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="default" icon="el-icon-close" size="small" @click="closeLinkForm">取 消</el-button>
          <el-button type="primary" icon="el-icon-check" size="small" @click="sumitLinkForm">提 交</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { uploadImage } from '@/api/upload'

export default {
  props: {
    type: {
      type: String,
      default: 'text'
    },
    content: {
      type: String,
      default: ''
    },
    image: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      form: {
        type: 'text',
        content: '',
        image: ''
      },
      formRules: {
        'type': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'content': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'image': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      },
      linkForm: null,
      linkFormRules: {
        'title': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        'url': [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  watch: {
    type() {
      this.form.type = this.type
    },
    content() {
      this.form.content = this.content
    },
    image() {
      this.form.image = this.image
    },
  },
  mounted() {
    this.form.type = this.type
    this.form.content = this.content
    this.form.image = this.image
  },
  methods: {
    getReply() {
      return this.form
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
    showLinkForm() {
      this.linkForm = {}
    },
    closeLinkForm() {
      this.linkForm = null
    },
    sumitLinkForm() {
      this.form.content += '<a href="' + this.linkForm.url + '">' + this.linkForm.title + '</a>'
      this.closeLinkForm()
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
  p {
    margin: 0;
    padding: 0;
    line-height: 24px;
    color: #666;
    font-size: 14px;
  }
</style>
