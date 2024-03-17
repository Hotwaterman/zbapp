<template>
  <div>
    <script id="editor" type="text/plain" style="width:100%; min-width:500px; height:400px; max-width: 800px;" />
  </div>
</template>
<script>
export default {
  name: 'Ue',
  props: {
    value: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      config: {
        serverUrl: '/admin.php/upload/ueditor',
      },
      editor: null
    }
  },
  mounted() {
    const _this = this
    window.UE.delEditor('editor')
    this.editor = window.UE.getEditor('editor', this.config) // 初始化UE
    this.editor.addListener('ready', function() {
      _this.editor.setContent(_this.value) // 载完成后，放入内容
    })
  },

  methods: {
    setUEContent(content) {
      this.editor.setContent(content)
    },
    getUEContent() { // 获取编辑器内容方法
      return this.editor.getContent()
    },
    destroyed() { // 销毁编辑器
      this.editor.destroy()
    }
  }
}
</script>
