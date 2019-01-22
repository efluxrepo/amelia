<template>
  <div>

    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2 v-if="showWorkingHoursOnly">
              {{ $root.labels.company_work_hours_settings }}
            </h2>
            <h2 v-else-if="showDaysOffOnly">
              {{ $root.labels.company_days_off_settings }}
            </h2>
            <h2 v-else="showWorkingHoursOnly">
              {{ $root.labels.work_hours_days_off_settings }}
            </h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form label-position="top" @submit.prevent="onSubmit">

        <!-- Working Hours & Days Off In Tabs -->
        <el-tabs v-if="showTabs" v-model="activeTab">
          <el-tab-pane :label="$root.labels.work_hours" name="hours">
            <!-- Working Hours -->
            <working-hours :weekSchedule="weekScheduleSettings"></working-hours>
          </el-tab-pane>
          <el-tab-pane :label="$root.labels.days_off" name="off">
            <BlockLite/>
            <!-- Days Off -->
            <days-off @changeDaysOff="changeDaysOff" :daysOff="dayOffSettings"></days-off>
          </el-tab-pane>
        </el-tabs>

        <!-- Working Hours -->
        <working-hours v-if="showWorkingHoursOnly" :weekSchedule="weekScheduleSettings"></working-hours>

        <!-- Days Off -->
        <days-off v-if="showDaysOffOnly" @changeDaysOff="changeDaysOff" :daysOff="dayOffSettings"></days-off>

      </el-form>
    </div>

    <!-- Dialog Footer -->
    <div class="am-dialog-footer">
      <div class="am-dialog-footer-actions">
        <el-row>
          <el-col :sm="24" class="align-right">
            <el-button type="" @click="closeDialog" class="">
              {{ $root.labels.cancel }}
            </el-button>
            <el-button type="primary" @click="checkForConfirmation()" class="am-dialog-create">
              {{ $root.labels.save }}
            </el-button>
          </el-col>
        </el-row>
      </div>
    </div>

    <!-- Dialog Save Confirm -->
    <transition name="slide-vertical">
      <div class="am-dialog-confirmation" v-show="showSaveConfirmation">
        <h3>{{ $root.labels.confirm_global_change_working_hours }}</h3>
        <div class="align-left">
          <el-button size="small" type="primary" @click="onSubmit(false)">
            {{ $root.labels.no }}
          </el-button>
          <el-button size="small" type="primary" @click="onSubmit(true)">
            {{ $root.labels.update_for_all }}
          </el-button>
        </div>
      </div>
    </transition>

  </div>
</template>

<script>
  import DaysOff from '../parts/DaysOff.vue'
  import WorkingHours from '../parts/WorkingHours.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'

  export default {

    mixins: [imageMixin, dateMixin],

    props: {
      weekSchedule: {
        type: Array
      },
      daysOff: {
        type: Array
      },
      showWorkingHours: {
        default: true,
        type: Boolean
      },
      showDaysOff: {
        default: true,
        type: Boolean
      }
    },

    data () {
      return {
        dayOffSettings: this.daysOff.slice(0),
        weekScheduleSettings: this.weekSchedule ? this.weekSchedule.slice(0) : [],
        activeTab: 'hours',
        showSaveConfirmation: false
      }
    },

    mounted () {
      if (this.showWorkingHours) {
        this.translateDayStrings()
      }

      this.inlineSVG()
    },

    methods: {
      closeDialog () {
        this.$emit('closeDialogSettingsWorkHoursDaysOff')
      },

      onSubmit (applyGlobally) {
        this.$emit('closeDialogSettingsWorkHoursDaysOff')
        this.$emit('updateSettings', {
          daysOff: this.dayOffSettings,
          weekSchedule: this.weekScheduleSettings
        }, {weekScheduleSettingsApplyGlobally: applyGlobally})
      },

      changeDaysOff (data) {
        data.forEach(function (v) { delete v.id })

        this.dayOffSettings = data
      },

      checkForConfirmation () {
        let showConfirmation = false

        // Go through all working days
        for (let i = 0; i < this.weekScheduleSettings.length; i++) {
          // Check if start or end time of working day is changed
          for (let j = 0; j < this.weekScheduleSettings[i].time.length; j++) {
            if (this.weekScheduleSettings[i].time[j] !== this.$root.settings.weekSchedule[i].time[j]) {
              showConfirmation = true
              break
            }
          }

          // If break is added or removed show confirmation
          if (this.weekScheduleSettings[i].breaks.length !== this.$root.settings.weekSchedule[i].breaks.length) {
            showConfirmation = true
            break
          }

          // Go through all breaks and check if start or end time for one of them has been changed
          for (let k = 0; k < this.weekScheduleSettings[i].breaks.length; k++) {
            for (let m = 0; m < this.weekScheduleSettings[i].breaks[k].time.length; m++) {
              if (this.weekScheduleSettings[i].breaks[k].time[m] !== this.$root.settings.weekSchedule[i].breaks[k].time[m]) {
                showConfirmation = true
                break
              }
            }
          }

          if (showConfirmation === true) {
            break
          }
        }

        if (showConfirmation === true) {
          this.showSaveConfirmation = true
        } else {
          this.onSubmit(false)
        }
      },

      translateDayStrings () {
        let translations = [
          'weekday_monday',
          'weekday_tuesday',
          'weekday_wednesday',
          'weekday_thursday',
          'weekday_friday',
          'weekday_saturday',
          'weekday_sunday'
        ]

        for (let i = 0; i < this.weekSchedule.length; i++) {
          let day = translations[i]
          this.weekSchedule[i].day = this.$root.labels[day]
        }
      }
    },

    computed: {
      showTabs () {
        return this.showWorkingHours && this.showDaysOff
      },

      showWorkingHoursOnly () {
        return this.showWorkingHours && !this.showDaysOff
      },

      showDaysOffOnly () {
        return !this.showWorkingHours && this.showDaysOff
      }
    },

    components: {
      DaysOff,
      WorkingHours
    }

  }
</script>