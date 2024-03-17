<template>
  <div>
    <sidebar :menus="submenus" :module-name="moduleName" />
    <router-view name="subRouter" />
  </div>
</template>

<script>

import sidebar from '../sidebar'

export default {
  components: { sidebar },
  data() {
    return {
      moduleName: 'AI绘画',
      submenus: [
        {
          title: '生成记录',
          routeName: 'ModuleDrawMsg',
          routeQuery: {}
        },
        {
          title: '参数配置',
          routeName: 'ModuleDrawSetting',
          routeQuery: {}
        }
      ]
    }
  },
  methods: {

  }
}
</script>
<style scoped>
  .app-container {
    margin-left: 130px;
  }
</style>

