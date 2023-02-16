<template>
  AdminView
  <a-range-picker
      v-model:value="selectedDateRange"
      v-on:change="update"
  />
  <div v-if="currentTab === 'Shops'">
    <Shops
        :shopsData="shopsData"
        v-on:toShop="setTab(selectedShop)"
    ></Shops>
  </div>
  <div v-else-if="currentTab === 'Shop'">
    <Shop
        :shopData="shopData"
        v-on:toPersonal="setTab(selectedWorker)"
    ></Shop>
  </div>
  <div v-else-if="currentTab === 'Worker'">
    <Worker
        :workerData="workerData"
    ></Worker>
  </div>
</template>

<script>

import Shops from "../../components/kpi/Shops.vue";
import Shop from "../../components/kpi/Shop.vue";
import Worker from "../../components/kpi/Worker.vue";
import getWorkerMixin from "../../mixins/kpi/getWorkerMixin.vue";
import getShopsMixin from "../../mixins/kpi/getShopsMixin.vue";
import getShopMixin from "../../mixins/kpi/getShopMixin.vue";
import dateRangeMixin from "../../mixins/kpi/dateRangeMixin.vue";

export default {
  name: "AdminView",
  components: {Shops, Shop, Worker},
  mixins: [getShopsMixin, getShopMixin, getWorkerMixin, dateRangeMixin],
  data() {
    return {
      currentTab: 'Shops',
      selectedShop: '',
      selectedWorker: ''
    }
  },
  async created() {
    this.setDefaultDateRange()
    await this.setTab(this.currentTab, {
      dateRange: this.getFormattedDateRange()
    });
  },
  methods: {
    async setTab(tabName, data) {
      if (tabName === 'Shops') {
        await this.getShops(data.dateRange)
        this.currentTab = tabName
      } else if (tabName === 'Shop' && data.shopId) {
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
    },
    update() {
      this.setTab(this.currentTab, {
        dateRange: this.getFormattedDateRange()
      });
    }
  }
}
</script>

<style scoped>

</style>