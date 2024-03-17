<template>
  <div class="login-container">
    <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" auto-complete="on" label-position="left">

      <div class="title-container">
        <h3 class="title">管理中心登录</h3>
      </div>

      <el-form-item prop="username">
        <span class="svg-container">
          <svg-icon icon-class="user" />
        </span>
        <el-input
          ref="username"
          v-model="loginForm.username"
          placeholder="管理员账号"
          name="username"
          type="text"
          tabindex="1"
          auto-complete="on"
        />
      </el-form-item>

      <el-form-item prop="password">
        <span class="svg-container">
          <svg-icon icon-class="password" />
        </span>
        <el-input
          :key="passwordType"
          ref="password"
          v-model="loginForm.password"
          :type="passwordType"
          placeholder="密码"
          name="password"
          tabindex="2"
          auto-complete="on"
          @keyup.enter.native="handleLogin"
        />
        <span class="show-pwd" @click="showPwd">
          <svg-icon :icon-class="passwordType === 'password' ? 'eye' : 'eye-open'" />
        </span>
      </el-form-item>

      <el-button :loading="loading" type="primary" style="width:100%; margin-bottom:10px; margin-top: 20px;" @click.native.prevent="handleLogin">确定登录</el-button>

    </el-form>

    <div v-if="system" class="copyright">
      <a v-if="system.gongan" :href="'https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' + system.gongan.replace(/[^\d]/g, '')" target="_blank">
        <span style="display: flex; align-items: center; margin-right: 20px;">
          <img src="@/assets/gongan.png" style="margin-right: 5px;" />{{ system.gongan }}
        </span>
      </a>
      <a v-if="system.icp" href="https://beian.miit.gov.cn/" target="_blank">{{ system.icp }}</a>
    </div>
  </div>
</template>

<script>
import { getSystemSetting } from '@/api/user'
import { setSystemTitle } from '@/utils/system'

export default {
  name: 'Login',
  data() {
    const validateUsername = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请输入账号'))
      } else {
        callback()
      }
    }
    const validatePassword = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请输入密码'))
      } else {
        callback()
      }
    }
    return {
      loginForm: {
        username: '',
        password: ''
      },
      loginRules: {
        username: [{ required: true, trigger: 'blur', validator: validateUsername }],
        password: [{ required: true, trigger: 'blur', validator: validatePassword }]
      },
      loading: false,
      passwordType: 'password',
      redirect: undefined,
      system: ''
    }
  },
  watch: {
    $route: {
      handler: function(route) {
        this.redirect = route.query && route.query.redirect
      },
      immediate: true
    }
  },
  created() {
    this.getSystemSetting()
  },
  methods: {
    showPwd() {
      if (this.passwordType === 'password') {
        this.passwordType = ''
      } else {
        this.passwordType = 'password'
      }
      this.$nextTick(() => {
        this.$refs.password.focus()
      })
    },
    getSystemSetting() {
      getSystemSetting().then(res => {
        this.system = res.data
        if (this.system && this.system.title) {
          document.title = this.system.title
          setSystemTitle(this.system.title)
        }
      })
    },
    handleLogin() {
      this.$refs.loginForm.validate(valid => {
        if (valid) {
          this.loading = true
          this.$store.dispatch('user/login', this.loginForm).then((response) => {
            this.loading = false
            if (response.errno) {
              this.$message({
                message: response.message,
                type: 'error'
              })
            } else {
              this.$message({
                message: response.message,
                type: 'success'
              })

              this.$router.push({ path: this.redirect || '/' })
            }
          }).catch(() => {
            this.loading = false
          })
        } else {
          console.log('error submit')
          return false
        }
      })
    }
  }
}
</script>

<style lang="scss">
  /* 修复input 背景不协调 和光标变色 */
  /* Detail see https://github.com/PanJiaChen/vue-element-admin/pull/927 */

  $bg:#283443;
  $light_gray:#666;
  $cursor: #666;

  @supports (-webkit-mask: none) and (not (cater-color: $cursor)) {
    .login-container .el-input input {
      color: $cursor;
    }
  }

  /* reset element-ui css */
  .login-container {
    .el-input {
      display: inline-block;
      height: 47px;
      width: 85%;

      input {
        background: transparent;
        border: 0;
        -webkit-appearance: none;
        border-radius: 0;
        padding: 12px 5px 12px 15px;
        color: $light_gray;
        height: 47px;
        caret-color: $cursor;

        &:-webkit-autofill {
          box-shadow: 0 0 0 1000px $bg inset !important;
          -webkit-text-fill-color: $cursor !important;
        }
      }
    }

    .el-form-item {
      border: 1px solid rgba(255, 255, 255, 0.1);
      background: rgba(0, 0, 0, 0.08);
      border-radius: 5px;
      color: #454545;
    }

    .copyright {
      width: 100%;
      height: 40px;
      line-height: 40px;
      text-align: center;
      font-size: 14px;
      color: #666;
      position: fixed;
      left: 0;
      bottom: 30px;
      letter-spacing: 1px;
      a {
        color: #333;
        text-decoration: none;
        &:hover {
          color: #0575f3;
        }
      }
    }
  }
</style>

<style lang="scss" scoped>
$bg:#2d3a4b;
$dark_gray:#889aa4;
$light_gray:#666;

.login-container {
  min-height: 100%;
  width: 100%;
  background-color: rgba(255, 255, 255, 0.88);
  overflow: hidden;
  background:url(login_bg.jpg);

  .login-form {
    width: 460px;
    padding: 50px 70px;
    margin: 0 auto;
    overflow: hidden;
    background: rgba(255,255,255,0.6);
    position: absolute;
    left: 50%;
    top: 50%;
    margin-left: -230px;
    margin-top: -210px;
    border-radius: 15px;
    box-shadow: 0 0 10px #eee;
  }

  .tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 10px;

    span {
      &:first-of-type {
        margin-right: 16px;
      }
    }
  }

  .svg-container {
    padding: 6px 5px 6px 15px;
    color: $dark_gray;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
  }

  .title-container {
    position: relative;

    .title {
      font-size: 24px;
      color: $light_gray;
      margin: 0 auto 40px auto;
      text-align: center;
      font-weight: normal;
    }
  }

  .show-pwd {
    position: absolute;
    right: 10px;
    top: 7px;
    font-size: 16px;
    color: $dark_gray;
    cursor: pointer;
    user-select: none;
  }
  .copyright {
    position: fixed;
    left: 0;
    bottom: 20px;
    width: 100%;
    text-align: center;
    line-height: 30px;
    color: #666;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;

    a {
      color: #666;
    }
  }
}
</style>
