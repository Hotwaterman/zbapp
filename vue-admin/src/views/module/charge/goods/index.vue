<template>
  <div class="app-container">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-plus" size="mini" @click="showForm(0)">新增对话套餐</el-button>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      row-key="id"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="weight" label="权重" width="100" />
      <el-table-column prop="num" label="条数" width="100">
        <template slot-scope="scope">
          {{ scope.row.num }} 条
        </template>
      </el-table-column>
      <el-table-column prop="price" label="售价" width="200">
        <template slot-scope="scope">
          <span>{{ scope.row.price }} 元</span>
          <span v-if="scope.row.market_price" style="text-decoration: line-through; margin-left: 10px; color: #999;">{{ scope.row.market_price }}元</span>
        </template>
      </el-table-column>
      <el-table-column prop="sales" label="销量" width="100" />
      <el-table-column prop="status" label="是否上架" width="150">
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.status"
            :active-value="1"
            :inactive-value="0"
            @change="setStatus(scope.row.id, $event)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="is_default" label="是否默认" width="150">
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.is_default"
            :active-value="1"
            :inactive-value="0"
            @change="setDefault(scope.row.id, $event)"
          />
        </template>
      </el-table-column>
      <el-table-column label="操作" width="200">
        <template slot-scope="scope">
          <el-button type="primary" icon="el-icon-edit" size="mini" @click.native.prevent="showForm(scope.row.id)">编辑</el-button>
          <el-button type="danger" icon="el-icon-recycle" size="mini" @click.native.prevent="doDel(scope.row.id)">删除</el-button>
        </template>
      </el-table-column>
      <el-table-column prop="title" label="备注" />
    </el-table>
    <el-pagination
      :current-page="page"
      :page-size="pagesize"
      layout="total, prev, pager, next"
      :total="dataTotal"
      @current-change="pageChange"
    />
    <edit v-if="goods" :goods="goods" @close="closeForm" @submit="saveInfo" />
  </div>
</template>

<script>
import { getList, getInfo, saveInfo, del, setStatus, setDefault } from '@/api/goods'
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
      goods: null
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
          this.goods = res.data
        })
      } else {
        this.goods = {}
      }
    },
    closeForm() {
      this.goods = null
    },
    saveInfo(goods) {
      saveInfo(goods).then(res => {
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
    setDefault(id, is_default) {
      setDefault({
        id: id,
        is_default: is_default
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
