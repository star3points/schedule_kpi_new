<template>
  <a-row>
    <a-col :span="12">
      <a-button v-on:click="goToSchedule">toSchedule</a-button>
    </a-col>
    <a-col :span="12">
      <a-button v-on:click="goToKpi">toKpi</a-button>
    </a-col>
  </a-row>
</template>

<script>
export default {
  name: "AppSwitcher",
  methods: {
    goToSchedule() {
      this.$router.push('/schedule')
    },
    goToKpi() {
      this.$router.push('/kpi')
    }
  }

}
</script>

<style scoped>

</style>