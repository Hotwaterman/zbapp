<template>
  <div>
    <sidebar :menus="submenus" :module-name="moduleName" />
    <router-view name="subRouter" />
  </div>
</template>

<script>

import sidebar from '@/views/module/sidebar'

export default {
  components: { sidebar },
  data() {
    return {
      moduleName: '登录设置',
      submenus: [
        {
          title: '登录方式',
          routeName: 'userLoginSetting',
          routeQuery: {}
        },
        {
          title: '短信配置',
          routeName: 'userLoginSms',
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

