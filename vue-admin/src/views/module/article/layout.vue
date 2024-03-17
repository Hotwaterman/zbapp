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
      moduleName: '文档管理',
      submenus: [
        {
          title: '使用教程',
          routeName: 'ModuleArticleHelp',
          routeQuery: {}
        },
        {
          title: '通知公告',
          routeName: 'ModuleArticleNotice',
          routeQuery: {}
        },
        {
          title: '服务协议',
          routeName: 'ModuleArticleService',
          routeQuery: {}
        },
        {
          title: '隐私政策',
          routeName: 'ModuleArticlePrivacy',
          routeQuery: {}
        },
        {
          title: '免责声明',
          routeName: 'ModuleArticleLegal',
          routeQuery: {}
        },
        {
          title: '分销协议',
          routeName: 'ModuleArticleCommission',
          routeQuery: {}
        },
        {
          title: '关于我们',
          routeName: 'ModuleArticleAbout',
          routeQuery: {}
        },
        {
          title: '联系客服',
          routeName: 'ModuleArticleKefu',
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

