<template>
  <div>
    <div class="am-success-payment">

      <!-- Congratulations -->
      <h4>{{ $root.labels.congratulations }}!</h4>

      <!-- Trophy -->
      <div class="am-svg-wrapper">
        <img class="svg am-congrats" :src="$root.getUrl+'public/img/trophy.svg'">
      </div>

      <!-- Messages -->
      <div>
        <p v-if="bookingApproved">{{ $root.labels.booking_completed_approved }}</p>
        <p v-if="bookingPending">{{ $root.labels.booking_completed_pending }}</p>
        <p v-if="showApprovedEmailMessage || showPendingEmailMessage">{{ $root.labels.booking_completed_email }}</p>
      </div>

      <br>

      <!-- Selectbox -->
      <el-row type="flex" justify="center" v-if="addToCalendarData.active">
        <el-col :sm="12">
          <el-select :placeholder="$root.labels.select_calendar" v-model="calendarIndex">
            <el-option
                v-for="(item, index) in calendars"
                :key="index"
                :label="item.label"
                :value="index">
            </el-option>
          </el-select>
        </el-col>
      </el-row>

      <br>

      <!-- Button -->
      <div :class="{'is-disabled' : calendarIndex === null}" class="el-button el-button--primary calendar-link"
           v-if="addToCalendarData.active">
        <a v-if="calendarIndex !== null"
           :href="(calendarIndex in calendars) ? calendars[calendarIndex].href : ''"
           :target="(calendarIndex in calendars) && (calendars[calendarIndex].label === 'iCal Calendar' || calendars[calendarIndex].label === 'Outlook Calendar') ? '_self' : '_blank'"
        >
          {{ $root.labels.add_to_calendar }}
        </a>
        <span v-else>
          {{ $root.labels.add_to_calendar }}
        </span>
      </div>

    </div>
  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import ouical from 'add-to-calendar-buttons'

  export default {

    mixins: [imageMixin],

    props: {
      addToCalendarData: null
    },

    data () {
      return {
        calendarIndex: null,
        calendars: []
      }
    },

    mounted () {
      // Customization hook
      if ('beforeAddToCalendarLoaded' in window) {
        window.beforeAddToCalendarLoaded(this.addToCalendarData)
      }

      if (this.addToCalendarData.active) {
        this.showCalendar()
      }

      this.inlineSVG()
    },

    methods: {
      showCalendar: !AMELIA_LITE_VERSION ? function () {
        let myCalendar = ouical.createCalendar({
          options: {
            class: 'my-class',
            id: 'my-id'
          },
          data: {
            title: this.addToCalendarData.title,
            start: this.addToCalendarData.start,
            duration: this.addToCalendarData.duration,
            address: this.addToCalendarData.address,
            description: this.addToCalendarData.description
          }
        })

        let calendars = []

        // eslint-disable-next-line no-undef
        let links = jQuery(myCalendar).find('a')

        for (let i = 0; i < links.length; i++) {
          if (links[i].className === 'icon-ical') {
            calendars.push({
              label: 'iCal Calendar',
              href: this.$root.getAjaxUrl + '/bookings/ics/' + this.addToCalendarData.responseData.appointment.id
            })
          } else if (links[i].className === 'icon-outlook') {
            calendars.push({
              label: 'Outlook Calendar',
              href: this.$root.getAjaxUrl + '/bookings/ics/' + this.addToCalendarData.responseData.appointment.id
            })
          } else {
            calendars.push({
              label: links[i].innerText,
              href: links[i].href
            })
          }
        }

        this.calendars = calendars
      } : function () {}
    },

    computed: {
      bookingsLength () {
        return this.addToCalendarData.responseData.appointment.bookings.length
      },

      bookingApproved () {
        return this.addToCalendarData.responseData.appointment.bookings[this.bookingsLength - 1].status === 'approved'
      },

      bookingPending () {
        return this.addToCalendarData.responseData.appointment.bookings[this.bookingsLength - 1].status === 'pending'
      },

      showApprovedEmailMessage () {
        return typeof this.addToCalendarData.responseData.emailError === 'undefined' &&
          this.bookingApproved === true && this.addToCalendarData.notificationsSettings.customerAppointmentApproved === 'enabled' &&
          this.$root.settings.notifications.notifyCustomers === true
      },

      showPendingEmailMessage () {
        return typeof this.addToCalendarData.responseData.emailError === 'undefined' &&
          this.bookingPending === true && this.addToCalendarData.notificationsSettings.customerAppointmentPending === 'enabled' &&
          this.$root.settings.notifications.notifyCustomers === true
      }
    }

  }
</script>