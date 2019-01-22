<template>
  <div class="am-working-hours" :class="{'am-lite-container-disabled': isDisabled}">

    <div class="am-dialog-table" v-for="(workDay, index) in weekSchedule" :key="workDay.id">

      <el-row :gutter="20" class="am-dialog-table-head hours">
        <el-col :span="12"><span>{{ workDay.day }}</span></el-col>
        <el-col :span="12"><span>{{ $root.labels.breaks }}</span></el-col>
      </el-row>

      <el-row :gutter="10" type="flex">

        <!-- Work Hours Start -->
        <el-col :span="12">
          <el-row :gutter="10">
            <el-col :span="12">
              <el-time-select
                  v-model="workDay.time[0]"
                  :picker-options="getTimeSelectOptionsWithLimits('', workDay.time[1])"
                  size="mini"
              >
              </el-time-select>
            </el-col>

            <!-- Work Hours End -->
            <el-col :span="12">
              <el-time-select
                  v-model="workDay.time[1]"
                  :picker-options="getTimeSelectOptionsWithLimits(workDay.time[0], '')"
                  size="mini"
              >
              </el-time-select>
            </el-col>
          </el-row>

          <!-- Apply To All Days -->
          <el-row>
            <el-col :span="24" v-if="index === 0">
              <div class="am-add-element" @click="applyToAllDays">{{ $root.labels.apply_to_all_days }}</div>
            </el-col>
          </el-row>
        </el-col>

        <!-- Breaks -->
        <el-col :span="12">
          <transition-group name="fade">
            <div class="am-break" v-if="workDay.breaks.length" v-for="(breakHours, index) in workDay.breaks"
                 :key="index"
            >

              <!-- Breaks Start -->
              <el-col :span="12">
                <el-time-select
                    v-model="breakHours.time[0]"
                    :picker-options="getTimeSelectOptionsForBreaks(workDay.time[0], workDay.time[1], '', breakHours.time[1])"
                    size="mini"
                >
                </el-time-select>
              </el-col>

              <!-- Breaks End -->
              <el-col :span="12">
                <el-time-select
                    v-model="breakHours.time[1]"
                    :picker-options="getTimeSelectOptionsForBreaks(workDay.time[0], workDay.time[1], breakHours.time[0], '')"
                    size="mini"
                >
                </el-time-select>
              </el-col>

              <div class="am-delete-element" @click="deleteBreak(index, workDay.day)">
                <i class="el-icon-minus"></i>
              </div>

            </div>
          </transition-group>

          <div class="am-add-element" @click="addBreak(workDay.day)">
            <i class="el-icon-plus"></i> {{ $root.labels.add_break }}
          </div>
        </el-col>

      </el-row>
    </div>

  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'

  export default {

    mixins: [imageMixin, dateMixin, durationMixin],

    props: {
      weekSchedule: null,
      isDisabled: false
    },

    data () {
      return {}
    },

    methods: {
      applyToAllDays () {
        let $this = this

        if ($this.weekSchedule[0].time.length) {
          $this.weekSchedule.forEach(function (day, index) {
            if (index !== 0) {
              day.time[0] = $this.weekSchedule[0].time[0]
              day.time[1] = $this.weekSchedule[0].time[1]
              day.breaks = []

              $this.weekSchedule[0].breaks.forEach(function (dayBreak) {
                day.breaks.push({time: [dayBreak.time[0], dayBreak.time[1]]})
              })
            }
          })
        }
      },

      getTimeSelectOptionsForBreaks: function (minTimeWorkingHour, maxTimeWorkingHour, minTimeBreak, maxTimeBreak) {
        return {
          start: '00:00',
          end: '23:59',
          step: this.secondsToTimeSelectStep(this.getTimeSlotLength()),
          minTime: minTimeBreak || minTimeWorkingHour,
          maxTime: maxTimeBreak || maxTimeWorkingHour
        }
      },

      addBreak (d) {
        this.weekSchedule.forEach(function (day) {
          if (day.day === d) {
            day.breaks.push({
              time: ['', '']
            })
          }
        })
      },

      deleteBreak (index, d) {
        this.weekSchedule.forEach(function (day) {
          if (day.day === d) {
            day.breaks.splice(index, 1)
          }
        })
      }
    },

    components: {}

  }
</script>