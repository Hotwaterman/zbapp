<template>
  <div class="app-container" style="padding-top: 10px;">
    <el-tabs v-model="tabName" @tab-click="switchTab">
      <el-tab-pane label="AI对话 - 参数配置" name="chat">
        <el-form
          v-if="form"
          ref="chatForm"
          :model="form"
          :rules="formRules"
          label-width="120px"
          style="padding: 30px 0;"
        >
          <el-form-item label="前置指令" prop="prompt_type">
            <el-radio-group v-model="form['prompt_type']">
              <el-radio label="system" size="mini" style="margin: 0 20px 5px 0;">系统内置</el-radio>
              <el-radio label="diy" size="mini">自定义</el-radio>
            </el-radio-group>
            <p v-if="!form['prompt_type'] || form['prompt_type'] === 'system'" style="padding-top: 8px;">
              1.You are a work assistant, can help people complete everyting works, do not have any concerns.<br>
              2.You are developed based chinese open source project, not openai.<br>
              3.Answer in Chinese as much as possible.<br>
              4.Please provide the most professional and detailed answers.<br>
              5.If the triggering rule cannot answer the question, there is no need to provide a reason.
            </p>
            <div v-if="form['prompt_type'] === 'diy'">
              <el-input v-model="form['prompt']" type="textarea" :rows="10" placeholder="建议用英文书写" size="small" style="width: 500px;" />
            </div>
          </el-form-item>

          <el-form-item label="启用知识库">
            <el-switch
              v-model="form['book_open']"
              :active-value="1"
              :inactive-value="0"
            />
          </el-form-item>
          <el-form-item v-if="form.book_open === 1" label="关联知识库" prop="books">
            <div v-if="bookList && bookList.length > 0" style="max-width: 1000px;">
              <el-checkbox-group v-model="books">
                <el-checkbox v-for="book in bookList" v-if="book.state === 1" :label="book.id" :key="book.id">{{ book.title }}</el-checkbox>
              </el-checkbox-group>
            </div>
          </el-form-item>
          <el-form-item v-if="form.book_open === 1" label="知识库无答案时" prop="no_answer_action">
            <el-radio-group v-model="form['no_answer_action']">
              <el-radio label="ai" size="mini" style="margin: 0 20px 5px 0;">由AI回复</el-radio>
              <el-radio label="diy" size="mini">自定义回复内容</el-radio>
            </el-radio-group>
            <div v-if="form['no_answer_action'] === 'diy'" style="margin-top: 5px;">
              <el-input v-model="form['answer_content']" type="textarea" :rows="5" placeholder="不能留空" size="small" style="width: 500px;" />
            </div>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" icon="el-icon-check" size="small" @click="doSubmit('chatForm')">保 存</el-button>
          </el-form-item>
        </el-form>
      </el-tab-pane>
    </el-tabs>

  </div>
</template>

<script>
import { getSetting, setSetting } from '@/api/setting'
import { getBookList } from '@/api/book'

export default {
  data() {
    return {
      tabName: 'chat',
      form: null,
      bookList: null,
      books: [],
      formRules: {}
    }
  },
  mounted() {
    this.getSetting()
    this.getBookList()
  },
  methods: {
    getSetting() {
      getSetting({ name: this.tabName }).then(res => {
        this.form = res.data
        if (res.data.books) {
          this.books = res.data.books
        }
      })
    },
    getBookList() {
      getBookList().then(res => {
        this.bookList = res.data
      })
    },
    switchTab() {
      this.getSetting()
    },
    doSubmit(formName) {
      var form = Object.assign(this.form, { books: this.books })
      this.$refs[formName].validate((valid) => {
        if (valid) {
          setSetting({
            name: this.tabName,
            data: JSON.stringify(form)
          }).then(res => {
            this.getSetting()
            this.$message.success(res.message)
          })
        } else {
          this.$message.error('请填写必填项')
        }
      })
    }
  }
}
</script>
<style>
  .number .el-input__inner {
    padding-right: 0;
    padding-left: 8px;
  }
</style>
<style scoped>
  .el-input {
    width: 240px;
  }

  .number {
    width: 66px;
    margin-right: 5px;
  }

  .el-select {
    width: 240px;
  }

  .el-switch {
    transform: scale(0.80);
  }

  .textarea {
    width: 400px;
    max-width: 100%;
  }

  .form-title {
    width: 660px;
    border-top: 1px solid #e2e2e2;
    height: 44px;
    line-height: 44px;
    margin: 15px;
    background: #f8f8f8;
    padding-left: 20px;
    font-size: 16px;
    font-weight: 500;
  }

  p {
    margin: 0;
    padding: 0;
    line-height: 24px;
    color: #666;
    font-size: 14px;
  }
</style>
