<template>

</template>

<script>
export default {
  name: "ScheduleRoute",
  data() {
    return {
      role: ''
    }
  },
  created() {
    console.log('schedule_route')
    this.route()
  },
  methods: {
    async route() {
      let response = await this.$scheduleApi('/get_role')
      this.role = response.data.role
      if (this.role === 'Admin') {
        this.$router.push('/schedule/admin');
      } else if (this.role === 'Manager') {
        this.$router.push('/schedule/manager')
      } else if (this.role === 'Worker') {
        this.$router.push('/schedule/worker')
      } else {
        this.router.push('/no_data')
      }
    }
  }
}
</script>

<style scoped>

</style>