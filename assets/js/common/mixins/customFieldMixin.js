export default {
  data: () => ({}),

  methods: {
    isCustomFieldVisible (customField) {
      return customField.services.map(service => service.id).indexOf(this.appointment.serviceId) !== -1
    },

    setBookingCustomFields () {
      if (this.appointment.bookings.length) {
        // Go through all bookings
        for (let i = 0; i < this.appointment.bookings.length; i++) {
          // Go through all custom fields
          for (let j = 0; j < this.options.entities.customFields.length; j++) {
            // Add custom fields as empty object for backward compatibility
            if (this.appointment.bookings[i].customFields === null) {
              this.appointment.bookings[i].customFields = {}
            }

            // If custom field is not content and if custom field is not already set, add it in booking
            if (this.options.entities.customFields[j].type !== 'content' &&
              typeof this.appointment.bookings[i].customFields[this.options.entities.customFields[j].id] === 'undefined'
            ) {
              this.$set(
                this.appointment.bookings[i].customFields,
                this.options.entities.customFields[j].id,
                {
                  label: this.options.entities.customFields[j].label,
                  value: this.options.entities.customFields[j].type !== 'checkbox' ? '' : []
                }
              )
            }
          }
        }
      }
    },

    getCustomFieldOptions (customFieldOptions) {
      return customFieldOptions.map(option => option.label)
    }
  }
}
