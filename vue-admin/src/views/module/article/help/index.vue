<template>
  <div class="app-container">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-plus" size="mini" @click="showEdit(0)">新建文章</el-button>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      row-key="id"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="weight" label="权重" width="100" />
      <el-table-column prop="title" label="标题" />
      <el-table-column prop="views" label="点击量" width="100" />
      <el-table-column label="操作" width="200">
        <template slot-scope="scope">
          <el-button type="primary" icon="el-icon-edit" size="mini" @click.native.prevent="showEdit(scope.row.id)">编辑</el-button>
          <el-button type="danger" icon="el-icon-recycle" size="mini" @click.native.prevent="doDel(scope.row.id)">删除</el-button>
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
    <edit v-if="article" :article="article" @close="closeEdit" @submit="saveArticle" />
  </div>
</template>

<script>
import { getHelpList, getHelpArticle, saveHelpArticle, delHelpArticle } from '@/api/article'
import edit from './edit'
export default {
  components: { edit },
  data() {
    return {
      dataList: [],
      pagesize: 10,
      page: 1,
      dataTotal: 0,
      article: null
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    getList() {
      getHelpList({ page: this.page }).then(res => {
        this.dataList = res.data.list
        this.dataTotal = res.data.count
      })
    },
    pageChange(page) {
      this.page = page
      this.getList()
    },
    showEdit(id = 0) {
      if (id) {
        getHelpArticle({
          id: id
        }).then(res => {
          this.article = res.data
        })
      } else {
        this.article = {}
      }
    },
    closeEdit() {
      this.article = null
    },
    saveArticle(article) {
      saveHelpArticle(article).then(res => {
        this.getList()
        this.$message.success(res.message)
        this.closeEdit()
      })
    },
    doDel(id) {
      this.$confirm('删除后不可恢复，确认删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delHelpArticle({
          id: id
        }).then(res => {
          this.getList()
          this.$message.success(res.message)
        })
      })
    }
  }
}
</script>
