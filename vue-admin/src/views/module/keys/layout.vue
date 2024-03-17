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
      moduleName: 'Key池管理',
      submenus: [
        {
          title: '自定义接口',
          routeName: 'ModuleKeysOpenai',
          routeQuery: {}
        },
        {
          title: 'GPT4',
          routeName: 'ModuleKeysGpt4',
          routeQuery: {}
        },
        {
          title: 'Api2d',
          routeName: 'ModuleKeysApi2d',
          routeQuery: {}
        },
        {
          title: '百度文心',
          routeName: 'ModuleKeysBaidu',
          routeQuery: {}
        },
        // {
        //   title: '讯飞星火',
        //   routeName: 'ModuleKeysXunfei',
        //   routeQuery: {}
        // },
        {
          title: '灵犀AI',
          routeName: 'ModuleKeysLxai',
          routeQuery: {}
        },
        {
          title: '意间AI',
          routeName: 'ModuleKeysYijian',
          routeQuery: {}
        },
        // {
        //   title: 'Replicate',
        //   routeName: 'ModuleKeysReplicate',
        //   routeQuery: {}
        // }
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

