<template>
  <div class="am-wrap">
    <div id="am-employees" class="am-body">

      <!-- Page Header -->
      <page-header :employeesTotal="count" @newEmployeeBtnClicked="showDialogNewEmployee()"></page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched || !options.fetched">
        <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
      </div>

      <!-- Empty State -->
      <div class="am-empty-state am-section"
           v-if="fetched && employees.length === 0 && !filterApplied && fetchedFiltered">
        <img :src="$root.getUrl + 'public/img/emptystate.svg'">
        <h2>{{ $root.labels.no_employees_yet }}</h2>
        <p>{{ $root.labels.click_add_employee }}</p>
      </div>

      <!-- Employees -->
      <div
          v-show="fetched && options.fetched && (employees.length !== 0 || employees.length === 0 && filterApplied || !fetchedFiltered)"
      >

        <!-- Filters -->
        <div class="am-employees-filter am-section">
          <el-form class="demo-form-inline">
            <el-row :gutter="16">

              <!-- Global Search -->
              <el-popover :disabled="!$root.isLite" ref="filterEmployeePop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
              <el-col :lg="8" v-popover:filterEmployeePop>
                <el-form-item>
                  <div class="am-search">
                    <el-input
                        class="calc-width"
                        :placeholder="searchPlaceholder"
                        v-model="params.search"
                        :disabled="$root.isLite"
                    >
                    </el-input>
                    <el-button class="button-filter-toggle am-button-icon" title="Toggle Filters"
                               @click="filterFields = !filterFields">
                      <img class="svg" alt="Toggle Filters"
                           :src="$root.getUrl+'public/img/filter.svg'"/>
                    </el-button>
                  </div>
                </el-form-item>
              </el-col>

              <div class="am-filter-fields">

                <!-- Services -->
                <transition name="fade">
                  <div v-show="filterFields">
                    <el-popover :disabled="!$root.isLite" ref="filterServicePop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                    <el-col :md="options.locations.length ? 8 : 12" :lg="options.locations.length ? 5 : 8" v-popover:filterServicePop>
                      <el-form-item>
                        <el-select
                            v-model="params.services"
                            multiple
                            filterable
                            :placeholder="$root.labels.services"
                            @change="changeFilter"
                            collapse-tags
                            :disabled="$root.isLite"
                        >
                          <div v-for="category in options.categorized"
                               :key="category.id">
                            <div class="am-drop-parent"
                                 @click="selectAllInCategory(category.id)"
                            >
                              <span>{{ category.name }}</span>
                            </div>
                            <el-option
                                v-for="service in category.serviceList"
                                :key="service.id"
                                :label="service.name"
                                :value="service.id"
                                class="am-drop-child"
                            >
                            </el-option>
                          </div>
                        </el-select>
                      </el-form-item>
                    </el-col>
                  </div>
                </transition>

                <!-- Location -->
                <transition name="fade" v-if="options.locations.length">
                  <div v-show="filterFields">
                    <el-popover :disabled="!$root.isLite" ref="filterLocationPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                    <el-col :md="8" :lg="5" v-popover:filterLocationPop>
                      <el-form-item>
                        <el-select
                            v-model="params.location"
                            :placeholder="$root.labels.location"
                            clearable
                            @change="changeFilter"
                            :disabled="$root.isLite"
                        >
                          <el-option
                              v-for="location in options.locations"
                              :key="location.id"
                              :label="location.name"
                              :value="location.id"
                          >
                          </el-option>
                        </el-select>
                      </el-form-item>
                    </el-col>
                  </div>
                </transition>

                <!-- Sort -->
                <transition name="fade">
                  <div v-show="filterFields">
                    <el-col :md="options.locations.length ? 8 : 12" :lg="options.locations.length ? 6 : 8">
                      <el-popover :disabled="!($root.isLite)" ref="filterSortPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                      <el-form-item  v-popover:filterSortPop>
                        <el-select v-model="params.sort"
                                   :placeholder="$root.labels.sort"
                                   class="calc-width sort"
                                   @change="filterData"
                                   :disabled="$root.isLite"
                        >

                          <el-option
                              v-for="option in options.sort"
                              :key="option.value"
                              :label="option.label"
                              :value="option.value"
                          >
                          </el-option>
                        </el-select>
                      </el-form-item>
                    </el-col>
                  </div>
                </transition>

              </div>

              <!-- Content View -->
              <div class="am-filter-buttons">
                <el-tooltip class="item" effect="dark" :content="$root.labels.grid_view" placement="top">
                  <el-button :title="$root.labels.grid_view"
                             @click="gridView"
                             class="am-button-icon change-view"
                             :class="{active:gridViewActive}">
                    <img class="svg" alt="Grid View" :src="$root.getUrl+'public/img/grid-view.svg'"/>
                  </el-button>
                </el-tooltip>
                <el-tooltip class="item" effect="dark" :content="$root.labels.table_view" placement="top">
                  <el-button :title="$root.labels.table_view"
                             @click="tableView"
                             class="am-button-icon change-view"
                             :class="{active:tableViewActive}"
                  >
                    <img class="svg" alt="Table View" :src="$root.getUrl+'public/img/list-view.svg'"/>
                  </el-button>
                </el-tooltip>
              </div>

            </el-row>
          </el-form>
        </div>

        <!-- No Results -->
        <div class="am-empty-state am-section"
             v-show="fetched && employees.length === 0 && filterApplied && fetchedFiltered">
          <img :src="$root.getUrl + 'public/img/emptystate.svg'">
          <h2>{{ $root.labels.no_results }}</h2>
        </div>

        <!-- Content Table View -->
        <div class="am-employees-table-view"
             v-if="tableViewActive"
             v-show="fetchedFiltered && employees.length !== 0"
        >
          <div class="am-employees-list-head">
            <el-row>
              <el-col :lg="1" v-if="$root.settings.capabilities.canDelete === true">
                <p>
                  <el-checkbox
                      v-model="checkEmployeeData.allChecked"
                      @change="checkEmployeeData = handleCheckAll(employees, checkEmployeeData)">
                  </el-checkbox>
                </p>
              </el-col>
              <el-col :lg="2"><p>{{ $root.labels.activity }}:</p></el-col>
              <el-col :lg="6"><p>{{ $root.labels.employee }}:</p></el-col>
              <el-col :lg="7"><p>{{ $root.labels.email }}:</p></el-col>
              <el-col :lg="7"><p>{{ $root.labels.phone }}:</p></el-col>
            </el-row>
          </div>
          <transition name="fadeIn">
            <div class="am-employees-list">
              <div
                  v-for="employee in employees"
                  v-if="employee.status === 'hidden' || employee.status === 'visible'"
                  :class="{'am-employee-row am-hidden-entity' : employee.status === 'hidden', 'am-employee-row' : employee.status === 'visible'}"
              >
                <el-row>
                  <el-col :lg="1" :sm="1" v-if="$root.settings.capabilities.canDelete === true">
                    <span class="am-employee-col" @click.stop>
                      <el-checkbox
                          v-model="employee.checked"
                          @change="checkEmployeeData = handleCheckSingle(employees, checkEmployeeData)"
                      >
                      </el-checkbox>
                    </span>
                  </el-col>
                  <el-col :lg="2" :md="8">
                    <p class="am-col-title">{{ $root.labels.status }}:</p>
                    <div class="am-employee-col">
                      <span
                          v-if="employee.status === 'visible'"
                          class="am-employee-status-label"
                          :class="employee.activity"
                      >
                        {{ getEmployeeActivityLabel(employee.activity) }}
                      </span>
                    </div>
                  </el-col>
                  <el-col :lg="6" :md="16">
                    <p class="am-col-title">{{ $root.labels.employee }}:</p>
                    <div class="am-employee-col">
                      <img :src="pictureLoad(employee, true)" @error="imageLoadError(employee, true)"/>
                      <h4>{{ employee.firstName }} {{ employee.lastName }}</h4>
                    </div>
                  </el-col>
                  <el-col :lg="7" :md="8">
                    <p class="am-col-title">{{ $root.labels.email }}:</p>
                    <div class="am-employee-col">
                      <h4 class="am-email">{{ employee.email }}</h4>
                    </div>
                  </el-col>
                  <el-col :lg="5" :md="16">
                    <p class="am-col-title">{{ $root.labels.phone }}:</p>
                    <div class="am-employee-col">
                      <h4>{{ employee.phone }}</h4>
                    </div>
                  </el-col>
                  <el-col :lg="3" :md="8">
                    <div class="am-employee-col am-edit-btn">
                      <el-button @click="showDialogEditEmployee(employee.id)">{{ $root.labels.edit }}</el-button>
                    </div>
                  </el-col>
                </el-row>
              </div>
            </div>
          </transition>
        </div>

        <!-- Content Grid View -->
        <div class="am-employees-grid-view am-section" v-if="gridViewActive"
             v-show="fetchedFiltered && employees.length !== 0">
          <el-row :gutter="16">
            <template v-for="employee in employees">
              <el-col :lg="8" :md="12" v-if="employee.status === 'hidden' || employee.status === 'visible'">
                <transition name="fadeIn">
                  <div class="am-employee-card" @click="showDialogEditEmployee(employee.id)"
                       :class="{'am-hidden-entity' : employee.status === 'hidden'}"
                  >
                    <span
                        v-if="employee.status === 'visible'"
                        class="am-employee-status-label"
                        :class="employee.activity"
                    >
                      {{ getEmployeeActivityLabel(employee.activity) }}
                    </span>
                    <img :src="pictureLoad(employee, true)" @error="imageLoadError(employee, true)"/>
                    <div class="">
                      <h4>{{ employee.firstName + ' ' + employee.lastName }}</h4>
                      <p class="am-email">{{ employee.email }}</p>
                      <p>{{ employee.phone }}</p>
                    </div>
                  </div>
                </transition>
              </el-col>
            </template>
          </el-row>
        </div>

        <!-- Pagination -->
        <pagination-block
            v-if="$root.settings.capabilities.canReadOthers === true"
            :params="params"
            :count="options.countFiltered"
            :label="$root.labels.employees_lower"
            :visible="fetched && employees.length !== 0 && fetchedFiltered"
            @change="filterData"
        >
        </pagination-block>
      </div>

      <!-- Content Spinner -->
      <div class="am-spinner am-section" v-show="fetched && !fetchedFiltered">
        <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
      </div>

      <!-- Button New -->
      <div v-if="$root.settings.capabilities.canWriteOthers === true"
           id="am-button-new"
           class="am-button-new"
           v-popover:addEmployeePlusPop
      >
        <el-popover :disabled="!($root.isLite && employees.length > 0)" ref="addEmployeePlusPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-button id="am-plus-symbol"
                   type="primary"
                   icon="el-icon-plus"
                   @click="showDialogNewEmployee()"
                   :class="{'am-lite-disabled': ($root.isLite && employees.length > 0)}"
                   :disabled="$root.isLite && employees.length > 0"
        ></el-button>
      </div>

      <!-- Selected Popover Delete -->
      <group-delete
          name="users/providers"
          :entities="employees"
          :checkGroupData="checkEmployeeData"
          :confirmDeleteMessage="$root.labels.confirm_delete_employee"
          :successMessage="{single: $root.labels.employee_deleted, multiple: $root.labels.employees_deleted}"
          :errorMessage="{single: $root.labels.employee_not_deleted, multiple: $root.labels.employees_not_deleted}"
          @groupDeleteCallback="groupDeleteCallback"
      >
      </group-delete>

      <!-- Dialog Employee -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-employee"
            :visible.sync="dialogEmployee"
            :show-close="false"
            v-if="dialogEmployee"
        >
          <dialog-employee
              :locations=options.locations
              :employee="employee"
              :futureAppointments="futureAppointments"
              :editCategorizedServiceList="editCategorizedServiceList"
              :editWeekDayList="employee ? (employee.id !== 0 ? editWeekDayList : (isDuplicated ? editWeekDayList : this.$root.settings.weekSchedule)) : null"
              :companyDaysOff="$root.settings.daysOff"
              @saveCallback="saveEmployeeCallback"
              @duplicateCallback="duplicateEmployeeCallback"
              @closeDialog="dialogEmployee = false"
              @showCompanyDaysOffSettingsDialog="dialogCompanyDaysOffSettings = true"
              :isDisabledDuplicate="$root.isLite && employees.length > 0"
          >
          </dialog-employee>
        </el-dialog>
      </transition>

      <!-- Dialog Company Days Off Settings -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-employee"
            :visible.sync="dialogCompanyDaysOffSettings"
            :show-close="false"
            v-if="dialogCompanyDaysOffSettings">
          <dialog-settings-work-hours-days-off
              @closeDialogSettingsWorkHoursDaysOff="dialogCompanyDaysOffSettings = false"
              @updateSettings="updateCompanyWorkingHoursAndDaysOffSettings"
              :daysOff="$root.settings.daysOff"
              :showWorkingHours="false"
              :showDaysOff="true"
          >
          </dialog-settings-work-hours-days-off>
        </el-dialog>
      </transition>

      <DialogLite/>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/employees/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import DialogEmployee from './DialogEmployee.vue'
  import DialogSettingsWorkHoursDaysOff from '../settings/DialogSettingsWorkHoursDaysOff.vue'
  import PageHeader from '../parts/PageHeader.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import employeeMixin from '../../../js/common/mixins/employeeMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import checkMixin from '../../../js/backend/mixins/checkMixin'
  import GroupDelete from '../parts/GroupDelete.vue'
  import PaginationBlock from '../parts/PaginationBlock.vue'
  import helperMixin from '../../../js/backend/mixins/helperMixin'
  import Form from 'form-object'

  export default {
    mixins: [imageMixin, notifyMixin, checkMixin, helperMixin, employeeMixin],

    data () {
      return {
        checkEmployeeData: {
          toaster: false,
          allChecked: false
        },
        count: 0,
        dialogCompanyDaysOffSettings: false,
        dialogEmployee: false,
        editCategorizedServiceList: null,
        editWeekDayList: [],
        employee: null,
        employees: [],
        fetched: false,
        fetchedFiltered: false,
        filterFields: true,
        form: new Form(),
        gridViewActive: true,
        isDuplicated: false,
        options: {
          categorized: [],
          countFiltered: 0,
          fetched: false,
          locations: [],
          sort: [
            {
              value: 'employee',
              label: this.$root.labels.name_ascending
            },
            {
              value: '-employee',
              label: this.$root.labels.name_descending
            }
          ]
        },
        params: {
          page: 1,
          sort: 'employee',
          search: '',
          services: [],
          location: ''
        },
        searchPlaceholder: this.$root.labels.employee_search_placeholder,
        settings: {
          daysOff: []
        },
        showDeleteConfirmation: false,
        tableViewActive: false,
        timer: null,
        futureAppointments: {}
      }
    },

    created () {
      this.googleCalendarSync()
      this.fetchData()
      this.handleResize()
    },

    mounted () {
      this.inlineSVG()
    },

    methods: {
      fetchData () {
        this.fetched = false
        this.options.fetched = false

        this.getProviders()
        this.getProvidersOptions()
      },

      filterData () {
        this.fetchedFiltered = false
        this.getProviders()
      },

      getProviders () {
        Object.keys(this.params).forEach((key) => (!this.params[key]) && delete this.params[key])

        this.$http.get(`${this.$root.getAjaxUrl}/users/providers`, {
          params: this.params
        })
          .then(response => {
            response.data.data.users.forEach(function (empItem) {
              empItem.checked = false
            })

            this.parseProviders(response)

            this.fetched = true
            this.fetchedFiltered = true
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
            this.fetchedFiltered = true
          })
      },

      parseProviders: !AMELIA_LITE_VERSION ? function (response) {
        this.employees = response.data.data.users

        this.count = response.data.data.countTotal
        this.options.countFiltered = response.data.data.countFiltered
      } : function (response) {
        this.employees = response.data.data.users.slice(0, 1)

        this.count = this.employees.length
        this.options.countFiltered = this.employees.length
      },

      getProvidersOptions () {
        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: !AMELIA_LITE_VERSION ? ['categories', 'locations', 'appointments'] : ['categories', 'appointments']
          }
        })
          .then(response => {
            let appointments = response.data.data.appointments['futureAppointments']

            for (let key in appointments) {
              let serviceId = appointments[key].serviceId
              let providerId = appointments[key].providerId

              if (!(providerId in this.futureAppointments)) {
                this.futureAppointments[providerId] = []
                this.futureAppointments[providerId].push(serviceId)
              } else if (this.futureAppointments[providerId].indexOf(serviceId) === -1) {
                this.futureAppointments[providerId].push(serviceId)
              }
            }

            response.data.data.categories.forEach(function (catItem) {
              catItem.serviceList.forEach(function (catSerItem) {
                catSerItem.state = AMELIA_LITE_VERSION
              })
            })

            this.parseProvidersOptions(response)

            this.options.fetched = true
          })
          .catch(e => {
            console.log(e.message)
            this.options.fetched = true
          })
      },

      parseProvidersOptions: !AMELIA_LITE_VERSION ? function (response) {
        this.options.locations = response.data.data.locations
        this.options.categorized = response.data.data.categories
      } : function (response) {
        this.options.locations = []
        this.options.categorized = response.data.data.categories.slice(0, 1)
        if (this.options.categorized.length) {
          this.options.categorized[0].serviceList = this.options.categorized[0].serviceList.slice(0, 4)
        }
      },

      getProvider (id) {
        this.$http.get(`${this.$root.getAjaxUrl}/users/providers/` + id)
          .then(response => {
            this.employee = response.data.data.user

            if (!response.data.data.successfulGoogleConnection) {
              this.notify(this.$root.labels.error, this.$root.labels.google_calendar_error, 'error')

              this.employee.googleCalendar = {
                calendarId: null,
                calendarList: []
              }
            }

            this.editWeekDayList = this.getParsedWeekDayList(this.employee)
            this.editCategorizedServiceList = this.getParsedCategorizedServiceList(this.employee, this.options.categorized)
            this.fetchedFiltered = true
          })
          .catch(e => {
            console.log(e.message)
            this.fetchedFiltered = true
          })
      },

      changeFilter: !AMELIA_LITE_VERSION ? function () {
        this.params.page = 1
        this.filterData()
      } : function (employee) {},

      handleResize () {
        this.filterFields = window.innerWidth >= 992
      },

      showDialogNewEmployee () {
        this.isDuplicated = false
        this.employee = this.getInitEmployeeObject()
        this.editWeekDayList = this.getParsedWeekDayList(this.employee)
        this.editCategorizedServiceList = this.getParsedCategorizedServiceList(this.employee, this.options.categorized)
        this.dialogEmployee = true
      },

      showDialogEditEmployee (id) {
        this.isDuplicated = false
        this.employee = null
        this.dialogEmployee = true
        this.getProvider(id)
      },

      duplicateEmployeeCallback: !AMELIA_LITE_VERSION ? function (employee) {
        this.isDuplicated = true
        this.employee = employee
        this.employee.id = 0
        this.employee.externalId = ''
        this.employee.email = ''

        setTimeout(() => {
          this.dialogEmployee = true
        }, 300)
      } : function (employee) {},

      saveEmployeeCallback () {
        this.dialogEmployee = false
        this.filterData()
      },

      selectAllInCategory: !AMELIA_LITE_VERSION ? function (id) {
        let services = this.options.categorized.find(category => category.id === id).serviceList
        let servicesIds = services.map(service => service.id)

        // Deselect all services if they are already selected
        if (_.isEqual(_.intersection(servicesIds, this.params.services), servicesIds)) {
          this.params.services = _.difference(this.params.services, servicesIds)
        } else {
          this.params.services = _.uniq(this.params.services.concat(servicesIds))
        }

        this.filterData()
      } : function (employee) {},

      gridView: function () {
        this.gridViewActive = true
        this.tableViewActive = false
      },

      tableView: function () {
        this.gridViewActive = false
        this.tableViewActive = true
      },

      getParsedWeekDayList: !AMELIA_LITE_VERSION ? function (employee) {
        let tempList = []

        let days = [
          this.$root.labels.weekday_monday,
          this.$root.labels.weekday_tuesday,
          this.$root.labels.weekday_wednesday,
          this.$root.labels.weekday_thursday,
          this.$root.labels.weekday_friday,
          this.$root.labels.weekday_saturday,
          this.$root.labels.weekday_sunday
        ]

        for (let i = 0; i < 7; i++) {
          tempList.push(
            {
              day: days[i],
              time: [],
              breaks: []
            }
          )
        }

        if (employee) {
          employee.weekDayList.forEach(function (weekDayItem) {
            let dayIndex = weekDayItem.dayIndex - 1

            weekDayItem.timeOutList.forEach(function (timeOutItem) {
              tempList[dayIndex].breaks.push(
                {
                  time: [
                    timeOutItem.startTime.substring(0, timeOutItem.startTime.length - 3),
                    timeOutItem.endTime.substring(0, timeOutItem.endTime.length - 3)
                  ]
                })
            })

            tempList[dayIndex].time = [
              weekDayItem.startTime.substring(0, weekDayItem.startTime.length - 3),
              weekDayItem.endTime.substring(0, weekDayItem.endTime.length - 3)
            ]

            tempList[dayIndex].day = days[weekDayItem.dayIndex - 1]
          })
        }

        return tempList
      } : function () {
        return this.$root.settings.weekSchedule
      },

      getParsedCategorizedServiceList (employee, categories) {
        let categorizedServiceList = []

        categories.forEach(function (catItem) {
          let serviceList = []

          catItem.serviceList.filter(service =>
            (service.status === 'visible') ||
            (service.status === 'hidden' && employee.serviceList.map(employeeService => employeeService.id).indexOf(service.id) !== -1)
          ).forEach(function (catSerItem) {
            let employeeService = null

            if (employee) {
              employee.serviceList.forEach(function (serItem) {
                if (serItem.id === catSerItem.id) {
                  employeeService = Object.assign({}, serItem)
                  employeeService.state = true
                }
              })
            }

            if (employeeService) {
              serviceList.push(employeeService)
            } else {
              let service = Object.assign({}, catSerItem)
              service.state = AMELIA_LITE_VERSION
              serviceList.push(service)
            }
          })

          categorizedServiceList.push(
            {
              id: catItem.id,
              name: catItem.name,
              serviceList: serviceList
            }
          )
        })

        return categorizedServiceList
      },

      updateCompanyWorkingHoursAndDaysOffSettings: !AMELIA_LITE_VERSION ? function (settings) {
        this.settings['daysOff'] = settings['daysOff']
        this.$http.post(`${this.$root.getAjaxUrl}/settings`, this.settings)
          .then(response => {
            // Update front-end days-off settings with added days off
            this.$root.settings.daysOff = response.data.data.settings.daysOff
            this.notify(this.$root.labels.success, this.$root.labels.settings_saved, 'success')
          })
          .catch(e => {
            this.notify(this.$root.labels.error, e.message, 'error')
          })
      } : function (employee) {},

      groupDeleteCallback () {
        this.checkEmployeeData.allChecked = false
        this.checkEmployeeData.toaster = false
        this.fetchData()
      },

      getInitEmployeeObject () {
        return {
          id: 0,
          type: 'provider',
          status: 'visible',
          firstName: '',
          lastName: '',
          email: '',
          externalId: '',
          locationId: '',
          phone: '',
          googleCalendar: [],
          note: '',
          pictureFullPath: '',
          pictureThumbPath: '',
          serviceList: [],
          weekDayList: [],
          dayOffList: []
        }
      },

      googleCalendarSync: !AMELIA_LITE_VERSION ? function () {
        if (this.$root.settings.googleCalendar.clientID && this.$root.settings.googleCalendar.clientSecret) {
          let queryParams = this.getUrlQueryParams(window.location.href)

          if (queryParams['code']) {
            this.fetchAccessTokenWithAuthCode(queryParams['code'])
          }
        }
      } : function () {},

      fetchAccessTokenWithAuthCode: !AMELIA_LITE_VERSION ? function (authCode) {
        let employeeId = this.getUrlQueryParams(window.location.href)['state']

        this.form.post(`${this.$root.getAjaxUrl}/google/authorization/token`, {
          'authCode': authCode,
          'userId': employeeId
        }).then(() => {
          let redirectURL = this.removeURLParameter(window.location.href, 'code')
          redirectURL = this.removeURLParameter(redirectURL, 'state')
          history.pushState(null, null, redirectURL + '#/employees')

          this.showDialogEditEmployee(employeeId)
        }).catch(e => {
          console.log(e)
        })
      } : function () {}
    },

    computed: {
      filterApplied () {
        return !!this.params.search || !!this.params.services.length || !!this.params.location
      }
    },

    watch: {
      'params.search' () {
        if (typeof this.params.search !== 'undefined') {
          this.fetchedFiltered = false
          clearTimeout(this.timer)
          this.timer = setTimeout(this.filterData, 500)
        }
      }
    },

    components: {
      PageHeader,
      DialogEmployee,
      DialogSettingsWorkHoursDaysOff,
      GroupDelete,
      PaginationBlock
    }
  }
</script>
