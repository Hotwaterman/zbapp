<template>
  <div class="app-container">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-plus" size="mini" @click="clickCreate">添加Key</el-button>
      <div style="float:right;">
        <el-input
          v-model="search.keyword"
          placeholder="key / 备注"
          class="input-with-select"
          size="small"
          clearable
          style="width: 240px;"
        >
          <el-button
            slot="append"
            icon="el-icon-search"
            @click="doSearch()"
          />
        </el-input>
      </div>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="create_time" label="添加时间" width="160" />
      <el-table-column prop="apikey" label="apikey" width="260" />
      <el-table-column prop="apisecret" label="apisecret" width="260" />
      <el-table-column prop="remark" label="备注" width="220" />
      <el-table-column prop="state" label="状态" width="100">
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.state"
            :active-value="1"
            :inactive-value="0"
            @change="setKeyState(scope.row.id, $event)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="stop_reason" label="停用原因" width="220" />
      <el-table-column label="操作">
        <template slot-scope="scope">
          <el-button-group>
            <el-button
              type="text text-danger"
              size="mini"
              icon="el-icon-delete"
              @click.native.prevent="clickDel(scope.row.id)"
            >删除
            </el-button>
          </el-button-group>
        </template>
      </el-table-column>
    </el-table>
    <el-pagination
      :current-page="page"
      :page-size="pagesize"
      layout="total, prev, pager, next"
      :total="dataTotal"
      @current-change="pageChange"
    />

    <div v-if="form">
      <el-dialog
        custom-class="my-dialog"
        title="添加key"
        :visible="true"
        width="500px"
        :close-on-click-modal="false"
        :before-close="closeForm"
      >
        <el-form ref="form" :model="form" :rules="formRules" label-width="100px">
          <el-form-item label="ApiKey" prop="apikey">
            <el-input v-model="form.apikey" placeholder="填入意间的ApiKey" size="small" style="width: 300px;" />
          </el-form-item>
          <el-form-item label="ApiSecret" prop="apisecret">
            <el-input v-model="form.apisecret" placeholder="填入意间的ApiSecret" size="small" style="width: 300px;" />
          </el-form-item>
          <el-form-item label="备注" prop="remark">
            <el-input
              v-model="form.remark"
              type="textarea"
              placeholder="自定义备注"
              size="small"
              style="width: 300px; max-width: 100%;"
            />
            <p style="line-height: 26px; margin: 5px 0 0 0;">资费￥0.05/张，<a class="text-primary" href="https://console.ttk.ink/api.php/link/ai/name/yijian" target="_blank">直达链接</a></p>
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="default" icon="el-icon-close" size="small" @click="closeForm">取 消</el-button>
          <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">提 交</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { getKeyList, saveKey, delKey, setKeyState } from '@/api/keys'

export default {
  data() {
    return {
      type: 'yijian',
      form: null,
      search: {},
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 15,
      formRules: {
        apikey: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        apisecret: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  created() {
    this.getKeyList()
  },
  methods: {
    getKeyList() {
      getKeyList({
        page: this.page,
        pagesize: this.pagesize,
        keyword: this.search.keyword,
        type: this.type
      }).then(res => {
        this.dataList = res.data.list
        this.dataTotal = res.data.count
      })
    },
    pageChange(page) {
      this.page = page
      this.getKeyList()
    },
    clickCreate() {
      this.form = {}
    },
    // 关闭弹框
    closeForm() {
      this.form = null
    },
    doSubmit(formName) {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.form.type = this.type
          this.form.key = this.form.apikey + '|' + this.form.apisecret
          saveKey(this.form).then(res => {
            this.page = 1
            this.getKeyList()
            this.$message.success(res.message)
            this.closeForm()
          })
        }
      })
    },
    clickDel(id) {
      this.$confirm('删除后不可找回，确定删除吗？', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delKey({ id: id }).then(res => {
          if (res.errno) {
            this.$message({
              message: res.message,
              type: 'error'
            })
          } else {
            this.getKeyList()
            this.$message({
              message: res.message,
              type: 'success',
              duration: 1500
            })
          }
        })
      })
    },
    setKeyState(id, state) {
      setKeyState({
        id: id,
        state: state
      }).then(res => {
        this.getKeyList()
        this.$message({
          message: res.message,
          type: 'success',
          duration: 1500
        })
      })
    },
    doSearch() {
      this.page = 1
      this.getKeyList()
    }
  }
}
</script>
