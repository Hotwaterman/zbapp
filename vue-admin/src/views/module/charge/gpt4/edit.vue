<template>
  <div v-if="form">
    <el-dialog
      custom-class="my-dialog"
      :title="gpt4.id ? '编辑' : '创建'"
      width="660px"
      :visible="true"
      :close-on-click-modal="false"
      :append-to-body="true"
      :before-close="closeForm"
    >
      <el-form ref="form" :model="form" :rules="formRules" label-width="160px">
        <el-form-item label="权重" prop="weight">
          <el-input v-model="form.weight" placeholder="越大越靠前" size="small" style="width: 110px;" />
          <span class="tips">越大越靠前</span>
        </el-form-item>
        <el-form-item label="包含数量" prop="num">
          <el-input v-model="form.num" placeholder="填入整数" size="small" style="width: 110px;" />
          <span class="tips"> 万字</span>
        </el-form-item>
        <el-form-item label="售价" prop="price">
          <el-input v-model="form.price" placeholder="实际售价" size="small" style="width:100px;" />
          <span class="tips"> 元</span>
        </el-form-item>
        <el-form-item label="市场价（划线价）" prop="market_price">
          <el-input v-model="form.market_price" placeholder="划线价格" size="small" style="width:100px;" />
          <span class="tips"> 元</span>
        </el-form-item>
        <el-form-item label="本套餐优势" prop="hint">
          <el-input v-model="form.hint" type="text" placeholder="" size="small" style="width: 200px;" />
          <p style="margin: 0; color: #999;">不超过10个字，例如：最多人买、性价比最高</p>
        </el-form-item>
        <el-form-item label="权益描述" prop="desc">
          <el-input v-model="form.desc" :rows="5" type="textarea" placeholder="一行一条" size="small" style="width: 300px;" />
          <p style="margin: 0; color: #999;">一行一条</p>
        </el-form-item>
        <el-form-item label="备注" prop="title">
          <el-input v-model="form.title" :rows="3" type="textarea" placeholder="备注" size="small" style="width: 300px;" />
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
    gpt4: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      form: null,
      formRules: {
        price: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        num: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  created() {
    this.form = this.gpt4
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
