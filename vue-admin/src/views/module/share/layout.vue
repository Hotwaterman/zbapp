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
      moduleName: '分享配置',
      submenus: [
        {
          title: '分享文案',
          routeName: 'ModuleShareText',
          routeQuery: {}
        },
        {
          title: '分享海报',
          routeName: 'ModuleShareHaibao',
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

