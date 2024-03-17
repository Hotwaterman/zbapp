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
      moduleName: 'AI自由对话',
      submenus: [
        {
          title: '对话记录',
          routeName: 'ModuleChatMsg',
          routeQuery: {}
        },
        {
          title: '参数配置',
          routeName: 'ModuleChatSetting',
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

