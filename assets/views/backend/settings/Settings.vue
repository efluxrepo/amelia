<template>
  <div class="am-wrap">
    <div id="am-settings" class="am-body">

      <!-- Page Header -->
      <page-header></page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched">
        <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
      </div>

      <!-- Settings -->
      <div v-show="fetched" class="am-section am-settings-cards">
        <el-row :gutter="48">

          <!-- General -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/setting.svg'" class="svg"/> {{ $root.labels.general }}</h3>
              <p>{{ $root.labels.general_settings_description }}</p>
              <p class="link" @click="showDialogSettingsGeneral">
                {{ $root.labels.view_general_settings }}
              </p>
            </div>
          </el-col>

          <!-- Company -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/company.svg'" class="svg"/> {{ $root.labels.company }}</h3>
              <p>{{ $root.labels.company_settings_description }}</p>
              <p class="link" @click="showDialogSettingsCompany">
                {{ $root.labels.view_company_settings }}
              </p>
            </div>
          </el-col>

          <!-- Notification -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/email-settings.svg'" class="svg"/> {{ $root.labels.notifications
                }}</h3>
              <p>{{ $root.labels.notifications_settings_description }}</p>
              <p class="link" @click="showDialogSettingsNotifications">
                {{ $root.labels.view_notifications_settings }}
              </p>
            </div>
          </el-col>

        </el-row>

        <el-row :gutter="48">

          <!-- Working Hours & Days Off -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl+'public/img/calendar.svg'" class="svg"/> {{ $root.labels.work_hours_days_off
                }}</h3>
              <p>{{ $root.labels.days_off_settings_description }}</p>
              <p class="link" @click="showDialogSettingsWorkHoursDaysOff">
                {{ $root.labels.view_days_off_settings }}
              </p>
            </div>
          </el-col>

          <!-- Payments -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl+'public/img/credit-card.svg'" class="svg"/> {{ $root.labels.payments }}</h3>
              <p>{{ $root.labels.payments_settings_description }}</p>
              <p class="link" @click="showDialogSettingsPayments">
                {{ $root.labels.view_payments_settings }}
              </p>
            </div>
          </el-col>

          <!-- Google Calendar -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl+'public/img/google.svg'" class="svg"/> {{ $root.labels.google_calendar }}</h3>
              <p>{{ $root.labels.google_calendar_settings_description }}</p>
              <p class="link" @click="showDialogSettingsGoogleCalendar">
                {{ $root.labels.view_google_calendar_settings }}
              </p>
            </div>
          </el-col>

        </el-row>

        <el-row :gutter="48">

          <!-- Labels -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/labels.svg'" class="svg"/>
                {{ $root.labels.labels }}
              </h3>
              <p>{{ $root.labels.labels_settings_description }}</p>
              <p class="link" @click="showDialogSettingsLabels">
                {{ $root.labels.view_labels_settings }}
              </p>
            </div>
          </el-col>

          <!-- Purchase Code -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/purchase-code.svg'" class="svg"/>
                {{ $root.labels.purchase_code }}
              </h3>
              <p>{{ $root.labels.purchase_code_settings_description }}</p>
              <p class="link" @click="showDialogSettingsPurchaseCode">
                {{ $root.labels.view_purchase_code_settings }}
              </p>
            </div>
          </el-col>

          <!-- Roles -->
          <el-col :md="8">
            <div class="am-settings-card">
              <h3><img :src="$root.getUrl + 'public/img/roles.svg'" class="svg"/>
                {{ $root.labels.roles_settings }}
              </h3>
              <p>{{ $root.labels.roles_settings_description }}</p>
              <p class="link" @click="showDialogSettingsRoles">
                {{ $root.labels.view_roles_settings_description }}
              </p>
            </div>
          </el-col>

        </el-row>

      </div>

      <!-- Dialog General -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsGeneral"
            :show-close="false"
            v-if="dialogSettingsGeneral"
        >
          <dialog-settings-general
              @closeDialogSettingsGeneral="dialogSettingsGeneral = false"
              @updateSettings="updateSettings"
              :general="settings.general"
          >
          </dialog-settings-general>
        </el-dialog>
      </transition>

      <!-- Dialog Company -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsCompany"
            :show-close="false"
            v-if="dialogSettingsCompany"
        >
          <dialog-settings-company
              @closeDialogSettingsCompany="dialogSettingsCompany = false"
              @updateSettings="updateSettings"
              :company="settings.company"
          >
          </dialog-settings-company>
        </el-dialog>
      </transition>

      <!-- Dialog Notification -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsNotifications"
            :show-close="false"
            v-if="dialogSettingsNotifications"
        >
          <dialog-settings-notifications
              @closeDialogSettingsNotifications="dialogSettingsNotifications = false"
              @updateSettings="updateSettings"
              :notifications="settings.notifications"
          >
          </dialog-settings-notifications>
        </el-dialog>
      </transition>

      <!-- Dialog Work Hours & Days Off -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsWorkHoursDaysOff"
            :show-close="false"
            v-if="dialogSettingsWorkHoursDaysOff"
        >
          <dialog-settings-work-hours-days-off
              @closeDialogSettingsWorkHoursDaysOff="dialogSettingsWorkHoursDaysOff = false"
              @updateSettings="updateSettings"
              :daysOff="settings.daysOff"
              :weekSchedule="settings.weekSchedule"
          >
          </dialog-settings-work-hours-days-off>
        </el-dialog>
      </transition>

      <!-- Dialog Payment -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsPayments"
            :show-close="false"
            v-if="dialogSettingsPayments"
        >
          <dialog-settings-payments
              @closeDialogSettingsPayments="dialogSettingsPayments = false"
              @updateSettings="updateSettings"
              :payments="settings.payments"
          >
          </dialog-settings-payments>
        </el-dialog>
      </transition>

      <!-- Dialog Google Calendar -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsGoogleCalendar"
            :show-close="false"
            v-if="dialogSettingsGoogleCalendar"
        >
          <dialog-settings-google-calendar
              @closeDialogSettingsGoogleCalendar="dialogSettingsGoogleCalendar = false"
              @updateSettings="updateSettings"
              :googleCalendar="settings.googleCalendar"
          >
          </dialog-settings-google-calendar>
        </el-dialog>
      </transition>

      <!-- Dialog Labels -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsLabels"
            :show-close="false"
            v-if="dialogSettingsLabels"
        >
          <dialog-settings-labels
              @closeDialogSettingsLabels="dialogSettingsLabels = false"
              @updateSettings="updateSettings"
              :labels="settings.labels"
          >
          </dialog-settings-labels>
        </el-dialog>
      </transition>

      <!-- Dialog Purchase Code -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-settings"
            :visible.sync="dialogSettingsPurchaseCode"
            :show-close="false"
            v-if="dialogSettingsPurchaseCode"
        >
          <dialog-settings-purchase-code
              @closeDialogSettingsPurchaseCode="dialogSettingsPurchaseCode = false"
              @updateSettings="updateSettings"
              :activation="settings.activation"
          >
          </dialog-settings-purchase-code>
        </el-dialog>
      </transition>

      <!-- Dialog Purchase Code -->
      <transition name="slide">
        <el-dialog
                class="am-side-dialog am-dialog-settings"
                :visible.sync="dialogSettingsRoles"
                :show-close="false"
                v-if="dialogSettingsRoles"
        >
          <dialog-settings-roles
                  @closeDialogSettingsRoles="dialogSettingsRoles = false"
                  @updateSettings="updateSettings"
                  :general="settings.general"
          >
          </dialog-settings-roles>
        </el-dialog>
      </transition>

      <DialogLite/>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/general-settings/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import DialogSettingsGeneral from './DialogSettingsGeneral.vue'
  import DialogSettingsCompany from './DialogSettingsCompany.vue'
  import DialogSettingsNotifications from './DialogSettingsNotifications.vue'
  import DialogSettingsWorkHoursDaysOff from './DialogSettingsWorkHoursDaysOff.vue'
  import DialogSettingsPayments from './DialogSettingsPayments.vue'
  import DialogSettingsGoogleCalendar from './DialogSettingsGoogleCalendar.vue'
  import DialogSettingsLabels from './DialogSettingsLabels.vue'
  import DialogSettingsPurchaseCode from './DialogSettingsPurchaseCode.vue'
  import DialogSettingsRoles from './DialogSettingsRoles.vue'
  import PageHeader from '../parts/PageHeader.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'

  export default {

    mixins: [imageMixin, notifyMixin],

    data () {
      return {
        dialogSettingsGeneral: false,
        dialogSettingsCompany: false,
        dialogSettingsNotifications: false,
        dialogSettingsWorkHoursDaysOff: false,
        dialogSettingsPayments: false,
        dialogSettingsGoogleCalendar: false,
        dialogSettingsLabels: false,
        dialogSettingsPurchaseCode: false,
        dialogSettingsRoles: false,
        fetched: false,
        settings: {}
      }
    },

    created () {
      this.fetchData()
    },

    updated () {
      this.inlineSVG()
    },

    mounted () {
      this.inlineSVG()
    },

    methods: {

      fetchData () {
        this.$http.get(`${this.$root.getAjaxUrl}/settings`)
          .then(response => {
            this.fetched = true
            this.settings = response.data.data.settings
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
          })
      },

      updateSettings (settings, additionalParams) {
        for (let category in settings) {
          if (settings.hasOwnProperty(category)) {
            this.settings[category] = settings[category]
          }
        }

        if (typeof additionalParams !== 'undefined') {
          this.settings['additionalParams'] = additionalParams
        } else {
          delete this.settings['additionalParams']
        }

        this.settings['customization'] = null

        this.$http.post(`${this.$root.getAjaxUrl}/settings`, this.settings)
          .then(response => {
            this.$root.settings = response.data.data.settings
            this.notify(this.$root.labels.success, this.$root.labels.settings_saved, 'success')
          })
          .catch(e => {
            this.notify(this.$root.labels.error, e.message, 'error')
          })
      },

      showDialogSettingsGeneral () {
        this.dialogSettingsGeneral = true
      },

      showDialogSettingsCompany () {
        this.dialogSettingsCompany = true
      },

      showDialogSettingsWorkHoursDaysOff () {
        this.dialogSettingsWorkHoursDaysOff = true
      },

      showDialogSettingsNotifications () {
        this.dialogSettingsNotifications = true
      },

      showDialogSettingsPayments () {
        this.dialogSettingsPayments = true
      },

      showDialogSettingsGoogleCalendar () {
        this.dialogSettingsGoogleCalendar = true
      },

      showDialogSettingsLabels () {
        this.dialogSettingsLabels = true
      },

      showDialogSettingsPurchaseCode () {
        this.dialogSettingsPurchaseCode = true
      },

      showDialogSettingsRoles () {
        this.dialogSettingsRoles = true
      }
    },

    components: {
      PageHeader,
      DialogSettingsGeneral,
      DialogSettingsCompany,
      DialogSettingsNotifications,
      DialogSettingsWorkHoursDaysOff,
      DialogSettingsPayments,
      DialogSettingsGoogleCalendar,
      DialogSettingsLabels,
      DialogSettingsPurchaseCode,
      DialogSettingsRoles
    }

  }
</script>
