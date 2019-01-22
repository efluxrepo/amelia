<template>
  <div>

    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="20">
            <h2>{{ $root.labels.purchase_code_settings }}</h2>
          </el-col>
          <el-col :span="4" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form
          :model="settings"
          ref="settings"
          :rules="rules"
          label-position="top"
          @submit.prevent="onSubmit"
      >

        <!-- Purchase Code -->
        <el-form-item
            class="am-purchase-code"
            :label="$root.labels.codecanyon_purchase_code + ':'"
            prop="code"
        >
          <el-input v-model="settings.purchaseCode" auto-complete="off">
          </el-input>
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
      activation: {
        type: Object
      }
    },

    data () {
      let validatePurchaseCode = (rule, value, callback) => {

      }

      return {
        settings: Object.assign({}, this.activation),
        rules: {
          code: [
            {validator: validatePurchaseCode, trigger: 'submit'}
          ]
        }

      }
    },

    created () {

    },

    updated () {
      this.inlineSVG()
    },

    mounted () {
      this.inlineSVG()
    },

    methods: {

      closeDialog () {
        this.$emit('closeDialogSettingsPurchaseCode')
      },

      onSubmit () {
        this.$emit('closeDialogSettingsPurchaseCode')
        this.$emit('updateSettings', {'activation': this.settings})
      }

    },

    components: {}
  }
</script>