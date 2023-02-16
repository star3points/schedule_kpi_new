<script>
export default {
  name: "getWorkerMixin",
  data() {
    return {
      workerData: {}
    }
  },
  methods: {
    async getWorker() {
      let response = await this.$kpiApi.get('/get_personal')
      this.workerData = response.data
    }

  }
}
</script>