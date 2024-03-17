<template>
  <div class="app-container">
    <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <el-input
          v-model="keyword"
          placeholder="联系方式 / 关键词"
          class="input-with-select"
          size="small"
          clearable
          style="width: 320px;"
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
      <el-table-column prop="id" label="ID" width="60" />
      <el-table-column prop="user_id" label="用户ID" width="100" />
      <el-table-column prop="create_time" label="留言时间" width="150" />
      <el-table-column prop="avatar" label="头像" width="80" align="center">
        <template slot-scope="scope">
          <a :href="scope.row.avatar" target="_blank"><img :src="scope.row.avatar" style="height:36px;"></a>
        </template>
      </el-table-column>
      <el-table-column prop="nickname" label="昵称" width="160" />
      <el-table-column prop="phone" label="联系方式" width="120" />
      <el-table-column prop="type" label="留言类型" width="120" />
      <el-table-column label="留言内容">
        <template slot-scope="scope">
          {{ scope.row.content }}
        </template>
      </el-table-column>
      <el-table-column label="状态" width="100">
        <template slot-scope="scope">
          <el-button v-if="scope.row.state === 0" type="primary" size="mini" plain @click="setState(scope.row.id)" title="点击设为已处理">未处理</el-button>
          <el-button v-else :disabled="true" type="success" size="mini" plain>已处理</el-button>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="100">
        <template slot-scope="scope">
          <el-button type="text text-danger" size="mini" icon="el-icon-delete" @click.native.prevent="doDel(scope.row.id)">删除</el-button>
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
  </div>
</template>

<script>
import { getList, setState, del } from '@/api/feedback'

export default {
  data() {
    return {
      dataList: [],
      dataTotal: 0,
      keyword: '',
      page: 1,
      pagesize: 15
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      getList({
        page: this.page,
        pagesize: this.pagesize,
        keyword: this.keyword
      }).then(res => {
        const data = res.data
        this.dataList = data.list
        this.dataTotal = res.data.count
      })
    },
    pageChange(page) {
      this.page = page
      this.getList()
    },
    doSearch() {
      this.page = 1
      this.getList()
    },
    setState(id) {
      this.$confirm('确定设置为已处理吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        setState({ id: id }).then(res => {
          this.getList()
          this.$message({
            message: res.message,
            type: 'success',
            duration: 1500
          })
        })
      })
    },
    doDel(id) {
      this.$confirm('删除后不可恢复，确定删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        del({ id: id }).then(res => {
          this.getList()
          this.$message({
            message: res.message,
            type: 'success',
            duration: 1500
          })
        })
      })
    }
  }
}
</script>
<style scoped>
  .img {
      height: 50px;
      margin: 5px 5px 0 0;
      float: left
  }
</style>
