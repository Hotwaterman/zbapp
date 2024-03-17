<template>
  <div class="app-container" style="padding-bottom: 20px;">
    <div class="toolbar">
      <div>
        <el-input
          v-model="search.keyword"
          placeholder="昵称 / 姓名 / 手机号"
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
      <el-table-column prop="id" label="用户ID" width="80" />
      <el-table-column prop="commission_time" label="成为分销商时间" width="180" />
      <el-table-column prop="nickname" label="微信" width="200">
        <template slot-scope="scope">
          <div style="display:flex; align-items:center;">
            <img :src="scope.row.avatar" style="height: 24px; margin-right: 5px;">
            {{ scope.row.nickname }}
          </div>
        </template>
      </el-table-column>
      <el-table-column prop="realname" label="姓名" width="100" />
      <el-table-column prop="phone" label="手机号" width="150" />
      <el-table-column prop="total_fee" label="可提现 / 已获得总佣金" width="200">
        <template slot-scope="scope">
          <p>{{ scope.row.commission_money }}元 / {{ scope.row.commission_total }}元</p>
        </template>
      </el-table-column>
      <el-table-column prop="nickname" label="他的上级" width="200">
        <template slot-scope="scope">
          <div v-if="scope.row.puser" style="display:flex; align-items:center;">
            <img :src="scope.row.puser.avatar" style="height: 24px; margin-right: 5px;">
            {{ scope.row.puser.nickname }} [ID: {{ scope.row.puser.id }}]
          </div>
        </template>
      </el-table-column>
      <el-table-column label="操作">
        <template slot-scope="scope">
          <el-button-group>
            <el-button type="text" size="mini" icon="el-icon-s-marketing" @click.native.prevent="sonList(scope.row.id)">下级分销商({{ scope.row.son_count }})</el-button>
            <el-button type="text" size="mini" icon="el-icon-s-marketing" @click.native.prevent="tuserList(scope.row.id)">推广的用户({{ scope.row.tuser_count }})</el-button>
            <el-button type="text" size="mini" icon="el-icon-s-order" @click.native.prevent="myOrderList(scope.row.id)">分销订单({{ scope.row.order_count }})</el-button>
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

    <son v-if="sonListShow" :pid="pid" @close="closeSonList" />
    <tuser v-if="tuserListShow" :pid="pid" @close="closeTuserList" />
    <myorder v-if="myOrderListShow" :pid="pid" @close="closeMyOrderList" />
  </div>

</template>

<script>
import { getList } from '@/api/commission'
import son from './son'
import tuser from './tuser'
import myorder from './myorder'
export default {
  components: { son, tuser, myorder },
  data() {
    return {
      search: {},
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 10,
      detailShow: false,
      pid: 0,
      tuserListShow: false,
      sonListShow: false,
      myOrderListShow: false
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
        keyword: this.search.keyword
      }).then(res => {
        const data = res.data
        this.dataList = data.list
        this.dataTotal = res.data.count
      })
    },
    // 显示下级分销商列表
    sonList(pid) {
      this.pid = pid
      this.sonListShow = true
    },
    // 关闭下级分销商列表
    closeSonList() {
      this.pid = 0
      this.sonListShow = false
    },
    // 显示推广客户列表
    tuserList(pid) {
      this.pid = pid
      this.tuserListShow = true
    },
    // 关闭推广客户列表
    closeTuserList() {
      this.pid = 0
      this.tuserListShow = false
    },
    // 显示推荐订单列表
    myOrderList(pid) {
      this.pid = pid
      this.myOrderListShow = true
    },
    // 关闭推荐订单列表
    closeMyOrderList() {
      this.pid = 0
      this.myOrderListShow = false
    },
    // 执行搜索
    doSearch() {
      this.getList()
      this.page = 1
    },
    pageChange(page) {
      this.page = page
      this.getList()
    }
  }
}
</script>
<style scoped>
  .el-input {
    width: 240px;
  }

  .el-select {
    width: 240px;
  }

  .el-switch {
    transform: scale(0.80);
  }

  .form-title {
    line-height: 40px;
    border-top: 1px solid #e6e6e6;
    padding-left: 20px;
    font-size: 18px;
    margin: 10px 0;
    background: #fafafa;
  }

  .upload_status {
    color: #149314;
    font-weight: bold;
  }

  .el-table p {
    margin: 0;
  }

  .el-table p.desc {
    font-size: 12px;
    color: #999;
  }

  .input-number {
    width: 80px;
    margin-right: 5px;
  }

  .avatar {
    background: #666;
  }
</style>
