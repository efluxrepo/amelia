import { Bar } from 'vue-chartjs'

export default {

  extends: Bar,

  props: ['data'],

  data () {
    return {
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          xAxes: [{
            barPercentage: 0.5,
            categoryPercentage: 0.8,
            ticks: {
              stepSize: 1,
              min: 0,
              autoSkip: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: true
            },
            ticks: {
              beginAtZero: true,
              userCallback: function (label) {
                if (Math.floor(label) === label) {
                  return label
                }
              }
            }
          }]
        }
      }
    }
  },

  methods: {
    update () {
      this.$data._chart.update()
    }
  },

  mounted () {
    this.renderChart(this.data, this.options)
  }
}
