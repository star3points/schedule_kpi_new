<template>
  <div class="table-card">
    <a-row>
      <a-col class="table-row" :span="24">
        <div class="cell cell-name">ФИО сотрудника</div>
        <div class="cell cell-num" v-for="day in month?.daysInMonth() ?? 31">
          {{day}}
        </div>
      </a-col>
    </a-row>
    <a-row>
      <a-col class="table-row" :span="24">
        <div class="cell cell-name">В смене</div>
        <div class="cell cell-num"
             v-for="result in tableData.workers_per_day"
        >
          {{result}}
        </div>
      </a-col>
    </a-row>
    <RoleBlock
        v-for="data in tableData.table"
        :month="month"
        :shopId="shopId"
        :data="data"
        :editable="editable"
    ></RoleBlock>
  </div>
</template>

<script>
import RoleBlock from "./RoleBlock.vue";
export default {
  name: "ScheduleTable",
  components: {RoleBlock},
  props: ['shopId', 'month', 'role', 'editable', 'tableData'],
  emits: ['editWorker'],

}
</script>

<style scoped>
.table-card {
  padding: 5px;
}
.form-container {
  margin-top: 5px;
  padding: 5px;
}
.cell {
  display: inline-block;
}
.cell-name {
  width: 14%;
}
.cell-num {
  font-size: 12px;
  text-align: center;
  vertical-align: center;
  width: 2.7%;
  height: 100%;
}
.green {
  background-color: rgba(200, 255, 220);
}
.red {
  background-color: rgba(255, 200, 200);
}
</style>