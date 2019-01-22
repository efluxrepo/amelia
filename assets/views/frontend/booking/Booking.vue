<template>

  <div class="am-wrap">
    <!-- Spinner -->
    <div class="am-spinner am-section" v-show="!fetched">
      <img class="svg-booking am-spin" :src="$root.getUrl + 'public/img/oval-spinner.svg'">
      <img class="svg-booking am-hourglass" :src="$root.getUrl + 'public/img/hourglass.svg'">
    </div>

    <div :id="id" class="am-step-booking-catalog" v-show="fetched">
      <!-- Select Service -->
      <div class="am-select-service">
        <p class="am-select-service-title" v-show="showServices">
          {{ $root.labels.please_select + ' ' + $root.labels.service }}:
        </p>
        <p class="am-select-service-title" v-show="!showServices">
          {{ $root.labels.book_appointment }}
        </p>

        <!-- Booking Form -->
        <el-form :model="appointment" ref="booking" :rules="rules" label-position="top">

          <!-- Service -->
          <el-form-item
              v-if="showServices"
              :label="capitalizeFirstLetter($root.labels.service) + ':'"
              prop="serviceId"
          >
            <el-select
                v-model="appointment.serviceId"
                @change="changeService"
                placeholder=""
                :loading=!fetched
            >
              <el-option
                  v-for="service in servicesFiltered"
                  :key="service.id"
                  :label="service.name"
                  :value="service.id"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <!-- Location -->
          <el-form-item
              :label="$root.labels.location_colon"
              v-if="showLocations"
          >
            <el-select
                v-model="appointment.locationId"
                placeholder=""
                clearable
                :loading=!fetched
            >
              <el-option
                  v-for="location in locationsFiltered"
                  :disabled="location.disabled"
                  :key="location.id"
                  :label="location.name"
                  :value="location.id">
              </el-option>
            </el-select>
          </el-form-item>

          <!-- Employee -->
          <el-form-item
              :label="capitalizeFirstLetter($root.labels.employee) + ':'"
              v-if="showEmployees"
          >
            <el-select
                v-model="appointment.providerId"
                @change="changeEmployee"
                @clear="appointment.providerId = 0"
                placeholder=""
                :clearable="appointment.providerId !== 0"
                :loading=!fetched
            >
              <el-option
                  :key="0"
                  :label="$root.labels.any + ' ' + $root.labels.employee"
                  :value="0"
              >
              </el-option>
              <el-option
                  v-for="employee in employeesFiltered"
                  :key="employee.id"
                  :label="employee.firstName + ' ' + employee.lastName"
                  :value="employee.id"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <!-- Bringing anyone with you -->
          <el-form-item label="" v-show="group.allowed && !$root.isLite">
            <el-row>
              <el-col :span="18">
                <span>{{ $root.labels.bringing_anyone_with_you }}</span>
              </el-col>
              <el-col :span="6" class="am-align-right">
                <el-switch v-model="group.enabled" @change="enableGrouping"></el-switch>
              </el-col>
            </el-row>
          </el-form-item>

          <!-- Number of persons -->
          <transition name="fade">
            <div class="am-grouped" v-show="group.enabled && !$root.isLite">
              <el-form-item :label="$root.labels.number_of_persons_colon">
                <el-select placeholder="" v-model="appointment.bookings[0].persons" @change="changeNumberOfPersons">
                  <el-option
                      v-for="item in group.options"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
            </div>
          </transition>

          <!-- Extra Block -->
          <transition-group name="fade" class="am-extras-add">
            <div class="am-extras"
                 v-if="appointment.serviceId && getServiceById(appointment.serviceId).extras.length > 0"
                 v-for="(selectedExtra, key) in selectedExtras"
                 :key="key"
            >
              <el-row :gutter="16" class="am-flex-row-middle-align-mobile">

                <!-- Extra Type -->
                <el-col :span="14">
                  <el-form-item :label="$root.labels.extra_colon">
                    <el-select
                        v-model="selectedExtra.id"
                        @change="changeSelectedExtra(selectedExtra)"
                        placeholder=""
                    >
                      <el-option
                          v-for="extra in getAvailableExtras(selectedExtra)"
                          :key="extra.id"
                          :label="extra.name"
                          :value="extra.id">
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>

                <!-- Extra Quantity -->
                <el-col :span="7">
                  <el-form-item :label="$root.labels.qty_colon">
                    <el-select
                        v-model="selectedExtra.quantity"
                        :disabled="selectedExtra.id === null"
                        @change="changeSelectedExtra(selectedExtra)"
                        placeholder=""
                    >
                      <el-option
                          v-for="i in getSelectedExtraMaxQuantity(selectedExtra)"
                          :key="i"
                          :label="i"
                          :value="i"
                      >
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>

                <!-- Remove Extra -->
                <el-col :span="3" class="am-align-right">
                  <div class="am-delete-element" @click="deleteExtra(key)">
                    <i class="el-icon-minus"></i>
                  </div>
                </el-col>

              </el-row>

              <!-- Extra Duration & Price-->
              <el-row
                  :gutter="16" class="am-flex-row-middle-align-mobile"
                  v-if="selectedExtra.duration || selectedExtra.price"
              >

                <!-- Extra Duration -->
                <el-col :span="14">
                  <el-form-item :label="$root.labels.duration_colon">
                    <span style="float: left">
                      {{  selectedExtra.duration ? secondsToNiceDuration(selectedExtra.duration) : '/'  }}</span>
                  </el-form-item>
                </el-col>

                <!-- Extra Price -->
                <el-col :span="10">
                  <el-form-item :label="$root.labels.price_colon">
                    <span style="float: left">
                      {{ getFormattedPrice(selectedExtra.price) }}</span>
                  </el-form-item>
                </el-col>

              </el-row>

            </div>
          </transition-group>

          <!-- Add extra -->
          <div class="am-add-element"
               v-show="appointment.serviceId && selectedExtras.length < getServiceById(appointment.serviceId).extras.length"
               @click="addExtra"
          >
            <i class="el-icon-plus"></i> <span>{{ $root.labels.add_extra }}</span>
          </div>

          <!-- Continue -->
          <div class="am-button-wrapper">
            <el-button
                :loading="loadingTimeSlots"
                type="primary"
                @click="getTimeSlots"
            >
              {{ $root.labels.continue }}
            </el-button>
          </div>

        </el-form>
      </div>

      <!-- Pick Date & Time -->
      <div :id="this.id + '-calendar'" class="am-select-date am-select-service-date-transition">
        <p class="am-select-date-title">{{ $root.labels.pick_date_and_time_colon }}</p>
        <v-date-picker
            v-model="selectedDate"
            mode="single"
            id="am-calendar-picker"
            class='am-calendar-picker'
            @dayclick="selectDate"
            @input="setTimeSlots"
            :available-dates="availableDates"
            :disabled-dates='disabledWeekdays'
            :show-day-popover=false
            :is-expanded=true
            :is-inline=true
            :disabled-attribute="disabledAttribute"
            :formats="vCalendarFormats"
        >
        </v-date-picker>

        <!-- Time Slots -->
        <transition name="fade">
          <div :id="calendarId" v-show="showTimes">
            <div class="am-appointment-times am-scroll">
              <el-radio-group v-model="appointment.bookingStartTime" size="medium" @change="selectTime">
                <el-radio-button
                    v-for="(slot, index) in availableTimeSlots"
                    :label="slot"
                    :key="index"
                >
                  {{ getFormattedTimeSlot(slot, appointment.duration) }}
                </el-radio-button>
              </el-radio-group>
            </div>
          </div>
        </transition>

        <!-- Back & Continue Buttons -->
        <div class="am-button-wrapper">

          <!-- Back Button -->
          <transition name="fade">
            <el-button
                id="am-back-button"
                @click="togglePicker()"
                v-if="showCalendarBackButton"
            >
              {{ $root.labels.back }}
            </el-button>
          </transition>

          <!-- Continue Button -->
          <transition name="fade">
            <el-button
                id="am-continue-button"
                v-show="showCalendarContinueButton"
                @click="showConfirmBooking"
            >
              {{ $root.labels.continue }}
            </el-button>
          </transition>

        </div>

      </div>

      <!-- Confirm Booking -->
      <confirm-booking
          v-if="activeConfirm"
          dialogClass="am-confirm-booking am-scroll"
          :service="getProviderById(appointment.providerId).serviceList.find(service => service.id === appointment.serviceId)"
          :appointment="appointment"
          :provider="getProviderById(appointment.providerId)"
          :location="getLocationById(getProviderById(appointment.providerId).locationId)"
          :notificationsSettings="options.entities.notifications"
          :customFields="options.entities.customFields"
          @confirmedBooking="confirmedBooking"
          @cancelBooking="cancelBooking"
      >
      </confirm-booking>

      <!-- Add To Calendar -->
      <transition name="fade">
        <add-to-calendar v-if="showAddToCalendar" :addToCalendarData="addToCalendarData"></add-to-calendar>
      </transition>

    </div>
  </div>
</template>

<script>
  import moment from 'moment'
  import liteMixin from '../../../js/common/mixins/liteMixin'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'
  import PhoneInput from '../../parts/PhoneInput.vue'
  import ConfirmBooking from '../parts/ConfirmBooking.vue'
  import AddToCalendar from '../parts/AddToCalendar.vue'
  import bookingMixin from '../../../js/frontend/mixins/bookingMixin'
  import helperMixin from '../../../js/backend/mixins/helperMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import customFieldMixin from '../../../js/common/mixins/customFieldMixin'

  export default {

    mixins: [liteMixin, imageMixin, dateMixin, entitiesMixin, bookingMixin, helperMixin, durationMixin, priceMixin, customFieldMixin],

    props: {
      id: {
        default: 'am-step-booking'
      },
      showService: {
        default: true,
        type: Boolean
      },
      passedService: {
        default: () => {},
        type: Object
      }
    },

    data () {
      return {
        calendarId: '',
        activeConfirm: false,
        activePicker: false,
        addToCalendarData: null,
        appointment: {
          bookingStart: '',
          bookingStartTime: '',
          bookings: [{
            customer: {
              email: '',
              externalId: null,
              firstName: '',
              id: null,
              lastName: '',
              phone: ''
            },
            customFields: {},
            customerId: 0,
            extras: [],
            persons: 1,
            status: this.$root.settings.general.defaultAppointmentStatus
          }],
          duration: 0,
          group: false,
          notifyParticipants: this.$root.settings.notifications.notifyCustomers,
          payment: {
            amount: 0,
            gateway: '',
            data: {}
          },
          providerId: 0,
          serviceId: null,
          locationId: null
        },
        availableDates: [],
        availableTimeSlots: [],
        calendar: '',
        calendarTimeSlots: {},
        calendarVisible: false,
        customer: {
          name: '',
          email: '',
          phone: '',
          paymentMethod: ''
        },
        customerRules: {
          name: [
            {required: true, message: 'Please input name', trigger: 'submit'},
            {min: 3, max: 50, message: 'Length should be 3 to 50', trigger: 'submit'}],
          email: [
            {required: true, message: 'Please input name', trigger: 'submit'},
            {min: 3, max: 5, message: 'Length should be 3 to 5', trigger: 'submit'}],
          phone: '',
          paymentMethod: ''
        },
        disabledAttribute: {
          contentStyle: {
            color: '#ccc',
            opacity: 0.4,
            textDecoration: 'line-through'
          }
        },
        disabledWeekdays: null,
        fetched: false,
        fetchedSlots: false,
        group: {
          allowed: false,
          enabled: false,
          count: 1,
          options: []
        },
        loadingTimeSlots: false,
        options: {
          entities: {
            services: [],
            employees: [],
            locations: [],
            notifications: {},
            customFields: []
          }
        },
        rules: {
          serviceId: [
            {
              required: true,
              message: this.$root.labels.please_select + ' ' + this.$root.labels.service,
              trigger: 'blur',
              type: 'number'
            }
          ]
        },
        selectedExtras: [],
        selectedDate: null,
        showAddToCalendar: false,
        showExtras: false,
        showFilters: false,
        showTimes: false,
        showServices: false,
        showEmployees: false,
        showLocations: false,
        showCalendarBackButton: false,
        showCalendarContinueButton: false,
        times: ''
      }
    },

    created () {
      this.calendarId = 'am-appointment-times' + this.$root.shortcodeData.counter
      window.addEventListener('resize', this.handleResize)
    },

    mounted () {
      if (!this.$root.shortcodeData.hasBookingShortcode || !this.$root.shortcodeData.hasCategoryShortcode) {
        this.inlineBookingSVG()
      }

      // Customization hook
      if ('beforeBookingLoaded' in window) {
        window.beforeBookingLoaded()
      }

      if (this.showService === true) {
        let entitiesToFetch = !AMELIA_LITE_VERSION ? ['categories', 'locations', 'employees', 'notifications', 'custom_fields'] : ['categories', 'employees', 'notifications']
        this.getEntities(entitiesToFetch)
      } else {
        this.options.entities.services = [this.passedService]
        let entitiesToFetch = !AMELIA_LITE_VERSION ? ['locations', 'employees', 'notifications', 'custom_fields'] : ['employees', 'notifications']
        this.getEntities(entitiesToFetch)
        this.appointment.serviceId = this.options.entities.services[0].id
      }

      this.getCurrentUser()
      this.times = document.getElementById(this.calendarId)
    },

    updated () {
      this.handleResize()
    },

    methods: {
      showCalendarOnly (initCall) {
        let providerService = []

        if (this.appointment.serviceId && this.appointment.providerId) {
          providerService = this.getProviderById(this.appointment.providerId).serviceList.filter(
            service => service.id === this.appointment.serviceId
          )
        }

        return initCall &&
          !this.showServices &&
          !this.showEmployees &&
          providerService.length &&
          (providerService[0].maxCapacity === 1 || (providerService[0].maxCapacity > 1 && !providerService[0].bringingAnyone)) &&
          this.getServiceById(this.appointment.serviceId).extras.length === 0
      },

      getEntities (types) {
        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            page: 'appointments',
            types: types
          }
        }).then(response => {
          this.filterResponseData(response)

          let employees = []
          let availableServicesIds = []

          response.data.data.employees.forEach(function (employee) {
            employee.serviceList.forEach(function (service) {
              availableServicesIds.push(service.id)
            })

            if (employee.serviceList.length) {
              employees.push(employee)
            }
          })

          this.options.entities.employees = employees
          this.options.entities.customFields = response.data.data.customFields
          this.setBookingCustomFields()

          if (types.indexOf('categories') !== -1) {
            this.options.entities.categories = response.data.data.categories
            this.options.entities.services = this.getServicesFromCategories().filter(
              service => availableServicesIds.indexOf(service.id) !== -1
            )
          }

          this.options.entities.locations = response.data.data.locations
          this.options.entities.notifications = response.data.data.notifications
          if (this.showService === false) {
            this.$emit('entitiesFetched', this.options.entities)
          }

          // filter response entities based on shortCode entities
          if (this.$root.shortcodeData.booking && this.servicesFiltered.length && this.employeesFiltered.length) {
            // find shortCode entities
            let shortCodeCategory = null
            let shortCodeService = null
            let shortCodeEmployee = null
            let shortCodeLocation = null

            if (this.$root.shortcodeData.booking) {
              shortCodeCategory = this.$root.shortcodeData.booking.category && this.options.entities.categories ? this.options.entities.categories.find(category => category.id === this.$root.shortcodeData.booking.category) : null
              shortCodeService = this.$root.shortcodeData.booking.service ? this.servicesFiltered.find(service => service.id === this.$root.shortcodeData.booking.service) : null
              shortCodeEmployee = this.$root.shortcodeData.booking.employee ? this.employeesFiltered.find(employee => employee.id === this.$root.shortcodeData.booking.employee) : null
              shortCodeLocation = this.$root.shortcodeData.booking.location ? this.locationsFiltered.find(location => location.id === this.$root.shortcodeData.booking.location) : null
            }

            let canFilterByShortCode = true

            // determine if combination of shortCode entities is possible for booking
            if (shortCodeService && shortCodeEmployee) {
              canFilterByShortCode = shortCodeEmployee.serviceList.find(service => service.id === shortCodeService.id)
            }

            if (shortCodeService && shortCodeLocation) {
              canFilterByShortCode = this.employeesFiltered.filter(employee => employee.locationId === shortCodeLocation.id && employee.serviceList.find(service => service.id === shortCodeService.id))
            }

            if (shortCodeCategory && shortCodeEmployee) {
              canFilterByShortCode = this.employeesFiltered.filter(employee => employee.id === shortCodeEmployee.id && employee.serviceList.find(service => service.categoryId === shortCodeCategory.id))
            }

            if (shortCodeCategory && shortCodeLocation) {
              canFilterByShortCode = this.employeesFiltered.filter(employee => employee.locationId === shortCodeLocation.id && employee.serviceList.find(service => service.categoryId === shortCodeCategory.id))
            }

            // filter entities by shortCode if possible for booking
            if (canFilterByShortCode) {
              if (shortCodeCategory) {
                this.options.entities.categories = [shortCodeCategory]

                this.options.entities.employees.forEach(function (employee) {
                  employee.serviceList = employee.serviceList.filter(service => service.categoryId === shortCodeCategory.id)
                })
              }

              if (shortCodeService) {
                this.options.entities.services = [shortCodeService]
              }

              if (shortCodeEmployee) {
                this.options.entities.employees = [shortCodeEmployee]
              }

              if (shortCodeLocation) {
                this.options.entities.locations = [shortCodeLocation]
              }
            }
          }

          // filter select boxes
          if (this.servicesFiltered.length === 1) {
            this.appointment.serviceId = this.servicesFiltered[0].id
          } else if (this.servicesFiltered.length > 1) {
            this.showServices = true
          } else {
            return
          }

          if (this.employeesFiltered.length === 1) {
            this.appointment.providerId = this.employeesFiltered[0].id
          } else if (this.employeesFiltered.length > 1) {
            this.showEmployees = true
          }

          if (this.locationsFiltered.length === 1) {
            this.appointment.locationId = this.locationsFiltered[0].id
          } else if (this.locationsFiltered.length > 1) {
            this.showLocations = true
          }

          this.handleCapacity()

          if (this.showCalendarOnly(true)) {
            document.getElementById(this.id + '-calendar').classList.remove('am-select-service-date-transition')
            this.getTimeSlots()
          } else {
            this.fetched = true
          }
        }).catch(e => {
          console.log(e.message)
        })
      },

      changeService () {
        this.clearValidation()

        this.appointment.bookings[0].extras = []

        this.handleCapacity()

        if (this.calendarVisible) {
          this.getTimeSlots()
        }
      },

      changeEmployee () {
        this.clearValidation()

        this.handleCapacity()

        if (this.calendarVisible) {
          this.getTimeSlots()
        }
      },

      enableGrouping () {
        this.group.enabled === true ? this.appointment.bookings[0].persons += 1 : this.appointment.bookings[0].persons = 1

        this.handleCapacity()

        if (this.calendarVisible) {
          this.getTimeSlots()
        }
      },

      changeNumberOfPersons () {
        if (this.calendarVisible) { this.getTimeSlots() }
      },

      handleCapacity: !AMELIA_LITE_VERSION ? function () {
        let $this = this
        let groupEnabled = false
        let maxCapacity = 0

        if ($this.appointment.serviceId) {
          if ($this.appointment.providerId) {
            let employee = this.options.entities.employees.find(employee => employee.id === $this.appointment.providerId)
            let service = employee.serviceList.find(service => service.id === $this.appointment.serviceId)

            groupEnabled = service.maxCapacity > 1 && service.bringingAnyone
            maxCapacity = service.maxCapacity
          } else {
            this.options.entities.employees.forEach(function (employee) {
              employee.serviceList.forEach(function (service) {
                if (service.id === $this.appointment.serviceId) {
                  if (service.maxCapacity > 1 && service.bringingAnyone) {
                    groupEnabled = true
                  }

                  if (service.maxCapacity > maxCapacity) {
                    maxCapacity = service.maxCapacity
                  }
                }
              })
            })
          }
        }

        this.group.options = []

        for (let i = 1; i < maxCapacity; i++) {
          this.group.options.push({
            label: i === 1 ? i + ' ' + this.$root.labels.person_upper : i + ' ' + this.$root.labels.persons_upper,
            value: i + 1
          })
        }

        if (maxCapacity !== 0 && this.appointment.bookings[0].persons > maxCapacity) {
          this.appointment.bookings[0].persons = maxCapacity
        }

        if (this.group.enabled) {
          this.group.enabled = groupEnabled
        }

        this.group.allowed = groupEnabled
      } : function () {
        return false
      },

      getSelectedExtraMaxQuantity (selectedExtra) {
        let extra = this.getServiceById(this.appointment.serviceId).extras.find(extra => extra.id === selectedExtra.id)

        return typeof extra !== 'undefined' ? extra.maxQuantity : ''
      },

      getAvailableExtras (selectedExtra) {
        let selectedExtras = []
        let availableExtras = []

        this.selectedExtras.forEach(function (extra) {
          if (extra.id) {
            selectedExtras.push(extra.id)
          }
        })

        this.getServiceById(this.appointment.serviceId).extras.forEach(function (extra) {
          if (selectedExtras.indexOf(extra.id) === -1 || selectedExtra.id === extra.id) {
            availableExtras.push(extra)
          }
        })

        return availableExtras
      },

      addExtra () {
        this.selectedExtras.push({
          id: null,
          extraId: null,
          quantity: '',
          duration: 0,
          name: ''
        })
      },

      deleteExtra (key) {
        if (this.calendarVisible && !!this.selectedExtras[key].duration) {
          this.selectedExtras.splice(key, 1)
          this.getTimeSlots()
        } else {
          this.selectedExtras.splice(key, 1)
        }
      },

      changeSelectedExtra (selectedExtra) {
        selectedExtra.quantity = (selectedExtra.quantity === '') ? 1 : selectedExtra.quantity

        let extra = this.getServiceById(this.appointment.serviceId).extras.find(extra => extra.id === selectedExtra.id)

        selectedExtra.extraId = extra.id
        selectedExtra.duration = extra.duration
        selectedExtra.name = extra.name
        selectedExtra.price = extra.price
        selectedExtra.selected = true

        if (selectedExtra.quantity > extra.maxQuantity) {
          selectedExtra.quantity = extra.maxQuantity
        }

        if (this.calendarVisible && !!extra.duration) { this.getTimeSlots() }
      },

      getTimeSlots () {
        this.$refs.booking.validate((valid) => {
          if (!valid) {
            return false
          }
        })

        if (this.appointment.serviceId) {
          this.loadingTimeSlots = true
          let extras = []

          this.selectedExtras.forEach(function (extra) {
            if (extra.id) {
              extras.push({
                id: extra.id,
                quantity: extra.quantity
              })
            }
          })

          let providerIds = []
          let $this = this

          // If Employee is not selected, select ones that can provide the service
          if (!this.appointment.providerId) {
            // If grouping is enabled check employees capacity for selected service
            if ($this.group.enabled) {
              this.employeesFiltered.forEach(function (employee) {
                if (typeof (employee.serviceList.find(service => service.id === $this.appointment.serviceId && service.maxCapacity >= $this.appointment.bookings[0].persons)) !== 'undefined') {
                  providerIds.push(employee.id)
                }
              })
            } else {
              this.employeesFiltered.forEach(function (employee) {
                if (typeof (employee.serviceList.find(service => service.id === $this.appointment.serviceId)) !== 'undefined') {
                  providerIds.push(employee.id)
                }
              })
            }
          }

          // Customization hook
          if ('afterBookingSelectService' in window) {
            window.afterBookingSelectService(this.appointment, this.getServiceById(this.appointment.serviceId), this.getProviderById(this.appointment.providerId), this.getLocationById(this.appointment.locationId))
          }

          this.$http.get(`${this.$root.getAjaxUrl}/slots`, {
            params: {
              serviceId: this.appointment.serviceId,
              providerIds: this.appointment.providerId ? [this.appointment.providerId] : providerIds,
              extras: JSON.stringify(extras),
              group: 1,
              persons: this.appointment.bookings[0].persons
            }
          }).then(response => {
            this.loadingTimeSlots = false
            if (!this.calendarVisible) {
              this.activePicker = !this.activePicker
              document.getElementById(this.id).classList.toggle('am-active-picker', this.activePicker)
            }

            let availableDates = []

            Object.keys(response.data.data.slots).forEach(function (dateString) {
              availableDates.push(moment(dateString).toDate())
            })

            this.calendarTimeSlots = response.data.data.slots
            this.disabledWeekdays = {weekdays: []}
            this.disabledWeekdays = (availableDates.length === 0) ? {weekdays: [1, 2, 3, 4, 5, 6, 7]} : null
            this.availableDates = availableDates
            this.calendarVisible = true

            if (this.availableDates.length) {
              this.setTimeSlots()
            }

            if (!this.availableDates.length || !this.isSelectedDateAvailable()) {
              this.showTimes = false
              let amContainer = document.getElementById(this.id)
              amContainer.classList.remove('am-show-times')
            }

            if (!this.availableDates.length || !this.isSelectedDateAvailable() || this.availableTimeSlots.indexOf(this.appointment.bookingStartTime) === -1) {
              this.unSelectTime()
            }

            this.fetchedSlots = true
            this.fetched = true
          }).catch(e => {
            console.log(e.message)
            this.fetchedSlots = true
            this.fetched = true
          })
        }
      },

      selectDate (dayInfo) {
        this.unSelectTime()
        let isDisabled = false

        dayInfo.attributes.forEach(function (attrItem) {
          if (attrItem.hasOwnProperty('key') && attrItem['key'] === 'disabled') {
            isDisabled = true
          }
        })

        if (isDisabled) {
          return
        }

        this.showTimes = false

        let amContainer = document.getElementById(this.id)
        amContainer.classList.remove('am-show-times')

        let weekRow = dayInfo.event.target.parentNode.parentNode.parentNode.parentNode.parentNode
        if (!weekRow.classList.contains('c-week')) {
          weekRow = dayInfo.event.target.parentNode.parentNode.parentNode.parentNode
        }

        if (!weekRow.classList.contains('c-week')) {
          weekRow = dayInfo.event.target.parentNode.parentNode.parentNode
        }

        weekRow.parentNode.insertBefore(this.times, weekRow.nextSibling)

        setTimeout(() => {
          if (this.availableTimeSlots.length && this.selectedDate) {
            this.showTimes = true
            amContainer.classList.add('am-show-times')
          }
        }, 200)
      },

      isSelectedDateAvailable () {
        return this.calendarTimeSlots.hasOwnProperty(moment(this.selectedDate).format('YYYY-MM-DD'))
      },

      setTimeSlots () {
        let dateString = moment(this.selectedDate).format('YYYY-MM-DD')

        if (this.isSelectedDateAvailable()) {
          this.availableTimeSlots = Object.keys(this.calendarTimeSlots[dateString])
          this.appointment.duration = this.getAppointmentDuration(this.getServiceById(this.appointment.serviceId), this.selectedExtras)
        }
      },

      togglePicker () {
        this.calendarVisible = false
        this.activePicker = !this.activePicker
        let amContainer = document.getElementById(this.id)
        amContainer.classList.toggle('am-active-picker', this.activePicker)
      },

      selectTime () {
        this.appointment.bookingStart = moment(this.selectedDate).format('YYYY-MM-DD') + ' ' + this.appointment.bookingStartTime
        this.showCalendarContinueButton = true
      },

      unSelectTime () {
        this.appointment.bookingStartTime = null
        this.showCalendarContinueButton = false
      },

      showConfirmBooking () {
        if (!this.appointment.providerId) {
          let freeSlotEmployees = this.calendarTimeSlots[moment(this.selectedDate).format('YYYY-MM-DD')][this.appointment.bookingStartTime]

          let randomlySelectedEmployeeIndex = Math.floor(Math.random() * (freeSlotEmployees.length) + 1)

          this.appointment.providerId = this.calendarTimeSlots[moment(this.selectedDate).format('YYYY-MM-DD')][this.appointment.bookingStartTime][randomlySelectedEmployeeIndex - 1]
        }

        this.appointment.bookings[0].extras = this.selectedExtras

        // Customization hook
        if ('afterBookingSelectDateAndTime' in window) {
          window.afterBookingSelectDateAndTime(this.appointment, this.getServiceById(this.appointment.serviceId), this.getProviderById(this.appointment.providerId), this.getLocationById(this.appointment.locationId))
        }

        this.activeConfirm = true
        let amContainer = document.getElementById(this.id)
        setTimeout(() => {
          amContainer.classList.toggle('am-active-confirm', this.activeConfirm)
        }, 1000)
      },

      cancelBooking () {
        this.activeConfirm = false
        let amContainer = document.getElementById(this.id)
        amContainer.classList.toggle('am-active-confirm', this.activeConfirm)
        if (this.showCalendarOnly(true)) {
          amContainer.classList.add('am-mobile-collapsed')
          amContainer.classList.remove('am-desktop')
        }
      },

      inlineBookingSVG () {
        let inlineSVG = require('inline-svg')
        inlineSVG.init({
          svgSelector: 'img.svg-booking',
          initClass: 'js-inlinesvg'
        })
      },

      handleResize () {
        let amContainer = document.getElementById(this.id)

        if (this.showCalendarOnly(true)) {
          amContainer.classList.add('am-mobile-collapsed')
          amContainer.classList.remove('am-desktop')
          this.showCalendarBackButton = false

          return
        }

        if (amContainer) {
          let amContainerWidth = amContainer.offsetWidth

          if (this.showCalendarOnly(false)) {
            amContainer.classList.add('am-mobile-collapsed')
            amContainer.classList.remove('am-desktop')
            this.showCalendarBackButton = false
          } else {
            if (amContainerWidth < 670) {
              amContainer.classList.add('am-mobile-collapsed')
              amContainer.classList.remove('am-desktop')
              this.showCalendarBackButton = true
            } else {
              amContainer.classList.add('am-desktop')
              amContainer.classList.remove('am-mobile-collapsed')
              this.showCalendarBackButton = false
            }
          }
        }
      },

      confirmedBooking (responseData) {
        this.showConfirmBooking = false

        let service = this.getServiceById(this.appointment.serviceId)
        let location = this.getLocationById((this.getProviderById(this.appointment.providerId)).locationId)

        this.addToCalendarData = {
          title: service.name,
          start: moment.utc(responseData.utcTime.start.replace(/ /g, 'T')).toDate(),
          duration: this.appointment.duration / 60,
          address: location !== null ? location.address : '',
          description: '',
          notificationsSettings: this.options.entities.notifications,
          responseData: responseData,
          active: this.$root.settings.general.addToCalendar
        }

        this.showAddToCalendar = true
      },

      clearValidation () {
        if (typeof this.$refs.booking !== 'undefined') {
          this.$refs.booking.clearValidate()
        }
      }
    },

    computed: {
      servicesFiltered () {
        let services = this.options.entities.services

        if (this.appointment.providerId) {
          let employeeServicesIds = []
          // Find selected employee
          let selectedEmployee = this.employeesFiltered.find(employee => employee.id === this.appointment.providerId)
          // Find selected employee services ids
          selectedEmployee.serviceList.map(service => employeeServicesIds.push(service.id))
          // Filter by provider
          services = services.filter(service => employeeServicesIds.indexOf(service.id) !== -1)
        }

        if (this.appointment.locationId) {
          let employeesServicesIds = []
          // Find employees services ids
          this.employeesFiltered.forEach(function (employee) {
            employeesServicesIds = employeesServicesIds.concat(employee.serviceList.map(service => service.id))
          })
          // Make array unique
          employeesServicesIds = employeesServicesIds.filter((v, i, a) => a.indexOf(v) === i)
          // Filter by location
          services = services.filter(service => employeesServicesIds.indexOf(service.id) !== -1)
        }

        // Sort by name
        services = services.sort((a, b) => (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0))

        // Filter by status
        services = services.filter(service => service.status === 'visible')

        // Hide service if all service employees have status "hidden"
        services = services.filter(service => this.getServiceProviders(service.id).filter(provider => provider.status === 'visible').length > 0)

        let hiddenLocations = this.options.entities.locations.filter(location => location.status === 'hidden')

        // Hide service if all employees that have status "visible" have locations that have status "hidden" or do not have location assigned
        if (this.options.entities.locations.length > 0) {
          services = services.filter(
            service => this.getServiceProviders(service.id).filter(
              provider => provider.status === 'visible' && provider.locationId !== null && ((this.getLocationById(provider.locationId) && this.getLocationById(provider.locationId).status === 'visible') || hiddenLocations.length === this.options.entities.locations.length)
            ).length > 0
          )
        }

        return services
      },

      employeesFiltered () {
        let employees = this.options.entities.employees

        if (this.appointment.serviceId) {
          employees = employees.filter(
            employee => employee.serviceList.find(service => service.id === this.appointment.serviceId)
          )
        }

        if (this.appointment.locationId) {
          employees = employees.filter(employee => employee.locationId === this.appointment.locationId)
        }

        // Hide employee if he doesn't have at least one service with status "visible"
        employees = employees.filter(employee => employee.serviceList.filter(service => service.status === 'visible').length > 0)

        let hiddenLocations = this.options.entities.locations.filter(location => location.status === 'hidden')

        // Show only employees which location has status "visible"
        if (this.options.entities.locations.length > 0) {
          employees = employees.filter(employee => employee.locationId !== null ? ((this.getLocationById(employee.locationId) && this.getLocationById(employee.locationId).status === 'visible') || hiddenLocations.length === this.options.entities.locations.length) : false)
        }

        return employees.filter(employee => employee.status === 'visible')
      },

      locationsFiltered () {
        let locations = this.options.entities.locations

        // Filter by provider
        if (this.appointment.providerId) {
          let selectedEmployee = this.employeesFiltered.find(employee => employee.id === this.appointment.providerId)

          locations = locations.filter(location => location.id === selectedEmployee.locationId)
        }

        // Filter by service
        if (this.appointment.serviceId) {
          let employees = this.employeesFiltered.filter(employee => employee.serviceList.find(service => service.id === this.appointment.serviceId))

          locations = locations.filter(
            location => employees.map(employee => employee.locationId).indexOf(location.id) !== -1
          )
        }

        // Find unique employees locations ids
        let employeesLocationsIds = this.employeesFiltered.map(employee => employee.locationId).filter((v, i, a) => a.indexOf(v) === i)
        // Remove locations that are not assigned to employee
        locations = locations.filter(location => employeesLocationsIds.indexOf(location.id) !== -1)

        return locations.filter(location => location.status === 'visible')
      }
    },

    components: {
      moment,
      PhoneInput,
      ConfirmBooking,
      AddToCalendar
    }

  }
</script>