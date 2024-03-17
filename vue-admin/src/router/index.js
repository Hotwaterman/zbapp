import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/layout'

export const staticRoutes = [
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/autologin',
    component: () => import('@/views/login/auto'),
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
      meta: { title: '首页', icon: 'el-icon-s-home', roles: ['admin'] }
    }]
  },
  {
    path: '/msg',
    component: Layout,
    children: [{
      path: 'web',
      name: 'Msg',
      component: () => import('@/views/msg/web'),
      meta: { title: '对话', icon: 'ic_chatmsg', roles: ['admin'] }
    }]
  },
  {
    path: '/msg/index',
    name: 'MsgIndex',
    component: () => import('@/views/msg/index'),
    meta: { title: '已归档对话', icon: 'ic_chatmsg', roles: ['admin'] },
    hidden: true
  },
  {
    path: '/user',
    component: Layout,
    meta: { title: '用户', icon: 'user', roles: ['admin'] },
    children: [
      {
        path: 'index',
        name: 'User',
        component: () => import('@/views/user/index'),
        meta: { title: '用户管理' }
      },
      {
        path: 'freeze',
        name: 'userFreeze',
        component: () => import('@/views/user/freeze'),
        meta: { title: '小黑屋' }
      },
      {
        path: 'feedback',
        name: 'userFeedback',
        component: () => import('@/views/user/feedback'),
        meta: { title: '用户反馈' }
      },
      {
        path: 'login',
        name: 'userLogin',
        component: () => import('@/views/user/login/layout'),
        meta: { title: '登录设置', roles: ['admin'] },
        children: [
          {
            path: 'setting',
            name: 'userLoginSetting',
            components: { subRouter: () => import('@/views/user/login/setting') },
            meta: { title: '登录设置' }
          },
          {
            path: 'sms',
            name: 'userLoginSms',
            components: { subRouter: () => import('@/views/user/login/sms') },
            meta: { title: '短信配置' },
            hidden: true
          }
        ]
      }
    ]
  },
  {
    path: '/commission',
    component: Layout,
    meta: { title: '分销', icon: 'el-icon-money', roles: ['admin'] },
    children: [
      {
        path: 'list',
        name: 'CommissionList',
        component: () => import('@/views/commission/list'),
        meta: { title: '分销商', roles: ['admin'] }
      },
      {
        path: 'order',
        name: 'CommissionOrder',
        component: () => import('@/views/commission/order'),
        meta: { title: '分销订单', roles: ['admin'] }
      },
      {
        path: 'apply',
        name: 'CommissionApply',
        component: () => import('@/views/commission/apply'),
        meta: { title: '入驻申请', roles: ['admin'] }
      },
      {
        path: 'withdraw',
        name: 'CommissionWithdraw',
        component: () => import('@/views/commission/withdraw'),
        meta: { title: '提现申请', roles: ['admin'] }
      },
      {
        path: 'setting',
        name: 'CommissionSetting',
        component: () => import('@/views/commission/setting'),
        meta: { title: '分销设置', roles: ['admin'] }
      }
    ]
  },
  {
    path: '/order',
    component: Layout,
    children: [{
      path: '',
      name: 'Order',
      component: () => import('@/views/order/index'),
      meta: { title: '订单', icon: 'ic_order', roles: ['admin'] }
    }]
  },
  {
    path: '/module',
    component: Layout,
    children: [
      {
        path: 'index',
        name: 'ModuleIndex',
        component: () => import('@/views/module/index'),
        meta: { title: '功能', icon: 'ic_module', roles: ['admin'] }
      },
      {
        path: 'chat',
        meta: { title: 'AI自由对话' },
        component: () => import('@/views/module/chat/layout'),
        children: [
          {
            path: 'msg',
            name: 'ModuleChatMsg',
            components: { subRouter: () => import('@/views/module/chat/msg') },
            meta: { title: '对话记录' }
          },
          {
            path: 'setting',
            name: 'ModuleChatSetting',
            components: { subRouter: () => import('@/views/module/chat/setting') },
            meta: { title: '参数设置' }
          }
        ],
        hidden: true
      },
      {
        path: 'write',
        meta: { title: 'AI文本创作' },
        component: () => import('@/views/module/write/layout'),
        children: [
          {
            path: 'msg',
            name: 'ModuleWriteMsg',
            components: { subRouter: () => import('@/views/module/write/msg') },
            meta: { title: '创作记录' }
          },
          {
            path: 'prompts',
            name: 'ModuleWritePrompts',
            components: { subRouter: () => import('@/views/module/write/prompts') },
            meta: { title: '模型管理' }
          },
          {
            path: 'topic',
            name: 'ModuleWriteTopic',
            components: { subRouter: () => import('@/views/module/write/topic') },
            meta: { title: '类别管理' }
          }
        ],
        hidden: true
      },
      {
        path: 'cosplay',
        meta: { title: 'AI角色扮演' },
        component: () => import('@/views/module/cosplay/layout'),
        children: [
          {
            path: 'msg',
            name: 'ModuleCosplayMsg',
            components: { subRouter: () => import('@/views/module/cosplay/msg') },
            meta: { title: '对话记录' }
          },
          {
            path: 'role',
            name: 'ModuleCosplayRole',
            components: { subRouter: () => import('@/views/module/cosplay/role') },
            meta: { title: '角色管理' }
          },
          {
            path: 'type',
            name: 'ModuleCosplayType',
            components: { subRouter: () => import('@/views/module/cosplay/type') },
            meta: { title: '类别管理' }
          }
        ],
        hidden: true
      },
      {
        path: 'draw',
        meta: { title: 'AI绘画' },
        component: () => import('@/views/module/draw/layout'),
        children: [
          {
            path: 'msg',
            name: 'ModuleDrawMsg',
            components: { subRouter: () => import('@/views/module/draw/msg') },
            meta: { title: '生成记录' }
          },
          {
            path: 'setting',
            name: 'ModuleDrawSetting',
            components: { subRouter: () => import('@/views/module/draw/setting') },
            meta: { title: '参数设置' }
          }
        ],
        hidden: true
      },
      {
        path: 'book',
        meta: { title: '私有知识库' },
        component: () => import('@/views/module/book/layout'),
        children: [
          {
            path: 'msg',
            name: 'ModuleBookData',
            components: { subRouter: () => import('@/views/module/book/data') },
            meta: { title: '内容管理' }
          },
          {
            path: 'group',
            name: 'ModuleBookBook',
            components: { subRouter: () => import('@/views/module/book/book') },
            meta: { title: '知识库' }
          }
        ],
        hidden: true
      },
      {
        path: 'reward',
        meta: { title: '任务中心' },
        component: () => import('@/views/module/reward/layout'),
        children: [
          {
            path: 'setting',
            name: 'ModuleRewardAdSetting',
            components: { subRouter: () => import('@/views/module/reward/ad/setting') },
            meta: { title: '任务设置 - 激励广告' }
          },
          {
            path: 'logs',
            name: 'ModuleRewardAdLogs',
            components: { subRouter: () => import('@/views/module/reward/ad/logs') },
            meta: { title: '观看记录 - 激励广告', roles: ['admin'] },
            hidden: true
          },
          {
            path: 'setting',
            name: 'ModuleRewardShareSetting',
            components: { subRouter: () => import('@/views/module/reward/share/setting') },
            meta: { title: '任务设置 - 分享奖励' }
          },
          {
            path: 'logs',
            name: 'ModuleRewardShareLogs',
            components: { subRouter: () => import('@/views/module/reward/share/logs') },
            meta: { title: '分享记录 - 分享奖励', roles: ['admin'] },
            hidden: true
          },
          {
            path: 'setting',
            name: 'ModuleRewardInviteSetting',
            components: { subRouter: () => import('@/views/module/reward/invite/setting') },
            meta: { title: '任务设置 - 邀请奖励' }
          },
          {
            path: 'logs',
            name: 'ModuleRewardInviteLogs',
            components: { subRouter: () => import('@/views/module/reward/invite/logs') },
            meta: { title: '邀请记录 - 邀请奖励', roles: ['admin'] },
            hidden: true
          }
        ],
        hidden: true
      },
      {
        path: 'charge',
        meta: { title: '套餐管理' },
        component: () => import('@/views/module/charge/layout'),
        children: [
          {
            path: 'goods',
            name: 'ModuleChargeGoods',
            components: { subRouter: () => import('@/views/module/charge/goods/index') },
            meta: { title: '对话套餐' }
          },
          {
            path: 'gpt4',
            name: 'ModuleChargeGpt4',
            components: { subRouter: () => import('@/views/module/charge/gpt4/index') },
            meta: { title: 'GPT4套餐' }
          },
          {
            path: 'draw',
            name: 'ModuleChargeDraw',
            components: { subRouter: () => import('@/views/module/charge/draw/index') },
            meta: { title: '绘画套餐' }
          },
          {
            path: 'vip',
            name: 'ModuleChargeVip',
            components: { subRouter: () => import('@/views/module/charge/vip/index') },
            meta: { title: 'vip套餐' }
          }
        ],
        hidden: true
      },
      {
        path: 'card',
        meta: { title: '充值卡密' },
        component: () => import('@/views/module/card/layout'),
        children: [
          {
            path: 'batch',
            name: 'ModuleCardBatch',
            components: { subRouter: () => import('@/views/module/card/batch') },
            meta: { title: '卡密管理' }
          },
          {
            path: 'logs',
            name: 'ModuleCardLogs',
            components: { subRouter: () => import('@/views/module/card/logs') },
            meta: { title: '使用记录' }
          }
        ],
        hidden: true
      },
      {
        path: 'keys',
        meta: { title: 'Key池管理' },
        component: () => import('@/views/module/keys/layout'),
        children: [
          {
            path: 'openai',
            name: 'ModuleKeysOpenai',
            components: { subRouter: () => import('@/views/module/keys/openai') },
            meta: { title: 'key池 - openai' }
          },
          {
            path: 'gpt4',
            name: 'ModuleKeysGpt4',
            components: { subRouter: () => import('@/views/module/keys/gpt4') },
            meta: { title: 'key池 - GPT4' }
          },
          {
            path: 'api2d',
            name: 'ModuleKeysApi2d',
            components: { subRouter: () => import('@/views/module/keys/api2d') },
            meta: { title: 'key池 - api2d' }
          },
          {
            path: 'baidu',
            name: 'ModuleKeysBaidu',
            components: { subRouter: () => import('@/views/module/keys/baidu') },
            meta: { title: 'key池 - 百度文心' }
          },
          {
            path: 'xunfei',
            name: 'ModuleKeysXunfei',
            components: { subRouter: () => import('@/views/module/keys/xunfei') },
            meta: { title: 'key池 - 讯飞星火' }
          },
          {
            path: 'lxai',
            name: 'ModuleKeysLxai',
            components: { subRouter: () => import('@/views/module/keys/lxai') },
            meta: { title: 'key池 - 灵犀AI' }
          },
          {
            path: 'yijian',
            name: 'ModuleKeysYijian',
            components: { subRouter: () => import('@/views/module/keys/yijian') },
            meta: { title: 'key池 - 意间AI' }
          },
          {
            path: 'replicate',
            name: 'ModuleKeysReplicate',
            components: { subRouter: () => import('@/views/module/keys/replicate') },
            meta: { title: 'key池 - Replicate' }
          }
        ],
        hidden: true
      },
      {
        path: 'article',
        meta: { title: '文档管理' },
        component: () => import('@/views/module/article/layout'),
        children: [
          {
            path: 'help',
            name: 'ModuleArticleHelp',
            components: { subRouter: () => import('@/views/module/article/help/index') },
            meta: { title: '使用教程' }
          },
          {
            path: 'notice',
            name: 'ModuleArticleNotice',
            components: { subRouter: () => import('@/views/module/article/article') },
            meta: { title: '通知公告', name: 'notice' }
          },
          {
            path: 'about',
            name: 'ModuleArticleAbout',
            components: { subRouter: () => import('@/views/module/article/article') },
            meta: { title: '关于我们', name: 'about' }
          },
          {
            path: 'privacy',
            name: 'ModuleArticlePrivacy',
            components: { subRouter: () => import('@/views/module/article/article') },
            meta: { title: '隐私政策', name: 'privacy' }
          },
          {
            path: 'service',
            name: 'ModuleArticleService',
            components: { subRouter: () => import('@/views/module/article/article') },
            meta: { title: '服务协议', name: 'service' }
          },
          {
            path: 'legal',
            name: 'ModuleArticleLegal',
            components: { subRouter: () => import('@/views/module/article/article') },
            meta: { title: '免责条款', name: 'legal' }
          },
          {
            path: 'commission',
            name: 'ModuleArticleCommission',
            components: { subRouter: () => import('@/views/module/article/article') },
            meta: { title: '分销协议', name: 'commission' }
          },
          {
            path: 'kefu',
            name: 'ModuleArticleKefu',
            components: { subRouter: () => import('@/views/module/article/article') },
            meta: { title: '联系客服', name: 'kefu' }
          }
        ],
        hidden: true
      },
      {
        path: 'share',
        meta: { title: '分享配置' },
        component: () => import('@/views/module/share/layout'),
        children: [
          {
            path: 'text',
            name: 'ModuleShareText',
            components: { subRouter: () => import('@/views/module/share/text') },
            meta: { title: '分享文案' }
          },
          {
            path: 'haibao',
            name: 'ModuleShareHaibao',
            components: { subRouter: () => import('@/views/module/share/haibao') },
            meta: { title: '分享海报' }
          }
        ],
        hidden: true
      },
      {
        path: 'wxapp',
        component: () => import('@/views/module/wxapp/layout'),
        children: [
          {
            path: 'index',
            name: 'ModuleWxappSetting',
            components: { subRouter: () => import('@/views/module/wxapp/setting') },
            meta: { title: '小程序' }
          },
          {
            path: 'upload',
            name: 'ModuleWxappUpload',
            components: { subRouter: () => import('@/views/module/wxapp/upload') },
            meta: { title: '上传小程序代码' }
          }
        ],
        hidden: true
      },
      {
        path: 'web',
        component: () => import('@/views/module/web/layout'),
        children: [
          {
            path: 'index',
            name: 'ModuleWebPC',
            components: { subRouter: () => import('@/views/module/web/pc') },
            meta: { title: 'PC版' }
          },
          {
            path: 'upload',
            name: 'ModuleWebH5',
            components: { subRouter: () => import('@/views/module/web/h5') },
            meta: { title: 'H5版' }
          }
        ],
        hidden: true
      }
    ]
  },
  {
    path: '',
    component: Layout,
    meta: { title: '系统', icon: 'el-icon-setting', roles: ['admin'] },
    children: [
      {
        path: '/setting',
        name: 'Setting',
        component: () => import('@/views/setting/index'),
        meta: { title: '系统配置', roles: ['admin'] }
      },
      {
        path: '/wxmp',
        name: 'ModuleWxmp',
        component: () => import('@/views/module/wxmp/layout'),
        meta: { title: '公众号', roles: ['admin'] },
        children: [
          {
            path: 'setting',
            name: 'ModuleWxmpSetting',
            components: { subRouter: () => import('@/views/module/wxmp/setting') },
            meta: { title: '公众号' }
          },
          {
            path: 'reply',
            name: 'ModuleWxmpReply',
            components: { subRouter: () => import('@/views/module/wxmp/reply') },
            meta: { title: '自动回复' },
            hidden: true
          },
          {
            path: 'keyword',
            name: 'ModuleWxmpKeyword',
            components: { subRouter: () => import('@/views/module/wxmp/keyword') },
            meta: { title: '关键词回复' },
            hidden: true
          }
        ]
      }
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
  base: '/admin/',
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
