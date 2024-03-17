<template>
  <div>
    <a class="btn-buy" @click="openLink">
      <img src="https://tuan.chinaz.com/uploads/def/20221207/d9399fcbca3e2e089b697f5fce0681ce.png" />
      <span>正版源码下单地址</span>
    </a>
  </div>
</template>
<script>
export default {
  methods: {
    openLink() {
      window.open('https://tuan.chinaz.com/goods_detail/3540', '_blank')
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
  width: 16px;
  height: 16px;
  margin-right: 10px;
}
</style>
