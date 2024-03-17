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
      moduleName: '充值套餐',
      submenus: [
        {
          title: '对话套餐',
          routeName: 'ModuleChargeGoods',
          routeQuery: {}
        },
        {
          title: 'GPT4套餐',
          routeName: 'ModuleChargeGpt4',
          routeQuery: {}
        },
        {
          title: '绘画套餐',
          routeName: 'ModuleChargeDraw',
          routeQuery: {}
        },
        {
          title: 'vip套餐',
          routeName: 'ModuleChargeVip',
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

