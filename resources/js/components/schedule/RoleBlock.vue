<template>
  <a-row>
    <a-col :span="24">
      <a-typography-title :level="5">
        {{data.role.name}}
      </a-typography-title>
    </a-col>
  </a-row>
  <WorkerRow
      v-for="worker in data.workers"
      :workerData="worker"
      :month="month"
      :shopId="shopId"
      :editable="editable"
  ></WorkerRow>
</template>

<script>
import WorkerRow from "./WorkerRow.vue";

export default {
  name: "RoleBlock",
  components: { WorkerRow },
  props: ['data', 'month', 'shopId', 'editable'],
  emits: ['editWorker'],
  data() {
    return {}
  }
}
</script>

<style scoped>

</style>