<template>
  <div class="app-container">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-plus" size="small" @click="showForm(0)">新建站点</el-button>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="large"
      row-key="id"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="id" label="站点ID" width="100" />
      <el-table-column prop="title" label="站点名称" />
      <el-table-column prop="phone" label="登录账号" width="120" />
      <el-table-column prop="last_time" label="最后登录时间" width="150" />
      <el-table-column prop="status" label="是否启用" width="100">
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.status"
            :active-value="1"
            :inactive-value="0"
            @change="setStatus(scope.row.id, $event)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="expire_time" label="到期时间" width="150" />
      <el-table-column prop="create_time" label="创建时间" width="150" />
      <el-table-column label="操作" width="300">
        <template slot-scope="scope">
          <el-button type="primary" icon="el-icon-office-building" size="small" @click.native.prevent="toSiteAdmin(scope.row.id)">进入管理</el-button>
          <el-button type="warning" icon="el-icon-edit" size="small" @click.native.prevent="showForm(scope.row.id)">编辑</el-button>
          <el-button type="default" icon="el-icon-delete" size="small" @click.native.prevent="doDel(scope.row.id)">删除</el-button>
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
    <edit v-if="site" :site="site" @close="closeForm" @submit="saveInfo" />
  </div>
</template>

<script>
import { getList, getInfo, saveInfo, del, setStatus, getLoginToken } from '@/api/site'
import edit from './edit'
export default {
  components: { edit },
  data() {
    return {
      type: 'qiche',
      dataList: [],
      pagesize: 10,
      page: 1,
      dataTotal: 0,
      site: null
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    getList() {
      getList({ type: this.type, page: this.page }).then(res => {
        this.dataList = res.data.list
        this.dataTotal = res.data.count
      })
    },
    pageChange(page) {
      this.page = page
      this.getList()
    },
    showForm(id = 0) {
      if (id) {
        getInfo({
          id: id
        }).then(res => {
          this.site = res.data
        })
      } else {
        this.site = {}
      }
    },
    closeForm() {
      this.site = null
    },
    saveInfo(site) {
      saveInfo(site).then(res => {
        this.getList()
        this.$message({
          message: res.message,
          type: 'success',
          duration: 1500
        })
        this.closeForm()
      })
    },
    doDel(id) {
      this.$confirm('删除后不可恢复，确认删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        del({
          id: id
        }).then(res => {
          this.getList()
          this.$message({
            message: res.message,
            type: 'success',
            duration: 1500
          })
        })
      })
    },
    setStatus(id, status) {
      setStatus({
        id: id,
        status: status
      }).then(res => {
        this.getList()
        this.$message({
          message: res.message,
          type: 'success',
          duration: 1500
        })
      })
    },
    toSiteAdmin(id) {
      getLoginToken({
        id: id
      }).then(res => {
        window.open('/admin/#/autologin?token=' + res.data.token, 'fox_chatgpt_newsite')
      })
    }
  }
}
</script>
<style scoped>
  .price {
    font-weight: bold;
  }
  .market-price {
    text-decoration: line-through;
    color: #999;
    margin-left: 5px;
  }
  .el-switch {
    transform: scale(0.80);
  }
</style>
