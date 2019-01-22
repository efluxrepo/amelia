<template>
  <div>

    <div class="am-dialog-scrollable">
      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2>{{ $root.labels.roles_settings }}</h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form label-position="top" @submit.prevent="onSubmit">

        <el-tabs>
          <!-- Provider -->
          <el-tab-pane :label="$root.labels.employee">
            <!-- Default allow configure own schedule -->
            <el-popover :disabled="!$root.isLite" ref="allowConfigureSchedulePop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
            <div class="am-setting-box am-switch-box" v-popover:allowConfigureSchedulePop :class="{'am-lite-disabled': ($root.isLite)}" >
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.allow_configure_schedule }}
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                          v-model="settings.allowConfigureSchedule"
                          active-text=""
                          inactive-text=""
                          :disabled="$root.isLite"
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>

            <!-- Default allow provider add/edit appointment -->
            <el-popover :disabled="!$root.isLite" ref="allowWriteAppointmentsPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
            <div class="am-setting-box am-switch-box" v-popover:allowWriteAppointmentsPop :class="{'am-lite-disabled': ($root.isLite)}">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.allow_write_appointments }}
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                          v-model="settings.allowWriteAppointments"
                          active-text=""
                          inactive-text=""
                          :disabled="$root.isLite"
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
          </el-tab-pane>
          <!-- Customer -->
          <el-tab-pane :label="$root.labels.customer">
            <!-- Automatically create Amelia Customer user -->
            <el-popover :disabled="!$root.isLite" ref="automaticallyCreateCustomer" v-bind="$root.popLiteProps"><PopLite/></el-popover>
            <div class="am-setting-box am-switch-box" v-popover:automaticallyCreateCustomer :class="{'am-lite-disabled': ($root.isLite)}">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.automatically_create_customer }}
                  <el-tooltip placement="top">
                    <div slot="content" v-html="$root.labels.automatically_create_customer_tooltip"></div>
                    <i class="el-icon-question am-tooltip-icon"></i>
                  </el-tooltip>
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                          v-model="settings.automaticallyCreateCustomer"
                          active-text=""
                          inactive-text=""
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>

            <!-- Default inspect customer firstName & lastName matching with email -->
            <div class="am-setting-box am-switch-box">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.inspect_customer_info }}
                  <el-tooltip placement="top">
                    <div slot="content" v-html="$root.labels.inspect_customer_info_tooltip"></div>
                    <i class="el-icon-question am-tooltip-icon"></i>
                  </el-tooltip>
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                          v-model="settings.inspectCustomerInfo"
                          active-text=""
                          inactive-text=""
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>
          </el-tab-pane>
        </el-tabs>
      </el-form>

    </div>

    <!-- Dialog Footer -->
    <div class="am-dialog-footer">
      <div class="am-dialog-footer-actions">
        <el-row>
          <el-col :sm="24" class="align-right">
            <el-button type="" @click="closeDialog" class="">Cancel</el-button>
            <el-button type="primary" @click="onSubmit" class="am-dialog-create">Save</el-button>
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
        settings: Object.assign({}, this.general)
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
        this.$emit('closeDialogSettingsRoles')
      },
      onSubmit () {
        this.$emit('closeDialogSettingsRoles')
        this.$emit('updateSettings', {'general': this.settings})
      }
    }
  }
</script>