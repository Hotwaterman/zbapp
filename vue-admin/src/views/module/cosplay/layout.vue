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
      moduleName: 'AI身份模拟',
      submenus: [
        {
          title: '对话记录',
          routeName: 'ModuleCosplayMsg',
          routeQuery: {}
        },
        {
          title: '角色管理',
          routeName: 'ModuleCosplayRole',
          routeQuery: {}
        },
        {
          title: '类别管理',
          routeName: 'ModuleCosplayType',
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

