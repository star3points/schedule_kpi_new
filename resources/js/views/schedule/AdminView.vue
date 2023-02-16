<template>
  <a-row justify="space-around">
    <a-col :span="9" class="container">
      <a-date-picker
          class="select"
          v-model:value="month"
          v-on:select="reloadTable(shopId, $event)"
          picker="month"
      />
    </a-col>
    <a-col :span="9" class="container">
      <a-select
          v-model:value="shopId"
          v-on:select="reloadTable($event, month)"
          style="width: 100%"
          placeholder="Выбирите магазин"
      >
        <a-select-option
            v-for="shop in shopList"
            v-bind:key="shop.id"
            v-bind:value="shop.bx_id"
        >{{shop.name}}</a-select-option>
      </a-select>
    </a-col>
    <a-col :span="6" class="container">
      <a-button v-on:click="showAddWorker = !showAddWorker">
        Добавить сотрудника
      </a-button>
    </a-col>
  </a-row>
  <AddWorker
      v-if="showAddWorker"
      :shopId="shopId"
      :month="month"
      v-on:addWorker="addWorker"
      v-on:closeAddWorker="showAddWorker = false"
  />
  <ScheduleTable
      :shopId="shopId"
      :month="month"
      :editable="true"
      :role="'Admin'"
      :tableData="tableData"
  />
  <EditMonthData
      :month="month"
      :shopId="shopId"
      :monthData="monthData"
      :editable="true"
      v-on:editMonthData="editMonthData"
  />
</template>

<script>
import ScheduleTable from "../../components/schedule/ScheduleTable.vue";
import dayjs from 'dayjs';
import getSchedule from "../../mixins/schedule/getSchedule.vue";
import EditMonthData from "../../components/schedule/EditMonthData.vue";
import monthData from "../../mixins/schedule/monthData.vue";
import AddWorker from "../../components/schedule/AddWorker.vue";
export default {
  name: "ScheduleAdmin",
  components: {AddWorker, EditMonthData, ScheduleTable},
  mixins: [getSchedule, monthData],
  data() {
    return {
      shopList: [],
      month: dayjs('2022-11-05'),
      shopId: 191,

      showAddWorker: false
    }
  },
  async created() {
    await this.getShopList()
    this.month = dayjs('2022-11-05')
    await this.getSchedule()
    await this.getMonthData()
  },
  mounted() {

  },
  methods: {
    async reloadTable(shopId, month) {
      this.shopId = shopId
      this.month = month
      await this.getSchedule();
    },
    async getShopList() {
      let response = await this.$scheduleApi.get('get_shop_list')
      this.shopList = response.data
    },
    async addWorker(data) {
      console.log(data)
    },
    async editWorker(data) {
      console.log(data)
      await this.$scheduleApi.post('update_worker')
      await this.getSchedule()
    },
  }
}
</script>

<style scoped>
.container {
  padding: 5px
}
.select {
  width: 100%;
}
</style>