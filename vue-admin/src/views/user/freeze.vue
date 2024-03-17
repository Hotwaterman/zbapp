<template>
  <div class="app-container" style="padding: 0;">
    <div class="toolbar" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
      <el-button type="primary" icon="el-icon-plus" size="mini" @click="showForm(0)">新增禁言用户</el-button>
      <div>
        <el-input
          v-model="search.keyword"
          placeholder="用户ID"
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
    <div class="box-panel" style="padding-bottom: 10px; margin-bottom: 0;">
      <el-table
        :data="dataList"
        stripe
        size="medium"
        header-cell-class-name="bg-gray"
      >
        <el-table-column prop="freeze_time" label="禁言时间" width="160" />
        <el-table-column prop="id" label="用户ID" width="80" />
        <el-table-column prop="create_time" label="注册时间" width="160" />
        <el-table-column prop="nickname" label="头像昵称" width="200">
          <template slot-scope="scope">
            <div style="display:flex; align-items:center;">
              <img :src="scope.row.avatar" style="height: 24px; margin-right: 5px;">
              {{ scope.row.nickname }}
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="phone" label="手机号" width="180" />
        <el-table-column label="对话余额" width="110">
          <template slot-scope="scope">
            <span>{{ scope.row.balance }} 条</span>
          </template>
        </el-table-column>
        <el-table-column label="绘画余额" width="110">
          <template slot-scope="scope">
            <span>{{ scope.row.balance_draw }} 张</span>
          </template>
        </el-table-column>
        <el-table-column label="GPT4余额" width="110">
          <template slot-scope="scope">
            <span>{{ scope.row.balance_gpt4 }} 万字</span>
          </template>
        </el-table-column>
        <el-table-column label="会员" width="140">
          <template slot-scope="scope">
            <span>{{ scope.row.vip_expire_time || '未开通' }}</span>
          </template>
        </el-table-column>
        <el-table-column label="共消费" width="100">
          <template slot-scope="scope">
            {{ scope.row.order_total }} 元
          </template>
        </el-table-column>
        <el-table-column label="共提问" width="100">
          <template slot-scope="scope">
            {{ scope.row.msg_count }} 条
          </template>
        </el-table-column>
        <el-table-column prop="commission1" label="推荐人" width="200">
          <template slot-scope="scope">
            <div v-if="scope.row.tuser" style="display:flex; align-items:center;">
              <img :src="scope.row.tuser.avatar" style="height: 24px; margin-right: 5px;">
              {{ scope.row.tuser.nickname }} [ID: {{scope.row.tuid}}]
            </div>
          </template>
        </el-table-column>
        <el-table-column label="操作">
          <template slot-scope="scope">
            <el-button type="danger" icon="el-icon-recycle" size="mini" @click.native.prevent="doUnfreeze(scope.row.id)">解除禁言</el-button>
          </template>
        </el-table-column>
      </el-table>
      <div class="footer">
        <el-pagination
          :current-page="page"
          :page-size="pagesize"
          layout="total, prev, pager, next"
          :total="dataTotal"
          @current-change="pageChange"
        />
      </div>
    </div>
    <div v-if="form">
      <el-dialog
        custom-class="my-dialog"
        title="禁言用户"
        width="480px"
        :visible="true"
        :close-on-click-modal="false"
        :append-to-body="true"
        :before-close="closeForm"
      >
        <el-form ref="form" :model="form" label-width="120px">
          <el-form-item label="用户ID" prop="num">
            <el-input v-model="form.user_id" placeholder="填入用户ID（数字）" size="small" style="width: 150px;" />
          </el-form-item>
        </el-form>
        <span slot="footer" class="dialog-footer">
          <el-button type="default" icon="el-icon-close" size="small" @click="closeForm">取 消</el-button>
          <el-button type="primary" icon="el-icon-check" size="small" @click="doFreeze">禁言他</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import { getUserList, doFreeze, doUnfreeze } from '@/api/user'

export default {
  data() {
    return {
      dataList: [],
      dataTotal: 0,
      page: 1,
      pagesize: 15,
      dateType: '',
      search: {
        user_id: '',
      },
      form: null
    }
  },
  created() {
    this.doInit()
  },
  methods: {
    doInit() {
      this.search = {
        user_id: '',
      }
      this.doSearch()
    },
    getList() {
      const param = Object.assign(this.search, {
        is_freeze: 1,
        page: this.page,
        pagesize: this.pagesize
      })
      getUserList(param).then(res => {
        this.dataList = res.data.list
        this.dataTotal = res.data.count
      })
    },
    tableIndex(index) {
      return this.pagesize * (this.page - 1) + index + 1
    },
    pageChange(page) {
      this.page = page
      this.getList()
    },
    doSearch() {
      this.page = 1
      this.getList()
    },
    showForm() {
      this.form = {}
    },
    closeForm() {
      this.form = null
    },
    doFreeze() {
      doFreeze(this.form).then(res => {
        this.getList()
        this.$message({
          message: res.message,
          type: 'success',
          duration: 1500
        })
        this.closeForm()
      })
    },
    doUnfreeze(user_id) {
      this.$confirm('确定解除他的禁言吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        doUnfreeze({
          user_id: user_id
        }).then(res => {
          this.getList()
          this.$message({
            message: res.message,
            type: 'success',
            duration: 1500
          })
          this.closeForm()
        })
      })
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
  .footer {
    width: 100%;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;

    .tongji {
      height: 32px;
      line-height: 32px;
      font-size: 14px;
      color: #666;
      min-width: 240px;
    }

    .tongji span {
      margin: 0 4px;
      font-weight: bold;
      font-size: 16px;
    }

    .tongji span i {
      font-size: 18px;
      margin-right: 5px;
    }
  }
</style>
