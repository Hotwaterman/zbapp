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
      moduleName: '微信公众号',
      submenus: [
        {
          title: '参数配置',
          routeName: 'ModuleWxmpSetting',
          routeQuery: {}
        },
        {
          title: '自动回复',
          routeName: 'ModuleWxmpReply',
          routeQuery: {}
        },
        {
          title: '关键词回复',
          routeName: 'ModuleWxmpKeyword',
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

