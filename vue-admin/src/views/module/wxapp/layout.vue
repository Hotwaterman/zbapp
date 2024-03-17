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
      moduleName: '微信小程序',
      submenus: [
        {
          title: '参数配置',
          routeName: 'ModuleWxappSetting',
          routeQuery: {}
        },
        {
          title: '上传代码',
          routeName: 'ModuleWxappUpload',
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

