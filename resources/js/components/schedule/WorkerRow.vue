<template>
  <ShowRow v-if="!showEdit"
           v-on:showEdit="showEdit = true"
           :workerData="workerData"
  ></ShowRow>
  <EditRow v-else-if="showEdit && editable"
           v-on:showRow="showEdit = false"
           v-on:editWorker="$emit('editWorker', data)"
           :workerData="workerData"
           :month="month"
           :shopId="shopId"
  ></EditRow>
</template>

<script>
import ShowRow from "./ShowRow.vue";
import EditRow from "./EditRow.vue";

export default {
  name: "WorkerRow",
  components: {EditRow, ShowRow},
  props: ['workerData', 'month', 'shopId', 'editable'],
  emits: ['editWorker'],
  data() {
    return {
      showEdit: false,
      edited: []
    }
  },
  created() {
    this.edited = this.workerData.schedule
  }
}
</script>

<style scoped>

</style>