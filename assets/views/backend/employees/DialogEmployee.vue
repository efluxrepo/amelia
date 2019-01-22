<template>
  <div>

    <!-- Dialog Loader -->
    <div class="am-dialog-loader" v-show="dialogLoading">
      <div class="am-dialog-loader-content">
        <img :src="$root.getUrl+'public/img/spinner.svg'" class="">
        <p>{{ $root.labels.loader_message }}</p>
      </div>
    </div>

    <!-- Dialog Content -->
    <div class="am-dialog-scrollable" v-if="employee && !dialogLoading">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="14">
            <h2 v-if="employee.id !== 0">{{ $root.labels.edit_employee }} </h2>
            <h2 v-else>{{ $root.labels.new_employee }}</h2>
          </el-col>
          <el-col :span="10" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form :model="employee" ref="employee" :rules="rules" label-position="top" @submit.prevent="onSubmit">
        <el-tabs v-model="employeeTabs">

          <!-- Details -->
          <el-tab-pane :label="$root.labels.details" name="details">

            <!-- Profile Photo -->
            <div class="am-employee-profile">
              <picture-upload
                  :edited-entity="this.employee"
                  :entity-name="'employee'"
                  v-on:pictureSelected="pictureSelected"
              >
              </picture-upload>
              <h2>{{ employee.firstName }} {{ employee.lastName }}</h2>
              <span
                  v-if="typeof employee.activity !== 'undefined'"
                  class="am-employee-status-label"
                  :class="employee.activity"
              >
                {{ getEmployeeActivityLabel(employee.activity) }}
              </span>
            </div>

            <!-- First Name -->
            <el-form-item :label="$root.labels.first_name + ':'" prop="firstName">
              <el-input v-model="employee.firstName" auto-complete="off" @input="clearValidation()"></el-input>
            </el-form-item>

            <!-- Last Name -->
            <el-form-item :label="$root.labels.last_name + ':'" prop="lastName">
              <el-input v-model="employee.lastName" auto-complete="off" @input="clearValidation()"></el-input>
            </el-form-item>

            <!-- Email -->
            <el-form-item :label="$root.labels.email + ':'" prop="email" :error="errors.email">
              <el-input
                  v-model="employee.email"
                  auto-complete="off"
                  :placeholder="$root.labels.email_placeholder"
                  @input="clearValidation()"
              >
              </el-input>
            </el-form-item>

            <!-- Location -->
            <el-form-item :label="$root.labels.location + ':'" prop="locationId" v-if="locations.length">
              <el-select
                  v-model="employee.locationId"
                  placeholder=""
                  @change="clearValidation()"
              >
                <el-option
                    v-for="location in filteredLocations"
                    :key="location.id"
                    :label="location.name"
                    :value="location.id"
                >
                </el-option>
              </el-select>
            </el-form-item>

            <!-- WP User -->
            <el-form-item label="placeholder" v-if="$root.settings.capabilities.canWriteOthers === true">
              <label slot="label">
                {{ $root.labels.wp_user }}:
                <el-tooltip placement="top">
                  <div slot="content" v-html="$root.labels.wp_user_employee_tooltip"></div>
                  <i class="el-icon-question am-tooltip-icon"></i>
                </el-tooltip>
              </label>
              <el-popover :disabled="!$root.isLite" ref="externalIdPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
              <el-select
                  v-model="employee.externalId"
                  ref="wpUser"
                  filterable
                  :placeholder="$root.labels.select_wp_user"
                  clearable
                  @change="clearValidation()"
                  :disabled="$root.isLite"
                  v-popover:externalIdPop
              >
                <div class="am-drop">
                  <div class="am-drop-create-item" @click="selectCreateNewWPUser">
                    {{ $root.labels.create_new }}
                  </div>
                  <el-option
                      :class="{'hidden' : item.value === 0}"
                      v-for="item in formOptions.wpUsers"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value">
                  </el-option>
                </div>
              </el-select>
            </el-form-item>

            <!-- Phone -->
            <el-form-item :label="$root.labels.phone + ':'">
              <phone-input
                  :savedPhone="employee.phone"
                  @phoneFormatted="phoneFormatted"
              >
              </phone-input>
            </el-form-item>

            <!-- Google Calendar -->
            <el-row
                :gutter="24"
                v-if="$root.settings.googleCalendar.clientID && $root.settings.googleCalendar.clientSecret && employee.id !== 0"
            >

              <!-- Google Calendar List -->
              <el-col :span="21" v-if="employee.googleCalendar && employee.googleCalendar.calendarList">
                <el-form-item label="placeholder">
                  <label slot="label">
                    {{ $root.labels.google_calendar }}:
                    <el-tooltip placement="top">
                      <div slot="content" v-html="$root.labels.google_calendar_tooltip"></div>
                      <i class="el-icon-question am-tooltip-icon"></i>
                    </el-tooltip>
                  </label>
                  <el-select
                      v-model="employee.googleCalendar.calendarId"
                      placeholder=""
                      :disabled="!employee.googleCalendar.token || googleLoading"
                      @change="clearValidation()"
                  >
                    <el-option
                        v-for="calendar in employee.googleCalendar.calendarList"
                        :key="calendar.id"
                        :label="calendar.summary"
                        :value="calendar.id"
                    >
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>

              <!-- Google Calendar Connect/Disconnect Buttons -->
              <el-col :span="3" v-if="employee.googleCalendar">

                <!-- Google Calendar Connect Button -->
                <el-tooltip
                    class="am-google-calendar-tooltip"
                    effect="dark"
                    :content="$root.labels.connect"
                    placement="top"
                    v-if="!employee.googleCalendar.token"
                >
                  <el-badge id="am-google-calendar-disconnected" is-dot>
                    <el-button
                        class="am-google-calendar-button am-button-icon"
                        type="primary"
                        @click="redirectToGoogleAuthPage()"
                        :loading="googleLoading"
                    >
                      <img
                          v-if="!googleLoading"
                          class="svg am-google-icon"
                          :src="$root.getUrl + 'public/img/google.svg'"
                      />
                    </el-button>
                  </el-badge>
                </el-tooltip>

                <!-- Google Calendar Disconnect Button -->
                <el-tooltip
                    class="am-google-calendar-tooltip"
                    effect="dark"
                    :content="$root.labels.disconnect"
                    placement="top"
                    v-else
                >
                  <el-badge id="am-google-calendar-connected" is-dot>
                    <el-button
                        class="am-google-calendar-button am-button-icon"
                        type="danger"
                        @click="disconnectFromGoogleAccount()"
                        :loading="googleLoading"
                    >
                      <img
                          v-if="!googleLoading"
                          class="svg am-google-icon"
                          :src="$root.getUrl + 'public/img/google.svg'"
                      />
                    </el-button>
                  </el-badge>
                </el-tooltip>

              </el-col>

            </el-row>

            <!-- Notes -->
            <div class="am-divider"></div>
            <el-form-item :label="$root.labels.note_internal + ':'">
              <el-input
                  type="textarea"
                  :autosize="{minRows: 4, maxRows: 6}"
                  placeholder=""
                  v-model="employee.note"
                  @input="clearValidation()"
              >
              </el-input>
            </el-form-item>

          </el-tab-pane>

          <!-- Assigned Services -->
          <el-tab-pane :label="$root.labels.assigned_services" name="services"
                       v-if="$root.settings.capabilities.canWriteOthers === true">
            <div v-if="$root.isLite" class="am-lite-container-disabled" v-popover:servicesPop>
              <el-popover :disabled="!$root.isLite" ref="servicesPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
            </div>
            <div class="am-dialog-table"
                 v-for="category in editCategorizedServiceList"
                 v-if="category.serviceList.length > 0"
                 :key="category.id"
            >

              <!-- Header -->
              <el-row :gutter="10" class="am-dialog-table-head">

                <!-- Check All -->
                <el-col :span="2">
                  <el-checkbox
                      v-model="category.state"
                      @change="handleCheckAllInCategory(category.id)">
                  </el-checkbox>
                </el-col>

                <!-- Category Name -->
                <el-col class="am-three-dots" :span="10"><span>{{ category.name }}</span></el-col>

                <!-- Price Label -->
                <el-col :span="6">{{ $root.labels.price }}</el-col>

                <!-- Capacity Label -->
                <el-col :span="6">{{ $root.labels.capacity }}</el-col>
              </el-row>

              <!-- Body -->
              <el-row :gutter="10" type="flex" align="middle" v-for="item in category.serviceList" :key="item.value">

                <!-- Checkbox -->
                <el-col :span="2">
                  <el-checkbox
                      v-model="item.state"
                      @change="handleCheckService(category, item)"
                  >
                  </el-checkbox>
                </el-col>

                <!-- Service Name -->
                <el-col :span="10"><span>{{ item.name }}</span></el-col>

                <!-- Service Price -->
                <el-col :span="6">
                  <money
                      v-model="item.price"
                      v-bind="moneyComponentData"
                      class="el-input__inner"
                      :disabled="!item.state"
                      @input="clearValidation()"
                  >
                  </money>
                </el-col>

                <!-- Min. Capacity -->
                <el-col :span="3">
                  <el-input-number
                      v-model="item.minCapacity"
                      :disabled="!item.state"
                      :value="item.minCapacity"
                      :min="1"
                      @input="checkCapacityLimits(item)"
                      :controls=false
                  >
                  </el-input-number>
                </el-col>

                <!-- Max. Capacity -->
                <el-col :span="3">
                  <el-input-number
                      v-model="item.maxCapacity"
                      :disabled="!item.state"
                      :value="item.maxCapacity"
                      :min="item.minCapacity"
                      @input="checkCapacityLimits(item)"
                      :controls=false
                  >
                  </el-input-number>
                </el-col>
              </el-row>
            </div>
          </el-tab-pane>

          <!-- Work Hours -->
          <el-tab-pane
              :label="$root.labels.work_hours" name="hours"
              v-if="$root.settings.capabilities.canWriteOthers === true || $root.settings.general.allowConfigureSchedule === true"
          >
            <BlockLite/>

            <working-hours :isDisabled="$root.isLite" :weekSchedule="editWeekDayList"></working-hours>
          </el-tab-pane>

          <!-- Days Off -->
          <el-tab-pane
              :label="$root.labels.days_off" name="off"
              v-if="$root.settings.capabilities.canWriteOthers === true"
          >
            <BlockLite/>

            <div class="am-days-off">
              <div class="am-employee-days-off">
                <days-off
                    @changeDaysOff="changeDaysOff"
                    @showCompanyDaysOffSettingsDialog="showCompanyDaysOffSettingsDialog"
                    :daysOff="employee.id !== 0 ? employee.dayOffList : []"
                    :listedDaysOff="companyDaysOff"
                >
                </days-off>
              </div>
            </div>
          </el-tab-pane>
        </el-tabs>

      </el-form>
    </div>

    <!-- Dialog Actions -->
    <dialog-actions
        v-if="employee && !dialogLoading"
        formName="employee"
        urlName="users/providers"
        :isNew="employee.id === 0"
        :entity="employee"
        :getParsedEntity="getParsedEntity"
        @errorCallback="errorCallback"
        @validationFailCallback="validationFailCallback"
        :isDisabledDuplicate="isDisabledDuplicate"

        :action="{
          haveAdd: true,
          haveEdit: true,
          haveStatus: $root.settings.capabilities.canWriteOthers === true,
          haveRemove: $root.settings.capabilities.canDelete === true,
          haveRemoveEffect: true,
          haveDuplicate: $root.settings.capabilities.canWriteOthers === true
        }"

        :message="{
          success: {
            save: $root.labels.employee_saved,
            remove: $root.labels.employee_deleted,
            show: $root.labels.employee_visible,
            hide: $root.labels.employee_hidden
          },
          confirm: {
            remove: $root.labels.confirm_delete_employee,
            show: $root.labels.confirm_show_employee,
            hide: $root.labels.confirm_hide_employee,
            duplicate: $root.labels.confirm_duplicate_employee
          }
        }"
    >
    </dialog-actions>

  </div>
</template>

<script>
  import DaysOff from '../parts/DaysOff.vue'
  import WorkingHours from '../parts/WorkingHours.vue'
  import DialogActions from '../parts/DialogActions.vue'
  import PhoneInput from '../../parts/PhoneInput.vue'
  import { Money } from 'v-money'
  import PictureUpload from '../parts/PictureUpload.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import employeeMixin from '../../../js/common/mixins/employeeMixin'

  export default {
    mixins: [imageMixin, dateMixin, durationMixin, notifyMixin, priceMixin, employeeMixin],

    props: {
      locations: null,
      employee: null,
      editCategorizedServiceList: null,
      editWeekDayList: null,
      companyDaysOff: null,
      futureAppointments: null,
      isDisabledDuplicate: null
    },

    data () {
      return {
        appointmentsServices: [],
        dialogLoading: true,
        employeeTabs: 'details',
        errors: {
          email: ''
        },
        executeUpdate: true,
        formOptions: {
          wpUsers: []
        },
        googleAuthURL: '',
        googleLoading: false,
        rules: {
          firstName: [
            {required: true, message: this.$root.labels.enter_first_name_warning, trigger: 'submit'}
          ],
          lastName: [
            {required: true, message: this.$root.labels.enter_last_name_warning, trigger: 'submit'}
          ],
          email: [
            {required: true, message: this.$root.labels.enter_email_warning, trigger: 'submit'},
            {type: 'email', message: this.$root.labels.enter_valid_email_warning, trigger: 'submit'}
          ],
          locationId: [
            {
              required: this.visibleLocations().length > 0,
              message: this.$root.labels.enter_location_warning,
              trigger: 'blur'
            }
          ]
        }
      }
    },

    created () {
      this.instantiateDialog()
    },

    updated () {
      this.instantiateDialog()
    },

    methods: {
      instantiateDialog () {
        if ((this.employee !== null || (this.employee !== null && this.employee.id === 0)) && this.executeUpdate === true) {
          if (this.$root.settings.capabilities.canWriteOthers) {
            if (this.employee.id !== 0) {
              this.getWPUsers(this.employee.externalId)
            } else {
              this.getWPUsers(0)
            }
          }

          // Send request for Google Authorization URL if token is not already set
          if (this.employee.googleCalendar && !this.employee.googleCalendar.token) {
            this.getGoogleAuthURL(false)
          }

          let locations = this.visibleLocations()

          if (locations.length === 1) {
            this.employee.locationId = locations[0].id
          }

          let $this = this

          this.editCategorizedServiceList.forEach(function (catItem) {
            $this.handleCheckSingleInCategory(catItem)
          })

          if (this.employee.id in this.futureAppointments) {
            this.appointmentsServices = this.futureAppointments[this.employee.id]
          }

          // Remove loading when employee is connected to google calendar
          if (!this.$root.settings.capabilities.canWriteOthers) {
            this.dialogLoading = false
            this.inlineDialogEmployeeSVG()
          }

          this.executeUpdate = false
        }
      },

      checkCapacityLimits (item) {
        this.clearValidation()
        if (item.minCapacity > item.maxCapacity) {
          item.maxCapacity = item.minCapacity
        }
      },

      validationFailCallback () {
        this.employeeTabs = 'details'
      },

      errorCallback (responseData) {
        let $this = this

        $this.errors.email = ''

        setTimeout(function () {
          $this.errors.email = responseData.message
          $this.employeeTabs = 'details'
        }, 200)
      },

      closeDialog () {
        this.$emit('closeDialog')
      },

      getParsedEntity () {
        this.employee.serviceList = this.getParsedServiceList(this.editCategorizedServiceList)
        this.employee.weekDayList = this.getParsedWeekDayList(this.editWeekDayList)

        return this.employee
      },

      getParsedServiceList (list) {
        let serviceList = []

        list.forEach(function (catItem) {
          catItem.serviceList.forEach(function (catSerItem) {
            if (catSerItem.state) {
              serviceList.push(Object.assign({}, catSerItem))
            }
          })
        })

        return serviceList
      },

      getParsedWeekDayList (list) {
        let weekDayList = []

        list.forEach(function (weekDayItem, weekDayItemIndex) {
          if (weekDayItem.time && weekDayItem.time.length) {
            let timeOutList = []

            weekDayItem.breaks.forEach(function (timeOutItem) {
              if (timeOutItem.time && timeOutItem.time.length && timeOutItem.time[0] && timeOutItem.time[1]) {
                timeOutList.push(
                  {
                    startTime: timeOutItem.time[0] + ':00',
                    endTime: timeOutItem.time[1] + ':00'
                  }
                )
              }
            })

            if (weekDayItem.time[0] && weekDayItem.time[1]) {
              weekDayList.push(
                {
                  dayIndex: weekDayItemIndex + 1,
                  startTime: weekDayItem.time[0] + ':00',
                  endTime: weekDayItem.time[1] + ':00',
                  timeOutList: timeOutList
                }
              )
            }
          }
        })

        return weekDayList
      },

      changeDaysOff: !AMELIA_LITE_VERSION ? function (data) {
        this.clearValidation()
        this.employee.dayOffList = data
      } : function () {},

      handleCheckAllInCategory: !AMELIA_LITE_VERSION ? function (catId) {
        let $this = this
        this.clearValidation()

        let category = this.editCategorizedServiceList.find(category => category.id === catId)

        category.serviceList.forEach(function (service) {
          if ($this.appointmentsServices.indexOf(service.id) !== -1 && category.state === false) {
            $this.notify(
              $this.$root.labels.error,
              $this.$root.labels.service_provider_remove_fail_all + ' ' + service.name + ' ' + $this.$root.labels.service,
              'error'
            )
          } else {
            service.state = category.state
          }
        })
      } : function () {},

      handleCheckService: !AMELIA_LITE_VERSION ? function (category, service) {
        if (this.appointmentsServices.indexOf(service.id) !== -1) {
          service.state = true
          this.notify(this.$root.labels.error, this.$root.labels.service_provider_remove_fail, 'error')
        }

        this.handleCheckSingleInCategory(category)
      } : function () {},

      handleCheckSingleInCategory: !AMELIA_LITE_VERSION ? function (category) {
        this.clearValidation()
        let checkedItemsLength = 0

        category.serviceList.forEach(function (serItem) {
          if (serItem.state) {
            checkedItemsLength++
          }
        })

        category.state = (checkedItemsLength === category.serviceList.length)
      } : function (category) {
        category.state = AMELIA_LITE_VERSION
      },

      phoneFormatted (phone) {
        this.clearValidation()
        this.employee.phone = phone
      },

      pictureSelected (pictureFullPath, pictureThumbPath) {
        this.employee.pictureFullPath = pictureFullPath
        this.employee.pictureThumbPath = pictureThumbPath
      },

      getWPUsers (currentId) {
        this.$http.get(`${this.$root.getAjaxUrl}/users/wp-users`, {
          params: {
            id: currentId,
            role: 'provider'
          }
        }).then(response => {
          this.formOptions.wpUsers = response.data.data.users
          this.formOptions.wpUsers.unshift({'value': 0, 'label': this.$root.labels.create_new})

          if (this.formOptions.wpUsers.map(user => user.value).indexOf(this.employee.externalId) === -1) {
            this.employee.externalId = ''
          }

          this.dialogLoading = false
          this.inlineDialogEmployeeSVG()
        })
      },

      visibleLocations () {
        return this.locations.filter(location => location.status === 'visible' || (this.employee && location.status === 'hidden' && location.id === this.employee.locationId))
      },

      showCompanyDaysOffSettingsDialog: !AMELIA_LITE_VERSION ? function () {
        this.$emit('showCompanyDaysOffSettingsDialog')
      } : function () {},

      clearValidation () {
        if (typeof this.$refs.employee !== 'undefined') {
          this.$refs.employee.clearValidate()
        }
      },

      selectCreateNewWPUser () {
        this.employee.externalId = 0
        this.$refs.wpUser.blur()
      },

      getGoogleAuthURL: !AMELIA_LITE_VERSION ? function (inlineSVG) {
        if (this.employee.id && this.$root.settings.googleCalendar.clientID && this.$root.settings.googleCalendar.clientSecret) {
          this.$http.get(`${this.$root.getAjaxUrl}/google/authorization/url/` + this.employee.id)
            .then(response => {
              this.googleAuthURL = response.data.data.authUrl
              this.googleLoading = false
              this.dialogLoading = false

              if (inlineSVG) {
                this.inlineDialogEmployeeSVG()
              }
            })
            .catch(e => {
              this.notify(this.$root.labels.error, e.message, 'error')
            })
        }
      } : function () {},

      redirectToGoogleAuthPage: !AMELIA_LITE_VERSION ? function () {
        this.googleLoading = true
        window.location.href = this.googleAuthURL
      } : function () {},

      disconnectFromGoogleAccount: !AMELIA_LITE_VERSION ? function () {
        this.googleLoading = true

        this.$http.delete(
          `${this.$root.getAjaxUrl}/google/disconnect/` + this.employee.id
        ).then(() => {
          this.employee.googleCalendar = {
            calendarId: null,
            calendarList: []
          }

          this.getGoogleAuthURL(true)
        }).catch(e => {
          this.notify(this.$root.labels.error, e.message, 'error')
        })
      } : function () {},

      inlineDialogEmployeeSVG () {
        let $this = this
        setTimeout(function () {
          $this.inlineSVG()
        }, 10)
      }
    },

    computed: {
      filteredLocations () {
        return this.visibleLocations()
      }
    },

    components: {
      PhoneInput,
      PictureUpload,
      DaysOff,
      WorkingHours,
      DialogActions,
      Money
    }
  }
</script>