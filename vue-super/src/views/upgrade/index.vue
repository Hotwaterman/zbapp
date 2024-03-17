<template>
  <div class="app-container" style="padding-bottom: 20px;">
    <div class="toolbar">
      <el-button type="primary" icon="el-icon-refresh" size="mini" @click="getList">重新检查</el-button>
      <span v-if="version" style="margin-left: 20px; font-size: 14px;">当前版本：{{ version }}</span>
    </div>
    <el-tabs v-model="tabname" class="my-tabs" @tab-click="switchTab">
      <el-tab-pane label="系统升级" name="upgrade">
        <el-table
          v-if="dataList && dataList.length > 0"
          :data="dataList"
          stripe
          size="medium"
          header-cell-class-name="bg-gray"
        >
          <el-table-column label="序号" type="index" :idnex="tableIndex" width="80" align="center" />
          <el-table-column prop="add_time" label="发布时间" width="150" />
          <el-table-column prop="version" label="版本号" width="130" />
          <el-table-column prop="content" label="更新内容">
            <template slot-scope="scope">
              <div v-html="scope.row.content" />
            </template>
          </el-table-column>
          <el-table-column label="操作" width="200">
            <template slot-scope="scope">
              <el-button :disabled="scope.row.index > 1" type="text" size="mini" icon="el-icon-s-promotion" @click.native.prevent="doUpgrade(scope.row.version)">升级
              </el-button>
            </template>
          </el-table-column>
        </el-table>
        <div v-else style="width: 100%; background-color: #f8f8f8; text-align: center; padding: 100px 0;">
          <span class="el-icon-circle-check" style="font-size: 48px; color:#06b306;" />
          <p style="color:#666;font-size: 16px;">已是最新版</p>
        </div>
      </el-tab-pane>
      <el-tab-pane label="升级记录" name="history">
        <el-table
          v-if="historyList && historyList.length > 0"
          :data="historyList"
          stripe
          size="medium"
          header-cell-class-name="bg-gray"
          empty-text="无内容"
        >
          <el-table-column prop="add_time" label="发布时间" width="150" />
          <el-table-column prop="version" label="版本号" width="130" />
          <el-table-column prop="content" label="更新内容">
            <template slot-scope="scope">
              <div v-html="scope.row.content" />
            </template>
          </el-table-column>
        </el-table>
        <el-pagination
          :current-page="historyPage"
          :page-size="historyPagesize"
          layout="total, prev, pager, next"
          :total="historyTotal"
          @current-change="historyPageChange"
        />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import { getList, doUpgrade, getHistory } from '@/api/upgrade'

export default {
  data() {
    return {
      tabname: 'upgrade',
      version: '',
      dataList: [],
      historyList: [],
      historyTotal: 0,
      historyPage: 0,
      historyPagesize: 10
    }
  },
  mounted() {
    this.getList()
  },
  methods: {
    getList() {
      this.tabname = 'upgrade'
      getList().then(res => {
        this.version = res.data.version
        this.dataList = res.data.list
      })
    },
    tableIndex(index) {
      return index + 1
    },
    doUpgrade(version) {
      doUpgrade({ type: this.tabname, version: version }).then(res => {
        this.getList()
        this.$message({
          message: res.message,
          type: 'success',
          duration: 1500
        })
      })
    },
    getHistory() {
      getHistory({
        page: this.historyPage,
        pagesize: this.historyPagesize
      }).then(res => {
        this.historyList = res.data.list
        this.historyTotal = res.data.total
      })
    },
    switchTab() {
      if (this.tabname === 'history') {
        this.historyPage = 1
        this.historyList = []
        this.historyTotal = 0
        this.getHistory()
      } else {
        this.dataList = []
        this.getList()
      }
    },
    historyPageChange(page) {
      this.historyPage = page
      this.getHistory()
    }
  }
}
</script>
