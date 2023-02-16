<script>
import dayjs from "dayjs";
export default {
  name: "dateRangeMixin",
  data() {
    return {
      selectedDateRange: []
    }
  },
  methods: {
    setDefaultDateRange() {
      // let now = dayjs();
      // this.selectedDateRange = [
      //     now.startOf('month'),
      //     now.endOf('month')
      // ]

      // test
      let date = dayjs('2022-12-05');
      this.selectedDateRange = [
        date.startOf('month'),
        date.endOf('month')
      ]
    },
    getFormattedDateRange(format = 'YYYY-MM-DD') {
      return {
        date_from: this.selectedDateRange[0].format(format),
        date_to: this.selectedDateRange[1].format(format)
      }
    }
  }
}
</script>