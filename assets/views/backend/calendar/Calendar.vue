<template>
  <div class="am-wrap">
    <div id="am-calendar" class="am-body">

      <!-- Page Header -->
      <page-header
          :categories="options.entities.categories"
          :locations="options.entities.locations"
          :params="params"
          :fetched="fetched"
          @newAppointmentBtnClicked="showDialogNewAppointment"
          @changeFilter="filterData"
          @selectAllInCategory="selectAllInCategory"
      >
      </page-header>

      <div class="am-section am-calendar">

        <!-- Employees Filter -->
        <div v-if="$root.settings.capabilities.canReadOthers === true" class="am-calendar-employees">
          <div class="am-calendar-employee" :class="{ active: params.providers.length === 0 }"
               @click="filterAllEmployees">
            <div class="am-profile-photo">
              <div class="am-all">{{ $root.labels.all }}</div>
            </div>
            <p>{{ $root.labels.all_employees }}</p>
          </div>

          <div v-for="employee in options.entities.employees"
               :key="employee.id"
               class="am-calendar-employee"
               :class="{ active: params.providers.indexOf(employee.id) !== -1 }"
               @click="filterEmployees(employee)"
          >
            <div class="am-profile-photo">
              <img :src="pictureLoad(employee, true)" @error="imageLoadError(employee, true)">
            </div>
            <p class="am-calendar-employee-name">{{ employee.firstName + ' ' + employee.lastName }}</p>
          </div>
        </div>

        <!-- Calendar -->
        <div class="am-calendar-scroll">

          <v-date-picker
              :locale="$root.locale"
              @input="selectDay"
              v-model="selectedDate"
              mode="single"
              id="am-calendar-picker"
              class='am-calendar-picker'
              tint-color='#1A84EE'
              :show-day-popover=false
              :is-expanded=false
              :is-inline=true
              :is-required=true
              :formats="vCalendarFormats"
          >
          </v-date-picker>

          <full-calendar
              :class="{'am-lite-calendar': this.$root.isLite}"
              ref="calendar"
              :events="events"
              :config="config"
              @event-selected="eventSelected"
              @event-render="eventRender"
              @event-drop="eventDrop"
          >
          </full-calendar>

          <!-- Content Spinner -->
          <div class="am-spinner am-section" v-show="fetched && !fetchedFiltered">
            <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
          </div>

        </div>

      </div>

      <!-- Button New -->
      <div v-if="canWriteAppointments()" id="am-button-new" class="am-button-new">
        <el-button id="am-plus-symbol" type="primary" icon="el-icon-plus"
                   @click="showDialogNewAppointment()"></el-button>
      </div>

      <!-- Dialog New Appointment -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog"
            :visible.sync="dialogAppointment"
            :show-close="false"
            v-if="dialogAppointment"
        >
          <dialog-appointment
              :appointment="appointment"
              :bookings="bookings"
              :entitiesChoices="options.entitiesChoices"
              :options="options"
              @sortBookings="sortBookings"
              @saveCallback="getCalendarOptions"
              @duplicateCallback="duplicateAppointmentCallback"
              @closeDialog="closeDialogAppointment()"
              @showDialogNewCustomer="showDialogNewCustomer"
              @editPayment="editPayment"
          >
          </dialog-appointment>
        </el-dialog>
      </transition>

      <!-- Dialog New Customer -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog"
            size="full"
            :visible.sync="dialogCustomer"
            :show-close="false"
            v-if="dialogCustomer">
          <dialog-customer
              :customer="customer"
              @saveCallback="saveCustomerCallback"
              @closeDialog="dialogCustomer = false"
          ></dialog-customer>
        </el-dialog>
      </transition>

      <!-- Dialog Payment -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-coupon"
            :visible.sync="dialogPayment"
            :show-close="false"
            v-if="dialogPayment"
        >
          <dialog-payment
              :modalData="selectedPaymentModalData"
              :appointmentFetched=true
              @closeDialogPayment="dialogPayment = false"
          >
          </dialog-payment>
        </el-dialog>
      </transition>

      <DialogLite/>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/admin-calendar/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import PageHeader from '../parts/PageHeader.vue'
  import moment from 'moment'
  import liteMixin from '../../../js/common/mixins/liteMixin'
  import appointmentMixin from '../../../js/backend/mixins/appointmentMixin'
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'
  import paymentMixin from '../../../js/backend/mixins/paymentMixin'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import { FullCalendar } from 'vue-full-calendar'
  import 'fullcalendar-scheduler'
  import DialogAppointment from '../appointments/DialogAppointment.vue'
  import DialogCustomer from '../customers/DialogCustomer.vue'
  import DialogPayment from '../finance/DialogFinancePayment.vue'
  import Form from 'form-object'
  import customerMixin from '../../../js/backend/mixins/customerMixin'

  export default {

    mixins: [liteMixin, paymentMixin, entitiesMixin, appointmentMixin, imageMixin, dateMixin, durationMixin, notifyMixin, customerMixin],

    data () {
      return {
        customer: null,
        config: {
          locale: this.$root.locale,
          buttonText: {
            today: this.$root.labels.today,
            month: this.$root.labels.month,
            week: this.$root.labels.week,
            day: this.$root.labels.day,
            list: this.$root.labels.list,
            timelineDay: this.$root.labels.timelineDay,
          },
          allDaySlot: false,
          businessHours: {},
          defaultView: 'agendaWeek',
          displayEventEnd: true,
          displayEventTime: true,
          editable: true,
          eventDurationEditable: false,
          eventLimit: 2,
          eventLimitClick: 'day',
          filterResourcesWithEvents: true,
          firstDay: this.$root.settings.wordpress.startOfWeek,
          header: {
            left: 'prev, today, next',
            center: 'title',
            right: 'month, agendaWeek, day, listWeek, timelineDay'
          },
          listDayAltFormat: '',
          noEventsMessage: this.$root.labels.no_appointments_to_display,
          nowIndicator: this.$root.settings.role !== 'customer',
          resources: [],
          resourceLabelText: this.$root.labels.employees,
          schedulerLicenseKey: '0395133833-fcs-1528299690',
          selectable: false,
          slotEventOverlap: false,
          slotDuration: this.secondsToTimeSelectStep(this.$root.settings.general.timeSlotLength),
          slotLabelFormat: '',
          timeFormat: '',
          viewRender: this.callViewRender,
          views: {
            month: {},
            week: {
              columnFormat: ''
            },
            day: {
              titleFormat: '',
              type: 'agenda',
              duration: {days: 1},
              buttonText: 'day'
            },
            timelineDay: {
              type: 'timelineDay',
              buttonText: 'timeline',
              displayEventTime: false,
              slotLabelFormat: ''
            }
          }
        },
        dialogAppointment: false,
        dialogPayment: false,
        events: [],
        fetched: false,
        fetchedFiltered: false,
        form: new Form(),
        params: {
          dates: [],
          locations: [],
          providers: [],
          services: []
        },
        selectedDate: moment().toDate(),
        selectedPaymentModalData: null
      }
    },

    created () {
      this.config.slotLabelFormat = this.config.timeFormat = this.config.views.timelineDay.slotLabelFormat = this.momentTimeFormat
      this.config.listDayAltFormat = this.momentDateFormat
      this.config.views.week.columnFormat = 'ddd ' + this.momentDateFormat
        .replace(/Y/g, '').replace(/y/g, '').replace(/^([^a-zA-Z0-9])*/g, '').replace(/([^a-zA-Z0-9])*$/g, '')
    },

    mounted () {
      this.getCalendarOptions()
      let view = this.$refs.calendar.fireMethod('getView')
      this.params.dates = [view.start.format('YYYY-MM-DD'), view.end.format('YYYY-MM-DD')]
      this.initVCalendar()
    },

    methods: {
      showDialogNewCustomer () {
        this.customer = this.getInitCustomerObject()
        this.dialogCustomer = true
      },

      editPayment (obj) {
        this.selectedPaymentModalData = this.getPaymentData(obj.paymentId, this.savedAppointment)
        this.dialogPayment = true
      },

      getCalendarOptions () {
        this.options.fetched = false

        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: !AMELIA_LITE_VERSION ? ['categories', 'locations', 'employees', 'customers', 'custom_fields'] : ['categories', 'employees', 'customers']
          }
        })
          .then(response => {
            this.filterResponseData(response)

            this.options.entities = response.data.data

            this.options.entities.services = this.getServicesFromCategories()

            this.options.entities.services.forEach(function (serItem) {
              serItem.extras.forEach(function (serExtItem) {
                serExtItem.extraId = serExtItem.id
              })
            })

            this.setBookings(0)
            this.setEntitiesFilter()

            this.fetched = true
            this.options.fetched = true

            this.createResourcesForScheduler(this.options.entities.employees)

            this.getCalendar()
          })
          .catch(e => {
            console.log('getCalendarOptions fail')
            this.options.fetched = true
          })
      },

      getCalendar () {
        this.events = []
        this.fetchedFiltered = false

        Object.keys(this.params).forEach((key) => (!this.params[key] && this.params[key] !== 0) && delete this.params[key])

        this.$http.get(`${this.$root.getAjaxUrl}/appointments`, {
          params: this.params
        })
          .then(response => {
            let that = this
            let appointmentDays = response.data.data.appointments

            Object.keys(appointmentDays).forEach(function (appointmentDay) {
              for (let i = 0; i < appointmentDays[appointmentDay].appointments.length; i++) {
                // Do not render appointments with rejected and canceled status
                if (['rejected', 'canceled'].indexOf(appointmentDays[appointmentDay].appointments[i].status) === -1) {
                  // If customer is logged in don't render appointments where his customer booking has status rejected or canceled
                  if (that.$root.settings.role !== 'customer' || (that.$root.settings.role === 'customer' && ['rejected', 'canceled'].indexOf(appointmentDays[appointmentDay].appointments[i].bookings[0].status) === -1)) {
                    let appointment = appointmentDays[appointmentDay].appointments[i]
                    let service = that.getServiceById(appointment.serviceId)
                    let customer = that.getCustomerInfo(appointment.bookings[0])
                    let employee = that.getProviderById(appointment.providerId)

                    if (!employee) {
                      continue
                    }

                    let location = that.getLocationById(employee.locationId)

                    that.events.push({
                      id: appointment.id,
                      title: service.name,
                      start: moment(appointment.bookingStart, 'YYYY-MM-DD HH:mm').format(),
                      end: moment(appointment.bookingEnd, 'YYYY-MM-DD HH:mm').format(),
                      color: that.shadeColor(service.color, 0.7),
                      borderColor: service.color,
                      customer: appointment.bookings.length > 1 ? that.$root.labels.group_booking : customer.firstName + ' ' + customer.lastName,
                      employee: employee.firstName + ' ' + employee.lastName,
                      employeeId: employee.id,
                      employeePhotoUrl: that.pictureLoad(employee, true),
                      location: location ? location.address : '',
                      status: appointment.status,
                      eventOverlap: false,
                      resourceId: employee.id.toString(),
                      editable: moment().diff(moment(appointment.bookingStart, 'YYYY-MM-DD HH:mm'), 'seconds') < 0 && that.$root.settings.role !== 'customer' && that.canWriteAppointments() && !appointment.past,
                      past: appointment.past
                    })
                  }
                }
              }
            })

            this.setGlobalBusinessHours()

            this.fetched = true
            this.fetchedFiltered = true
          })
          .catch(e => {
            this.fetched = true
            this.fetchedFiltered = true
          })
      },

      callViewRender (view) {
        let that = this

        if (that.params.dates.length > 0) {
          let start = view.start.format('YYYY-MM-DD')
          let end = view.end.format('YYYY-MM-DD')

          if (
            !moment(start).isBetween(this.params.dates[0], this.params.dates[1], null, '[]') ||
            !moment(end).isBetween(this.params.dates[0], this.params.dates[1], null, '[]')
          ) {
            this.params.dates = [start, end]
            this.filterData()
          }
        }
      },

      filterData () {
        this.getCalendar()
      },

      eventSelected (event) {
        if (!this.canWriteAppointments() || this.$root.settings.role === 'customer' || event.past) {
          return
        }

        this.showDialogEditAppointment(event.id)
      },

      eventRender (event, element, view) {
        if (view.name !== 'timelineDay') {
          element.find('.fc-content').append('<span class="am-calendar-customer">' + event.customer + '</span>')

          element.find('.fc-content').append('<div class="flexed-between"><span class="am-calendar-employee"><img src="' + event.employeePhotoUrl + '">' +
            event.employee +
            '</span><span class="am-calendar-status ' + event.status + '"></span></div>')
        }

        if (view.name !== 'listWeek') {
          let tooltip = '<div class="am-tooltip el-tooltip__popper is-light">' +
            '<span class="am-tooltip-color" style="background-color:' + event.borderColor + ';"></span> ' +
            '<span class="am-calendar-status ' + event.status + '"></span>' +
            '<h4>' + event.customer + '</h4>' +
            '<p>' + event.start.format(this.momentTimeFormat) + ' - ' + event.end.format(this.momentTimeFormat) + '</p>' +
            '<p>' + event.title + '</p>'

          let button = (this.$root.settings.role === 'customer' || !this.canWriteAppointments() || event.past) ? '' : '<button type="button" class="el-button el-button--default"><span>' + this.$root.labels.edit + '</span></button>'

          tooltip += (typeof event.location !== 'undefined' && event.location !== '') ? '<p icon="el-icon-location" class="am-tooltip-address">' + event.location + '</p>' : ''

          tooltip += '<div class="flexed-between"><h4><img src="' + event.employeePhotoUrl + '">' + event.employee + '</h4>' + button + '</div>' + '</div>'

          element.append(tooltip)
        }
      },

      eventDrop (event, delta, revertFunc) {
        let draggedEvent = event
        let employee = this.getProviderById(draggedEvent.employeeId)
        let dayIndex = draggedEvent.start.isoWeekday()
        let workingHoursForDayIndex = employee.weekDayList.find(day => day.dayIndex === dayIndex)

        // Check if event is dropped in past
        let droppedInPast = moment().diff(moment(draggedEvent.start.format('YYYY-MM-DD HH:mm')), 'seconds') >= 0

        // Check if event is dropped in the employee's working hours
        let droppedInWorkingHours =
          typeof workingHoursForDayIndex !== 'undefined' &&
          (
            moment(draggedEvent.start.format('HH:mm:ss'), 'HH:mm:ss').isBetween(moment(workingHoursForDayIndex.startTime, 'HH:mm:ss'), moment(workingHoursForDayIndex.endTime, 'HH:mm:ss'), null, '[)') &&
            moment(draggedEvent.end.format('HH:mm:ss'), 'HH:mm:ss').isBetween(moment(workingHoursForDayIndex.startTime, 'HH:mm:ss'), moment(workingHoursForDayIndex.endTime, 'HH:mm:ss'), null, '(]')
          )

        // Check if event is dropped outside of the employee's break
        let droppedInBreak = false
        if (typeof workingHoursForDayIndex !== 'undefined') {
          for (let i = 0; i < workingHoursForDayIndex.timeOutList.length; i++) {
            if (
              (
                moment(draggedEvent.start.format('HH:mm:ss'), 'HH:mm:ss').isBetween(moment(workingHoursForDayIndex.timeOutList[i].startTime, 'HH:mm:ss'), moment(workingHoursForDayIndex.timeOutList[i].endTime, 'HH:mm:ss'), null, '[)') ||
                moment(draggedEvent.end.format('HH:mm:ss'), 'HH:mm:ss').isBetween(moment(workingHoursForDayIndex.timeOutList[i].startTime, 'HH:mm:ss'), moment(workingHoursForDayIndex.timeOutList[i].endTime, 'HH:mm:ss'), null, '(]')
              ) ||
              (
                moment(workingHoursForDayIndex.timeOutList[i].startTime, 'HH:mm:ss').isBetween(moment(draggedEvent.start.format('HH:mm:ss'), 'HH:mm:ss'), moment(draggedEvent.end.format('HH:mm:ss'), 'HH:mm:ss'), null, '[)') ||
                moment(workingHoursForDayIndex.timeOutList[i].endTime, 'HH:mm:ss').isBetween(moment(draggedEvent.start.format('HH:mm:ss'), 'HH:mm:ss'), moment(draggedEvent.end.format('HH:mm:ss'), 'HH:mm:ss'), null, '(]')
              )
            ) {
              droppedInBreak = true
            }
          }
        }

        // Check if there is already an appointment in the dropped time for this provider
        let eventInDroppedTime = this.$refs.calendar.fireMethod('clientEvents', function (event) {
          return event.employeeId === draggedEvent.employeeId &&
            draggedEvent.id !== event.id &&
            ((moment(draggedEvent.start.format('YYYY-MM-DD HH:mm')).isBetween(event.start.format('YYYY-MM-DD HH:mm'), event.end.format('YYYY-MM-DD HH:mm'), null, '[)') || moment(draggedEvent.end.format('YYYY-MM-DD HH:mm')).isBetween(event.start.format('YYYY-MM-DD HH:mm'), event.end.format('YYYY-MM-DD HH:mm'), null, '(]')) ||
              (moment(event.start.format('YYYY-MM-DD HH:mm')).isBetween(draggedEvent.start.format('YYYY-MM-DD HH:mm'), draggedEvent.end.format('YYYY-MM-DD HH:mm'), null, '[)') || moment(event.end.format('YYYY-MM-DD HH:mm')).isBetween(draggedEvent.start.format('YYYY-MM-DD HH:mm'), draggedEvent.end.format('YYYY-MM-DD HH:mm'), null, '(]'))
            )
        })

        // If one of the conditions is not satisfied revert event on the past position
        if (!this.canWriteAppointments() || droppedInPast || !droppedInWorkingHours || droppedInBreak || eventInDroppedTime.length !== 0) {
          if (droppedInPast) {
            var message = this.$root.labels.appointment_drag_past
          } else if (!droppedInWorkingHours) {
            message = this.$root.labels.appointment_drag_working_hours
          } else if (droppedInBreak) {
            message = this.$root.labels.appointment_drag_breaks
          } else {
            message = this.$root.labels.appointment_drag_exist
          }

          this.notify(this.$root.labels.error, message, 'error')
          revertFunc()
        } else {
          let eventStartBeforeDrag = draggedEvent.start.clone()
          eventStartBeforeDrag.subtract(delta)

          // Confirmation modal and ajax request
          this.$confirm(
            this.$root.labels.appointment_change_time + '<br>' +
            '<div class="am-old-time">' +
            '<div><i class="el-icon-date"></i> ' + this.getFrontedFormattedDate(eventStartBeforeDrag) + '</div>' +
            '<div><i class="el-icon-time"></i> ' + this.getFrontedFormattedTime(eventStartBeforeDrag) + '</div>' +
            '</div>' +
            '<div class="am-new-time">' +
            '<div><i class="el-icon-date"></i> ' + this.getFrontedFormattedDate(draggedEvent.start) + '</div>' +
            '<div><i class="el-icon-time"></i> ' + this.getFrontedFormattedTime(draggedEvent.start) + '</div>' +
            '</div>', 'Warning', {
              confirmButtonText: this.$root.labels.confirm,
              cancelButtonText: this.$root.labels.cancel,
              type: 'warning',
              center: true,
              dangerouslyUseHTMLString: true
            })
            .then(() => {
              this.fetchedFiltered = false
              this.form.post(`${this.$root.getAjaxUrl}/appointments/time/${draggedEvent.id}`, {
                'bookingStart': draggedEvent.start.format('YYYY-MM-DD HH:mm')
              })
                .then(response => {
                  this.fetchedFiltered = true
                  this.notify(this.$root.labels.success, this.$root.labels.appointment_rescheduled, 'success')
                })
                .catch(e => {
                  this.fetchedFiltered = true
                  revertFunc()
                })
            })
            .catch(() => {
              revertFunc()
            })
        }
      },

      shadeColor (color, percent) {
        let f = parseInt(color.slice(1), 16)
        let t = percent < 0 ? 0 : 255
        let p = percent < 0 ? percent * -1 : percent
        let R = f >> 16
        let G = f >> 8 & 0x00FF
        let B = f & 0x0000FF
        return '#' + (0x1000000 + (Math.round((t - R) * p) + R) * 0x10000 + (Math.round((t - G) * p) + G) * 0x100 + (Math.round((t - B) * p) + B)).toString(16).slice(1)
      },

      selectAllInCategory (id) {
        let services = this.getCategoryServices(id)
        let servicesIds = services.map(service => service.id)

        // Deselect all services if they are already selected
        if (_.isEqual(_.intersection(servicesIds, this.params.services), servicesIds)) {
          this.params.services = _.difference(this.params.services, servicesIds)
        } else {
          this.params.services = _.uniq(this.params.services.concat(servicesIds))
        }

        this.filterData()
      },

      filterEmployees (employee) {
        let index = this.params.providers.indexOf(employee.id)
        if (index !== -1) {
          this.params.providers.splice(index, 1)
        } else {
          this.params.providers.push(employee.id)
        }

        this.filterData()
      },

      filterAllEmployees () {
        if (this.params.providers.length !== 0) {
          this.params.providers = []
          this.filterData()
        }
      },

      createResourcesForScheduler (employees) {
        // Go through all employees
        for (let i = 0; i < employees.length; i++) {
          let businessHours = []
          // Go through all employee's working hours to set business hours
          for (let j = 0; j < employees[i].weekDayList.length; j++) {
            let day = employees[i].weekDayList[j]

            // If there is at least one break for employee
            if (day.timeOutList.length > 0) {
              // Go through all employee's breaks
              for (let k = 0; k < day.timeOutList.length; k++) {
                let breakStartTime = day.timeOutList[k].startTime
                let breakEndTime = day.timeOutList[k].endTime

                // If this is first break, start time will be working start time, and end time will be
                // beginning of the break
                if (k === 0) {
                  businessHours.push({
                    dow: day.dayIndex === 7 ? [0] : [day.dayIndex],
                    start: day.startTime,
                    end: breakStartTime
                  })
                }

                // Start time will be this break end time, and end time will be:
                // If this is last break, then end time will be working end time otherwise it will be
                // next break start time
                businessHours.push({
                  dow: day.dayIndex === 7 ? [0] : [day.dayIndex],
                  start: breakEndTime,
                  end: k + 1 === day.timeOutList.length ? day.endTime : day.timeOutList[k + 1].startTime
                })
              }
            } else {
              businessHours.push({
                dow: day.dayIndex === 7 ? [0] : [day.dayIndex],
                start: day.startTime,
                end: day.endTime
              })
            }
          }

          this.config.resources.push({
            id: employees[i].id.toString(),
            title: employees[i].firstName + ' ' + employees[i].lastName,
            businessHours: businessHours
          })
        }

        this.$refs.calendar.fireMethod('refetchResources')
      },

      initVCalendar () {
        let dateTrigger = document.getElementsByClassName('fc-center')

        if (dateTrigger.length) {
          let datepicker = document.getElementById('am-calendar-picker')
          dateTrigger[0].addEventListener('click', function () {
            if (dateTrigger[0].className.indexOf('am-datepicker-active') < 0) {
              datepicker.style.opacity = '1'
              datepicker.style.zIndex = '11'
              dateTrigger[0].className += ' am-datepicker-active'
            } else {
              datepicker.style.opacity = '0'
              datepicker.style.zIndex = '0'
              dateTrigger[0].classList.remove('am-datepicker-active')
            }
          })
        }
      },

      selectDay () {
        if (this.selectedDate !== null) {
          this.$refs.calendar.fireMethod('gotoDate', this.selectedDate)
        }
        document.getElementsByClassName('fc-center')[0].click()
      },

      setGlobalBusinessHours () {
        let businessHours = []
        let minTime = moment.duration('24:00:00')
        let maxTime = moment.duration('00:00:00')

        let employees = this.options.entities.employees.slice()

        // If employee filter is selected take just filtered employees in count
        if (this.params.providers.length !== 0) {
          let employeesToRemove = []

          for (let i = 0; i < employees.length; i++) {
            if (_.indexOf(this.params.providers, employees[i].id) === -1) {
              employeesToRemove.push(employees[i])
            }
          }

          employees = _.difference(employees, employeesToRemove)
        }

        // Go through all employees
        for (let i = 0; i < employees.length; i++) {
          // Go through all employee's working hours
          for (let j = 0; j < employees[i].weekDayList.length; j++) {
            let day = employees[i].weekDayList[j]

            if (typeof businessHours[day.dayIndex] === 'undefined') {
              businessHours[day.dayIndex] = {}
            }

            // Min time is earliest working start time
            if (moment.duration(day.startTime) < minTime) {
              minTime = moment.duration(day.startTime)
            }

            // Max time is latest working end time
            if (moment.duration(day.endTime) > maxTime) {
              maxTime = moment.duration(day.endTime)
            }

            // Set Global Business Hours
            if (typeof businessHours[day.dayIndex].start === 'undefined' || moment.duration(day.startTime) < businessHours[day.dayIndex].start) {
              businessHours[day.dayIndex].start = moment.duration(day.startTime)
            }

            if (typeof businessHours[day.dayIndex].end === 'undefined' || moment.duration(day.endTime) > businessHours[day.dayIndex].end) {
              businessHours[day.dayIndex].end = moment.duration(day.endTime)
            }

            businessHours[day.dayIndex].dow = day.dayIndex === 7 ? [0] : [day.dayIndex]
          }
        }

        // In case there is no employee working hours, set that there is no working hours
        if (businessHours.length === 0) {
          minTime = moment.duration('02:00:00')
          maxTime = moment.duration('22:00:00')
          businessHours = {
            dow: [0, 1, 2, 3, 4, 5, 6],
            start: moment.duration('00:00:00'),
            end: moment.duration('00:00:00')
          }
        }

        // Change fullcalendar options dynamically
        this.$refs.calendar.fireMethod('option', {
          businessHours: _.compact(businessHours),
          minTime: minTime < moment.duration('02:00:00') ? moment.duration('00:00:00') : minTime.subtract(2, 'hour'),
          maxTime: maxTime > moment.duration('22:00:00') ? moment.duration('24:00:00') : maxTime.add(2, 'hour')
        })

        // Init v-calendar again because fullcalendar is rerendered
        this.initVCalendar()
      },

      canWriteAppointments () {
        return this.$root.settings.role === 'admin' || this.$root.settings.role === 'manager' || (this.$root.settings.role === 'provider' && this.$root.settings.general.allowWriteAppointments)
      }

    },

    components: {
      PageHeader,
      FullCalendar,
      DialogAppointment,
      DialogCustomer,
      DialogPayment
    }

  }
</script>
