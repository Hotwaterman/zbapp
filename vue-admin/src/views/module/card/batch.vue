<template>
  <div class="app-container">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-plus" size="mini" @click="clickCreate">生成卡密</el-button>
      <div style="float:right;">
        <el-input
          v-model="search.keyword"
          placeholder="搜备注"
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
      <el-table-column prop="id" label="批次ID" width="80" />
      <el-table-column prop="create_time" label="生成时间" width="150" />
      <el-table-column prop="type" label="类型" width="100" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.type === 'times'">对话次数</span>
          <span v-if="scope.row.type === 'draw'">绘画次数</span>
          <span v-if="scope.row.type === 'gpt4'">GPT4字数</span>
          <span v-if="scope.row.type === 'vip'">vip时长</span>
        </template>
      </el-table-column>
      <el-table-column prop="val" label="每卡额度" width="100" align="center">
        <template slot-scope="scope">
          {{ scope.row.val }}
          <span v-if="scope.row.type === 'times'">条</span>
          <span v-if="scope.row.type === 'draw'">张</span>
          <span v-if="scope.row.type === 'gpt4'">万字</span>
          <span v-if="scope.row.type === 'vip'">个月</span>
        </template>
      </el-table-column>
      <el-table-column prop="num" label="总数量" width="100" align="center" />
      <el-table-column prop="used" label="已使用" width="120" align="center" />
      <el-table-column prop="remark" label="备注" width="220" />
      <el-table-column label="操作">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="text" size="mini" @click.native.prevent="showCardList(scope.row.id)"><i
              class="el-icon-view"
            /> 查看
            </el-button>
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
        title="生成卡密"
        :visible="true"
        width="500px"
        :close-on-click-modal="false"
        :before-close="closeForm"
      >
        <el-form ref="form" :model="form" :rules="formRules" label-width="120px">
          <el-form-item label="卡密类型" prop="type">
            <el-radio-group v-model="form['type']">
              <el-radio label="times" size="mini" style="margin: 0 20px 5px 0;">对话次数</el-radio>
              <el-radio label="draw" size="mini" style="margin: 0 20px 5px 0;">绘画次数</el-radio>
              <el-radio label="gpt4" size="mini" style="margin: 0 20px 5px 0;">GPT4字数</el-radio>
              <el-radio label="vip" size="mini">vip时长</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="每卡额度" prop="val">
            <el-input v-model="form.val" placeholder="填入整数" size="small" style="width: 110px;" />
            <span class="tips" v-if="form.type === 'times'"> 条</span>
            <span class="tips" v-if="form.type === 'draw'"> 张</span>
            <span class="tips" v-if="form.type === 'gpt4'"> 万字</span>
            <span class="tips" v-if="form.type === 'vip'"> 个月</span>
          </el-form-item>
          <el-form-item label="备注" prop="remark">
            <el-input
              v-model="form.remark"
              type="textarea"
              placeholder="备注必填"
              size="small"
              style="width: 300px; max-width: 100%;"
            />
          </el-form-item>
          <el-form-item label="生成卡密数" prop="num">
            <el-input v-model="form.num" placeholder="1 - 2000" size="small" style="width: 150px;" />
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="default" icon="el-icon-close" size="small" @click="closeForm">取 消</el-button>
          <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit">提 交</el-button>
        </span>
      </el-dialog>
    </div>

    <batch-list v-if="batch_id" :batch-id="batch_id" @close="closeCardList" />
  </div>
</template>

<script>
import { getBatchList, createBatch, batchDel } from '@/api/card'
import batchList from './batch_list'

export default {
  components: { batchList },
  data() {
    return {
      batch_id: 0,
      form: null,
      search: {},
      agentList: null,
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 15,
      formRules: {
        type: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        val: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        num: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        remark: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      },
      cardListShow: false
    }
  },
  created() {
    this.getBatchList()
  },
  methods: {
    getBatchList() {
      getBatchList({
        page: this.page,
        pagesize: this.pagesize,
        keyword: this.search.keyword
      }).then(res => {
        const data = res.data
        this.dataList = data.list
        this.dataTotal = res.data.count
      })
    },
    tableIndex(index) {
      return this.pagesize * (this.page - 1) + index + 1
    },
    pageChange(page) {
      this.page = page
      this.getBatchList()
    },
    clickCreate() {
      this.form = {}
    },
    // 关闭弹框
    closeForm() {
      this.form = null
    },
    showCardList(batch_id) {
      this.batch_id = batch_id
      this.cardListShow = true
    },
    closeCardList() {
      this.batch_id = 0
      this.cardListShow = false
    },
    doSubmit(formName) {
      this.$refs.form.validate((valid) => {
        if (valid) {
          createBatch(this.form).then(res => {
            this.page = 1
            this.getBatchList()
            this.$message({
              message: res.message,
              type: 'success',
              duration: 1500
            })
            this.closeForm()
          })
        }
      })
    },
    clickDel(id) {
      this.$confirm('此操作将删除此批次的所有卡密', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        batchDel({ id: id }).then(res => {
          if (res.errno) {
            this.$message({
              message: res.message,
              type: 'error'
            })
          } else {
            this.getBatchList()
            this.$message({
              message: res.message,
              type: 'success',
              duration: 1500
            })
          }
        })
      })
    },
    doSearch() {
      this.page = 1
      this.getBatchList()
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

  .form-title {
    line-height: 40px;
    border-top: 1px solid #e6e6e6;
    padding-left: 20px;
    font-size: 18px;
    margin: 10px 0;
    background: #fafafa;
  }

  .upload_status {
    color: #149314;
    font-weight: bold;
  }
</style>
