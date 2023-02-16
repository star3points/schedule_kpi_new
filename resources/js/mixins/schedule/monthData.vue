<script>
export default {
  name: "monthData",
  data() {
    return {
      monthData: {
        monthClosed: true,
        comment: '',
        salesPlan: 0,
        workersQty: 0
      },
    }
  },
  methods: {
    async getMonthData() {
      let response = await this.$scheduleApi.get('/get_month_data', {
        params: {
          shop_id: this.shopId,
          month: this.month.format('YYYY-MM-DD')
        }
      })
      this.monthData = response.data
    },
    async editMonthData() {
      await this.$scheduleApi.post('/edit_month_data', {
        shop_id: this.shopId,
        month: this.date,
        month_closed: this.monthClosed,
        sales_plan: this.salesPlan,
        workers_qty: this.workersQty
      })
    }
  }
}
</script>