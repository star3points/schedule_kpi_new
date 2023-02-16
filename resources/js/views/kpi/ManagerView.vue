<template>
  ManagerView
  <a-range-picker v-model:value="selectedDateRange" />
  <div v-if="currentTab === 'Shop'">
    <Shop
        :shopData="shopData"
        v-on:toPersonal="setTab()"
    ></Shop>
  </div>
  <div v-else-if="currentTab === 'Worker'">
    <Worker
        :workerData="workerData"
    ></Worker>
  </div>
</template>

<script>
import getShopMixin from "../../mixins/kpi/getShopMixin.vue";
import getWorkerMixin from "../../mixins/kpi/getWorkerMixin.vue";
import dateRangeMixin from "../../mixins/kpi/dateRangeMixin.vue";
import Shop from "../../components/kpi/Shop.vue";
import Worker from "../../components/kpi/Worker.vue";

export default {
  name: "ManagerView",
  mixins: [getShopMixin, getWorkerMixin, dateRangeMixin],
  components: [Worker, Shop],
  data() {
    return {
      currentTab: 'Shop',
    }
  },
  methods: {
    async setTab(tabName, data) {
      if (tabName === 'Shop' && data.shopId) {
        this.selectedShop = data.shopId
        await this.getShop()
        this.currentTab = tabName
      } else if (tabName === 'Worker' && data.shopId && data.workerId) {
        this.selectedShop = data.shopId
        this.selectedWorker = data.workerId
        await this.getWorker()
        this.currentTab = tabName
      } else {
        console.log(tabName, data)
      }
    }
  }
}
</script>

<style scoped>

</style>