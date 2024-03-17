<template>
  <div class="submenu" :class="{'collapsed': isCollapse}" :style="'width:' + width + 'px;'">
    <div class="module-name">{{ moduleName }}</div>
    <ul class="list-group">
      <li v-for="(item, index) in menus" :key="index" class="list-item" :class="{active: routeName === item.routeName}" @click="linkto(item.routeName, item.routeQuery)">
        {{ item.title }}</li>
    </ul>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  props: {
    menus: {
      type: Array,
      default: null
    },
    moduleName: {
      type: String,
      default: ''
    },
    width: {
      type: Number,
      default: 110
    }
  },
  computed: {
    ...mapGetters([
      'sidebar'
    ]),
    isCollapse() {
      return !this.sidebar.opened
    },
    routeName() {
      return this.$route.name
    }
  },
  methods: {
    linkto(name, query = {}) {
      this.$router.replace({
        name: name,
        query: query
      })
    }
  }
}
</script>
<style lang="scss" scoped>
  .submenu {
    width: 110px;
    background-color: #fff;
    box-shadow: 0 0 1px 0 rgba(153, 153, 153, 0.2);
    position: fixed;
    left: 150px;
    top: 50px;
    bottom: 0;
    z-index: 8;
    overflow-y: auto;
    transition: left 0.28s;

    &.collapsed {
      left: 54px;
    }

    .module-name {
      height: 56px;
      line-height: 56px;
      color: #333;
      font-size: 14px;
      padding: 0 10px 0 20px;
      border: 1px solid #fff;
      border-bottom: 1px solid #e8e8e8;
    }

    .list-group {
      padding: 20px 10px;
      margin: 0;
      .list-item {
        height: 30px;
        line-height: 30px;
        margin: 0 0 9px 0;
        padding: 0 10px!important;
        display: block;
        color: #444;
        font-size: 13px;
        cursor: pointer;

        &.active {
          color: #409EFF;
          background-color: #f3f6f9;
          &:hover {
            background-color: #f3f6f9;
          }
        }
        &:hover {
          background-color: #fff;
          color: #409EFF;
        }
      }
    }
  }
</style>
