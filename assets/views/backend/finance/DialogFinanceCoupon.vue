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
    <div class="am-dialog-scrollable" v-if="!dialogLoading">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="18">
            <h2 v-if="coupon.id !== 0">{{ $root.labels.edit_coupon }}</h2>
            <h2 v-else>{{ $root.labels.new_coupon }}</h2>
          </el-col>
          <el-col :span="6" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form :model="coupon" ref="coupon" :rules="rules" label-position="top" @submit.prevent="onSubmit">

        <!-- Code -->
        <el-form-item label="placeholder" prop="code">
          <label slot="label">
            {{ $root.labels.code }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.code_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input v-model="coupon.code" :placeholder="$root.labels.code" @input="clearValidation()"></el-input>
        </el-form-item>

        <!-- Discount & Deduction -->
        <el-row :gutter="20">

          <el-col :span="12">
            <el-form-item :label="$root.labels.discount + ':'" prop="discount">
              <el-input-number
                  v-model="coupon.discount"
                  :min="0"
                  :max="100"
                  @input="clearValidation()"
              >
              </el-input-number>
            </el-form-item>
          </el-col>

          <el-col :span="12">
            <el-form-item :label="$root.labels.deduction + ' (' + getCurrencySymbol() + '):'" prop="deduction">
              <el-input-number v-model="coupon.deduction" :min="0" @input="clearValidation()"></el-input-number>
            </el-form-item>

          </el-col>
        </el-row>

        <!-- Usage Limit -->
        <el-form-item label="placeholder" prop="limit">
          <label slot="label">
            {{ $root.labels.usage_limit }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.usage_limit_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-input-number
              v-model="coupon.limit"
              :min="0"
              @input="clearValidation()"
          >
          </el-input-number>
        </el-form-item>

        <!-- Services -->
        <el-form-item label="placeholder" v-if="services.length > 0" prop="services">
          <label slot="label">
            {{ $root.labels.services }}:
            <el-tooltip placement="top">
              <div slot="content" v-html="$root.labels.services_tooltip"></div>
              <i class="el-icon-question am-tooltip-icon"></i>
            </el-tooltip>
          </label>
          <el-select
              v-model="coupon.serviceList"
              value-key="id"
              filterable
              multiple
              :placeholder="$root.labels.select_service"
              collapse-tags
              @change="clearValidation()"

          >
            <div class="am-drop-parent"
                 @click="allServicesSelection"
            >
              <span>{{ $root.labels.select_all_services }}</span>
            </div>
            <el-option
                v-for="item in services"
                :key="item.id"
                :label="item.name"
                :value="item">
            </el-option>
          </el-select>
        </el-form-item>


      </el-form>

    </div>

    <!-- Dialog Actions -->
    <dialog-actions
        v-if="!dialogLoading"
        formName="coupon"
        urlName="coupons"
        :isNew="coupon.id === 0"
        :entity="coupon"
        :getParsedEntity="getParsedEntity"

        :action="{
          haveAdd: true,
          haveEdit: true,
          haveStatus: true,
          haveRemove: $root.settings.capabilities.canDelete === true,
          haveRemoveEffect: false,
          haveDuplicate: true
        }"

        :message="{
          success: {
            save: $root.labels.coupon_saved,
            remove: $root.labels.coupon_deleted,
            show: $root.labels.coupon_visible,
            hide: $root.labels.coupon_hidden
          },
          confirm: {
            remove: $root.labels.confirm_delete_coupon,
            show: $root.labels.confirm_show_coupon,
            hide: $root.labels.confirm_hide_coupon,
            duplicate: $root.labels.confirm_duplicate_coupon
          }
        }"
    >
    </dialog-actions>

  </div>
</template>

<script>
  import DialogActions from '../parts/DialogActions.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'

  export default {

    mixins: [imageMixin, notifyMixin, priceMixin],

    props: {
      coupon: null,
      couponFetched: false,
      services: null
    },

    data () {
      let validateAmount = (rule, value, callback) => {
        if (this.coupon.discount === 0 && this.coupon.deduction === 0) {
          callback(new Error(this.$root.labels.no_coupon_amount))
        } else {
          callback()
        }
      }

      let validateServicesCount = (rule, value, callback) => {
        if (this.coupon.serviceList.length === 0) {
          callback(new Error(this.$root.labels.no_services_selected))
        } else {
          callback()
        }
      }

      let validateUsageCount = (rule, value, callback) => {
        if (this.coupon.limit <= 0) {
          callback(new Error(this.$root.labels.coupon_usage_limit_validation))
        } else {
          callback()
        }
      }

      return {
        allServicesSelected: false,
        dialogLoading: true,
        rules: {
          code: [
            {required: true, message: this.$root.labels.enter_coupon_code_warning, trigger: 'submit'}
          ],
          discount: [
            {validator: validateAmount, trigger: 'submit'}
          ],
          deduction: [
            {validator: validateAmount, trigger: 'submit'}
          ],
          services: [
            {validator: validateServicesCount, trigger: 'submit'}
          ],
          limit: [
            {validator: validateUsageCount, trigger: 'submit'}
          ]
        }
      }
    },

    mounted: !AMELIA_LITE_VERSION ? function () {
      this.instantiateDialog()
      this.inlineSVG()
    } : function () {},

    created: !AMELIA_LITE_VERSION ? function () {
      this.inlineSVG()
    } : function () {},

    methods: {
      instantiateDialog: !AMELIA_LITE_VERSION ? function () {
        if (this.coupon !== null || (this.coupon !== null && this.coupon.id === 0)) {
          this.dialogLoading = false
        }
      } : function () {},

      getParsedEntity: !AMELIA_LITE_VERSION ? function () {
        return {
          id: this.coupon.id,
          code: this.coupon.code,
          discount: this.coupon.discount,
          deduction: this.coupon.deduction,
          limit: this.coupon.limit,
          status: this.coupon.status,
          services: this.coupon.serviceList.map(service => service.id)
        }
      } : function () {},

      closeDialog: !AMELIA_LITE_VERSION ? function () {
        this.$emit('closeDialog')
      } : function () {},

      allServicesSelection: !AMELIA_LITE_VERSION ? function () {
        this.coupon.serviceList = (this.allServicesSelected = !this.allServicesSelected) ? this.services : []
      } : function () {},

      clearValidation: !AMELIA_LITE_VERSION ? function () {
        if (typeof this.$refs.coupon !== 'undefined') {
          this.$refs.coupon.clearValidate()
        }
      } : function () {}
    },

    watch: {
      'couponFetched' () {
        if (this.couponFetched === true) {
          this.instantiateDialog()
        }
      }
    },

    components: {
      DialogActions
    }
  }
</script>