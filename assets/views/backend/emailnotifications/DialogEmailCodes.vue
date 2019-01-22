<template>
  <div>
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="14">
            <h2>{{ $root.labels.email_placeholders }}</h2>
          </el-col>
          <el-col :span="10" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Email Codes -->
      <div class="am-email-codes">
        <div class="am-email-code">
          <el-tooltip
              v-for="code in sortedPlaceholders"
              :key="code.code"
              effect="dark"
              :content="code.label"
              placement="left">
            <p @click="copyCodeText(code.value)" :class="{'am-lite-container-disabled' : code.value === '%appointment_cancel_url%' && $root.isLite}">
              <i class="el-icon-information"></i>
              <span>{{ code.value }}</span>
              <span class="am-copy-code">{{ $root.labels.copy }}</span>
              <span class="am-copied-code">{{ $root.labels.copied }}</span>
            </p>
          </el-tooltip>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'

  export default {

    mixins: [imageMixin],

    props: {
      customFields: {
        default: []
      }
    },

    data () {
      return {
        placeholders: [
          {
            value: '%appointment_date%',
            label: this.$root.labels.ph_appointment_date
          },
          {
            value: '%appointment_date_time%',
            label: this.$root.labels.ph_appointment_date_time
          },
          {
            value: '%appointment_start_time%',
            label: this.$root.labels.ph_appointment_start_time
          },
          {
            value: '%appointment_end_time%',
            label: this.$root.labels.ph_appointment_end_time
          },
          {
            value: '%appointment_notes%',
            label: this.$root.labels.ph_appointment_notes
          },
          {
            value: '%appointment_price%',
            label: this.$root.labels.ph_appointment_price
          },
          {
            value: '%company_address%',
            label: this.$root.labels.ph_company_address
          },
          {
            value: '%company_name%',
            label: this.$root.labels.ph_company_name
          },
          {
            value: '%company_phone%',
            label: this.$root.labels.ph_company_phone
          },
          {
            value: '%company_website%',
            label: this.$root.labels.ph_company_website
          },
          {
            value: '%category_name%',
            label: this.$root.labels.ph_category_name
          },
          {
            value: '%service_duration%',
            label: this.$root.labels.ph_service_duration
          },
          {
            value: '%service_name%',
            label: this.$root.labels.ph_service_name
          },
          {
            value: '%service_price%',
            label: this.$root.labels.ph_service_price
          },
          {
            value: '%customer_email%',
            label: this.$root.labels.ph_customer_email
          },
          {
            value: '%customer_first_name%',
            label: this.$root.labels.ph_customer_first_name
          },
          {
            value: '%customer_last_name%',
            label: this.$root.labels.ph_customer_last_name
          },
          {
            value: '%customer_full_name%',
            label: this.$root.labels.ph_customer_full_name
          },
          {
            value: '%customer_phone%',
            label: this.$root.labels.ph_customer_phone
          },
          {
            value: '%employee_email%',
            label: this.$root.labels.ph_employee_email
          },
          {
            value: '%employee_first_name%',
            label: this.$root.labels.ph_employee_first_name
          },
          {
            value: '%employee_last_name%',
            label: this.$root.labels.ph_employee_last_name
          },
          {
            value: '%employee_full_name%',
            label: this.$root.labels.ph_employee_full_name
          },
          {
            value: '%employee_phone%',
            label: this.$root.labels.ph_employee_phone
          },
          {
            value: '%location_address%',
            label: this.$root.labels.ph_location_address
          },
          {
            value: '%location_name%',
            label: this.$root.labels.ph_location_name
          },
          {
            value: '%appointment_cancel_url%',
            label: this.$root.labels.ph_appointment_cancel_url
          }
        ]

      }
    },

    mounted () {
      this.inlineSVG()
      this.addCustomFieldsPlaceholders()
    },

    methods: {
      closeDialog () {
        this.$emit('closeDialogEmailCodes')
      },

      copyCodeText (code) {
        let textArea = document.createElement('textarea')
        textArea.value = code
        document.body.appendChild(textArea)
        textArea.select()
        document.execCommand('Copy')
        document.body.removeChild(textArea)
      },

      addCustomFieldsPlaceholders () {
        for (let i = 0; i < this.customFields.length; i++) {
          this.placeholders.push({
            value: '%custom_field_' + this.customFields[i].id + '%',
            label: this.customFields[i].label
          })
        }
      }
    },

    computed: {
      sortedPlaceholders () {
        return this.placeholders.sort((a, b) => (a.value > b.value) ? 1 : ((b.value > a.value) ? -1 : 0))
      }
    },

    components: {}
  }
</script>