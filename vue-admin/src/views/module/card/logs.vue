<template>
  <div class="app-container" style="padding: 0;">
    <div class="search">
      <p>
        <span class="label">卡密：</span>
        <el-input v-model="search.code" type="text" size="mini" clearable placeholder="按卡密" style="width: 160px;" />
      </p>
      <p>
        <span class="label">用户ID：</span>
        <el-input v-model="search.user_id" type="text" size="mini" clearable placeholder="按用户id" style="width: 160px;" />
      </p>
      <p style="padding-top: 10px;">
        <span class="label" />
        <el-button
          type="primary"
          icon="el-icon-check"
          size="mini"
          @click="doSearch"
        >筛选</el-button>
        <el-button
          type="default"
          plain
          size="mini"
          style="margin-left: 10px;"
          @click="doInit"
        >重置</el-button>
      </p>
    </div>
    <div class="box-panel" style="padding-bottom: 10px; margin-bottom: 0;">
      <el-table
        :data="dataList"
        stripe
        size="medium"
      >
        <el-table-column property="bind_time" label="使用时间" width="160" />
        <el-table-column label="使用者" width="240">
          <template slot-scope="scope">
            <div v-if="scope.row.user_id" style="display:flex; align-items:center;">
              <img :src="scope.row.user.avatar" style="height: 24px; margin-right: 5px;">
              {{ scope.row.user.nickname || '无昵称' }} [ID:{{ scope.row.user_id }}]
            </div>
          </template>
        </el-table-column>
        <el-table-column property="code" label="卡密" width="120" />
        <el-table-column prop="type" label="类型" width="120">
          <template slot-scope="scope">
            <span v-if="scope.row.type === 'times'">对话次数</span>
            <span v-if="scope.row.type === 'draw'">绘画次数</span>
            <span v-if="scope.row.type === 'gpt4'">GPT4字数</span>
            <span v-if="scope.row.type === 'vip'">vip时长</span>
          </template>
        </el-table-column>
        <el-table-column prop="val" label="面额">
          <template slot-scope="scope">
            {{ scope.row.val }}
            <span v-if="scope.row.type === 'times'">条</span>
            <span v-if="scope.row.type === 'draw'">张</span>
            <span v-if="scope.row.type === 'gpt4'">万字</span>
            <span v-if="scope.row.type === 'vip'">个月</span>
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
  </div>
</template>

<script>
import { getCardLogs } from '@/api/card'

export default {
  data() {
    return {
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 15,
      search: {}
    }
  },
  created() {
    this.doInit()
  },
  methods: {
    doInit() {
      this.dateType = ''
      this.search = {
        user_id: '',
        code: ''
      }
      this.doSearch()
    },
    getList() {
      const param = Object.assign(this.search, {
        page: this.page,
        pagesize: this.pagesize
      })
      getCardLogs(param).then(res => {
        this.dataList = res.data.list
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
    }
  }
}
</script>
<style lang="scss" scoped>
  .search {
    padding: 10px 0;
    background: #f8fafc;

    .label {
      font-weight: normal;
      font-size: 13px;
      color: #666;
      display: inline-block;
      width: 100px;
      text-align: right;
      padding-right: 5px;
    }

    .el-input {
      width: 180px;
    }

    p {
      margin: 10px 0;
    }
  }
</style>
