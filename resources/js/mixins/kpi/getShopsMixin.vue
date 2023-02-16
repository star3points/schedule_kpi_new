<script>
export default {
  name: "getShopsMixin",
  data() {
    return {
      shopsData: new Proxy([], {})
    }
  },
  methods: {
    async getShops(dateRange) {
      let response = await this.$kpiApi.get('/get_shops', {
        params: dateRange
      });
      this.shopsData = response.data
      console.log(this.shopsData)
    }
  }
}
</script>