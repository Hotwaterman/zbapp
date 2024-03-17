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
      moduleName: '私有知识库',
      submenus: [
        {
          title: '内容管理',
          routeName: 'ModuleBookData',
          routeQuery: {}
        },
        {
          title: '知识库',
          routeName: 'ModuleBookBook',
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

