<template>
  <div v-if="form">
    <el-dialog
      custom-class="my-dialog"
      :title="form.id ? '编辑' : '添加文案'"
      width="520px"
      :visible="true"
      :close-on-click-modal="false"
      :append-to-body="true"
      :before-close="closeForm"
    >
      <el-form ref="form" :model="form" :rules="formRules" label-width="100px">
        <el-form-item label="权重" prop="weight">
          <el-input v-model="form.weight" placeholder="越大越靠前" size="small" style="width: 110px;" />
          <span class="tips">越大越靠前</span>
        </el-form-item>
        <el-form-item label="文案内容" prop="content">
          <el-input v-model="form.content" :rows="6" type="textarea" placeholder="不要过长" size="small" style="width: 340px;" />
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button type="default" icon="el-icon-close" size="small" @click="closeForm">取 消</el-button>
        <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">提 交</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
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
        content: [
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
    }
  }
}
</script>
<style lang="scss" scoped>
  .tips {
    margin-left: 10px;
    color: #999;
  }
</style>
