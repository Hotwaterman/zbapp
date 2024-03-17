<template>
  <div class="app-container">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-plus" size="mini" @click="clickAdd">添加关键词</el-button>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="weight" label="权重" width="100" />
      <el-table-column prop="keyword" label="关键词" width="400" />
      <el-table-column label="匹配方式" width="120">
        <template slot-scope="scope">
          <span v-if="scope.row.is_hit === 1">精准匹配</span>
          <span v-else>包含关键词</span>
        </template>
      </el-table-column>
      <el-table-column label="回复类型" width="120">
        <template slot-scope="scope">
          <span v-if="scope.row.type === 'text'">文本</span>
          <span v-if="scope.row.type === 'image'">图片</span>
        </template>
      </el-table-column>
      <el-table-column prop="state" label="启用" width="100">
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
          <el-form-item label="权重" prop="weight">
            <el-input v-model="form.weight" placeholder="越大越优先" size="normal" style="width: 100px;" />
          </el-form-item>
          <el-form-item label="关键词" prop="keyword">
            <el-input v-model="form.keyword" placeholder="关键词" size="normal" style="width: 400px;" />
          </el-form-item>
          <el-form-item label="匹配方式" prop="is_hit">
            <el-radio-group v-model="form['is_hit']">
              <el-radio :label="1" size="mini">精准匹配</el-radio>
              <el-radio :label="0" size="mini">包含关键词</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item label="回复内容">
            <replyEditor ref="replyEditor" :type="form.type" :content="form.content" :image="form.image" />
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
import { getKeywordList, getKeyword, saveKeyword, delKeyword, setKeywordState } from '@/api/wxmp'
import replyEditor from './replyEditor';

export default {
  components: { replyEditor },
  data() {
    return {
      form: null,
      formType: null,
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 20,
      formRules: {
        weight: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        keyword: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        is_hit: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ],
        content: [
          { required: true, message: '此项必填', trigger: 'blur' }
        ]
      }
    }
  },
  computed: {
    formTitle() {
      return this.formType === 'add' ? '添加关键词' : '编辑'
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    getList() {
      getKeywordList({
        page: this.page,
        pagesize: this.pagesize
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
        weight: 100,
        is_hit: 1
      }
    },
    clickEdit(id) {
      getKeyword({ id: id }).then(res => {
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
          var { type, content, image } = this.$refs['replyEditor'].getReply();
          this.form.type = type
          this.form.content = content
          this.form.image = image
          saveKeyword(this.form).then(res => {
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
        delKeyword({ id: id }).then(res => {
          this.getList()
          this.$message.success(res.message)
        })
      })
    },
    setState(id, state) {
      setKeywordState({
        id: id,
        state: state
      }).then(res => {
        this.getList()
        this.$message.success(res.message)
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
  .btn-import {
    display: inline-block;
    margin: 0 10px;
  }
</style>
