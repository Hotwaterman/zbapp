<template>
  <div>
    <sidebar :menus="submenus" :module-name="moduleName" />
    <router-view name="subRouter" />
  </div>
</template>

<script>

import sidebar from './sidebar'

export default {
  components: { sidebar },
  data() {
    return {
      moduleName: '任务中心',
      submenus: [
        {
          title: '分享奖励',
          children: [
            {
              title: '分享记录',
              routeName: 'ModuleRewardShareLogs',
              routeQuery: {}
            },
            {
              title: '参数配置',
              routeName: 'ModuleRewardShareSetting',
              routeQuery: {}
            }
          ]
        },
        {
          title: '邀请奖励',
          children: [
            {
              title: '邀请记录',
              routeName: 'ModuleRewardInviteLogs',
              routeQuery: {}
            },
            {
              title: '参数配置',
              routeName: 'ModuleRewardInviteSetting',
              routeQuery: {}
            }
          ]
        },
        {
          title: '激励广告',
          children: [
            {
              title: '观看记录',
              routeName: 'ModuleRewardAdLogs',
              routeQuery: {}
            },
            {
              title: '参数配置',
              routeName: 'ModuleRewardAdSetting',
              routeQuery: {}
            }
          ]
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

