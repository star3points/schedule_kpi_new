<script>

export default {
  name: "getSchedule",
  data() {
    return {
      tableData: {}
    }
  },
  methods: {
    async getSchedule() {
      let response = await this.$scheduleApi.get('/get_schedule', {
        params: {
          shop_id: this.shopId ? this.shopId : null,
          month: this.month.format('YYYY-MM-DD')
        }
      });
      this.tableData = response.data
    }
  }
}
</script>