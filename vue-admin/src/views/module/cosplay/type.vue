<template>
  <div class="app-container" style="padding-bottom: 30px;">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-plus" size="mini" @click="clickAdd">添加类别</el-button>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="weight" label="权重" width="60" />
      <el-table-column prop="title" label="类别名称" width="300" />
      <el-table-column prop="state" label="启用" width="150">
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.state"
            :active-value="1"
            :inactive-value="0"
            @change="setState(scope.row.id, $event)"
          />
        </template>
      </el-table-column>
      <el-table-column label="操作">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="text" size="mini" icon="el-icon-edit" @click.native.prevent="clickEdit(scope.row.id)">编辑
            </el-button>
            <el-button type="text text-danger" size="mini" icon="el-icon-delete" @click.native.prevent="doDel(scope.row.id)">删除</el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <div v-if="form">
      <el-dialog
        custom-class="my-dialog"
        :title="formTitle"
        width="460px"
        :visible="true"
        :close-on-click-modal="false"
        :before-close="formClose"
      >
        <el-form ref="form" :model="form" :rules="formRules" label-width="120px">
          <el-form-item label="权重" prop="weight">
            <el-input v-model="form.weight" placeholder="越大越靠前" size="small" style="width: 110px;" />
            <span style="color: #666; margin-left: 10px;">越大越靠前</span>
          </el-form-item>
          <el-form-item label="类别名称" prop="title">
            <el-input v-model="form.title" placeholder="类别名称" size="small" style="width: 200px;" />
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="default" icon="el-icon-close" size="small" @click="formClose">取 消</el-button>
          <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">提 交</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { getTypeList, saveType, getType, delType, setTypeState } from '@/api/cosplay'

export default {
  data() {
    return {
      form: null,
      formType: null,
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 10,
      formRules: {
        title: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  computed: {
    formTitle() {
      return this.formType === 'add' ? '添加类别' : '编辑'
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    getList() {
      getTypeList().then(res => {
        this.dataList = res.data
      })
    },
    clickAdd() {
      this.formType = 'add'
      this.form = {
        weight: 100
      }
    },
    clickEdit(id) {
      getType({ id: id }).then(res => {
        this.formType = 'edit'
        this.form = res.data
      })
    },
    formClose() {
      this.form = null
      this.formType = ''
    },
    doSubmit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          saveType(this.form).then(res => {
            this.getList()
            this.$message({
              message: res.message,
              type: 'success',
              duration: 1500
            })
            this.formClose()
          })
        } else {
          console.log('请填写必填项')
        }
      })
    },
    doDel(id) {
      this.$confirm('确定删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delType({ id: id }).then(res => {
          this.getList()
          this.$message({
            message: res.message,
            type: 'success',
            duration: 1500
          })
        })
      })
    },
    setState(id, state) {
      setTypeState({
        id: id,
        state: state
      }).then(res => {
        this.getList()
        this.$message({
          message: res.message,
          type: 'success',
          duration: 1500
        })
      })
    }
  }
}
</script>
<style scoped>
  .el-input {
    width: 240px;
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
  .upload_status {
    color: #149314;
    font-weight: bold;
  }
</style>
