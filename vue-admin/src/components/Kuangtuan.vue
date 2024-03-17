<template>
  <div>
    <a class="btn-buy" @click="openLink">
      <img src="https://www.kt8.cn/favicon.ico" />
      <span>正版源码下单地址</span>
    </a>
  </div>
</template>
<script>
export default {
  methods: {
    openLink() {
      window.open('https://www.kt8.cn/item/view1856.html', '_blank')
    }
  }
}
</script>
<style scoped>
.btn-buy {
  margin-right: 50px;
  display: flex;
  align-items: center;
  font-size: 14px;
  color: #409EFF;
}
.btn-buy img {
  width: 20px;
  height: 20px;
  margin-right: 6px;
}
</style>
