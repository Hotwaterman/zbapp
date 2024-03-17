<template>
  <div class="app-container">
    <div class="toolbar">
      <div>
        <el-button-group>
          <el-button :type="search.type_id === 'all' ? 'primary' : 'default'" size="small" @click="changeType('all')">全部类别</el-button>
          <el-button v-for="item in typeList" :type="search.type_id === item.id ? 'primary' : 'default'" size="small" @click="changeType(item.id)">
            {{ item.title }}</el-button>
        </el-button-group>
      </div>
      <div>
        <el-button type="primary" icon="el-icon-plus" size="mini" @click="clickAdd">新建角色</el-button>
      </div>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="weight" label="权重" width="60" />
      <el-table-column prop="type_title" label="所属类别" width="140" />
      <el-table-column label="头像" width="100">
        <template slot-scope="scope">
          <img v-if="scope.row.thumb" :src="scope.row.thumb" height="48" />
          <img v-else src="/static/chat/role_default.png" height="48" />
        </template>
      </el-table-column>
      <el-table-column prop="title" label="角色名称" width="200" />
      <el-table-column prop="views" label="点击量" width="100" />
      <el-table-column prop="usages" label="使用量" width="100" />
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
        custom-class="my-dialog full-dialog"
        :title="formTitle"
        width="800px"
        :visible="true"
        :close-on-click-modal="false"
        :before-close="formClose"
      >
        <el-form ref="form" :model="form" :rules="formRules" label-width="120px">
          <div style="display: flex;">
            <div>
              <el-form-item label="权重" prop="weight">
                <el-input v-model="form.weight" placeholder="越大越靠前" size="normal" style="width: 110px;" />
                <span style="color: #666; margin-left: 10px;">越大越靠前</span>
              </el-form-item>
              <el-form-item label="所属类别" prop="title">
                <el-select v-model="form.type_id" placeholder="选择所属类别" size="normal" style="width: 180px;">
                  <el-option
                    v-for="(item, index) in typeList"
                    :key="index"
                    :label="item.title"
                    :value="item.id"
                  />
                </el-select>
              </el-form-item>
              <el-form-item label="角色名称" prop="title">
                <el-input v-model="form.title" placeholder="角色名称" size="normal" style="width: 180px;" />
              </el-form-item>
            </div>
            <el-form-item label="头像" prop="thumb">
              <el-input v-model="form['thumb']" placeholder="输入图片地址或上传图片" size="small" />
              <el-upload
                class="avatar-uploader"
                action=""
                :data="{type: 'thumb'}"
                :http-request="uploadImage"
                :show-file-list="false"
                :multiple="false"
                style="height: 80px"
              >
                <img v-if="form['thumb']" :src="form['thumb']" class="avatar" style="height: 80px; width: auto;">
                <i v-else class="el-icon-plus avatar-uploader-icon" style="width: 80px; height: 80px; line-height:80px;" />
              </el-upload>
              <span style="color:#999;">建议100x100像素，正方形图片</span>
            </el-form-item>
          </div>
          <el-form-item label="前置指令" prop="prompt">
            <el-input v-model="form.prompt" type="textarea" :rows="6" placeholder="前置指令" size="normal" style="width: 500px;" />
          </el-form-item>
          <el-form-item label="输入框提示" prop="hint">
            <el-input v-model="form.hint" type="textarea" :rows="3" placeholder="在输入框里提示用户的文字" size="normal" style="width: 500px;" />
          </el-form-item>
          <el-form-item label="欢迎语" prop="welcome">
            <el-input v-model="form.welcome" type="textarea" :rows="3" placeholder="初次进入对话页面时的欢迎语，可留空，默认使用提示文字" size="normal" style="width: 500px;" />
          </el-form-item>
          <div style="display: flex;">
            <el-form-item label="虚拟点击量" prop="fake_views">
              <el-input v-model="form.fake_views" placeholder="" size="normal" style="width: 110px;" />
            </el-form-item>
            <el-form-item label="虚拟使用量" prop="fake_usages">
              <el-input v-model="form.fake_usages" placeholder="" size="normal" style="width: 110px;" />
            </el-form-item>
          </div>
          <el-form-item label="启用知识库">
            <el-switch
              v-model="form['book_open']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
          <el-form-item v-if="form.book_open === 1" label="关联知识库" prop="books">
            <div v-if="bookList && bookList.length > 0" style="max-width: 1000px;">
              <el-checkbox-group v-model="books">
                <el-checkbox v-for="book in bookList" v-if="book.state === 1" :label="book.id" :key="book.id">{{ book.title }}</el-checkbox>
              </el-checkbox-group>
            </div>
          </el-form-item>
          <el-form-item v-if="form.book_open === 1" label="知识库无答案时" prop="no_answer_action">
            <el-radio-group v-model="form['no_answer_action']">
              <el-radio label="ai" size="mini" style="margin: 0 20px 5px 0;">由AI回复</el-radio>
              <el-radio label="diy" size="mini">自定义回复内容</el-radio>
            </el-radio-group>
            <div v-if="form['no_answer_action'] === 'diy'" style="margin-top: 5px;">
              <el-input v-model="form['answer_content']" type="textarea" :rows="5" placeholder="不能留空" size="small" style="width: 500px;" />
            </div>
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
import { getTypeList, getRoleList, saveRole, getRole, delRole, setRoleState } from '@/api/cosplay'
import { getBookList } from '@/api/book'
import { uploadImage } from '@/api/upload'

export default {
  data() {
    return {
      form: null,
      formType: null,
      dataList: [],
      dataTotal: 0,
      typeList: [],
      bookList: null,
      books: [],
      page: 1,
      pagesize: 10,
      search: {
        type_id: 'all'
      },
      formRules: {
        title: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        prompt: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        hint: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  computed: {
    formTitle() {
      return this.formType === 'add' ? '新建角色' : '编辑'
    }
  },
  mounted() {
    this.getTypeList()
    this.getBookList()
    this.getList()
  },
  methods: {
    getTypeList() {
      getTypeList().then(res => {
        this.typeList = res.data
      })
    },
    getBookList() {
      getBookList().then(res => {
        this.bookList = res.data
      })
    },
    getList() {
      getRoleList({
        page: this.page,
        pagesize: this.pagesize,
        type_id: this.search.type_id
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
      this.form = {
        weight: 100
      }
    },
    clickEdit(id) {
      getRole({ id: id }).then(res => {
        this.formType = 'edit'
        this.form = res.data
        if (res.data.books) {
          this.books = res.data.books
        }
      })
    },
    formClose() {
      this.form = null
      this.formType = ''
      this.books = []
    },
    doSubmit() {
      var form = Object.assign(this.form, { books: JSON.stringify(this.books) })
      this.$refs.form.validate((valid) => {
        if (valid) {
          saveRole(form).then(res => {
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
      this.$confirm('删除后不可回复，确定删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delRole({ id: id }).then(res => {
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
      setRoleState({
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
    },
    changeType(type_id) {
      this.search.type_id = type_id
      this.page = 1
      this.getList()
    },
    uploadImage(item) {
      var form = new FormData()
      form.append('file', item.file)
      if (item.data) {
        form.append('data', JSON.stringify(item.data))
      }
      uploadImage(form).then(res => {
        if (item.data.type === 'thumb') {
          this.$set(this.form, 'thumb', res.data.path)
        }
        this.$message.success('上传成功')
      })
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
</style>
