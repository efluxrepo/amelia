import moment from 'moment'

export default {

  data () {
    return {}
  },

  methods: {

    getAppointmentDuration (service, extras) {
      return service.duration + extras.filter(extra => extra.selected).map(extra => extra.duration * extra.quantity).reduce((a, b) => a + b, 0)
    },

    getCurrentUser () {
      this.$http.get(`${this.$root.getAjaxUrl}/users/current`)
        .then(response => {
          this.currentUser = response.data.data.user

          if (this.currentUser) {
            this.appointment.bookings[0].customerId = this.currentUser.id
            this.appointment.bookings[0].customer.id = this.currentUser.id
            this.appointment.bookings[0].customer.externalId = this.currentUser.externalId
            this.appointment.bookings[0].customer.email = this.currentUser.email
            this.appointment.bookings[0].customer.firstName = this.currentUser.firstName
            this.appointment.bookings[0].customer.lastName = this.currentUser.lastName
            this.appointment.bookings[0].customer.phone = this.currentUser.phone || ''
          }
        })
        .catch(e => {
          console.log('getCurrentUser fail')
        })
    },

    getFormattedTimeSlot (slot, duration) {
      return this.getFrontedFormattedTime(slot) + ' - ' + moment(slot, 'HH:mm:ss').add(duration, 'seconds').format(this.momentTimeFormat)
    }

  }

}
