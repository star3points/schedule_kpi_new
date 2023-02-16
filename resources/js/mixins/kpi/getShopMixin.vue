<script>
export default {
  name: "getShopMixin",
  data () {
    return {
      shopData: {}
    }
  },
  methods: {
    async getShop() {
      let response = await this.$kpiApi('/get_shop')
      this.shopData = response.data
    }
  }
}
</script>
