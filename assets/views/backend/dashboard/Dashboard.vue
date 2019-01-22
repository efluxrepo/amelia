<template>
  <div class="am-wrap">
    <div id="am-dashboard" class="am-body">

      <!-- Page Header -->
      <page-header @changeFilter="getDashboard" :params="params"></page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched">
        <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
      </div>

      <!-- Statistics -->
      <div v-if="fetched === true">
        <div class="am-stats am-section">
          <div class="am-big-stats am-border-bottom">
            <el-row :gutter="32" class="am-eqheight">
              <el-col v-for="(item, index) in stats" :key="index" :sm="12" :md="6" :lg="6">
                <div class="am-grid-content ">
                  <div class="am-big-num">
                    <span>{{ item.price === false ? item.value : getFormattedPrice(item.value) }}</span>
                    <img :src="$root.getUrl+'/public/img/' + item.image + '.svg'">
                  </div>
                  <div class="am-title">
                    <h3>{{ item.label }}
                      <el-tooltip placement="top">
                        <div slot="content" v-html="item.tooltip"></div>
                        <i class="el-icon-question am-tooltip-icon"></i>
                      </el-tooltip>
                    </h3>
                  </div>
                  <div>
                    <a class="am-goto" @click="navigateTo(item)">{{ $root.labels.view }} {{ item.label }}</a>
                  </div>
                </div>
              </el-col>
            </el-row>
          </div>
        </div>

        <!-- Charts -->

        <BlockLite/>
        <div class="am-charts am-section">
          <el-row :gutter="32" :class="{'am-lite-container-disabled': $root.isLite}">

            <!-- Conversions Charts -->
            <el-col :md="16" class="am-border-right">
              <div class="am-chart bar-chart">
                <h2>
                  {{ $root.labels.conversions }}
                  <el-tooltip placement="top">
                    <div slot="content" v-html="$root.labels.conversions_tooltip"></div>
                    <i class="el-icon-question am-tooltip-icon"></i>
                  </el-tooltip>
                </h2>
                <el-tabs v-model="chartTabs">

                  <!-- Employees Conversions Chart Tab -->
                  <el-tab-pane :label="$root.labels.employees" name="employee">

                    <!-- Employees Conversions Chart Filter -->
                    <div class="am-chart-filter">
                      <el-row :gutter="10">
                        <el-col :sm="12">
                          <el-select
                              v-model="employees"
                              @change="filterEmployeesChart"
                              filterable
                              clearable
                              :placeholder="$root.labels.select_employee"
                              multiple
                              collapse-tags
                          >
                            <el-option
                                v-for="item in options.entities.employees"
                                :key="item.id"
                                :label="item.firstName + ' ' + item.lastName"
                                :value="item.id"
                            >
                            </el-option>
                          </el-select>
                        </el-col>
                      </el-row>
                    </div>

                    <!-- Employees Conversions Chart -->
                    <bar-chart
                        v-if="chartTabs === 'employee'"
                        ref="employeesChart"
                        :data="employeesChartData"
                    >
                    </bar-chart>

                  </el-tab-pane>

                  <!-- Services Conversions Chart Tab -->
                  <el-tab-pane :label="$root.labels.services" name="service">

                    <!-- Services Conversions Chart Filter -->
                    <div class="am-chart-filter">
                      <el-row :gutter="10">
                        <el-col :sm="12">
                          <el-select
                              v-model="services"
                              @change="filterServicesChart"
                              filterable
                              clearable
                              :placeholder="$root.labels.select_service"
                              multiple
                              collapse-tags
                          >
                            <el-option
                                v-for="item in options.entities.services"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            >
                            </el-option>
                          </el-select>
                        </el-col>
                      </el-row>
                    </div>

                    <!-- Services Conversions Chart -->
                    <bar-chart
                        v-if="chartTabs === 'service'"
                        ref="servicesChart"
                        :data="servicesChartData"
                    >
                    </bar-chart>

                  </el-tab-pane>

                  <!-- Locations Conversions Chart Tab -->
                  <el-tab-pane :label="$root.labels.locations" name="location" v-if="options.entities.locations.length">

                    <!-- Locations Conversions Chart Filter -->
                    <div class="am-chart-filter">
                      <el-row :gutter="10">
                        <el-col :sm="12">
                          <el-select
                              v-model="locations"
                              @change="filterLocationsChart"
                              filterable
                              clearable
                              :placeholder="$root.labels.select_location"
                              multiple
                              collapse-tags
                          >
                            <el-option
                                v-for="item in options.entities.locations"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id"
                            >
                            </el-option>
                          </el-select>
                        </el-col>
                      </el-row>
                    </div>

                    <!-- Locations Conversions Chart -->
                    <bar-chart
                        v-if="chartTabs === 'location'"
                        ref="locationsChart"
                        :data="locationsChartData"
                    >
                    </bar-chart>

                  </el-tab-pane>

                </el-tabs>
              </div>
            </el-col>

            <!-- Customers Chart -->
            <el-col :md="8">
              <div class="am-chart doughnut-chart">

                <!-- Customers Label and Growth Stats -->
                <el-row>
                  <el-col :span="12">
                    <h2>
                      {{ $root.labels.customers }}
                      <el-tooltip placement="top">
                        <div slot="content" v-html="$root.labels.customers_tooltip"></div>
                        <i class="el-icon-question am-tooltip-icon"></i>
                      </el-tooltip>
                    </h2>
                  </el-col>
                  <el-col :span="12">
                    <h2 class="align-right" v-if="fetched">{{ totalCustomers }}
                      <span :class="growthClass">
                      {{ customersGrowthPercentage }}{{ growthPercentageCharacter }}
                    </span>
                    </h2>
                  </el-col>
                </el-row>

                <!-- Customers Chart -->
                <div class="" style="padding: 0 40px;">
                  <doughnut-chart
                      ref="customersChart"
                      :data="customersChartData"
                  >
                  </doughnut-chart>
                </div>

                <!-- Customers Progress Charts -->
                <el-row>
                  <el-col :span="12">
                    <p class="am-big-num" v-if="fetched">
                      {{ newCustomers }}
                    </p>
                    <p>{{ $root.labels.new }}</p>
                    <el-progress
                        v-if="fetched"
                        :percentage="newCustomersPercentage"
                        color="#1A84EE"
                    >
                    </el-progress>
                  </el-col>
                  <el-col :span="12">
                    <p class="am-big-num" v-if="fetched">
                      {{ returningCustomers }}
                    </p>
                    <p>{{ $root.labels.returning }}</p>
                    <el-progress
                        v-if="fetched"
                        :percentage="returnedCustomersPercentage"
                        color="#FFD400"
                    >
                    </el-progress>
                  </el-col>
                </el-row>

              </div>
            </el-col>

          </el-row>
        </div>

        <!-- Today's Appointments -->
        <div id="am-appointments">

          <!-- Header -->
          <el-form :model="params" class="demo-form-inline" :action="exportAction" method="POST">
            <div class="am-section-header">
              <el-row>

                <!-- Header Title -->
                <el-col :span="20">
                  <h2>{{ $root.labels.today_appointments }}</h2>
                </el-col>

                <!-- Export Button -->
                <el-col :span="4">
                  <el-popover :disabled="!$root.isLite" ref="exportPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                  <div class="align-right" :class="{'am-lite-disabled': $root.isLite}" v-popover:exportPop>
                    <el-tooltip placement="top" :disabled="$root.isLite">
                      <div slot="content" v-html="$root.labels.export_tooltip_appointments"></div>
                      <el-button
                          class="button-export am-button-icon"
                          :disabled="appointments.length === 0"
                          @click="dialogExport = true"
                      >
                        <img class="svg" :alt="$root.labels.export" :src="$root.getUrl+'public/img/export.svg'"/>
                      </el-button>
                    </el-tooltip>
                  </div>
                </el-col>
              </el-row>
            </div>

            <!-- Dialog Export -->
            <transition name="slide">
              <el-dialog
                  class="am-side-dialog am-dialog-export"
                  :visible.sync="dialogExport"
                  :show-close="false"
                  v-if="dialogExport"
              >
                <dialog-export
                    :data="Object.assign(dateExportParams, exportParams)"
                    :action="$root.getAjaxUrl + '/report/appointments'"
                    @updateAction="(action) => {this.exportAction = action}"
                    @closeDialogExport="dialogExport = false"
                >
                </dialog-export>
              </el-dialog>
            </transition>
          </el-form>

          <!-- Appointments List Head -->
          <div class="am-appointments-list-head" v-if="appointments.length > 0">
            <el-row>

              <el-col :lg="15">
                <el-row :gutter="10" class="am-appointments-flex-row-middle-align">
                  <el-col :lg="3" :md="3">
                    <p>{{ $root.labels.time }}:</p>
                  </el-col>
                  <el-col :lg="6" :md="6">
                    <p>{{ $root.labels.customer }}:</p>
                  </el-col>
                  <el-col :lg="6" :md="6">
                    <p>{{ $root.labels.assigned_to }}:</p>
                  </el-col>
                  <el-col :lg="9" :md="9">
                    <p>{{ $root.labels.service }}:</p>
                  </el-col>
                </el-row>
              </el-col>

              <el-col :lg="9">
                <el-row :gutter="10" class="am-appointments-flex-row-middle-align">
                  <el-col :lg="0" :md="3"></el-col>
                  <el-col :lg="5" :md="6">
                    <p>{{ $root.labels.duration }}:</p>
                  </el-col>
                  <el-col :lg="6" :md="6">
                    <p>{{ $root.labels.payment }}:</p>
                  </el-col>
                  <el-col :lg="13" :md="6">
                    <p>{{ $root.labels.status }}:</p>
                  </el-col>
                </el-row>
              </el-col>

            </el-row>
          </div>

          <!-- Appointments List -->
          <div class="am-appointments" v-if="appointments.length > 0">
            <div class="am-appointments-list">
              <el-collapse>
                <el-collapse-item
                    v-for="app in appointments"
                    :key="app.id"
                    :name="app.id"
                    class="am-appointment">

                  <template slot="title">
                    <div class="am-appointment-data">
                      <el-row>
                        <el-col :lg="15">
                          <el-row :gutter="10" class="am-appointments-flex-row-middle-align">

                            <!-- Appointment Time -->
                            <el-col :lg="3" :sm="3">
                              <span class="am-appointment-time" :class="app.status">{{ getFrontedFormattedTime(getTime(app.bookingStart)) }}</span>
                            </el-col>

                            <!-- Appointment Customer(s) -->
                            <el-col :lg="6" :sm="6">
                              <p class="am-col-title">{{ $root.labels.customer }}:</p>
                              <template>
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    placement="top"
                                    :disabled="app.bookings.length === 1"
                                    popper-class="am-align-left"
                                >
                                  <div v-if="app.bookings.length > 1"
                                       slot="content"
                                       v-html="getCustomersFromGroup(app)"></div>
                                  <h3 :class="{ grouped: app.bookings.length > 1 }">
                                    <img
                                        v-show="app.bookings.length > 1"
                                        width="16px"
                                        :src="$root.getUrl+'public/img/group.svg'"
                                        class="svg"
                                    />
                                    <span v-for="(booking, index) in app.bookings">
                                      {{ ((user = getCustomerById(booking.customerId)) !== null ? user.firstName + ' ' + user.lastName : '') }}<span
                                        v-if="app.bookings.length > 1 && index + 1  !== app.bookings.length">,</span>
                                    </span>
                                  </h3>
                                </el-tooltip>
                                <span v-if="app.bookings.length === 1" v-for="booking in app.bookings">{{ ((user = getCustomerById(booking.customerId)) !== null ? user.email : '') }}</span>
                                <span v-if="app.bookings.length > 1">Multiple Emails</span>
                              </template>
                            </el-col>

                            <!-- Appointment Provider -->
                            <el-col :lg="6" :sm="6">
                              <p class="am-col-title">{{ $root.labels.assigned }}:</p>
                              <div class="am-assigned">
                                <img :src="pictureLoad(getProviderById(app.providerId), true)"
                                     @error="imageLoadError(getProviderById(app.providerId), true)"
                                     v-if="options.fetched"/>
                                <h4>
                                  {{ ((user = getProviderById(app.providerId)) !== null ? user.firstName + ' ' +
                                  user.lastName : '') }}
                                </h4>
                              </div>
                            </el-col>

                            <!-- Appointment Service -->
                            <el-col :lg="9" :sm="9">
                              <p class="am-col-title">{{ $root.labels.service }}:</p>
                              <h4>
                                {{ ((service = getServiceById(app.serviceId)) !== null ? service.name : '') }}
                              </h4>
                            </el-col>

                          </el-row>
                        </el-col>

                        <el-col :lg="9">
                          <el-row :gutter="10" class="am-appointments-flex-row-middle-align">
                            <el-col :lg="0" :sm="3"></el-col>

                            <!-- Appointment Duration -->
                            <el-col :lg="5" :sm="6" :xs="12">
                              <p class="am-col-title">{{ $root.labels.duration }}:</p>
                              <h4>{{
                                momentDurationToNiceDurationWithUnit(convertDateTimeRangeDifferenceToMomentDuration(app.bookingStart,
                                app.bookingEnd)) }}</h4>
                            </el-col>

                            <!-- Appointment Payment -->
                            <el-col class="am-appointment-payment" :lg="6" :sm="6" :xs="12">
                              <p class="am-col-title">{{ $root.labels.payment }}:</p>

                              <img
                                  v-for="method in getAppointmentPaymentMethods(app.bookings)"
                                  :src="$root.getUrl + 'public/img/payments/' + method + '.svg'"
                              >
                              <h4>{{ getAppointmentPrice(app.bookings) }}</h4>
                            </el-col>

                            <!-- Appointment Status -->
                            <el-col :lg="8" :sm="6" :xs="17">
                              <div class="am-appointment-status" @click.stop>
                                <span class="am-appointment-status-symbol" :class="app.status"></span>
                                <el-select
                                    v-model="app.status"
                                    :placeholder="$root.labels.status"
                                    @change="updateAppointmentStatus(app, app.status, false)"
                                    :disabled="app.past"
                                >
                                  <el-option
                                      v-for="opt in statuses"
                                      :key="opt.value"
                                      :label="opt.label"
                                      :value="opt.value"
                                      class="am-appointment-status-option"

                                  >
                                    <span class="am-appointment-status-symbol" :class="opt.value">{{ opt.label }}</span>
                                  </el-option>
                                </el-select>
                              </div>
                            </el-col>

                            <!-- Appointment Edit -->
                            <el-col :lg="5" :sm="3" :xs="7">
                              <div class="am-edit-btn" @click.stop>
                                <el-button @click="showDialogEditAppointment(app.id)" :disabled="app.past">
                                  {{ $root.labels.edit }}
                                </el-button>
                              </div>
                            </el-col>

                          </el-row>
                        </el-col>
                      </el-row>
                    </div>
                  </template>

                  <appointment-list-collapsed
                      :app="app"
                      :options="options"
                  >
                  </appointment-list-collapsed>

                </el-collapse-item>
              </el-collapse>
            </div>
          </div>

          <!-- No Results -->
          <div class="am-empty-state am-section" v-if="appointments.length === 0">
            <img :src="$root.getUrl + 'public/img/emptystate.svg'">
            <p>{{ $root.labels.no_today_appointments }}</p>
          </div>

        </div>

        <!-- Button New -->
        <div v-if="$root.settings.capabilities.canWrite === true" id="am-button-new" class="am-button-new">
          <el-popover
              ref="popover"
              placement="top"
              width="160"
              v-model="popover"
              visible-arrow="false"
              popper-class="am-button-popover">
            <div class="am-overlay" @click="popover = false; buttonNewItems = !buttonNewItems">
              <div class="am-button-new-items">
                <transition name="el-zoom-in-bottom">
                  <div v-show="buttonNewItems">
                    <el-button @click="showDialogNewAppointment">{{ $root.labels.new_appointment }}</el-button>
                    <el-button @click="showDialogNewCustomer">{{ $root.labels.create_customer }}</el-button>
                  </div>
                </transition>
              </div>
            </div>
          </el-popover>
          <el-button
              id="am-plus-symbol"
              v-popover:popover
              type="primary"
              icon="el-icon-plus"
              @click="buttonNewItems = !buttonNewItems"
              ref="rotating"
          >
          </el-button>
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
                @saveCallback="getDashboardOptions"
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
              :visible.sync="dialogCustomer"
              :show-close="false"
              v-if="dialogCustomer">
            <dialog-customer
                :customer="customer"
                @saveCallback="saveCustomerCallback"
                @closeDialog="dialogCustomer = false"
            >
            </dialog-customer>
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
      </div>

      <DialogLite/>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/admin-dashboard/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import AppointmentListCollapsed from '../appointments/AppointmentListCollapsed.vue'
  import appointmentMixin from '../../../js/backend/mixins/appointmentMixin'
  import appointmentPriceMixin from '../../../js/backend/mixins/appointmentPriceMixin'
  import BarChart from '../../../js/backend/components/barchart'
  import customerMixin from '../../../js/backend/mixins/customerMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import DialogAppointment from '../appointments/DialogAppointment.vue'
  import DialogCustomer from '../customers/DialogCustomer.vue'
  import DialogExport from '../parts/DialogExport.vue'
  import DialogPayment from '../finance/DialogFinancePayment.vue'
  import DoughnutChart from '../../../js/backend/components/doughnutchart'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import entitiesMixin from '../../../js/common/mixins/entitiesMixin'
  import Form from 'form-object'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import moment from 'moment'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import PageHeader from '../parts/PageHeader.vue'
  import paymentMixin from '../../../js/backend/mixins/paymentMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'

  export default {

    mixins: [paymentMixin, entitiesMixin, appointmentMixin, imageMixin, dateMixin, durationMixin, priceMixin, customerMixin, notifyMixin, appointmentPriceMixin],

    data () {
      return {
        customer: null,
        appointments: [],
        buttonNewItems: false,
        chartTabs: 'employee',
        customersChartData: {
          labels: [this.$root.labels.new, this.$root.labels.returning, ''],
          datasets: [
            {
              backgroundColor: ['#1a84ee', '#ffd400', '#ebeef5'],
              borderColor: '#E2E6EC',
              data: [0, 0, 1],
              hoverBackgroundColor: ['#117ce6', '#eec600', '#ebeef5'],
              hoverBorderColor: '#D3DDEA'
            }
          ]
        },
        dialogAppointment: false,
        dialogPayment: false,
        dialogExport: false,
        employees: [],
        employeesChartData: {
          labels: [],
          datasets: [
            {
              backgroundColor: '#D3DDEA',
              borderColor: '#E2E6EC',
              data: [],
              hoverBackgroundColor: '#c8d4e5',
              hoverBorderColor: '#D3DDEA',
              label: this.$root.labels.views,
              borderWidth: 2
            },
            {
              backgroundColor: '#5FCE19',
              borderColor: '#E2E6EC',
              data: [],
              hoverBackgroundColor: '#58BF17',
              hoverBorderColor: '#D3DDEA',
              label: this.$root.labels.scheduled_appointments,
              borderWidth: 2
            }
          ]
        },
        employeesStats: [],
        fetched: false,
        form: new Form(),
        locations: [],
        locationsChartData: {
          labels: [],
          datasets: [
            {
              backgroundColor: '#D3DDEA',
              borderColor: '#E2E6EC',
              data: [],
              hoverBackgroundColor: '#c8d4e5',
              hoverBorderColor: '#D3DDEA',
              label: this.$root.labels.views,
              borderWidth: 2
            },
            {
              backgroundColor: '#5FCE19',
              borderColor: '#E2E6EC',
              data: [],
              hoverBackgroundColor: '#58BF17',
              hoverBorderColor: '#D3DDEA',
              label: this.$root.labels.scheduled_appointments,
              borderWidth: 2
            }
          ]
        },
        locationsStats: [],
        params: {
          dates: {
            start: moment().startOf('week').day(
              this.getWordPressFirstDayOfWeek() === 6 ? -1 : this.getWordPressFirstDayOfWeek()
            ).toDate(),
            end: moment().startOf('week').day(
              this.getWordPressFirstDayOfWeek() === 6 ? -1 : this.getWordPressFirstDayOfWeek()
            ).add(6, 'days').toDate()
          }
        },
        popover: false,
        selectedPaymentModalData: null,
        services: [],
        servicesChartData: {
          labels: [],
          datasets: [
            {
              backgroundColor: '#D3DDEA',
              borderColor: '#E2E6EC',
              data: [],
              hoverBackgroundColor: '#c8d4e5',
              hoverBorderColor: '#D3DDEA',
              label: this.$root.labels.views,
              borderWidth: 2
            },
            {
              backgroundColor: '#5FCE19',
              borderColor: '#E2E6EC',
              data: [],
              hoverBackgroundColor: '#58BF17',
              hoverBorderColor: '#D3DDEA',
              label: this.$root.labels.scheduled_appointments,
              borderWidth: 2
            }
          ]
        },
        stats: null,
        dateExportParams: {
          dates: {
            start: moment().toDate(),
            end: moment().toDate()
          }
        },
        totalPastPeriodCustomers: 0
      }
    },

    created () {
      this.getDashboardOptions()
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

      getDashboardOptions () {
        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: !AMELIA_LITE_VERSION ? ['categories', 'locations', 'employees', 'customers', 'custom_fields'] : ['categories', 'employees', 'customers']
          }
        })
          .then(response => {
            this.options.entities = response.data.data

            this.options.entities.services = this.getServicesFromCategories()

            this.options.entities.services.forEach(function (service) {
              service.extras.forEach(function (extra) {
                extra.extraId = extra.id
              })
            })

            this.setBookings(0)
            this.setEntitiesFilter()

            this.options.fetched = true
            this.getDashboard()
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
            this.options.fetched = true
          })
      },

      getDashboard () {
        let params = JSON.parse(JSON.stringify(this.params))
        let dates = []

        if (params.dates) {
          if (params.dates.start) {
            dates.push(moment(params.dates.start).format('YYYY-MM-DD'))
          }

          if (params.dates.end) {
            dates.push(moment(params.dates.end).format('YYYY-MM-DD'))
          }

          params.dates = dates
        }

        this.$http.get(`${this.$root.getAjaxUrl}/stats`, {
          params: params
        })
          .then(response => {
            this.stats = response.data.data.stats
            this.appointments = response.data.data.appointments
            this.fillCustomersChart(response.data.data.customersStats)
            this.employeesStats = response.data.data.employeesStats
            this.fillEmployeesChart(response.data.data.employeesStats)
            this.servicesStats = response.data.data.servicesStats
            this.fillServicesChart(response.data.data.servicesStats)
            this.locationsStats = response.data.data.locationsStats
            this.fillLocationsChart(response.data.data.locationsStats)

            this.updateCharts()
            this.filterEmployeesChart()
            this.filterServicesChart()
            this.filterLocationsChart()
            this.fetched = true
          })
      },

      navigateTo (item) {
        let url = 'admin.php?page=wpamelia-' + item.redirect + '&dateFrom=' + moment(this.params.dates.start).format('YYYY-MM-DD') + '&dateTo=' + moment(this.params.dates.end).format('YYYY-MM-DD')

        for (let param in item['params']) {
          if (item['params'].hasOwnProperty(param)) {
            url += '&' + param + '=' + item['params'][param]
          }
        }

        window.location = url
      },

      updateCharts: !AMELIA_LITE_VERSION ? function () {
        if (typeof this.$refs.customersChart !== 'undefined') { this.$refs.customersChart.update() }
        if (typeof this.$refs.employeesChart !== 'undefined') { this.$refs.employeesChart.update() }
        if (typeof this.$refs.servicesChart !== 'undefined') { this.$refs.servicesChart.update() }
        if (typeof this.$refs.locationsChart !== 'undefined') { this.$refs.locationsChart.update() }
      } : function () {},

      fillCustomersChart: !AMELIA_LITE_VERSION ? function (data) {
        this.customersChartData.datasets[0].data.splice(0, 1, data.newCustomersCount)
        this.customersChartData.datasets[0].data.splice(1, 1, data.returningCustomersCount)
        this.customersChartData.datasets[0].data.splice(2, 1, (this.newCustomers === 0 && this.returningCustomers === 0) ? 1 : 0)
        this.totalPastPeriodCustomers = data.totalPastPeriodCustomers
      } : function () {},

      fillEmployeesChart: !AMELIA_LITE_VERSION ? function (data) {
        this.employeesChartData.labels = []
        this.employeesChartData.datasets[0].data = []
        this.employeesChartData.datasets[1].data = []

        for (let i = 0; i < data.length; i++) {
          this.employeesChartData.labels.push(data[i].name)
          this.employeesChartData.datasets[0].data.push(data[i].views)
          this.employeesChartData.datasets[1].data.push(data[i].appointments)
        }
      } : function () {},

      filterEmployeesChart: !AMELIA_LITE_VERSION ? function () {
        let employeesStats = []
        let employeesToRemoveFromStats = []

        for (let i = 0; i < this.employeesStats.length; i++) {
          if (_.indexOf(this.employees, this.employeesStats[i].id) === -1) {
            employeesToRemoveFromStats.push(this.employeesStats[i])
          }
        }

        if (_.difference(this.employeesStats, employeesToRemoveFromStats).length === 0) {
          employeesStats = this.employees.length === 0 ? this.employeesStats : []
        } else {
          employeesStats = _.difference(this.employeesStats, employeesToRemoveFromStats)
        }

        this.fillEmployeesChart(employeesStats)

        if (typeof this.$refs.employeesChart !== 'undefined') {
          this.$refs.employeesChart.update()
        }
      } : function () {},

      fillServicesChart: !AMELIA_LITE_VERSION ? function (data) {
        this.servicesChartData.labels = []
        this.servicesChartData.datasets[0].data = []
        this.servicesChartData.datasets[1].data = []

        for (let i = 0; i < data.length; i++) {
          this.servicesChartData.labels.push(data[i].name)
          this.servicesChartData.datasets[0].data.push(data[i].views)
          this.servicesChartData.datasets[1].data.push(data[i].appointments)
        }
      } : function () {},

      filterServicesChart: !AMELIA_LITE_VERSION ? function () {
        let servicesStats = []
        let servicesToRemoveFromStats = []

        for (let i = 0; i < this.servicesStats.length; i++) {
          if (_.indexOf(this.services, this.servicesStats[i].id) === -1) {
            servicesToRemoveFromStats.push(this.servicesStats[i])
          }
        }

        if (_.difference(this.servicesStats, servicesToRemoveFromStats).length === 0) {
          servicesStats = this.services.length === 0 ? this.servicesStats : []
        } else {
          servicesStats = _.difference(this.servicesStats, servicesToRemoveFromStats)
        }

        this.fillServicesChart(servicesStats)

        if (typeof this.$refs.servicesChart !== 'undefined') {
          this.$refs.servicesChart.update()
        }
      } : function () {},

      fillLocationsChart: !AMELIA_LITE_VERSION ? function (data) {
        this.locationsChartData.labels = []
        this.locationsChartData.datasets[0].data = []
        this.locationsChartData.datasets[1].data = []

        for (let i = 0; i < data.length; i++) {
          this.locationsChartData.labels.push(data[i].name)
          this.locationsChartData.datasets[0].data.push(data[i].views)
          this.locationsChartData.datasets[1].data.push(data[i].appointments)
        }
      } : function () {},

      filterLocationsChart: !AMELIA_LITE_VERSION ? function () {
        let locationsStats = []
        let locationsToRemoveFromStats = []

        for (let i = 0; i < this.locationsStats.length; i++) {
          if (_.indexOf(this.locations, this.locationsStats[i].id) === -1) {
            locationsToRemoveFromStats.push(this.locationsStats[i])
          }
        }

        if (_.difference(this.locationsStats, locationsToRemoveFromStats).length === 0) {
          locationsStats = this.locations.length === 0 ? this.locationsStats : []
        } else {
          locationsStats = _.difference(this.locationsStats, locationsToRemoveFromStats)
        }

        this.fillLocationsChart(locationsStats)

        if (typeof this.$refs.locationsChart !== 'undefined') {
          this.$refs.locationsChart.update()
        }
      } : function () {}
    },

    computed: {
      newCustomers () {
        return this.customersChartData.datasets[0].data[0]
      },

      returningCustomers () {
        return this.customersChartData.datasets[0].data[1]
      },

      totalCustomers () {
        return this.newCustomers + this.returningCustomers
      },

      newCustomersPercentage () {
        return this.totalCustomers === 0 ? 0 : parseFloat((this.newCustomers / this.totalCustomers * 100).toFixed(1))
      },

      returnedCustomersPercentage () {
        return this.totalCustomers === 0 ? 0 : parseFloat((this.returningCustomers / this.totalCustomers * 100).toFixed(1))
      },

      customersGrowthPercentage () {
        if (this.totalCustomers === 0 && this.totalPastPeriodCustomers === 0) {
          return 0
        }

        if (this.totalCustomers === 0 && this.totalPastPeriodCustomers !== 0) {
          return '-∞'
        }

        if (this.totalCustomers !== 0 && this.totalPastPeriodCustomers === 0) {
          return '+∞'
        }

        return this.totalCustomers - this.totalPastPeriodCustomers === 0 ? 0 : ((this.totalCustomers - this.totalPastPeriodCustomers) / this.totalPastPeriodCustomers * 100).toFixed(1)
      },

      growthClass () {
        if (this.customersGrowthPercentage > 0 || this.customersGrowthPercentage === '+∞') {
          return 'am-growth-increase'
        }

        if (this.customersGrowthPercentage < 0 || this.customersGrowthPercentage === '-∞') {
          return 'am-growth-decrease'
        }

        return 'am-growth-equal'
      },

      growthPercentageCharacter () {
        if (this.customersGrowthPercentage === '+∞' || this.customersGrowthPercentage === '-∞') {
          return ''
        }

        return '%'
      }
    },

    components: {
      BarChart,
      DoughnutChart,
      DialogCustomer,
      DialogAppointment,
      DialogPayment,
      PageHeader,
      DialogExport,
      AppointmentListCollapsed
    }

  }
</script>
