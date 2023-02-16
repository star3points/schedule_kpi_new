<template>
  WorkerView
  <Worker
      :workerData="workerData"
  ></Worker>
</template>

<script>
import Worker from "../../components/kpi/Worker.vue";
import getWorkerMixin from "../../mixins/kpi/getWorkerMixin.vue";
import dateRangeMixin from "../../mixins/kpi/dateRangeMixin.vue";

export default {
  name: "WorkerView",
  components: [Worker],
  mixins: [getWorkerMixin, dateRangeMixin],
}
</script>

<style scoped>

</style>