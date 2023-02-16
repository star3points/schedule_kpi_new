<template>
  <div class="form-container">
    <a-row>
      <a-col :span="24" class="form-container">
        <a-checkbox
            size="small"
            v-model:checked="newMonthData.monthClosed"
        >{{newMonthData.monthClosed ? 'Открыть месяц' : 'Закрыть месяц'}}
        </a-checkbox>
      </a-col>
      <a-col :span="9" class="form-container">
        Количество работников:
        <a-input-number
            size="small"
            v-model:value="newMonthData.workersQty"
        />
      </a-col>
      <a-col :span="9" class="form-container">
        План:
        <a-input-number
            size="small"
            style="width: 150px"
            :formatter="value => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ' ')"
            :parser="value => value.replace(/\$\s?|( *)/g, '')"
            v-model:value="newMonthData.salesPlan"
        />
      </a-col>
      <a-col :span="24" class="form-container">
        <a-textarea
            v-model:value="newMonthData.comment"
            v-bind:disabled="monthData.monthClosed && editable"
            placeholder="Коментарии к месяцу"
            :rows="4"
        />
      </a-col>
      <a-col :span="24" class="form-container">
        <a-button
            style="width: 100%"
            v-on:click="editMonthData"
        >
          Сохранить изменения
        </a-button>
      </a-col>
    </a-row>
  </div>
</template>

<script>
export default {
  name: "EditMonthData",
  props: ['shopId', 'month', 'monthData', 'editable'],
  emits: ['editMonthData'],
  data () {
    return {
      newMonthData: {
        monthClosed: true,
        salesPlan: 0,
        workersQty: 0
      }
    }
  },
  created() {
    this.newMonthData = this.monthData
  },
  methods: {
    editMonthData() {
      this.$emit('editMonthData', {
        shopId: this.shopId,
        month: this.month,
        monthData: this.newMonthData
      })
    }
  }
}
</script>

<style scoped>
.form-container {
  padding: 6px;
}
</style>