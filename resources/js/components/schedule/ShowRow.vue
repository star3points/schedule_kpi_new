<template>
  <a-row>
    <a-col :span="24">
      <div class="cell cell-name">{{workerData.name}}</div>
      <div
          class="cell cell-num"
          v-for="(hours, day) in workerData.schedule"
          :class="{green : hours > 0, red: hours == 0}"
      >
        {{hours}}
      </div>
      <div class="cell cell-num">
        <div v-on:click="$emit('showEdit')">
          <div class="img-container">
            <img src="../../../res/edit-icon.png" style="height: 100%"/>
          </div>
        </div>
      </div>
    </a-col>
  </a-row>
</template>

<script>
import editOutlined from "@ant-design/icons-vue/lib/icons/EditOutlined";
export default {
  name: "ShowRow",
  props: ['workerData'],
  emits: ['showEdit'],
  components: [editOutlined],
  methods: {
    // showEdit() {
    //   this.$emit.showEdit()
    // }
  }
}
</script>

<style scoped>
.table-card {
  padding: 8px;
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
  height: auto;
}
.green {
  background-color: rgba(200, 255, 220);
}
.red {
  background-color: rgba(255, 200, 200);
}
.img-container {
  height: 24px;
  padding: 2px 2px 6px;
}
</style>