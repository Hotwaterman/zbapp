<template slot-scope="scope">
  <div class="fox-editor" :style="'width:' + width + 'px;'">
    <div class="editor-tools">
      <div style="display:flex; align-items:center; line-height: 14px;">
        <a class="item" title="插入一段文字" @click="addText">+ 文字</a>
        <el-upload
          action=""
          :before-upload="uploadCheck"
          :http-request="uploadImage"
          :show-file-list="false"
          :multiple="false"
          style="display: inline;"
        >
          <a class="item" title="插入一张图片">+ 图片</a>
        </el-upload>
      </div>
      <div v-if="activeIndex !== null" style="display:flex; align-items:center; line-height: 14px;">
        <a class="item" v-if="form[activeIndex].type === 'text'" :class="{'active': form[activeIndex].style.fontWeight === 'bold'}" title="文字加粗" @click="setTextStyle('fontWeight')"><svg-icon icon-class="ic_text_bold" /></a>
        <a class="item" v-if="form[activeIndex].type === 'text'" :class="{'active': form[activeIndex].style.textAlign === 'center'}" title="文字居中" @click="setTextStyle('textAlign')"><svg-icon icon-class="ic_text_center" /></a>
        <el-color-picker v-if="form[activeIndex].type === 'text'" class="item" size="mini" :value="form[activeIndex].style.color || '#333333'" style="padding: 0;" title="文字颜色" @change="setTextColor" />
        <el-select v-if="form[activeIndex].type === 'text'" :value="form[activeIndex].style.fontSize || 14" placeholder="字号" size="mini" style="width: 68px; margin-right: 5px;" title="文字大小" @change="setTextFontSize">
          <el-option
            v-for="size in fontSizeArr"
            :key="size"
            :label="size + 'px'"
            :value="size"
          />
        </el-select>
        <a class="item" title="删除选中" @click="removeItem"><i class="el-icon-delete" /></a>
        <a class="item" title="上移" @click="moveTop">↑</a>
        <a class="item" title="下移" style="margin-right: 0;" @click="moveBottom">↓</a>
      </div>
    </div>
    <div class="editor-content" :style="'height:' + height + 'px;'">
      <div v-for="(item, index) in form" :key="index" class="editor-block" :class="{'active': activeIndex === index}" @click="itemSelect(index)">
        <div v-if="item.type === 'text'" class="block-text">
          <textarea :id="'textarea' + index" v-model="form[index].content" :style="formatStyle(item.style)" @input="setTextHeight(index)" />
          <div style="clear: both;" />
        </div>
        <div v-if="item.type === 'image'" class="block-image">
          <img :src="item.content">
          <div style="clear: both;" />
        </div>
      </div>
    </div>
  </div>

</template>
<script>
import { uploadImage } from '@/api/upload'
export default {
  name: 'FoxEditor',
  props: {
    content: {
      type: String,
      default: ''
    },
    width: {
      type: Number,
      default: 500
    },
    height: {
      type: Number,
      default: 300
    }
  },
  data() {
    return {
      form: [],
      activeIndex: null,
      fontSizeArr: [12, 13, 14, 15, 16, 18, 20, 24, 28, 32]
    }
  },
  watch: {
    content: {
      handler: function(val) {
        if (this.content) {
          this.form = JSON.parse(this.content)
        } else {
          this.form = []
        }
        this.$nextTick(() => {
          this.setAllTextHeiht()
        })
      },
      immediate: true,
      deep: true
    }
  },
  created() {
    /*if (this.content) {
      this.form = JSON.parse(this.content)
    } else {
      this.form = []
    }*/
    // this.$nextTick(() => {
    //   this.setAllTextHeiht()
    // })
  },
  methods: {
    formatStyle(style) {
      var str = ''
      if (style) {
        if (style.color) {
          str += 'color:' + style.color + ';'
        }
        if (style.fontWeight) {
          str += 'font-weight:' + style.fontWeight + ';'
        }
        if (style.fontSize) {
          str += 'font-size:' + style.fontSize + 'px;'
        }
        if (style.textAlign) {
          str += 'textAlign:' + style.textAlign + ';'
        }
      }
      return str
    },
    // 添加文本
    addText() {
      var index = this.activeIndex ? this.activeIndex : 0
      var form = this.form
      form.splice(index + 1, 0, {
        type: 'text',
        content: '',
        style: {}
      })
      this.form = form
      index = this.activeIndex === null ? 0 : (index + 1)
      this.itemSelect(index)
      this.$forceUpdate()
    },
    // 检验图片类型
    uploadCheck(field) {
      if (field.name.substr(-3) !== 'jpg' && field.name.substr(-3) !== 'png' && field.name.substr(-3) !== 'gif') {
        this.$message({
          showClose: true,
          message: '请上传后缀为.png / .jpg / .gif的图片',
          type: 'warning'
        })
        return false
      }
    },
    // 上传图片
    uploadImage(item) {
      const index = this.activeIndex ? this.activeIndex : 0
      var form = new FormData()
      form.append('file', item.file)
      uploadImage(form).then(res => {
        this.form.splice(index + 1, 0, {
          type: 'image',
          content: res.data.path
        })
        this.itemSelect(this.activeIndex === null ? 0 : (index + 1))
        this.$forceUpdate()
      })
    },
    // 删除
    removeItem() {
      const index = this.activeIndex ? this.activeIndex : 0
      this.$confirm('确认删除选中的内容吗?', '提示', {
        confirmButtonText: '确定删除',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        var form = this.form
        form.splice(index, 1)
        this.form = form
        this.activeIndex = null
        this.$forceUpdate()
      })
    },
    // 上移
    moveTop() {
      if (this.activeIndex === null || this.activeIndex === 0) {
        return
      }
      const index = this.activeIndex
      var form = this.form
      const temp = form[index - 1]
      form[index - 1] = form[index]
      form[index] = temp
      this.form = form
      this.activeIndex = index - 1
      this.$forceUpdate()
      this.$nextTick(() => {
        this.setAllTextHeiht()
      })
    },
    // 下移
    moveBottom() {
      var form = this.form
      if (this.activeIndex === null || this.activeIndex === form.length - 1) {
        return
      }
      const index = this.activeIndex
      const temp = form[index + 1]
      form[index + 1] = form[index]
      form[index] = temp
      this.form = form
      this.activeIndex = index + 1
      this.$forceUpdate()
      this.$nextTick(() => {
        this.setAllTextHeiht()
      })
    },
    // 选中某行
    itemSelect(index) {
      this.activeIndex = index
    },
    // 设置文本格式
    setTextStyle(type, value) {
      const index = this.activeIndex
      if (index === null) {
        return
      }
      var style = this.form[index].style
      if (type === 'fontWeight') {
        style.fontWeight = (style.fontWeight === 'bold' ? 'normal' : 'bold')
      }
      if (type === 'textAlign') {
        style.textAlign = (style.textAlign === 'center' ? 'left' : 'center')
      }
      if (type === 'color') {
        style.color = value
      }
      if (type === 'fontSize') {
        style.fontSize = value
        setTimeout(() => {
          this.setTextHeight(index)
        }, 10)
      }
      this.form[index].style = style
      this.$forceUpdate()
    },
    setTextColor(color) {
      this.setTextStyle('color', color)
    },
    setTextFontSize(fontSize) {
      this.setTextStyle('fontSize', fontSize)
    },
    setAllTextHeiht() {
      this.form.forEach((item, index) => {
        if (item.type === 'text') {
          this.setTextHeight(index)
        }
      })
    },
    setTextHeight(index) {
      const dom = document.getElementById('textarea' + index)
      dom.style.height = 0
      dom.style.height = dom.scrollHeight + 'px'
    },
    getContent() {
      if (this.form.length > 0) {
        return JSON.stringify(this.form)
      } else {
        return ''
      }
    }
  }
}
</script>
<style lang="scss">
  .fox-editor {
    .el-color-picker__trigger {
      border: none;
    }
    .el-color-picker__color {
      top: -1px;
      border-color: #fff;
      overflow: hidden;
      border-radius: 4px;
    }
    .el-input__inner {
      padding: 0 10px;
    }
  }
</style>
<style lang="scss" scoped>
  .fox-editor {
    border-radius: 5px;
    overflow: hidden;
    border: 1px solid #DCDFE6;
  }
  .editor-tools {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 46px;
    padding: 0 10px;
    background: #f3f6f9;
    border-bottom: 1px solid #DCDFE6;
    .item {
      display: inline-block;
      height: 28px;
      padding: 6px 10px;
      border: 1px solid #DCDFE6;
      border-radius: 4px;
      margin-right: 5px;
      font-size: 13px;
      background-color: #fff;
      &:hover {
        color: #409EFF;
      }
      &.active {
        color: #409EFF !important;
      }
    }
    .el-input__inner {
      padding: 0 10px;
    }
  }
  .editor-content {
    width: 100%;
    box-sizing: border-box;
    padding: 10px;
    overflow: hidden;
    overflow-y: auto;
  }
  .editor-block {
    border: 2px solid #fff;
    position: relative;
    background: #ffffff;
    border-radius: 5px;
    margin-bottom: 5px;
    .block-text {
      textarea {
        width: 100%;
        padding: 10px;
        line-height: 1.5;
        height: 41px;
        border: none;
        background: none;
        outline: none;
        resize: none;
        float: left;
        overflow: hidden;
      }
    }
    .block-image {
      text-align: center;
      img {
        max-width: 100%;
        height: auto;
        margin: 0 auto;
        float: left;
      }
    }
    &:hover {
      border: 2px dashed #409EFF;
    }
    &.active {
      border: 2px solid #409EFF !important;
    }
  }
</style>
