<template>
  <div class="app-container">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-plus" size="mini" @click="showForm(0)">添加海报</el-button>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      row-key="id"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="weight" label="权重" width="100" />
      <el-table-column prop="num" label="海报" width="300">
        <template v-slot="scope">
          <a :href="scope.row.bg" target="_blank">
            <img :src="scope.row.bg" style="height: 100px;" />
          </a>
        </template>
      </el-table-column>
      <el-table-column prop="state" label="是否启用" width="150">
        <template v-slot="scope">
          <el-switch
            v-model="scope.row.state"
            :active-value="1"
            :inactive-value="0"
            @change="setState(scope.row.id, $event)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="is_default" label="是否默认" width="150">
        <template v-slot="scope">
          <el-switch
            v-model="scope.row.is_default"
            :active-value="1"
            :inactive-value="0"
            @change="setDefault(scope.row.id, $event)"
          />
        </template>
      </el-table-column>
      <el-table-column prop="create_time" width="200" label="添加时间" />
      <el-table-column label="操作">
        <template v-slot="scope">
          <el-button type="primary" icon="el-icon-edit" size="mini" @click.native.prevent="showForm(scope.row.id)">编辑</el-button>
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
    <haibaoEdit v-if="form" :values="form" @close="closeForm" @submit="saveInfo" />
  </div>
</template>

<script>
import { getHaibaoList, getHaibaoInfo, saveHaibaoInfo, delHaibao, setHaibaoState, setHaibaoDefault } from '@/api/share'
import haibaoEdit from './haibaoEdit'
export default {
  components: { haibaoEdit },
  data() {
    return {
      dataList: [],
      pagesize: 5,
      page: 1,
      dataTotal: 0,
      form: null
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    getList() {
      getHaibaoList({ page: this.page }).then(res => {
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
        getHaibaoInfo({
          id: id
        }).then(res => {
          this.form = res.data
        })
      } else {
        this.form = {}
      }
    },
    closeForm() {
      this.form = null
    },
    saveInfo(form) {
      saveHaibaoInfo(form).then(res => {
        this.getList()
        this.$message.success(res.message)
        this.closeForm()
      })
    },
    doDel(id) {
      this.$confirm('删除不影响已生成的海报，确认删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delHaibao({
          id: id
        }).then(res => {
          this.getList()
          this.$message.success(res.message)
        })
      })
    },
    setState(id, state) {
      setHaibaoState({
        id: id,
        state: state
      }).then(res => {
        this.getList()
        this.$message.success(res.message)
      })
    },
    setDefault(id, is_default) {
      setHaibaoDefault({
        id: id,
        is_default: is_default
      }).then(res => {
        this.getList()
        this.$message.success(res.message)
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
