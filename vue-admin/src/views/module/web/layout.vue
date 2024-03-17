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
      moduleName: 'Web版',
      submenus: [
        {
          title: 'PC版',
          routeName: 'ModuleWebPC',
          routeQuery: {}
        },
        {
          title: 'H5版',
          routeName: 'ModuleWebH5',
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

