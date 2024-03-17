<template>
  <div>
    <el-popover
      placement="top-start"
      title=""
      width="180"
      trigger="hover"
    >
      <div>
        <img src="@/assets/wework_fox.jpg" width="150px" height="150px">
      </div>
      <el-button class="btn-contact" slot="reference" type="text" style="margin-right: 50px;">
        <svg-icon icon-class="ic_wechat"></svg-icon>
        <span style="margin-left: 10px;">联系开发者</span>
      </el-button>
    </el-popover>
  </div>
</template>

<script>

</script>

<style lang="scss" scoped>
  .btn-contact {
    .svg-icon {
      font-size: 16px;
    }
  }

</style>
