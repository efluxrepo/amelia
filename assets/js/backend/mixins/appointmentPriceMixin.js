export default {

  data () {
    return {}
  },

  methods: {
    getAppointmentPrice (bookings) {
      let totalBookings = 0

      bookings.forEach(function (booking) {
        booking.payments.forEach(function () {
          if (['approved', 'pending'].includes(booking.status)) {
            let extrasPriceTotal = 0

            booking.extras.forEach(function (extra) {
              if (typeof extra.selected === 'undefined' || extra.selected === true) {
                extrasPriceTotal += extra.price * extra.quantity * booking.persons
              }
            })

            let servicePriceTotal = booking.price * booking.persons
            let subTotal = servicePriceTotal + extrasPriceTotal
            let discountTotal = (subTotal / 100 * (booking.coupon ? booking.coupon.discount : 0)) + (booking.coupon ? booking.coupon.deduction : 0)

            totalBookings += subTotal - discountTotal
          }
        })
      })

      return this.getFormattedPrice(totalBookings >= 0 ? totalBookings : 0)
    }
  },

  watch: {}
}
