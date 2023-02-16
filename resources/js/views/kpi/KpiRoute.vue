<template>

</template>

<script>
export default {
  name: "KpiRoute",
  data() {
    return {
      role: ''
    }
  },
  created() {
    console.log('kpi_route')
    this.route()
  },
  methods: {
    async route() {
      let response = await this.$kpiApi('/get_role')
      this.role = response.data.role
      if (this.role === 'Admin') {
        this.$router.push('/kpi/admin');
      } else if (this.role === 'Manager') {
        this.$router.push('/kpi/manager')
      } else if (this.role === 'Worker') {
        this.$router.push('/kpi/worker')
      } else {
        this.router.push('/no_data')
      }
    }
  }
}
</script>

<style scoped>

</style>