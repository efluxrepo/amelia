<template>
  <div>
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2>{{ $root.labels.general_settings }}</h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form :model="settings" ref="settings" label-position="top" @submit.prevent="onSubmit">

        <el-row :gutter="24">

          <!-- Time Slot -->
          <el-popover :disabled="!$root.isLite" ref="timeSlotStepPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
          <el-col :span="12" v-popover:timeSlotStepPop>
            <el-form-item label="placeholder">
              <label slot="label">
                {{ $root.labels.default_time_slot_step }}:
                <el-tooltip placement="top">
                  <div slot="content" v-html="$root.labels.default_time_slot_step_tooltip"></div>
                  <i class="el-icon-question am-tooltip-icon"></i>
                </el-tooltip>
              </label>
              <el-select v-model="settings.timeSlotLength" :disabled="$root.isLite">
                <el-option
                    v-for="item in options.timeSlotLength"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>

          <!-- Appointment Status -->
          <el-col :span="12">
            <el-form-item label="placeholder">
              <label slot="label">
                {{ $root.labels.default_appointment_status }}:
                <el-tooltip placement="top">
                  <div slot="content" v-html="$root.labels.default_appointment_status_tooltip"></div>
                  <i class="el-icon-question am-tooltip-icon"></i>
                </el-tooltip>
              </label>
              <el-select v-model="settings.defaultAppointmentStatus">
                <el-option
                    v-for="item in options.defaultAppointmentStatus"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>

        </el-row>

        <!-- Use Service Duration As Booking Time Slot -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="16">
              {{ $root.labels.service_duration_as_slot }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.service_duration_as_slot_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.serviceDurationAsSlot"
                  active-text=""
                  inactive-text=""
              ></el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Minimum Time Prior to Booking -->
        <el-popover :disabled="!$root.isLite" ref="minimumTimeBeforeBookingPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-form-item label="placeholder" v-popover:minimumTimeBeforeBookingPop>
          <label slot="label">
            {{ $root.labels.minimum_time_before_booking }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.minimum_time_before_booking_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-select v-model="settings.minimumTimeRequirementPriorToBooking" :disabled="$root.isLite">
            <el-option
                v-for="item in options.minimumTime"
                :key="item.value"
                :label="item.label"
                :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Minimum Time Prior to Canceling -->
        <el-popover :disabled="!$root.isLite" ref="minimumTimeBeforeCancelingPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-form-item label="placeholder" v-popover:minimumTimeBeforeCancelingPop>
          <label slot="label">
            {{ $root.labels.minimum_time_before_canceling }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.minimum_time_before_canceling_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-select v-model="settings.minimumTimeRequirementPriorToCanceling" :disabled="$root.isLite">
            <el-option
                v-for="item in options.minimumTime"
                :key="item.value"
                :label="item.label"
                :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Number of days available for booking -->
        <el-form-item label="placeholder">
          <label slot="label">
            {{ $root.labels.period_available_for_booking }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.period_available_for_booking_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input-number
              v-model="settings.numberOfDaysAvailableForBooking"
              :min="1"
          >
          </el-input-number>
        </el-form-item>

        <!-- Phone default country code -->
        <el-form-item :label="$root.labels.default_phone_country_code+':'">
          <el-select v-model="settings.phoneDefaultCountryCode" placeholder=""
                     :class="'am-selected-flag am-selected-flag-'+settings.phoneDefaultCountryCode">
            <el-option
                v-for="country in options.countries"
                :key="country.id"
                :value="country.iso"
                :label="country.nicename"
            >
              <span :class="'am-flag am-flag-'+country.iso"></span>
              <span style="float: left">{{ country.nicename }}</span>
              <span style="float: right; color: #7F8BA4; font-size: 13px">{{ country.phonecode ? '+' : ''}}{{ country.phonecode }}</span>
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Default required phone number input -->
        <div class="am-setting-box am-switch-box">
          <el-row type="flex" align="middle" :gutter="24">
            <el-col :span="16">
              {{ $root.labels.required_phone_number_field }}
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.requiredPhoneNumberField"
                  active-text=""
                  inactive-text=""
              ></el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Default add to calendar -->
        <el-popover :disabled="!$root.isLite" ref="addToCalendarPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <div class="am-setting-box am-switch-box" :class="{'am-lite-disabled': ($root.isLite)}" v-popover:addToCalendarPop>
          <el-row type="flex" align="middle" :gutter="24" >
            <el-col :span="16">
              {{ $root.labels.add_to_calendar }}
              <el-tooltip placement="top">
                <div slot="content" v-html="$root.labels.add_to_calendar_tooltip"></div>
                <i class="el-icon-question am-tooltip-icon"></i>
              </el-tooltip>
            </el-col>
            <el-col :span="8" class="align-right">
              <el-switch
                  v-model="settings.addToCalendar"
                  active-text=""
                  inactive-text=""
                  :disabled="$root.isLite"
              ></el-switch>
            </el-col>
          </el-row>
        </div>

        <!-- Default items per page -->
        <el-form-item :label="$root.labels.default_items_per_page+':'">
          <el-select v-model="settings.itemsPerPage">
            <el-option
                v-for="item in options.itemsPerPage"
                :key="item"
                :label="item"
                :value="item">
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Default page on the backend -->
        <el-form-item :label="$root.labels.default_page_on_backend+':'">
          <el-select v-model="settings.defaultPageOnBackend">
            <el-option
                v-for="item in options.defaultPageOnBackend"
                :key="item.value"
                :label="item.label"
                :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>


        <!-- Google Map Api Key -->
        <el-popover :disabled="!$root.isLite" ref="gMapApiKeyPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-form-item label="placeholder" v-popover:gMapApiKeyPop>
          <label slot="label">
            {{ $root.labels.gMap_api_key }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.gMap_api_key_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input v-model="settings.gMapApiKey" auto-complete="off" :disabled="$root.isLite"></el-input>
        </el-form-item>

      </el-form>

    </div>

    <!-- Dialog Footer -->
    <div class="am-dialog-footer">
      <div class="am-dialog-footer-actions">
        <el-row>
          <el-col :sm="24" class="align-right">
            <el-button type="" @click="closeDialog" class="">{{ $root.labels.cancel }}</el-button>
            <el-button type="primary" @click="onSubmit" class="am-dialog-create">{{ $root.labels.save }}</el-button>
          </el-col>
        </el-row>
      </div>
    </div>

  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'

  export default {

    mixins: [imageMixin],

    props: {
      general: {
        type: Object
      }
    },

    data () {
      return {
        settings: Object.assign({}, this.general),
        options: {
          timeSlotLength: !AMELIA_LITE_VERSION ? [
            {
              label: this.$root.labels.min5,
              value: 300
            },
            {
              label: this.$root.labels.min10,
              value: 600
            },
            {
              label: this.$root.labels.min12,
              value: 720
            },
            {
              label: this.$root.labels.min15,
              value: 900
            },
            {
              label: this.$root.labels.min20,
              value: 1200
            },
            {
              label: this.$root.labels.min30,
              value: 1800
            },
            {
              label: this.$root.labels.min45,
              value: 2700
            },
            {
              label: this.$root.labels.h1,
              value: 3600
            },
            {
              label: this.$root.labels.h1min30,
              value: 5400
            },
            {
              label: this.$root.labels.h2,
              value: 7200
            },
            {
              label: this.$root.labels.h3,
              value: 10800
            },
            {
              label: this.$root.labels.h4,
              value: 14400
            },
            {
              label: this.$root.labels.h6,
              value: 21600
            },
            {
              label: this.$root.labels.h8,
              value: 28800
            }
          ] : [
            {
              label: this.$root.labels.min30,
              value: 1800
            }
          ],
          defaultAppointmentStatus: [
            {
              label: this.$root.labels.pending,
              value: 'pending'
            },
            {
              label: this.$root.labels.approved,
              value: 'approved'
            }
          ],
          minimumTime: [
            {
              label: this.$root.labels.disabled,
              value: 0
            },
            {
              label: this.$root.labels.min30,
              value: 1800
            },
            {
              label: this.$root.labels.min45,
              value: 2700
            },
            {
              label: this.$root.labels.h1,
              value: 3600
            },
            {
              label: this.$root.labels.h1min30,
              value: 5400
            },
            {
              label: this.$root.labels.h2,
              value: 7200
            },
            {
              label: this.$root.labels.h3,
              value: 10800
            },
            {
              label: this.$root.labels.h4,
              value: 14400
            },
            {
              label: this.$root.labels.h6,
              value: 21600
            },
            {
              label: this.$root.labels.h8,
              value: 28800
            },
            {
              label: this.$root.labels.h9,
              value: 32400
            },
            {
              label: this.$root.labels.h10,
              value: 36000
            },
            {
              label: this.$root.labels.h11,
              value: 39600
            },
            {
              label: this.$root.labels.h12,
              value: 43200
            },
            {
              label: this.$root.labels.day1,
              value: 86400
            },
            {
              label: this.$root.labels.days2,
              value: 172800
            },
            {
              label: this.$root.labels.days3,
              value: 259200
            },
            {
              label: this.$root.labels.days4,
              value: 345600
            },
            {
              label: this.$root.labels.days5,
              value: 432000
            },
            {
              label: this.$root.labels.days6,
              value: 518400
            },
            {
              label: this.$root.labels.week1,
              value: 604800
            },
            {
              label: this.$root.labels.weeks2,
              value: 1209600
            },
            {
              label: this.$root.labels.weeks3,
              value: 1814400
            },
            {
              label: this.$root.labels.weeks4,
              value: 2419200
            }
          ],
          countries: [
            {
              'id': 0,
              'iso': 'auto',
              'nicename': 'Identify country code by user\'s IP address'
            },
            {
              'id': 1,
              'iso': 'af',
              'nicename': 'Afghanistan',
              'phonecode': 93
            },
            {
              'id': 2,
              'iso': 'al',
              'nicename': 'Albania',
              'phonecode': 355
            },
            {
              'id': 3,
              'iso': 'dz',
              'nicename': 'Algeria',
              'phonecode': 213
            },
            {
              'id': 4,
              'iso': 'as',
              'nicename': 'American Samoa',
              'phonecode': 1
            },
            {
              'id': 5,
              'iso': 'ad',
              'nicename': 'Andorra',
              'phonecode': 376
            },
            {
              'id': 6,
              'iso': 'ao',
              'nicename': 'Angola',
              'phonecode': 244
            },
            {
              'id': 7,
              'iso': 'ai',
              'nicename': 'Anguilla',
              'phonecode': 1
            },
            {
              'id': 8,
              'iso': 'ag',
              'nicename': 'Antigua and Barbuda',
              'phonecode': 1
            },
            {
              'id': 9,
              'iso': 'ar',
              'nicename': 'Argentina',
              'phonecode': 54
            },
            {
              'id': 10,
              'iso': 'am',
              'nicename': 'Armenia',
              'phonecode': 374
            },
            {
              'id': 11,
              'iso': 'aw',
              'nicename': 'Aruba',
              'phonecode': 297
            },
            {
              'id': 12,
              'iso': 'au',
              'nicename': 'Australia',
              'phonecode': 61
            },
            {
              'id': 13,
              'iso': 'at',
              'nicename': 'Austria',
              'phonecode': 43
            },
            {
              'id': 14,
              'iso': 'az',
              'nicename': 'Azerbaijan',
              'phonecode': 994
            },
            {
              'id': 15,
              'iso': 'bs',
              'nicename': 'Bahamas',
              'phonecode': 1
            },
            {
              'id': 16,
              'iso': 'bh',
              'nicename': 'Bahrain',
              'phonecode': 973
            },
            {
              'id': 17,
              'iso': 'bd',
              'nicename': 'Bangladesh',
              'phonecode': 880
            },
            {
              'id': 18,
              'iso': 'bb',
              'nicename': 'Barbados',
              'phonecode': 1
            },
            {
              'id': 19,
              'iso': 'by',
              'nicename': 'Belarus',
              'phonecode': 375
            },
            {
              'id': 20,
              'iso': 'be',
              'nicename': 'Belgium',
              'phonecode': 32
            },
            {
              'id': 21,
              'iso': 'bz',
              'nicename': 'Belize',
              'phonecode': 501
            },
            {
              'id': 22,
              'iso': 'bj',
              'nicename': 'Benin',
              'phonecode': 229
            },
            {
              'id': 23,
              'iso': 'bm',
              'nicename': 'Bermuda',
              'phonecode': 1
            },
            {
              'id': 24,
              'iso': 'bt',
              'nicename': 'Bhutan',
              'phonecode': 975
            },
            {
              'id': 25,
              'iso': 'bo',
              'nicename': 'Bolivia',
              'phonecode': 591
            },
            {
              'id': 26,
              'iso': 'ba',
              'nicename': 'Bosnia and Herzegovina',
              'phonecode': 387
            },
            {
              'id': 27,
              'iso': 'bw',
              'nicename': 'Botswana',
              'phonecode': 267
            },
            {
              'id': 28,
              'iso': 'br',
              'nicename': 'Brazil',
              'phonecode': 55
            },
            {
              'id': 29,
              'iso': 'vg',
              'nicename': 'British Virgin Islands',
              'phonecode': 1
            },
            {
              'id': 30,
              'iso': 'bn',
              'nicename': 'Brunei',
              'phonecode': 673
            },
            {
              'id': 31,
              'iso': 'bg',
              'nicename': 'Bulgaria',
              'phonecode': 359
            },
            {
              'id': 32,
              'iso': 'bf',
              'nicename': 'Burkina Faso',
              'phonecode': 226
            },
            {
              'id': 33,
              'iso': 'bi',
              'nicename': 'Burundi',
              'phonecode': 257
            },
            {
              'id': 34,
              'iso': 'kh',
              'nicename': 'Cambodia',
              'phonecode': 855
            },
            {
              'id': 35,
              'iso': 'cm',
              'nicename': 'Cameroon',
              'phonecode': 237
            },
            {
              'id': 36,
              'iso': 'ca',
              'nicename': 'Canada',
              'phonecode': 1
            },
            {
              'id': 37,
              'iso': 'cv',
              'nicename': 'Cape Verde',
              'phonecode': 238
            },
            {
              'id': 38,
              'iso': 'ky',
              'nicename': 'Cayman Islands',
              'phonecode': 1
            },
            {
              'id': 39,
              'iso': 'cf',
              'nicename': 'Central African Republic',
              'phonecode': 236
            },
            {
              'id': 40,
              'iso': 'td',
              'nicename': 'Chad',
              'phonecode': 235
            },
            {
              'id': 41,
              'iso': 'cl',
              'nicename': 'Chile',
              'phonecode': 56
            },
            {
              'id': 42,
              'iso': 'cn',
              'nicename': 'China',
              'phonecode': 86
            },
            {
              'id': 43,
              'iso': 'co',
              'nicename': 'Colombia',
              'phonecode': 57
            },
            {
              'id': 44,
              'iso': 'km',
              'nicename': 'Comoros',
              'phonecode': 269
            },
            {
              'id': 45,
              'iso': 'cd',
              'nicename': 'Congo (DRC)',
              'phonecode': 243
            },
            {
              'id': 46,
              'iso': 'cg',
              'nicename': 'Congo (Republic)',
              'phonecode': 242
            },
            {
              'id': 47,
              'iso': 'ck',
              'nicename': 'Cook Islands',
              'phonecode': 682
            },
            {
              'id': 48,
              'iso': 'cr',
              'nicename': 'Costa Rica',
              'phonecode': 506
            },
            {
              'id': 49,
              'iso': 'ci',
              'nicename': 'Cote D\'Ivoire',
              'phonecode': 225
            },
            {
              'id': 50,
              'iso': 'hr',
              'nicename': 'Croatia',
              'phonecode': 385
            },
            {
              'id': 51,
              'iso': 'cu',
              'nicename': 'Cuba',
              'phonecode': 53
            },
            {
              'id': 56,
              'iso': 'cw',
              'nicename': 'Curacao',
              'phonecode': 599
            },
            {
              'id': 57,
              'iso': 'cy',
              'nicename': 'Cyprus',
              'phonecode': 357
            },
            {
              'id': 58,
              'iso': 'cz',
              'nicename': 'Czech Republic',
              'phonecode': 420
            },
            {
              'id': 59,
              'iso': 'dk',
              'nicename': 'Denmark',
              'phonecode': 45
            },
            {
              'id': 60,
              'iso': 'dj',
              'nicename': 'Djibouti',
              'phonecode': 253
            },
            {
              'id': 61,
              'iso': 'dm',
              'nicename': 'Dominica',
              'phonecode': 1
            },
            {
              'id': 62,
              'iso': 'do',
              'nicename': 'Dominican Republic',
              'phonecode': 1
            },
            {
              'id': 63,
              'iso': 'ec',
              'nicename': 'Ecuador',
              'phonecode': 593
            },
            {
              'id': 64,
              'iso': 'eg',
              'nicename': 'Egypt',
              'phonecode': 20
            },
            {
              'id': 65,
              'iso': 'sv',
              'nicename': 'El Salvador',
              'phonecode': 503
            },
            {
              'id': 66,
              'iso': 'gq',
              'nicename': 'Equatorial Guinea',
              'phonecode': 240
            },
            {
              'id': 67,
              'iso': 'er',
              'nicename': 'Eritrea',
              'phonecode': 291
            },
            {
              'id': 68,
              'iso': 'ee',
              'nicename': 'Estonia',
              'phonecode': 372
            },
            {
              'id': 69,
              'iso': 'et',
              'nicename': 'Ethiopia',
              'phonecode': 251
            },
            {
              'id': 70,
              'iso': 'fk',
              'nicename': 'Falkland Islands (Malvinas)',
              'phonecode': 500
            },
            {
              'id': 71,
              'iso': 'fo',
              'nicename': 'Faroe Islands',
              'phonecode': 298
            },
            {
              'id': 72,
              'iso': 'fj',
              'nicename': 'Fiji',
              'phonecode': 679
            },
            {
              'id': 73,
              'iso': 'fi',
              'nicename': 'Finland',
              'phonecode': 358
            },
            {
              'id': 74,
              'iso': 'fr',
              'nicename': 'France',
              'phonecode': 33
            },
            {
              'id': 75,
              'iso': 'gf',
              'nicename': 'French Guiana',
              'phonecode': 594
            },
            {
              'id': 76,
              'iso': 'pf',
              'nicename': 'French Polynesia',
              'phonecode': 689
            },
            {
              'id': 77,
              'iso': 'ga',
              'nicename': 'Gabon',
              'phonecode': 241
            },
            {
              'id': 78,
              'iso': 'gm',
              'nicename': 'Gambia',
              'phonecode': 220
            },
            {
              'id': 79,
              'iso': 'ge',
              'nicename': 'Georgia',
              'phonecode': 995
            },
            {
              'id': 80,
              'iso': 'de',
              'nicename': 'Germany',
              'phonecode': 49
            },
            {
              'id': 81,
              'iso': 'gh',
              'nicename': 'Ghana',
              'phonecode': 233
            },
            {
              'id': 82,
              'iso': 'gi',
              'nicename': 'Gibraltar',
              'phonecode': 350
            },
            {
              'id': 83,
              'iso': 'gr',
              'nicename': 'Greece',
              'phonecode': 30
            },
            {
              'id': 84,
              'iso': 'gl',
              'nicename': 'Greenland',
              'phonecode': 299
            },
            {
              'id': 87,
              'iso': 'gu',
              'nicename': 'Guam',
              'phonecode': 1
            },
            {
              'id': 88,
              'iso': 'gt',
              'nicename': 'Guatemala',
              'phonecode': 502
            },
            {
              'id': 89,
              'iso': 'gg',
              'nicename': 'Guernsey',
              'phonecode': 44
            },
            {
              'id': 90,
              'iso': 'gn',
              'nicename': 'Guinea',
              'phonecode': 224
            },
            {
              'id': 91,
              'iso': 'gw',
              'nicename': 'Guinea-Bissau',
              'phonecode': 245
            },
            {
              'id': 92,
              'iso': 'gy',
              'nicename': 'Guyana',
              'phonecode': 592
            },
            {
              'id': 93,
              'iso': 'ht',
              'nicename': 'Haiti',
              'phonecode': 509
            },
            {
              'id': 94,
              'iso': 'hn',
              'nicename': 'Honduras',
              'phonecode': 504
            },
            {
              'id': 95,
              'iso': 'hk',
              'nicename': 'Hong Kong',
              'phonecode': 852
            },
            {
              'id': 96,
              'iso': 'hu',
              'nicename': 'Hungary',
              'phonecode': 36
            },
            {
              'id': 97,
              'iso': 'is',
              'nicename': 'Iceland',
              'phonecode': 354
            },
            {
              'id': 98,
              'iso': 'in',
              'nicename': 'India',
              'phonecode': 91
            },
            {
              'id': 99,
              'iso': 'id',
              'nicename': 'Indonesia',
              'phonecode': 62
            },
            {
              'id': 100,
              'iso': 'ir',
              'nicename': 'Iran',
              'phonecode': 98
            },
            {
              'id': 101,
              'iso': 'iq',
              'nicename': 'Iraq',
              'phonecode': 964
            },
            {
              'id': 102,
              'iso': 'ie',
              'nicename': 'Ireland',
              'phonecode': 353
            },
            {
              'id': 103,
              'iso': 'im',
              'nicename': 'Isle of Man',
              'phonecode': 44
            },
            {
              'id': 104,
              'iso': 'il',
              'nicename': 'Israel',
              'phonecode': 972
            },
            {
              'id': 105,
              'iso': 'it',
              'nicename': 'Italy',
              'phonecode': 39
            },
            {
              'id': 106,
              'iso': 'jm',
              'nicename': 'Jamaica',
              'phonecode': 1
            },
            {
              'id': 107,
              'iso': 'jp',
              'nicename': 'Japan',
              'phonecode': 81
            },
            {
              'id': 108,
              'iso': 'je',
              'nicename': 'Jersey',
              'phonecode': 44
            },
            {
              'id': 109,
              'iso': 'jo',
              'nicename': 'Jordan',
              'phonecode': 962
            },
            {
              'id': 110,
              'iso': 'kz',
              'nicename': 'Kazakhstan',
              'phonecode': 7
            },
            {
              'id': 111,
              'iso': 'ke',
              'nicename': 'Kenya',
              'phonecode': 254
            },
            {
              'id': 112,
              'iso': 'ki',
              'nicename': 'Kiribati',
              'phonecode': 686
            },
            {
              'id': 113,
              'iso': 'kw',
              'nicename': 'Kuwait',
              'phonecode': 965
            },
            {
              'id': 114,
              'iso': 'kg',
              'nicename': 'Kyrgyzstan',
              'phonecode': 996
            },
            {
              'id': 115,
              'iso': 'la',
              'nicename': 'Laos',
              'phonecode': 856
            },
            {
              'id': 116,
              'iso': 'lv',
              'nicename': 'Latvia',
              'phonecode': 371
            },
            {
              'id': 117,
              'iso': 'lb',
              'nicename': 'Lebanon',
              'phonecode': 961
            },
            {
              'id': 118,
              'iso': 'ls',
              'nicename': 'Lesotho',
              'phonecode': 266
            },
            {
              'id': 119,
              'iso': 'lr',
              'nicename': 'Liberia',
              'phonecode': 231
            },
            {
              'id': 120,
              'iso': 'ly',
              'nicename': 'Libya',
              'phonecode': 218
            },
            {
              'id': 121,
              'iso': 'li',
              'nicename': 'Liechtenstein',
              'phonecode': 423
            },
            {
              'id': 122,
              'iso': 'lt',
              'nicename': 'Lithuania',
              'phonecode': 370
            },
            {
              'id': 123,
              'iso': 'lu',
              'nicename': 'Luxembourg',
              'phonecode': 352
            },
            {
              'id': 124,
              'iso': 'mo',
              'nicename': 'Macao',
              'phonecode': 853
            },
            {
              'id': 125,
              'iso': 'mk',
              'nicename': 'Macedonia (FYROM)',
              'phonecode': 389
            },
            {
              'id': 126,
              'iso': 'mg',
              'nicename': 'Madagascar',
              'phonecode': 261
            },
            {
              'id': 127,
              'iso': 'mw',
              'nicename': 'Malawi',
              'phonecode': 265
            },
            {
              'id': 128,
              'iso': 'my',
              'nicename': 'Malaysia',
              'phonecode': 60
            },
            {
              'id': 129,
              'iso': 'mv',
              'nicename': 'Maldives',
              'phonecode': 960
            },
            {
              'id': 130,
              'iso': 'ml',
              'nicename': 'Mali',
              'phonecode': 223
            },
            {
              'id': 131,
              'iso': 'mt',
              'nicename': 'Malta',
              'phonecode': 356
            },
            {
              'id': 132,
              'iso': 'mh',
              'nicename': 'Marshall Islands',
              'phonecode': 692
            },
            {
              'id': 133,
              'iso': 'mq',
              'nicename': 'Martinique',
              'phonecode': 596
            },
            {
              'id': 134,
              'iso': 'mr',
              'nicename': 'Mauritania',
              'phonecode': 222
            },
            {
              'id': 135,
              'iso': 'mu',
              'nicename': 'Mauritius',
              'phonecode': 230
            },
            {
              'id': 136,
              'iso': 'yt',
              'nicename': 'Mayotte',
              'phonecode': 269
            },
            {
              'id': 137,
              'iso': 'mx',
              'nicename': 'Mexico',
              'phonecode': 52
            },
            {
              'id': 138,
              'iso': 'fm',
              'nicename': 'Micronesia',
              'phonecode': 691
            },
            {
              'id': 139,
              'iso': 'md',
              'nicename': 'Moldova',
              'phonecode': 373
            },
            {
              'id': 140,
              'iso': 'mc',
              'nicename': 'Monaco',
              'phonecode': 377
            },
            {
              'id': 141,
              'iso': 'mn',
              'nicename': 'Mongolia',
              'phonecode': 976
            },
            {
              'id': 142,
              'iso': 'me',
              'nicename': 'Montenegro',
              'phonecode': 382
            },
            {
              'id': 143,
              'iso': 'ms',
              'nicename': 'Montserrat',
              'phonecode': 1
            },
            {
              'id': 144,
              'iso': 'ma',
              'nicename': 'Morocco',
              'phonecode': 212
            },
            {
              'id': 145,
              'iso': 'mz',
              'nicename': 'Mozambique',
              'phonecode': 258
            },
            {
              'id': 146,
              'iso': 'mm',
              'nicename': 'Myanmar',
              'phonecode': 95
            },
            {
              'id': 147,
              'iso': 'na',
              'nicename': 'Namibia',
              'phonecode': 264
            },
            {
              'id': 148,
              'iso': 'nr',
              'nicename': 'Nauru',
              'phonecode': 674
            },
            {
              'id': 149,
              'iso': 'np',
              'nicename': 'Nepal',
              'phonecode': 977
            },
            {
              'id': 150,
              'iso': 'nl',
              'nicename': 'Netherlands',
              'phonecode': 31
            },
            {
              'id': 151,
              'iso': 'nc',
              'nicename': 'New Caledonia',
              'phonecode': 687
            },
            {
              'id': 152,
              'iso': 'nz',
              'nicename': 'New Zealand',
              'phonecode': 64
            },
            {
              'id': 153,
              'iso': 'ni',
              'nicename': 'Nicaragua',
              'phonecode': 505
            },
            {
              'id': 154,
              'iso': 'ne',
              'nicename': 'Niger',
              'phonecode': 227
            },
            {
              'id': 155,
              'iso': 'ng',
              'nicename': 'Nigeria',
              'phonecode': 234
            },
            {
              'id': 156,
              'iso': 'nu',
              'nicename': 'Niue',
              'phonecode': 683
            },
            {
              'id': 157,
              'iso': 'nf',
              'nicename': 'Norfolk Island',
              'phonecode': 672
            },
            {
              'id': 158,
              'iso': 'kp',
              'nicename': 'North Korea',
              'phonecode': 850
            },
            {
              'id': 159,
              'iso': 'mp',
              'nicename': 'Northern Mariana Islands',
              'phonecode': 1
            },
            {
              'id': 160,
              'iso': 'no',
              'nicename': 'Norway',
              'phonecode': 47
            },
            {
              'id': 161,
              'iso': 'om',
              'nicename': 'Oman',
              'phonecode': 968
            },
            {
              'id': 162,
              'iso': 'pk',
              'nicename': 'Pakistan',
              'phonecode': 92
            },
            {
              'id': 163,
              'iso': 'pw',
              'nicename': 'Palau',
              'phonecode': 680
            },
            {
              'id': 164,
              'iso': 'ps',
              'nicename': 'Palestine',
              'phonecode': 970
            },
            {
              'id': 165,
              'iso': 'pa',
              'nicename': 'Panama',
              'phonecode': 507
            },
            {
              'id': 166,
              'iso': 'pg',
              'nicename': 'Papua New Guinea',
              'phonecode': 675
            },
            {
              'id': 167,
              'iso': 'py',
              'nicename': 'Paraguay',
              'phonecode': 595
            },
            {
              'id': 168,
              'iso': 'pe',
              'nicename': 'Peru',
              'phonecode': 51
            },
            {
              'id': 169,
              'iso': 'ph',
              'nicename': 'Philippines',
              'phonecode': 63
            },
            {
              'id': 170,
              'iso': 'pl',
              'nicename': 'Poland',
              'phonecode': 48
            },
            {
              'id': 171,
              'iso': 'pt',
              'nicename': 'Portugal',
              'phonecode': 351
            },
            {
              'id': 172,
              'iso': 'pr',
              'nicename': 'Puerto Rico',
              'phonecode': 1
            },
            {
              'id': 173,
              'iso': 'qa',
              'nicename': 'Qatar',
              'phonecode': 974
            },
            {
              'id': 175,
              'iso': 'ro',
              'nicename': 'Romania',
              'phonecode': 40
            },
            {
              'id': 176,
              'iso': 'ru',
              'nicename': 'Russia',
              'phonecode': 7
            },
            {
              'id': 177,
              'iso': 'rw',
              'nicename': 'Rwanda',
              'phonecode': 250
            },
            {
              'id': 178,
              'iso': 'bl',
              'nicename': 'Saint Barthelemy',
              'phonecode': 590
            },
            {
              'id': 179,
              'iso': 'sh',
              'nicename': 'Saint Helena',
              'phonecode': 290
            },
            {
              'id': 180,
              'iso': 'kn',
              'nicename': 'Saint Kitts and Nevis',
              'phonecode': 1
            },
            {
              'id': 181,
              'iso': 'lc',
              'nicename': 'Saint Lucia',
              'phonecode': 1
            },
            {
              'id': 182,
              'iso': 'mf',
              'nicename': 'Saint Martin',
              'phonecode': 590
            },
            {
              'id': 184,
              'iso': 'vc',
              'nicename': 'Saint Vincent and the Grenadines',
              'phonecode': 1
            },
            {
              'id': 185,
              'iso': 'ws',
              'nicename': 'Samoa',
              'phonecode': 684
            },
            {
              'id': 186,
              'iso': 'sm',
              'nicename': 'San Marino',
              'phonecode': 378
            },
            {
              'id': 187,
              'iso': 'st',
              'nicename': 'Sao Tome and Principe',
              'phonecode': 239
            },
            {
              'id': 188,
              'iso': 'sa',
              'nicename': 'Saudi Arabia',
              'phonecode': 966
            },
            {
              'id': 189,
              'iso': 'sn',
              'nicename': 'Senegal',
              'phonecode': 221
            },
            {
              'id': 190,
              'iso': 'rs',
              'nicename': 'Serbia',
              'phonecode': 381
            },
            {
              'id': 191,
              'iso': 'sc',
              'nicename': 'Seychelles',
              'phonecode': 248
            },
            {
              'id': 192,
              'iso': 'sl',
              'nicename': 'Sierra Leone',
              'phonecode': 232
            },
            {
              'id': 193,
              'iso': 'sg',
              'nicename': 'Singapore',
              'phonecode': 65
            },
            {
              'id': 195,
              'iso': 'sk',
              'nicename': 'Slovakia',
              'phonecode': 421
            },
            {
              'id': 196,
              'iso': 'si',
              'nicename': 'Slovenia',
              'phonecode': 386
            },
            {
              'id': 197,
              'iso': 'sb',
              'nicename': 'Solomon Islands',
              'phonecode': 677
            },
            {
              'id': 198,
              'iso': 'so',
              'nicename': 'Somalia',
              'phonecode': 252
            },
            {
              'id': 199,
              'iso': 'za',
              'nicename': 'South Africa',
              'phonecode': 27
            },
            {
              'id': 200,
              'iso': 'kr',
              'nicename': 'South Korea',
              'phonecode': 82
            },
            {
              'id': 201,
              'iso': 'ss',
              'nicename': 'South Sudan',
              'phonecode': 211
            },
            {
              'id': 202,
              'iso': 'es',
              'nicename': 'Spain',
              'phonecode': 34
            },
            {
              'id': 203,
              'iso': 'lk',
              'nicename': 'Sri Lanka',
              'phonecode': 94
            },
            {
              'id': 204,
              'iso': 'sd',
              'nicename': 'Sudan',
              'phonecode': 249
            },
            {
              'id': 205,
              'iso': 'sr',
              'nicename': 'Suriname',
              'phonecode': 597
            },
            {
              'id': 207,
              'iso': 'sz',
              'nicename': 'Swaziland',
              'phonecode': 268
            },
            {
              'id': 208,
              'iso': 'se',
              'nicename': 'Sweden',
              'phonecode': 46
            },
            {
              'id': 209,
              'iso': 'ch',
              'nicename': 'Switzerland',
              'phonecode': 41
            },
            {
              'id': 210,
              'iso': 'sy',
              'nicename': 'Syria',
              'phonecode': 963
            },
            {
              'id': 211,
              'iso': 'tw',
              'nicename': 'Taiwan',
              'phonecode': 886
            },
            {
              'id': 212,
              'iso': 'tj',
              'nicename': 'Tajikistan',
              'phonecode': 992
            },
            {
              'id': 214,
              'iso': 'th',
              'nicename': 'Thailand',
              'phonecode': 66
            },
            {
              'id': 215,
              'iso': 'tl',
              'nicename': 'Timor-Leste',
              'phonecode': 670
            },
            {
              'id': 216,
              'iso': 'tg',
              'nicename': 'Togo',
              'phonecode': 228
            },
            {
              'id': 217,
              'iso': 'tk',
              'nicename': 'Tokelau',
              'phonecode': 690
            },
            {
              'id': 218,
              'iso': 'to',
              'nicename': 'Tonga',
              'phonecode': 676
            },
            {
              'id': 219,
              'iso': 'tt',
              'nicename': 'Trinidad and Tobago',
              'phonecode': 1
            },
            {
              'id': 220,
              'iso': 'tn',
              'nicename': 'Tunisia',
              'phonecode': 216
            },
            {
              'id': 221,
              'iso': 'tr',
              'nicename': 'Turkey',
              'phonecode': 90
            },
            {
              'id': 222,
              'iso': 'tm',
              'nicename': 'Turkmenistan',
              'phonecode': 7370
            },
            {
              'id': 223,
              'iso': 'tc',
              'nicename': 'Turks and Caicos Islands',
              'phonecode': 1
            },
            {
              'id': 224,
              'iso': 'tv',
              'nicename': 'Tuvalu',
              'phonecode': 688
            },
            {
              'id': 225,
              'iso': 'ug',
              'nicename': 'Uganda',
              'phonecode': 256
            },
            {
              'id': 226,
              'iso': 'ua',
              'nicename': 'Ukraine',
              'phonecode': 380
            },
            {
              'id': 227,
              'iso': 'ae',
              'nicename': 'United Arab Emirates',
              'phonecode': 971
            },
            {
              'id': 228,
              'iso': 'gb',
              'nicename': 'United Kingdom',
              'phonecode': 44
            },
            {
              'id': 229,
              'iso': 'us',
              'nicename': 'United States',
              'phonecode': 1
            },
            {
              'id': 230,
              'iso': 'uy',
              'nicename': 'Uruguay',
              'phonecode': 598
            },
            {
              'id': 231,
              'iso': 'uz',
              'nicename': 'Uzbekistan',
              'phonecode': 998
            },
            {
              'id': 232,
              'iso': 'vu',
              'nicename': 'Vanuatu',
              'phonecode': 678
            },
            {
              'id': 233,
              'iso': 'va',
              'nicename': 'Vatican City',
              'phonecode': 39
            },
            {
              'id': 234,
              'iso': 've',
              'nicename': 'Venezuela',
              'phonecode': 58
            },
            {
              'id': 235,
              'iso': 'vn',
              'nicename': 'Vietnam',
              'phonecode': 84
            },
            {
              'id': 236,
              'iso': 'vi',
              'nicename': 'Virgin Islands, U.S.',
              'phonecode': 1
            },
            {
              'id': 237,
              'iso': 'ye',
              'nicename': 'Yemen',
              'phonecode': 967
            },
            {
              'id': 238,
              'iso': 'zm',
              'nicename': 'Zambia',
              'phonecode': 260
            },
            {
              'id': 239,
              'iso': 'zw',
              'nicename': 'Zimbabwe',
              'phonecode': 263
            },
            {
              'id': 240,
              'iso': 'ax',
              'nicename': 'Åland Islands',
              'phonecode': 358
            }
          ],
          itemsPerPage: [9, 12, 15, 18, 21, 24, 27, 30],
          defaultPageOnBackend: [
            {
              label: this.$root.labels.dashboard,
              value: 'Dashboard'
            },
            {
              label: this.$root.labels.calendar,
              value: 'Calendar'
            },
            {
              label: this.$root.labels.appointments,
              value: 'Appointments'
            }
          ]
        }
      }
    },

    updated () {
      this.inlineSVG()
    },

    mounted () {
      this.inlineSVG()
    },

    methods: {
      closeDialog () {
        this.$emit('closeDialogSettingsGeneral')
      },
      onSubmit () {
        this.$emit('closeDialogSettingsGeneral')
        this.$emit('updateSettings', {'general': this.settings})
      }
    }
  }
</script>