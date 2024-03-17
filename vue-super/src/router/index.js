import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

/**
 * Note: sub-menu only appear when route children.length >= 1
 * Detail see: https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
 *
 * hidden: true                   if set true, item will not show in the sidebar(default is false)
 * alwaysShow: true               if set true, will always show the root menu
 *                                if not set alwaysShow, when item has more than one children route,
 *                                it will becomes nested mode, otherwise not show the root menu
 * redirect: noRedirect           if set noRedirect will no redirect in the breadcrumb
 * name:'router-name'             the name is used by <keep-alive> (must set!!!)
 * meta : {
    roles: ['super','editor']    control the page roles (you can set multiple roles)
    title: 'title'               the name show in sidebar and breadcrumb (recommend set)
    icon: 'svg-name'/'el-icon-x' the icon show in the sidebar
    breadcrumb: false            if set false, the item will hidden in breadcrumb(default is true)
    activeMenu: '/example/list'  if set path, the sidebar will highlight the path you set
  }
 */

/**
 * adminRoutes
 * a base page that does not have permission requirements
 * all roles can be accessed
 */
export const staticRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/404',
    component: () => import('@/views/404'),
    hidden: true
  }
]

export const asyncRoutes = [
  {
    path: '/',
    component: Layout,
    redirect: '/index',
    children: [{
      path: 'index',
      name: 'Index',
      component: () => import('@/views/index/index'),
      meta: { title: '首页', icon: 'el-icon-s-home', roles: ['super'] }
    }]
  },
  {
    path: '/site',
    component: Layout,
    children: [{
      path: '',
      name: 'Site',
      component: () => import('@/views/site/index'),
      meta: { title: '站点', icon: 'ic_site', roles: ['super'] }
    }]
  },
  {
    path: '/data',
    component: Layout,
    meta: { title: '数据', icon: 'ic_chatmsg', roles: ['super'] },
    children: [
      {
        path: '/msg/web',
        name: 'Msg',
        component: () => import('@/views/msg/web'),
        meta: { title: '对话记录', roles: ['super'] }
      },
      {
        path: '/msg/write',
        name: 'Msg',
        component: () => import('@/views/msg/write'),
        meta: { title: '创作记录', roles: ['super'] }
      },
      {
        path: '/order',
        name: 'Order',
        component: () => import('@/views/order/index'),
        meta: { title: '付费记录', roles: ['super'] }
      },
      {
        path: '/user',
        name: 'User',
        component: () => import('@/views/user/index'),
        meta: { title: '用户', roles: ['super'] }
      }
    ]
  },
  {
    path: '/msg/index',
    name: 'MsgIndex',
    component: () => import('@/views/msg/index'),
    meta: { title: '已归档对话', roles: ['super'] },
    hidden: true
  },
  {
    path: '/system',
    component: Layout,
    meta: { title: '系统', icon: 'el-icon-setting', roles: ['super'] },
    children: [
      {
        path: '/setting',
        name: 'Setting',
        component: () => import('@/views/setting/index'),
        meta: { title: '系统配置', roles: ['super'] }
      },
      {
        path: '/filter',
        name: 'Filter',
        component: () => import('@/views/filter/index'),
        meta: { title: '内容过滤', roles: ['super'] }
      },
      {
        path: '/storage',
        name: 'Storage',
        component: () => import('@/views/storage/index'),
        meta: { title: '存储空间', roles: ['super'] }
      },
      
    ]
  },
  {
    path: '*',
    redirect: '/404',
    hidden: true
  }
]

const createRouter = () => new Router({
  // mode: 'history', // require service support
  base: '/super/',
  scrollBehavior: () => ({ y: 0 }),
  routes: staticRoutes
})
const router = createRouter()

// Detail see: https://github.com/vuejs/vue-router/issues/1234#issuecomment-357941465
export function resetRouter() {
  const newRouter = createRouter()
  router.matcher = newRouter.matcher // reset router
}

export default router
