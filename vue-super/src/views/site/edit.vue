<template>
  <div v-if="form">
    <el-dialog
      custom-class="my-dialog"
      :title="site.id ? '编辑' : '新建站点'"
      width="500px"
      :visible="true"
      :close-on-click-modal="false"
      :append-to-body="true"
      :before-close="closeForm"
    >
      <el-form ref="form" :model="form" :rules="formRules" label-width="120px">
        <el-form-item label="名称" prop="title">
          <el-input v-model="form.title" placeholder="请输入站点名称" size="small" style="width: 240px;" />
        </el-form-item>
        <el-form-item label="登录账号" prop="phone">
          <el-input v-model="form.phone" placeholder="建议使用手机号" size="small" style="width: 240px;" />
        </el-form-item>
        <el-form-item label="登录密码" prop="password">
          <el-input v-model="form.password" type="password" placeholder="请输入登录密码" size="small" style="width: 240px;" />
        </el-form-item>
        <el-form-item label="到期时间" prop="expire_time">
          <el-date-picker
            v-model="form.expire_time"
            type="date"
            placeholder="请选择时间"
            value-format="yyyy-MM-dd"
            editable
            clearable
          >
          </el-date-picker>
          <p>留空表示【长期】</p>
        </el-form-item>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="form.remark" :rows="3" type="textarea" placeholder="备注" size="small" style="width: 300px;" />
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
    site: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      form: null,
      formRules: {
        title: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        phone: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  created() {
    this.form = this.site
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
