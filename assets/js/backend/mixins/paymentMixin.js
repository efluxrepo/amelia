export default {

  data: () => ({}),

  methods: {
    getPaymentData (paymentId, appointment) {
      let $this = this
      let selectedPaymentModalData = {}

      selectedPaymentModalData.paymentId = paymentId
      selectedPaymentModalData.appointment = appointment
      selectedPaymentModalData.service = this.getServiceById(selectedPaymentModalData.appointment.serviceId)
      selectedPaymentModalData.provider = this.getProviderById(selectedPaymentModalData.appointment.providerId)

      selectedPaymentModalData.appointment.bookings.forEach(function (bookItem) {
        bookItem.payments.forEach(function (payItem) {
          if (payItem.id === paymentId) {
            selectedPaymentModalData.customer = $this.getCustomerById(bookItem.customerId)
          }
        })
      })
      return selectedPaymentModalData
    }
  }

}
