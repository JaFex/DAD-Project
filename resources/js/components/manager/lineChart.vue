<script>
import { Line } from 'vue-chartjs'

export default {
  extends: Line,
  data() {
    return {
      datacollection: {
        labels: ['Mercury', 'Venus', 'Earth', 'Mars', 'Jupiter', 'Saturn', 'Uranus', 'Neptune'],
        datasets: [
          {
            label: 'Number of Moons',
            data: [0, 0, 1, 2, 67, 62, 27, 14],
            backgroundColor: [
              'rgba(44,168,30,.5)',
            ],
          }
        ]
      },
      options: {
        maintainAspectRatio: true,
      }
    }
  },

  mounted () {
    this.renderChart(this.datacollection, this.options)
  }
}
</script>
