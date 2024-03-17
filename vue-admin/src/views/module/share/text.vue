<template>
  <div class="app-container">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-plus" size="mini" @click="showForm(0)">添加文案</el-button>
    </div>
    <el-table
      :data="dataList"
      stripe
      size="medium"
      row-key="id"
      header-cell-class-name="bg-gray"
    >
      <el-table-column prop="weight" label="权重" width="100" />
      <el-table-column prop="content" label="文案内容">
        <template v-slot="scope">
          <div v-html="scope.row.content.replace('\n', '<br>')"></div>
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
      <el-table-column label="操作" width="200">
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
    <textEdit v-if="form" :values="form" @close="closeForm" @submit="saveInfo" />
  </div>
</template>

<script>
import { getTextList, getTextInfo, saveTextInfo, delText, setTextState, setTextDefault } from '@/api/share'
import textEdit from './textEdit'
export default {
  components: { textEdit },
  data() {
    return {
      dataList: [],
      pagesize: 10,
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
      getTextList({ page: this.page }).then(res => {
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
        getTextInfo({
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
      saveTextInfo(form).then(res => {
        this.getList()
        this.$message.success(res.message)
        this.closeForm()
      })
    },
    doDel(id) {
      this.$confirm('确认删除吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delText({
          id: id
        }).then(res => {
          this.getList()
          this.$message.success(res.message)
        })
      })
    },
    setState(id, state) {
      setTextState({
        id: id,
        state: state
      }).then(res => {
        this.getList()
        this.$message.success(res.message)
      })
    },
    setDefault(id, is_default) {
      setTextDefault({
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
  .el-switch {
    transform: scale(0.80);
  }
</style>
