<template>
  <a-table
      :columns="shopsData.columns"
      :data-source="shopsData.table"
  ></a-table>
</template>

<script>
export default {
  name: "Shops",
  props: ['shopsData'],
  emits: ['toShop'],
  // watch: {
  //   shopsData: {
  //     handler() {
  //       this.prepareTable()
  //     }
  //   }
  // },
  // data() {
  //   return {
  //     table: {
  //       columns: [],
  //       data: []
  //     }
  //   }
  // },
  created() {
    // this.prepareTable()
  },
  methods: {
    // prepareTable() {
    //   this.shopsData.forEach(function (shop, index) {
    //     if (index === 0) {
    //       Object.keys(shop).forEach(function (column) {
    //         this.columns.push({
    //           title: column,
    //           dataIndex: column,
    //           key: column,
    //         });
    //       })
    //     }
    //   })
    // }
  }
}
</script>

<style scoped>

</style>