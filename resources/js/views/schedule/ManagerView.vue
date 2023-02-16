<template>
  <a-card>
    <a-row>
      <a-col :span="12">
        <a-date-picker
            class="select"
            v-model:value="montn"
            picker="month"
        />
      </a-col>
      <a-col :span="12">

      </a-col>
    </a-row>
  </a-card>
</template>

<script>
export default {
  name: "ScheduleManager",
  mixins: ['getSchedule', 'monthData'],
  props: {

  },
  methods: {

  }
}
</script>

<style scoped>

</style>