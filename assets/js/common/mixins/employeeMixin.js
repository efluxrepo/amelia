export default {
  data: () => ({}),

  methods: {
    getEmployeeActivityLabel (activity) {
      switch (activity) {
        case 'available':
          return this.$root.labels.available
        case 'away':
          return this.$root.labels.away
        case 'break':
          return this.$root.labels.break
        case 'busy':
          return this.$root.labels.busy
        case 'dayoff':
          return this.$root.labels.dayoff
      }
    }
  }
}
