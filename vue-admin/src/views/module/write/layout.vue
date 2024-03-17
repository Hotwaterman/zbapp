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
      moduleName: 'AI文本创作',
      submenus: [
        {
          title: '创作记录',
          routeName: 'ModuleWriteMsg',
          routeQuery: {}
        },
        {
          title: '模型管理',
          routeName: 'ModuleWritePrompts',
          routeQuery: {}
        },
        {
          title: '类别管理',
          routeName: 'ModuleWriteTopic',
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

