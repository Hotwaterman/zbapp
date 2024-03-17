<template>
  <div></div>
</template>

<script>
import { login } from '@/api/user'

export default {
  name: 'Autologin',
  created() {
    const token = this.$route.query.token
    console.log('sss', token)
    if (token) {
      this.$store.dispatch('user/autologin', token).then(res => {
        this.loading = false
        if (res.errno) {
          this.$message({
            message: res.message,
            type: 'error'
          })
        } else {
          this.$message({
            message: res.message,
            type: 'success'
          })
          this.$nextTick(() => {
            this.$router.replace({
              path: '/index'
            })
          })
        }
      })
    }
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
