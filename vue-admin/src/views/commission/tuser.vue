<template>
  <div>
    <el-dialog
      custom-class="my-dialog"
      title="查看推广的用户"
      width="1000px"
      :visible="true"
      :close-on-click-modal="true"
      :before-close="handleClose"
    >
      <el-table
        :data="dataList"
        stripe
        size="medium"
        header-cell-class-name="bg-gray"
      >
        <el-table-column prop="create_time" label="注册时间" width="180" />
        <el-table-column prop="id" label="用户ID" width="80" />
        <el-table-column label="头像昵称" width="160">
          <template slot-scope="scope">
            <div style="display:flex; align-items:center;">
              <img :src="scope.row.avatar" style="height: 24px; margin-right: 5px;">
              {{ scope.row.nickname }}
            </div>
          </template>
        </el-table-column>
        <el-table-column label="是否分销商" width="100">
          <template slot-scope="scope">
            <span v-if="scope.row.commission_level > 0">分销商</span>
          </template>
        </el-table-column>
        <el-table-column prop="realname" label="姓名" width="120" />
        <el-table-column prop="phone" label="手机号" />
      </el-table>

      <el-pagination
        :current-page="page"
        :page-size="pagesize"
        layout="total, prev, pager, next"
        :total="dataTotal"
        @current-change="pageChange"
      />
    </el-dialog></div>
</template>

<script>
import { getTuserList } from '@/api/commission'

export default {
  props: {
    pid: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      dataTotal: 0,
      page: 1,
      pagesize: 10,
      dataList: []
    }
  },
  created() {
    this.getTuserList()
  },
  methods: {
    handleClose() {
      this.dataList = []
      this.$emit('close')
    },
    getTuserList() {
      getTuserList({
        page: this.page,
        pagesize: this.pagesize,
        pid: this.pid
      }).then(res => {
        const data = res.data
        this.dataList = data.list
        this.dataTotal = res.data.count
      })
    },
    pageChange(page) {
      this.page = page
      this.getTuserList()
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

