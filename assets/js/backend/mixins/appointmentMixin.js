export default {

  data () {
    return {
      appointment: null,
      bookings: [],
      duplicateEvent: false,
      exportAction: '',
      exportParams: {
        fields: [
          {label: this.$root.labels.customers, value: 'customers', checked: true},
          {label: this.$root.labels.employee, value: 'employee', checked: true},
          {label: this.$root.labels.service, value: 'service', checked: true},
          {label: this.$root.labels.start_time, value: 'startTime', checked: true},
          {label: this.$root.labels.end_time, value: 'endTime', checked: true},
          {label: this.$root.labels.note, value: 'note', checked: true},
          {label: this.$root.labels.status, value: 'status', checked: true}
        ]
      },
      savedAppointment: null,
      statuses: [
        {
          value: 'approved',
          label: this.$root.labels.approved

        }, {
          value: 'pending',
          label: this.$root.labels.pending

        },
        {
          value: 'canceled',
          label: this.$root.labels.canceled

        },
        {
          value: 'rejected',
          label: this.$root.labels.rejected

        }
      ],
      options: {
        fetched: false,
        entities: {
          categories: [],
          customers: [],
          customFields: [],
          employees: [],
          locations: [],
          services: []
        },
        entitiesChoices: {}
      }
    }
  },

  methods: {
    getInitAppointmentObject () {
      return {
        id: 0,
        bookings: [],
        categoryId: '',
        serviceId: '',
        providerId: '',
        locationId: '',
        selectedDate: null,
        selectedPeriod: '',
        status: this.$root.settings.general.defaultAppointmentStatus,
        internalNotes: '',
        notifyParticipants: this.$root.settings.notifications.notifyCustomers,
        dateTimeSlots: [],
        calendarTimeSlots: [],
        extrasTotalPrice: 0,
        serviceTotalPrice: 0,
        discountTotalPrice: 0,
        providerServiceMinCapacity: 0,
        providerServiceMaxCapacity: 0,
        extrasCount: 0,
        extrasSelectedCount: 0
      }
    },

    showDialogNewAppointment () {
      this.resetEntitiesFilter()
      this.setBookings(0)
      this.savedAppointment = null
      this.appointment = this.getInitAppointmentObject()
      this.dialogAppointment = true
    },

    showDialogEditAppointment (id) {
      this.dialogAppointment = true
      this.resetEntitiesFilter()
      this.getAppointment(id)
    },

    closeDialogAppointment (duplicateEvent = false) {
      this.duplicateEvent = duplicateEvent
      this.dialogAppointment = false
    },

    setBookings (appointmentId) {
      let bookings = []
      let $this = this

      this.options.entities.customers.forEach(function (cusItem) {
        if (cusItem.status === 'visible') {
          let bookingId = 0
          let extras = []
          let payments = []
          let coupon = null
          let price = 0
          let persons = 1
          let info = JSON.stringify({
            firstName: cusItem.firstName,
            lastName: cusItem.lastName,
            email: cusItem.email,
            phone: cusItem.phone
          })

          if ($this.appointment && appointmentId) {
            $this.appointment.bookings.forEach(function (bookItem) {
              if (bookItem.customerId === cusItem.id) {
                bookingId = bookItem.id
                extras = bookItem.extras
                payments = bookItem.payments
                price = bookItem.price
                persons = bookItem.persons
                coupon = bookItem.coupon
                info = bookItem.info
              }
            })
          }

          bookings.push({
            id: bookingId,
            customer: cusItem,
            status: $this.$root.settings.general.defaultAppointmentStatus,
            persons: persons,
            total: 0,
            extras: extras,
            payments: payments,
            price: price,
            coupon: coupon,
            added: false,
            info: info,
            customFields: {}
          })
        }
      })

      this.bookings = bookings
    },

    getAppointment (id) {
      this.$http.get(`${this.$root.getAjaxUrl}/appointments/` + id)
        .then(response => {
          let $this = this
          this.savedAppointment = JSON.parse(JSON.stringify(response.data.data.appointment))

          this.appointment = Object.assign(this.getInitAppointmentObject(), response.data.data.appointment)
          this.appointment.notifyParticipants = !!this.appointment.notifyParticipants

          this.appointment.bookings.forEach(function (bookItem) {
            let serviceExtras = null

            $this.options.entities.services.forEach(function (serItem) {
              if (serItem.id === $this.appointment.serviceId) {
                serviceExtras = JSON.parse(JSON.stringify(serItem.extras))

                serviceExtras.forEach(function (serExtItem) {
                  serExtItem.quantity = 1
                  serExtItem.selected = false
                })
              }
            })

            bookItem.customer = null
            bookItem.added = false

            $this.options.entities.customers.forEach(function (cusItem) {
              if (cusItem.id === bookItem.customerId) {
                bookItem.customer = cusItem

                let customerBookingInfo = $this.getCustomerInfo(bookItem)

                if (bookItem.id !== 0 && customerBookingInfo) {
                  bookItem.info = JSON.stringify({
                    firstName: customerBookingInfo.firstName,
                    lastName: customerBookingInfo.lastName,
                    email: customerBookingInfo.email,
                    phone: customerBookingInfo.phone
                  })
                }

                bookItem.added = true
              }
            })

            bookItem.extras.forEach(function (bookExtItem) {
              serviceExtras.forEach(function (serExtItem) {
                if (serExtItem.extraId === bookExtItem.extraId) {
                  serExtItem.id = bookExtItem.id
                  serExtItem.selected = true
                  serExtItem.quantity = bookExtItem.quantity ? bookExtItem.quantity : 1
                  serExtItem.price = bookExtItem.price
                }
              })
            })

            serviceExtras.forEach(function (serExtItem) {
              if (!serExtItem.selected) {
                serExtItem.id = 0
              }
            })

            bookItem.extras = serviceExtras
            bookItem.customFields = JSON.parse(bookItem.customFields)
          })

          this.setBookings(id)
        })
        .catch(e => {
          console.log(e.message)
        })
    },

    sortBookings (bookings) {
      bookings.sort(function (a, b) {
        return (a.customer.firstName + ' ' + a.customer.lastName).localeCompare((b.customer.firstName + ' ' + b.customer.lastName))
      })
    },

    duplicateAppointmentCallback (appointment) {
      this.appointment = appointment
      this.appointment.id = 0
      this.appointment.selectedDate = null
      this.appointment.selectedPeriod = ''
      this.appointment.dateTimeSlots = []
      this.appointment.calendarTimeSlots = []
      setTimeout(() => {
        this.dialogAppointment = true
      }, 300)
    },

    getCustomersFromGroup (appointment) {
      let customers = ''
      let $this = this
      appointment.bookings.forEach(function (book) {
        if ($this.options.entities.customers.length) {
          let cus = $this.getCustomerById(book.customerId)

          customers += '<span class="am-appointment-status-symbol ' + book.status + '"></span><span>' + cus.firstName + ' ' + cus.lastName + '</span><br>'
        }
      })

      return customers
    },

    saveCustomerCallback (response) {
      this.options.entities.customers.push(response.user)

      let booking = {
        id: 0,
        customer: response.user,
        status: this.$root.settings.general.defaultAppointmentStatus,
        persons: 1,
        total: 0,
        extras: [],
        payments: [],
        coupon: null,
        info: JSON.stringify({
          firstName: response.user.firstName,
          lastName: response.user.lastName,
          email: response.user.email,
          phone: response.user.phone
        }),
        added: true
      }

      this.bookings.push(booking)
      this.sortBookings(this.bookings)

      if (this.appointment !== null) {
        this.appointment.bookings.push(booking)
        this.sortBookings(this.appointment.bookings)
      }
    },

    updateAppointmentStatus (app, status, updateCount) {
      this.updateStatusDisabled = true

      this.form.post(`${this.$root.getAjaxUrl}/appointments/status/${app.id}`, {
        'status': status
      })
        .then(response => {
          if (updateCount) {
            this.setTotalStatusCounts(app, status, response.data.status)
          }

          this.notify(
            (app.status === response.data.status) ? this.$root.labels.success : this.$root.labels.error,
            response.data.message,
            (app.status === response.data.status) ? 'success' : 'error'
          )

          app.status = response.data.status
          this.updateStatusDisabled = false
        })
        .catch(e => {
          this.errorMessage = e.message
          this.updateStatusDisabled = false
        })
    },

    getAppointmentPaymentMethods (bookings) {
      let methods = []
      bookings.forEach(function (booking) {
        let method = booking.payments.length ? booking.payments[0].gateway : null
        if (methods.indexOf(method) === -1) {
          methods.push(method)
        }
      })

      return methods
    }
  },

  watch: {
    'dialogAppointment' () {
      if (this.dialogAppointment === false && this.duplicateEvent === false) {
        this.appointment = null
      }
    }
  }
}
