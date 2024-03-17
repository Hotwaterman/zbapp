<template>
  <div class="app-container">
    <div class="mod-panel">
      <div class="mod-title"><span>AI功能</span></div>
      <ul class="mod-list">
        <li class="mod-item" @click="linkto('ModuleChatMsg')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_chat" />
          </div>
          <div class="content">
            <div class="name">
              <span>AI自由对话</span>
            </div>
            <div class="desc">可设置AI前置指令</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleWriteMsg')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_write" />
          </div>
          <div class="content">
            <div class="name">
              <span>AI文本创作</span>
            </div>
            <div class="desc">自定义创作模型，自由发挥</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleCosplayMsg')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_cosplay" />
          </div>
          <div class="content">
            <div class="name">
              <span>AI角色模拟</span>
            </div>
            <div class="desc">模拟各种身份、职位、专家</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleDrawMsg')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_draw" />
          </div>
          <div class="content">
            <div class="name">
              <span>AI绘画</span>
            </div>
            <div class="desc">内置多种绘画接口</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleBookData')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_book" />
          </div>
          <div class="content">
            <div class="name">
              <el-badge value="初级版" class="item">
              <span>私有知识库</span>
              </el-badge>
            </div>
            <div class="desc">可绑定到对话、角色等</div>
          </div>
        </li>
        <div style="clear: both;"></div>
      </ul>
      <div class="mod-title"><span>运营工具</span></div>
      <ul class="mod-list">
        <li class="mod-item" @click="linkto('ModuleRewardShareLogs')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_reward" class="text-orange" />
          </div>
          <div class="content">
            <div class="name">
              <span>任务中心</span>
            </div>
            <div class="desc">分享、邀请、广告等任务</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleChargeGoods')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_charge" class="text-orange" />
          </div>
          <div class="content">
            <div class="name">
              <span>充值套餐</span>
            </div>
            <div class="desc">对话、绘画、vip等充值套餐</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleCardBatch')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_card" class="text-orange" />
          </div>
          <div class="content">
            <div class="name">
              <span>卡密兑换</span>
            </div>
            <div class="desc">生成卡密，在其他平台销售</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleKeysOpenai')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_keys" class="text-orange" />
          </div>
          <div class="content">
            <div class="name">
              <span>Key池管理</span>
            </div>
            <div class="desc">多Key轮询</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleArticleHelp')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_article" class="text-orange" />
          </div>
          <div class="content">
            <div class="name">
              <span>文档中心</span>
            </div>
            <div class="desc">使用教程、服务协议等</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleShareText')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_share" class="text-orange" />
          </div>
          <div class="content">
            <div class="name">
              <span>分享配置</span>
            </div>
            <div class="desc">分享文案、分享海报等</div>
          </div>
        </li>
<!--        <li class="mod-item" @click="linkto('ModuleNoticeIndex')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_notice" class="text-orange" />
          </div>
          <div class="content">
            <div class="name">
              <span>通知公告</span>
            </div>
            <div class="desc"></div>
          </div>
        </li>-->
        <div style="clear: both;"></div>
      </ul>
      <div class="mod-title"><span>客户端</span></div>
      <ul class="mod-list">
        <li class="mod-item" @click="linkto('ModuleWxappSetting')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_wxapp" class="text-green" />
          </div>
          <div class="content">
            <div class="name">
              <span>微信小程序</span>
            </div>
            <div class="desc">参数配置、代码上传等</div>
          </div>
        </li>
        <li class="mod-item" @click="linkto('ModuleWebPC')">
          <div class="icon">
            <svg-icon icon-class="ic_mod_web" class="text-green" />
          </div>
          <div class="content">
            <div class="name">
              <span>Web版</span>
            </div>
            <div class="desc">管理PC版、H5版</div>
          </div>
        </li>

        <div style="clear: both;"></div>
      </ul>

    </div>

  </div>
</template>

<script>

export default {
  data() {
    return {

    }
  },
  computed: {
    roles() {
      return this.$store.getters.roles
    }
  },
  methods: {
    linkto(name, query = {}) {
      if (name === 'ModuleBillpayShop' && !this.roles.includes('billpay')) {
        this.$router.push({
          name: 'ModuleBuy',
          query: {
            module: 'billpay'
          }
        })
        return
      } else if (name === 'ModuleOpenapiShop' && !this.roles.includes('openapi')) {
        this.$router.push({
          name: 'ModuleBuy',
          query: {
            module: 'openapi'
          }
        })
        return
      }
      this.$router.push({
        name: name,
        query: query
      })
    }
  }
}
</script>

<style lang="scss" scoped>
  .mod-panel {
    width: 100%;
    max-width: 1330px;
    padding: 20px;
    margin-bottom: 20px;
    background-color: #fff;
    .mod-title {
      font-size: 16px;
      font-weight: bold;
      width: 100%;
      height: 60px;
      line-height: 60px;
      span {
        padding: 0 10px;
        border-left: 4px solid #0e86f5;
      }
    }
    .mod-list {
      list-style: none;
      padding: 0;
      margin: 0;

      .mod-item {
        width: 240px;
        height: 72px;
        padding: 12px 15px 12px 12px;
        border: 1px solid #eee;
        border-radius: 5px;
        background-color: #f9fbfd;
        cursor: pointer;
        float: left;
        margin: 10px 20px 10px 0;
        display: flex;
        overflow: hidden;
        &:hover {
          background-color: #f3f6f9;
        }
        .icon {
          width: 72px;
          height: 100%;
          font-size: 44px;
          line-height: 50px;
          box-sizing: border-box;
        }
        .content {
          width: 100%;
          .name {
            width: 100%;
            height: 24px;
            font-size: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3px;
            color: #333;
            .state {
              font-size: 14px;
              color: #8d8d8d;
              i {
                color: #d81e06;
              }
            }
          }
          .desc {
            font-size: 12px;
            color: #8d8d8d;
            line-height: 18px;
          }
        }

        .ctrl {
          width: 100%;
          background: #f7f8f9;
          line-height: 36px;
          text-align: center;
          font-size: 14px;
        }
        .text-orange {
          color: #f4c265;
        }
        .text-green {
          color: #06b13c;
        }
        .text-blue {
          color: #0e86f5;
        }
        .text-red {
          color: #d81e06;
        }
      }
    }
  }
</style>
