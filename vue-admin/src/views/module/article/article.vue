<template>
  <div class="app-container" style="padding-top: 10px;">
      <el-form
        ref="form"
        :model="form"
        :rules="formRules"
        label-width="120px"
        style="padding: 30px 0;"
      >
        <el-form-item label="文章内容" prop="content">
          <keep-alive>
            <UEditor ref="ue" />
          </keep-alive>
        </el-form-item>
        <el-form-item label="">
          <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">保 存</el-button>
        </el-form-item>
      </el-form>
  </div>
</template>

<script>
import { getArticle, saveArticle } from '@/api/article'
import UEditor from '@/components/UEditor'

export default {
  components: {
    UEditor
  },
  data() {
    return {
      form: {
        content: ''
      },
      formRules: {
        content: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  computed: {
    type() {
      return this.$route.meta.name
    }
  },
  watch: {
    type() {
      this.getArticle()
    }
  },
  mounted() {
    setTimeout(() => {
      this.getArticle()
    }, 500)
  },
  methods: {
    getArticle() {
      getArticle({ type: this.type }).then(res => {
        if (res.data) {
          this.$refs.ue.setUEContent(res.data.content)
        } else {
          this.$refs.ue.setUEContent('')
        }
      })
    },
    doSubmit() {
      const content = this.$refs.ue.getUEContent()
      saveArticle({
        type: this.type,
        content: content
      }).then(res => {
        this.getArticle()
        this.$message.success(res.message)
      })
    }

  }
}
</script>

<style scoped>
p {
  margin: 0;
  padding: 0;
  line-height: 24px;
  color: #666;
  font-size: 14px;
}
</style>
