<template>
  <div>
    <el-drawer
      :title="'卡密管理，批次ID：' + batchId"
      :visible.sync="dataList.length > 0"
      direction="rtl"
      size="900px"
      :close-on-click-modal="false"
      :before-close="closeList"
    >
      <div style="padding: 0 20px;">
        <div class="dialog-toolbar" style="padding-bottom: 20px;">
          <el-button
            :loading="exportLoading"
            type="primary"
            icon="el-icon-download"
            size="mini"
            style="margin-right: 20px;"
            @click="doExport"
          >导出Excel</el-button>
          <el-checkbox v-model="downUnbind">只导出未使用</el-checkbox>
        </div>
        <el-table
          :data="dataList"
          stripe
          size="medium"
        >
          <el-table-column property="id" label="ID" width="60" />
          <el-table-column property="code" label="卡密" width="100" />
          <el-table-column prop="type" label="类型" width="100" align="center">
            <template slot-scope="scope">
              <span v-if="scope.row.type === 'times'">对话次数</span>
              <span v-if="scope.row.type === 'draw'">绘画次数</span>
              <span v-if="scope.row.type === 'gpt4'">GPT4字数</span>
              <span v-if="scope.row.type === 'vip'">vip时长</span>
            </template>
          </el-table-column>
          <el-table-column prop="val" label="面额" width="100" align="center">
            <template slot-scope="scope">
              {{ scope.row.val }}
              <span v-if="scope.row.type === 'times'">条</span>
              <span v-if="scope.row.type === 'draw'">张</span>
              <span v-if="scope.row.type === 'gpt4'">万字</span>
              <span v-if="scope.row.type === 'vip'">个月</span>
            </template>
          </el-table-column>
          <el-table-column property="bind_time" label="使用时间" width="160" />
          <el-table-column label="使用者" width="200">
            <template slot-scope="scope">
              <div v-if="scope.row.user_id" style="display:flex; align-items:center;">
                <img :src="scope.row.user.avatar" style="height: 24px; margin-right: 5px;">
                {{ scope.row.user.nickname || '无昵称' }} [ID:{{ scope.row.user_id }}]
              </div>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="130">
            <template slot-scope="scope">
              <el-button
                v-if="scope.row.user_id"
                type="text text-danger"
                size="mini"
                icon="el-icon-delete"
                @click.native.prevent="unbind(scope.row.id)"
              >设为未使用
              </el-button>
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
    </el-drawer>
  </div>
</template>

<script>
import { getBatchCard, unbind, getBatchExport } from '@/api/card'

export default {
  props: {
    batchId: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      dataList: null,
      dataTotal: 0,
      page: 1,
      pagesize: 100,
      downUnbind: false,
      exportLoading: false
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    closeList() {
      this.dataList = null
      setTimeout(() => {
        this.$emit('close')
      }, 200)
    },
    tableIndex(index) {
      return this.pagesize * (this.page - 1) + index + 1
    },
    getList() {
      getBatchCard({
        batch_id: this.batchId,
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
    unbind(id) {
      this.$confirm('确认设为未使用吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        unbind({ id: id }).then(res => {
          this.getList()
          this.$message({
            message: res.message,
            type: 'success',
            duration: 1500
          })
        })
      })
    },
    doExport() {
      this.exportLoading = true
      getBatchExport({
        batch_id: this.batchId,
        unbind: this.downUnbind ? 1 : 0
      }).then(res => {
        import('@/vendor/Export2Excel').then(excel => {
          const filename = '卡密[' + this.batchId + ']'
          const tHeader = ['ID', '生成时间', '卡密', '类型', '面额', '使用时间', '使用者ID']
          const filterVal = ['id', 'create_time', 'code', 'type', 'val', 'bind_time', 'user_id']
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
  .el-input {
    width: 200px;
  }

  .el-select {
    width: 200px;
  }

  .el-switch {
    transform: scale(0.80);
  }
</style>

