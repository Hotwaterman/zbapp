<template>
  <div>
    <el-scrollbar wrap-class="scrollbar-wrapper">
      <el-menu
        :default-active="activeMenu"
        :collapse="isCollapse"
        :background-color="variables.menuBg"
        :text-color="variables.menuText"
        :unique-opened="false"
        :active-text-color="variables.menuActiveText"
        :collapse-transition="false"
        mode="vertical"
      >
        <sidebar-item v-for="route in routes" :key="route.path" :item="route" :base-path="route.path" />
      </el-menu>
    </el-scrollbar>
    <hamburger :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import SidebarItem from './SidebarItem'
import variables from '@/styles/variables.scss'
import store from '@/store'
import Hamburger from '@/components/Hamburger'

export default {
  components: { SidebarItem, Hamburger },
  computed: {
    ...mapGetters([
      'sidebar'
    ]),
    routes() {
      return store.getters.routes
    },
    activeMenu() {
      const route = this.$route
      const { meta, path } = route
      // if set path, the sidebar will highlight the path you set
      if (meta.activeMenu) {
        return meta.activeMenu
      }
      return path
    },
    variables() {
      return variables
    },
    isCollapse() {
      return !this.sidebar.opened
    },
    host() {
      return window.location.host
    }
  },
  methods: {
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar')
    }
  }
}
</script>
<style lang="scss" scoped>
  .hamburger-container {
    width: 100%;
    height: 54px;
    line-height: 54px;
    cursor: pointer;
    transition: background .3s;
    -webkit-tap-highlight-color:transparent;
    position: absolute;
    left: 0;
    bottom: 0;
    /*background: rgba(0, 0, 0, .1);*/

    &:hover {
      background: rgba(0, 0, 0, .2)
    }
  }
</style>
