<script>
import { Bar } from 'vue-chartjs'

export default {
  extends: Bar,
  props: {
    chartdata: {
      type: Object,
      default: null
    },
    options: {
      type: {
        responsive: true,
      },
      default: null
    }
  },

  mounted () {
    this.renderChart(this.chartdata, this.options)
  }

}
</script>
