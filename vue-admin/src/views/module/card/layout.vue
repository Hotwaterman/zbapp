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
      moduleName: '卡密兑换',
      submenus: [
        {
          title: '卡密管理',
          routeName: 'ModuleCardBatch',
          routeQuery: {}
        },
        {
          title: '使用记录',
          routeName: 'ModuleCardLogs',
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

