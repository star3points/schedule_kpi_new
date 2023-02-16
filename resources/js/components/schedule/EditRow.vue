<template>
  <div class="container">
    <a-row>
      <a-col :span="24">
        <div class="cell cell-name">{{workerData.name}}</div>
        <div
            class="cell cell-num"
            v-for="(hours, day) in workerData.schedule"
        >
          <input class="input-cell"
                 v-model="edited[day]"
          >
        </div>
        <div class="cell cell-num">
          <div v-on:click="$emit('showRow')">
            <div class="img-container">
              <img src="../../../res/edit-icon.png" style="height: 100%"/>
            </div>
          </div>
        </div>
      </a-col>
    </a-row>
    <a-row>
      <div class="cell cell-name"></div>
      <div class="cell" style="width: 82%">
        <a-row justify="space-between">
          <a-col :span="7" class="button-container">
            <a-button
                size="small"
                style="width: 100%"
                v-on:click="editWorker"
                danger
            >Уадалить</a-button>
          </a-col>
          <a-col :span="7" class="button-container">
            <a-button
                size="small"
                style="width: 100%"
                v-on:click="cancelEdit"
            >Отменить изменения</a-button>
          </a-col>
          <a-col :span="7" class="button-container">
            <a-button
                size="small"
                style="width: 100%"
                v-on:click="editWorker"
                type="primary"
            >Сохранить изменения</a-button>
          </a-col>
        </a-row>
      </div>
    </a-row>
  </div>
</template>

<script>
export default {
  name: "EditRow",
  props: ['workerData', 'month', 'shopId'],
  emits: ['showRow', 'editWorker'],
  data() {
    return {
      edited: {}
    }
  },
  created() {
    this.setEdited()
  },
  methods: {
    setEdited() {
      this.edited = {...this.workerData.schedule}
    },
    cancelEdit() {
      this.setEdited()
      this.$emit('showRow')
    },
    async editWorker() {
      console.log(
          {
            shop_id: this.shopId,
            worker_id: this.workerData.id,
            schedule: this.edited,
            month: this.month.format('YYYY-MM-DD')
          }
      )
      await this.$scheduleApi.post('update_worker', {
        worker_id: this.workerData.id,
        schedule: this.edited,
        month: this.month.format('YYYY-MM-DD')
      })
      this.$emit('editWorker')
    },
    inputNum(event, day) {
      this.edited[day] = event
    }
  }
}
</script>

<style scoped>
.container {
  margin-top: 3px;
  margin-bottom: 5px;
}
.cell {
  display: inline-block;
}
.cell-name {
  width: 14%;
}
.cell-num {
  font-size: 13px;
  text-align: center;
  vertical-align: center;
  width: 2.7%;
  height: 100%;
}
.input-cell {
  width: 100%;
  padding: 1px;
  font-size: 13px;
  text-align: center;
  border: solid 1px white;
  border-radius: 6px;
  outline: none;
}
.input-cell:focus {
  border: solid 1px #1890ff;
  box-shadow: 0 0 3px #1890ff;
}
.button-container {
  padding: 3px;
}
.img-container {
  height: 24px;
  padding: 2px 2px 6px;
}
</style>