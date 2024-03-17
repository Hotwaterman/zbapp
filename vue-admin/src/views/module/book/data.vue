<template>
  <div class="app-container">
    <div class="toolbar">
      <div>
        <el-button-group>
          <el-button :type="search.book_id === 'all' ? 'primary' : 'default'" size="small" @click="changeBook('all')">全部内容</el-button>
          <el-button v-for="item in bookList" :type="search.book_id === item.id ? 'primary' : 'default'" size="small" @click="changeBook(item.id)">
            {{ item.title }}</el-button>
        </el-button-group>
      </div>
      <div>
        <el-button type="text" icon="el-icon-document" size="mini" @click="downloadTemplate">下载导入模板</el-button>
        <el-upload
          :before-upload="checkExcelFile"
          :on-success="importSuccess"
          :show-file-list="false"
          action="/admin.php/book/importData"
          class="btn-import"
        >
          <el-button type="default" icon="el-icon-upload2" size="mini">导入Excel</el-button>
        </el-upload>
        <el-button type="default" icon="el-icon-download" size="mini" @click="exportExcel" :loading="exportLoading">导出Excel</el-button>
        <el-button type="primary" icon="el-icon-plus" size="mini" @click="clickAdd">添加内容</el-button>
      </div>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="book_title" label="所属知识库" width="160" />
      <el-table-column prop="title" label="问题" width="400" />
      <el-table-column prop="state" label="启用" width="80">
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
        :title="formTitle"
        width="800px"
        :visible="true"
        :close-on-click-modal="false"
        :before-close="formClose"
      >
        <el-form ref="form" :model="form" :rules="formRules" label-width="120px">
          <el-form-item label="所属知识库" prop="book_id">
            <el-select v-model="form.book_id" placeholder="选择所属知识库" size="normal" style="width: 180px;">
              <el-option
                v-for="(item, index) in bookList"
                :key="index"
                :label="item.title"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item label="问题" prop="title">
            <el-input v-model="form.title" placeholder="问题" size="normal" style="width: 400px;" />
          </el-form-item>
          <el-form-item label="答案" prop="content">
            <el-input v-model="form.content" type="textarea" :rows="15" placeholder="答案内容" size="normal" style="width: 500px;" />
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
import { getBookList, getDataList, saveData, getData, delData, setDataState, exportData, startTrain } from '@/api/book'

export default {
  data() {
    return {
      form: null,
      formType: null,
      dataList: [],
      dataTotal: 0,
      bookList: [],
      page: 1,
      pagesize: 10,
      search: {
        book_id: 'all'
      },
      formRules: {
        book_id: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        title: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        content: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      },
      exportLoading: false
    }
  },
  computed: {
    formTitle() {
      return this.formType === 'add' ? '添加数据' : '编辑'
    }
  },
  mounted() {
    this.getBookList()
    this.getList()
  },
  methods: {
    getBookList() {
      getBookList().then(res => {
        this.bookList = res.data;
        if (!res.data || res.data.length === 0) {
          this.$message.error('请先添加知识库')
        }
      })
    },
    getList() {
      getDataList({
        page: this.page,
        pagesize: this.pagesize,
        book_id: this.search.book_id
      }).then(res => {
        this.dataList = res.data.list
        this.dataTotal = res.data.count
      })
    },
    pageChange(page) {
      this.page = page
      this.getList()
    },
    clickAdd() {
      this.formType = 'add'
      this.form = {}
    },
    clickEdit(id) {
      getData({ id: id }).then(res => {
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
          saveData(this.form).then(res => {
            this.getList()
            this.$message.success(res.message)
            this.formClose()
          })
        } else {
          console.log('请填写必填项')
        }
      })
    },
    doDel(id) {
      this.$confirm('删除后不可回复，确定删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delData({ id: id }).then(res => {
          this.getList()
          this.$message.success(res.message)
        })
      })
    },
    setState(id, state) {
      setDataState({
        id: id,
        state: state
      }).then(res => {
        this.getList()
        this.$message.success(res.message)
      })
    },
    changeBook(book_id) {
      this.search.book_id = book_id
      this.page = 1
      this.getList()
    },
    downloadTemplate() {
      window.location.href = '/static/bookData.xlsx'
    },
    checkExcelFile(field) {
      if (field.type !== 'application/vnd.ms-excel' && field.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        this.$message.error('请上传Excel文件')
        return false
      }
    },
    // 导入成功
    importSuccess(res, file, fileList) {
      this.getBookList()
      this.getList()
      this.$message.success(res.message)
      /*startTrain({
        minId: res.data.minId,
        maxId: res.data.maxId
      })*/
    },
    exportExcel() {
      this.exportLoading = true
      exportData(this.search).then(res => {
        import('@/vendor/Export2Excel').then(excel => {
          const filename = '知识库'
          const tHeader = ['知识库名称', '问题', '答案']
          const filterVal = ['book_title', 'title', 'content']
          const list = res.data
          const data = this.formatJson(filterVal, list)
          excel.export_json_to_excel({
            header: tHeader,
            data,
            filename: filename,
            autoWidth: true,
            bookType: 'xlsx'
          })
          this.exportLoading = false
        })
      }).catch(() => {
        this.exportLoading = false
      })
    },
    formatJson(filterVal, jsonData) {
      return jsonData.map(v => filterVal.map(j => {
        return v[j]
      }))
    }
  }
}
</script>
<style scoped>
  .toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .btn-import {
    display: inline-block;
    margin: 0 10px;
  }
</style>
