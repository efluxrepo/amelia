<template>
  <div :class="dialogClass" class="am-confirmation-booking">

    <div v-show="fetched">

      <!-- Header Error-->
      <div class="am-payment-error">
        <el-alert
            :title="headerErrorMessage !== '' ? headerErrorMessage : $root.labels.payment_error"
            type="warning"
            v-show="headerErrorShow"
            show-icon
        >
        </el-alert>
      </div>

      <!-- Confirm Dialog Header -->
      <div class="am-confirmation-booking-header" v-show="fetched">
        <img :src="pictureLoad(service, false)" @error="imageLoadError(service, false)" :alt="service.name"/>
        <h2>{{ service.name }}</h2>
      </div>

      <!-- Confirm Dialog Body -->
      <el-form
          :model="appointment.bookings[0]"
          ref="booking"
          :rules="rules"
          label-position="top"
          @submit.prevent="onSubmit"
          class="am-confirm-booking-form"
      >
        <el-row class="am-confirm-booking-data" :gutter="24">

          <!-- Booking Data -->
          <el-col :sm="24">
            <div class="am-confirmation-booking-details">

              <!-- Employee -->
              <div>
                <p>{{ capitalizeFirstLetter($root.labels.employee) }}:</p>
                <p class="am-semi-strong">
                  <img
                      class="am-employee-photo"
                      :src="pictureLoad(provider, true)"
                      @error="imageLoadError(provider, true)"
                      alt="provider.firstName + ' ' + provider.lastName"
                  />
                  {{ provider.firstName + ' ' + provider.lastName }}
                </p>
              </div>

              <!-- Date -->
              <div>
                <p>{{ $root.labels.date_colon }}</p>
                <p class="am-semi-strong">
                  {{ getAppointmentDate() }}
                </p>
              </div>

              <!-- Time -->
              <div>
                <p>{{ $root.labels.time_colon }}</p>
                <p class="am-semi-strong">
                  {{ getAppointmentTime() }}
                </p>
              </div>

              <!-- Location -->
              <div>
                <p v-if="location !== null">{{ $root.labels.location_colon }}</p>
                <p class="am-semi-strong">{{ location ? location.name : '' }}</p>
              </div>

            </div>
          </el-col>

          <!-- Customer First Name -->
          <el-col :sm="columnsLg">
            <el-form-item :label="$root.labels.first_name_colon" prop="customer.firstName">
              <el-input
                  v-model="appointment.bookings[0].customer.firstName"
                  @keyup.native="validateFields"
                  @input="clearValidation()"
                  :disabled="!!appointment.bookings[0].customer.firstName && !!appointment.bookings[0].customer.id"
              >
              </el-input>
            </el-form-item>
          </el-col>

          <!-- Customer Last Name -->
          <el-col :sm="columnsLg">
            <el-form-item :label="$root.labels.last_name_colon" prop="customer.lastName">
              <el-input
                  v-model="appointment.bookings[0].customer.lastName"
                  @keyup.native="validateFields"
                  @input="clearValidation()"
                  :disabled="!!appointment.bookings[0].customer.lastName && !!appointment.bookings[0].customer.id"
              >
              </el-input>
            </el-form-item>
          </el-col>

          <!-- Customer Email -->
          <el-col :sm="columnsLg">
            <el-form-item :label="$root.labels.email_colon" prop="customer.email" :error="errors.email">
              <el-input
                  v-model="appointment.bookings[0].customer.email"
                  @keyup.native="validateFields"
                  @input="clearValidation()"
                  :disabled="!!appointment.bookings[0].customer.email && !!appointment.bookings[0].customer.id"
                  :placeholder="$root.labels.email_placeholder"
              >
              </el-input>
            </el-form-item>
          </el-col>

          <!-- User Phone -->
          <el-col :sm="columnsLg">
            <el-form-item :label="$root.labels.phone_colon" prop="customer.phone" :error="errors.phone">
              <phone-input
                  :savedPhone="appointment.bookings[0].customer.phone"
                  :disabled="!!appointment.bookings[0].customer.id"
                  v-on:phoneFormatted="phoneFormatted"
              >
              </phone-input>
            </el-form-item>
          </el-col>

          <!-- Custom Fields -->
          <div class="am-custom-fields">
            <el-row :gutter="24">
              <el-col
                  :sm="columnsLg"
                  v-for="customField in customFields"
                  :key="customField.id"
                  v-if="isCustomFieldVisible(customField)"
              >
                <el-form-item
                    :label="customField.type !== 'content' ? customField.label + ':' : ''"
                    :prop="customField.required === true && customField.type !== 'content' ? 'customFields.' + customField.id + '.value' : null"
                >

                  <!-- Text Field -->
                  <el-input
                      v-if="customField.type === 'text'"
                      placeholder=""
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      @input="clearValidation()"
                  >
                  </el-input>

                  <!-- Text Area -->
                  <el-input
                      v-else-if="customField.type === 'text-area'"
                      class="am-front-texarea"
                      placeholder=""
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      type="textarea"
                      :rows="3"
                      @input="clearValidation()"
                  >
                  </el-input>

                  <!-- Text Content -->
                  <div v-else-if="customField.type === 'content'" class="am-text-content">
                    <p><i class="el-icon-info"></i>{{ ' ' + customField.label }}</p>
                  </div>

                  <!-- Selectbox -->
                  <el-select
                      v-else-if="customField.type === 'select'"
                      placeholder=""
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      clearable
                      @change="clearValidation()"
                  >
                    <el-option
                        v-for="(option, index) in getCustomFieldOptions(customField.options)"
                        :key="index"
                        :value="option"
                        :label="option"
                    >
                    </el-option>
                  </el-select>

                  <!-- Checkbox -->
                  <el-checkbox-group
                      v-else-if="customField.type === 'checkbox'"
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      @change="clearValidation()"
                  >
                    <el-checkbox
                        v-for="(option, index) in getCustomFieldOptions(customField.options)"
                        :key="index"
                        :label="option"
                    >
                    </el-checkbox>
                  </el-checkbox-group>

                  <!-- Radio Buttons -->
                  <el-radio-group
                      v-else
                      v-model="appointment.bookings[0].customFields[customField.id].value"
                      @change="clearValidation()">
                    <el-radio
                        v-for="(option, index) in getCustomFieldOptions(customField.options)"
                        :key="index"
                        :label="option"
                        ref="customFieldsRadioButtons"
                    >
                    </el-radio>
                  </el-radio-group>

                </el-form-item>
              </el-col>

            </el-row>
          </div>

        </el-row>

        <!-- Payment Method & Stripe Card -->
        <el-row :gutter="24" class="am-confirm-booking-payment">

          <!-- Payment Method -->
          <el-col :sm="columnsLg">
            <transition name="fade">
              <el-form-item
                  :label="$root.labels.payment_method_colon"
                  v-if="getTotalPrice() > 0 && !this.$root.settings.payments.wc.enabled"
              >
                <el-select
                    v-model="appointment.payment.gateway"
                    placeholder=""
                    :disabled="paymentOptions.length === 1"
                    @change="clearValidation()"
                >
                  <el-option
                      v-for="item in paymentOptions"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
            </transition>
          </el-col>

          <!-- Stripe Card -->
          <el-col :sm="columnsLg">
            <transition name="fade">
              <el-form-item
                  :label="$root.labels.credit_or_debit_card_colon"
                  v-if="appointment.payment.gateway === 'stripe' && getTotalPrice() > 0"
                  :error="errors.stripe"
              >
                <card
                    class='am-stripe-card'
                    :stripe="getStripePublishableKey()"
                    :options="stripeOptions"
                    @change="setStripeOutcome($event)"
                >
                </card>
              </el-form-item>
            </transition>
          </el-col>

        </el-row>

        <!-- Appointment Data -->
        <el-row>
          <el-col :sm="24">

            <!-- Payment Data -->
            <div class="am-confirmation-booking-cost">

              <!-- Number Of Persons -->
              <el-row :gutter="24" v-if="service.maxCapacity > 1">
                <el-col :span="12">
                  <p>{{ $root.labels.number_of_persons_colon }}</p>
                </el-col>
                <el-col :span="12">
                  <p class="am-semi-strong am-align-right">
                    {{ appointment.bookings[0].persons }}
                  </p>
                </el-col>
              </el-row>

              <!-- Appointment Price -->
              <el-row :gutter="24" v-if="service.price">
                <el-col :span="8">
                  <p>{{ $root.labels.base_price_colon }}</p>
                </el-col>
                <el-col :span="16">
                  <p class="am-semi-strong am-align-right">
                    {{ getAppointmentPrice() }}
                  </p>
                </el-col>
              </el-row>

              <!-- Extras Price -->
              <el-row
                  class="am-confirmation-extras-cost" :gutter="24"
                  v-if="appointment.bookings[0].extras.length > 0 && getTotalPrice() > 0"
              >
                <!-- Extras Price -->
                <el-collapse accordion v-if="selectedExtras.length > 0">
                  <el-collapse-item name="1">
                    <template slot="title">
                      <div class="am-extras-title">{{ $root.labels.extras_costs_colon }}</div>
                      <div class="am-extras-total-cost am-semi-strong">{{ getFormattedPrice(getExtrasPrice()) }}</div>
                    </template>
                    <div v-for="extra in selectedExtras">
                      <div class="am-extras-details"> {{ getSelectedExtraDetails(extra) }}</div>
                      <div class="am-extras-cost">{{ getSelectedExtraPrice(extra) }}</div>
                    </div>
                  </el-collapse-item>
                </el-collapse>
                <div v-else>
                  <el-col :span="12">
                    <p>{{ $root.labels.extras_costs_colon }}</p>
                  </el-col>
                  <el-col :span="12">
                    <p class="am-semi-strong am-align-right">{{ getFormattedPrice(getExtrasPrice()) }}</p>
                  </el-col>
                </div>
              </el-row>

              <!-- Subtotal Price -->
              <el-row :gutter="24" v-if="appointment.bookings[0].extras.length > 0 && service.price">
                <el-col :span="8">
                  <p>{{ $root.labels.subtotal_colon }}</p>
                </el-col>
                <el-col :span="16">
                  <p class="am-semi-strong am-align-right">
                    {{ getFormattedPrice(getSubtotalPrice()) }}
                  </p>
                </el-col>
              </el-row>

              <!-- Discount Price -->
              <el-row :gutter="24" v-if="$root.settings.payments.coupons && service.price > 0">
                <el-col :span="8">
                  <p>{{ $root.labels.discount_amount_colon }}</p>
                </el-col>
                <el-col :span="16">
                  <p class="am-semi-strong am-align-right">
                    {{ getFormattedPrice(getDiscountPrice()) }}
                  </p>
                </el-col>
              </el-row>

              <!-- Coupon -->
              <el-row
                  :gutter="0" class="am-add-coupon am-flex-row-middle-align"
                  v-if="$root.settings.payments.coupons && service.price > 0"
              >
                <!-- Coupon Label -->
                <el-col :sm="10" :xs="10">
                  <img :src="$root.getUrl+'public/img/coupon.svg'" class="svg" alt="add-coupon">
                  <span>{{ $root.labels.add_coupon }}</span>
                </el-col>

                <!-- Coupon Input -->
                <el-col :sm="14" :xs="14">
                  <el-form
                      :model="appointment.bookings[0].customer"
                      ref="coupon"
                      :rules="rules"
                      label-position="top"
                      @submit.prevent="onSubmit"
                      status-icon
                  >
                    <el-form-item prop="couponCode" :error="errors.coupon">
                      <el-input
                          v-model="coupon.code"
                          @input="clearValidation()"
                          type="text"
                          size="small"
                          class="am-add-coupon-field"
                      >
                        <!-- Coupon Button -->
                        <el-button
                            slot="append"
                            size="mini"
                            icon="el-icon-check" @click="checkCoupon"
                            :disabled="coupon.code === ''"
                        >
                        </el-button>
                      </el-input>
                    </el-form-item>
                  </el-form>
                </el-col>
              </el-row>

              <!-- Total Price -->
              <el-row class="am-confirmation-total" :gutter="24" v-if="service.price > 0">
                <el-col :span="12">
                  <p>
                    {{ $root.labels.total_cost_colon }}
                  </p>
                </el-col>
                <el-col :span="12">
                  <p class="am-semi-strong am-align-right">
                    {{ getFormattedPrice(getTotalPrice()) }}
                  </p>
                </el-col>
              </el-row>

            </div>

          </el-col>

        </el-row>
      </el-form>

      <!-- Confirm Dialog Footer -->
      <div class="dialog-footer payment-dialog-footer"
           slot="footer">
        <div class="el-button el-button--default"
             @click="cancelBooking()"
             style="margin: 10px;">
          <span>{{ $root.labels.cancel }}</span>
        </div>

        <div class="paypal-button el-button el-button--primary"
             v-show="$root.settings.payments.payPal.enabled && appointment.payment.gateway === 'payPal' && getTotalPrice() > 0">
          <div id="am-paypal-button-container"></div>
          <span>{{ $root.labels.confirm }}</span>
        </div>

        <div class="el-button el-button--primary"
             v-show="showConfirmBookingButton"
             @click="confirmBooking()">
          <span>{{ $root.labels.confirm }}</span>
        </div>
      </div>

    </div>

    <!-- Spinner & Waiting For Payment -->
    <div class="am-booking-fetched" v-show="!fetched">
      <h4 v-if="appointment.payment.gateway === 'payPal'">{{ $root.labels.waiting_for_payment }}</h4>
      <h4 v-else>{{ $root.labels.please_wait }}</h4>
      <div class="am-svg-wrapper">
        <img class="svg am-spin" :src="$root.getUrl+'public/img/oval-spinner.svg'">
        <img class="svg am-hourglass" :src="$root.getUrl+'public/img/hourglass.svg'">
      </div>
    </div>

  </div>
</template>

<script>
  import moment from 'moment'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import PhoneInput from '../../parts/PhoneInput.vue'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import { Card, createToken } from 'vue-stripe-elements-plus'
  import helperMixin from '../../../js/backend/mixins/helperMixin'
  import customFieldMixin from '../../../js/common/mixins/customFieldMixin'

  export default {
    mixins: [imageMixin, dateMixin, priceMixin, helperMixin, customFieldMixin],

    props: {
      appointment: {
        default: () => {},
        type: Object
      },
      service: {
        default: () => {},
        type: Object
      },
      provider: {
        default: () => {},
        type: Object
      },
      location: {
        default: () => {},
        type: Object
      },
      dialogClass: {
        default: '',
        type: String
      },
      notificationsSettings: {
        default: () => {},
        type: Object
      },
      customFields: {
        default: () => []
      }
    },

    data () {
      let validateCoupon = (rule, bookings, callback) => {
        let field = document.getElementsByClassName('am-add-coupon-field')[0].getElementsByClassName('el-input__suffix')[0]

        if (this.coupon.code) {
          this.$http.get(`${this.$root.getAjaxUrl}/coupons/validate`, {
            params: {
              'code': this.coupon.code,
              'serviceId': this.appointment.serviceId
            }
          }).then(response => {
            this.coupon = response.data.data.coupon
            if (typeof field !== 'undefined') {
              field.style.visibility = 'visible'
            }
            callback()
          }).catch(e => {
            this.coupon.discount = 0
            this.coupon.deduction = 0

            if (e.response.data.data.couponUnknown === true) {
              callback(new Error(this.$root.labels.coupon_unknown))
            } else if (e.response.data.data.couponInvalid === true) {
              callback(new Error(this.$root.labels.coupon_invalid))
            } else {
              callback()
            }

            if (typeof field !== 'undefined') {
              field.style.visibility = 'hidden'
            }
          })
        } else {
          if (typeof field !== 'undefined') {
            field.style.visibility = 'hidden'
          }
          callback()
        }
      }

      let validatePhone = (rule, input, callback) => {
        if (input !== '' && !input.startsWith('+')) {
          callback(new Error(this.$root.labels.enter_valid_phone_warning))
        } else {
          callback()
        }
      }

      return {
        columnsLg: 12,
        coupon: {
          code: '',
          discount: 0,
          deduction: 0
        },
        clearValidate: true,
        errors: {
          email: '',
          coupon: '',
          stripe: ''
        },
        fetched: true,
        headerErrorMessage: '',
        headerErrorShow: false,
        payPalActions: null,
        rules: {
          'customer.firstName': [
            {required: true, message: this.$root.labels.enter_first_name_warning, trigger: 'submit'}
          ],
          'customer.lastName': [
            {required: true, message: this.$root.labels.enter_last_name_warning, trigger: 'submit'}
          ],
          'customer.email': [
            {required: true, message: this.$root.labels.enter_email_warning, trigger: 'submit'},
            {type: 'email', message: this.$root.labels.enter_valid_email_warning, trigger: 'submit'}
          ],
          'customer.phone': [
            {
              required: this.$root.settings.general.requiredPhoneNumberField && !this.appointment.bookings[0].customer.id,
              message: this.$root.labels.enter_phone_warning,
              trigger: 'submit'
            },
            {validator: validatePhone, trigger: 'submit'}
          ],
          couponCode: [
            {validator: validateCoupon, trigger: 'submit'}
          ]
        },
        stripeOptions: {
          style: {
            base: {
              iconColor: '#616e7c',
              color: '#354052',
              fontWeight: 400,
              fontFamily: 'Roboto, sans-serif',
              fontSize: '14px',
              '::placeholder': {
                color: '#c0c4cc'
              }
            },
            invalid: {
              iconColor: '#FF0040',
              color: '#FF0040'
            }
          }
        }
      }
    },

    created () {
      this.inlineSVG()
      window.addEventListener('resize', this.handleResize)
    },

    mounted () {
      this.inlineSVG()

      // Get Default Payment Option
      let paymentOption = this.paymentOptions.find(option => option.value === this.$root.settings.payments.defaultPaymentMethod)
      this.appointment.payment.gateway = paymentOption ? paymentOption.value : this.paymentOptions[0].value

      this.saveStats()
      this.addCustomFieldsValidationRules()
      if (this.$root.settings.payments.payPal.enabled) {
        this.payPalInit()
      }

      // Customization hook
      if ('beforeConfirmBookingLoaded' in window) {
        window.beforeConfirmBookingLoaded(this.appointment, this.service, this.provider, this.location)
      }
    },

    updated () {
      if (this.clearValidate === true) {
        this.clearValidation()
        this.clearValidate = false
      }
      this.handleResize()
    },

    methods: {
      saveStats: !AMELIA_LITE_VERSION ? function () {
        this.$http.post(`${this.$root.getAjaxUrl}/stats`, {
          'locationId': this.location !== null ? this.location.id : null,
          'providerId': this.provider.id,
          'serviceId': this.service.id
        })
      } : function () {},

      cancelBooking () {
        this.$emit('cancelBooking')
      },

      confirmBooking () {
        if (!this.fetched) {
          return
        }

        this.headerErrorShow = false
        this.errors.email = ''
        this.errors.coupon = ''
        this.clearValidation()
        // Validate Form
        this.$refs.booking.validate((valid) => {
          if (valid && this.errors.stripe === '' && this.errors.coupon === '') {
            // Customization hook
            if ('afterConfirmBooking' in window) {
              window.afterConfirmBooking(this.appointment, this.service, this.provider, this.location)
            }

            this.fetched = false
            this.inlineSVG()

            if (this.getTotalPrice() === 0) {
              this.appointment.payment.gateway = 'onSite'
            }

            switch (this.appointment.payment.gateway) {
              case 'stripe':
                createToken().then(data => {
                  this.appointment.payment.data = {
                    token: data.token.id
                  }
                  this.saveBooking()
                })
                break
              case 'onSite':
                this.saveBooking()
                break
              case 'wc':
                this.addToWooCommerceCart()
                break
            }
          } else {
            this.fetched = true
            return false
          }
        })
      },

      saveBooking () {
        this.$http.post(`${this.$root.getAjaxUrl}/bookings`, this.getBookingData()
        ).then(response => {
          if (response.data.data) {
            this.$emit('confirmedBooking', response.data.data)
          } else {
            this.fetched = true
          }
        }).catch(e => {
          this.handleSaveBookingErrors(e.response.data)
        })
      },

      addToWooCommerceCart: !AMELIA_LITE_VERSION ? function () {
        this.$http.post(`${this.$root.getAjaxUrl}/payment/wc`, this.getBookingData()
        ).then(response => {
          window.location = response.data.data.cartUrl
        }).catch(e => {
          this.handleSaveBookingErrors(e.response.data)
        })
      } : function () {},

      getAppointmentDate () {
        return this.getFrontedFormattedDate(
          moment(this.appointment.bookingStart, 'YYYY-MM-DD HH:mm:ss').format('YYYY-MM-DD')
        )
      },

      getAppointmentTime () {
        return this.getFrontedFormattedTime(this.appointment.bookingStartTime)
      },

      getAppointmentPrice () {
        let persons = this.appointment.bookings[0].persons
        let priceFormatted = this.getFormattedPrice(this.service.price)
        let totalPrice = this.appointment.bookings[0].persons * this.service.price
        let totalPriceFormatted = this.getFormattedPrice(totalPrice)

        return persons > 1 ? persons + ' ' + this.$root.labels.persons + ' x ' + priceFormatted + ' = ' + totalPriceFormatted : totalPriceFormatted
      },

      getExtrasPrice: !AMELIA_LITE_VERSION ? function () {
        let persons = this.appointment.bookings[0].persons
        let totalPrice = 0

        for (let i = 0; i < this.selectedExtras.length; i++) {
          totalPrice += this.selectedExtras[i].price * this.selectedExtras[i].quantity * persons
        }

        return totalPrice
      } : function () {
        return 0
      },

      getSubtotalPrice () {
        let appointmentPrice = this.appointment.bookings[0].persons * this.service.price

        return appointmentPrice + this.getExtrasPrice()
      },

      getDiscountPrice: !AMELIA_LITE_VERSION ? function () {
        return (this.getSubtotalPrice() / 100 * this.coupon.discount) + this.coupon.deduction
      } : function () {
        return 0
      },

      getTotalPrice () {
        let totalPrice = this.getSubtotalPrice() - this.getDiscountPrice()
        return totalPrice > 0 ? totalPrice : 0
      },

      getSelectedExtraDetails: !AMELIA_LITE_VERSION ? function (extra) {
        let persons = this.appointment.bookings[0].persons
        let result = ''

        if (persons > 1) {
          result += persons + ' ' + this.$root.labels.persons + ' x '
        }

        if (extra.quantity > 1) {
          result += extra.quantity + ' ' + ' x '
        }

        result += extra.name

        return result
      } : function () {},

      getSelectedExtraPrice: !AMELIA_LITE_VERSION ? function (extra) {
        let persons = this.appointment.bookings[0].persons
        let totalPrice = 0

        totalPrice += extra.price * persons * extra.quantity

        return this.getFormattedPrice(totalPrice)
      } : function () {},

      checkCoupon: !AMELIA_LITE_VERSION ? function () {
        this.$refs.coupon.clearValidate()
        this.$refs.coupon.validate((valid) => {
          let field = document.getElementsByClassName('am-add-coupon-field')[0].getElementsByClassName('el-input__suffix')[0]

          field.style.visibility = valid && typeof field !== 'undefined' ? 'visible' : 'hidden'
        })
      } : function () {},

      getBookingData () {
        this.appointment.payment.amount = this.getFormattedAmount()

        let bookings = JSON.parse(JSON.stringify(this.appointment.bookings))
        bookings[0].extras = JSON.parse(JSON.stringify(this.selectedExtras))
        bookings[0].extras.forEach(function (extItem) {
          extItem.extraId = extItem.id
          extItem.id = null
        })
        bookings[0].customFields = JSON.stringify(bookings[0].customFields)

        return {
          'bookings': bookings,
          'bookingStart': this.appointment.bookingStart,
          'notifyParticipants': this.appointment.notifyParticipants ? 1 : 0,
          'payment': this.appointment.payment,
          'providerId': this.appointment.providerId,
          'serviceId': this.appointment.serviceId,
          'couponCode': this.coupon.code
        }
      },

      getFormattedAmount () {
        return this.getTotalPrice().toFixed(2).toString()
      },

      handleSaveBookingErrors (response) {
        if ('data' in response) {
          if ('customerAlreadyBooked' in response.data && response.data.customerAlreadyBooked === true) {
            this.headerErrorShow = true
            this.headerErrorMessage = this.$root.labels.customer_already_booked
          }
          if ('timeSlotUnavailable' in response.data && response.data.timeSlotUnavailable === true) {
            this.headerErrorShow = true
            this.headerErrorMessage = this.$root.labels.time_slot_unavailable
          } else if ('emailError' in response.data && response.data.emailError === true) {
            this.errors.email = this.$root.labels.email_exist_error
          } else if ('couponUnknown' in response.data && response.data.couponUnknown === true) {
            this.errors.coupon = this.$root.labels.coupon_unknown
          } else if ('couponInvalid' in response.data && response.data.couponInvalid === true) {
            this.errors.coupon = this.$root.labels.coupon_invalid
          } else if ('couponMissing' in response.data && response.data.couponMissing === true) {
            this.errors.coupon = this.$root.labels.coupon_missing
          } else if ('paymentSuccessful' in response.data && response.data.paymentSuccessful === false) {
            this.headerErrorShow = true
            this.headerErrorMessage = this.$root.labels.payment_error
          } else if ('bookingAlreadyInWcCart' in response.data && response.data.bookingAlreadyInWcCart === true) {
            this.headerErrorShow = true
            this.headerErrorMessage = this.$root.labels.booking_already_in_wc_cart
          } else if ('wcError' in response.data && response.data.wcError === true) {
            this.headerErrorShow = true
            this.headerErrorMessage = this.$root.labels.wc_error
          }
        }

        this.fetched = true
      },

      validateFields () {
        if (this.payPalActions === null) {
          return
        }

        if (this.appointment.bookings[0].customer.lastName === '' ||
          this.appointment.bookings[0].customer.firstName === '' ||
          this.appointment.bookings[0].customer.email === '' ||
          !(/(.+)@(.+){2,}\.(.+){2,}/.test(this.appointment.bookings[0].customer.email))) {
          this.payPalActions.disable()
        } else {
          this.payPalActions.enable()
        }
      },

      payPalInit: !AMELIA_LITE_VERSION ? function () {
        let $this = this

        let transactionReference = ''

        window.paypal.Button.render({
          style: {
            size: 'responsive',
            color: 'gold',
            shape: 'rect',
            tagLine: false
          },

          env: $this.$root.settings.payments.payPal.sandboxMode ? 'sandbox' : 'production',

          client: {
            sandbox: $this.$root.settings.payments.payPal.testApiClientId,
            production: $this.$root.settings.payments.payPal.liveApiClientId
          },

          commit: true,

          onClick: function () {
            $this.confirmBooking()
          },

          validate: function (actions) {
            $this.payPalActions = actions
            $this.validateFields()
          },

          payment: function () {
            return window.paypal.request(
              {
                method: 'post',
                url: $this.$root.getAjaxUrl + '/payment/payPal',
                json: {
                  'amount': $this.getFormattedAmount(),
                  'serviceId': $this.appointment.serviceId,
                  'providerId': $this.appointment.providerId,
                  'couponCode': $this.coupon.code,
                  'bookings': $this.appointment.bookings
                }
              }
            ).then(
              function (response) {
                transactionReference = response.data.transactionReference
                return response.data.paymentID
              }
            ).catch(
              function (error) {
                $this.parseError(error)
              }
            )
          },

          onAuthorize: function (data, actions) {
            return actions.payment.get().then(function () {
              $this.appointment.payment.data = {
                'transactionReference': transactionReference,
                'PaymentId': data.paymentID,
                'PayerId': data.payerID
              }

              return window.paypal.request(
                {
                  method: 'post',
                  url: $this.$root.getAjaxUrl + '/bookings',
                  json: $this.getBookingData()
                }
              ).then(
                function (response) {
                  if (response.data) {
                    $this.$emit('confirmedBooking', response.data)
                  } else {
                    $this.fetched = true
                  }
                }
              ).catch(
                function (error) {
                  $this.parseError(error)
                }
              )
            })
          },

          onCancel: function () {
            $this.fetched = true
            $this.inlineSVG()
          },

          onError: function (error) {
            $this.parseError(error)
          }

        }, '#am-paypal-button-container')
      } : function () {},

      parseError: function (error) {
        let errorString = error.toString()
        let response = JSON.parse(JSON.stringify(JSON.parse(errorString.substring(errorString.indexOf('{'), errorString.lastIndexOf('}') + 1))))

        if (typeof response === 'object' && response.hasOwnProperty('data')) {
          this.handleSaveBookingErrors(response)
        } else {
          this.headerErrorShow = true
          this.headerErrorMessage = this.$root.labels.payment_error
        }

        this.fetched = true
        this.inlineSVG()
      },

      getStripePublishableKey: !AMELIA_LITE_VERSION ? function () {
        return this.$root.settings.payments.stripe.testMode === false
          ? this.$root.settings.payments.stripe.livePublishableKey : this.$root.settings.payments.stripe.testPublishableKey
      } : function () {},

      setStripeOutcome: !AMELIA_LITE_VERSION ? function (event) {
        if (typeof event.error === 'undefined') {
          this.errors.stripe = ''
        } else {
          this.errors.stripe = typeof this.$root.labels[event.error.code] === 'undefined'
            ? this.errors.stripe = event.error.message : this.$root.labels[event.error.code]
        }

        this.fetched = true
      } : function () {},

      clearValidation () {
        if (typeof this.$refs.booking !== 'undefined') {
          this.$refs.booking.clearValidate()
        }

        if (typeof this.$refs.coupon !== 'undefined') {
          this.$refs.coupon.clearValidate()
        }
      },

      phoneFormatted (phone) {
        this.appointment.bookings[0].customer.phone = phone
        this.clearValidation()
      },

      handleResize () {
        let amContainer = document.getElementById('amelia-app-booking' + this.$root.shortcodeData.counter)
        let amContainerWidth = amContainer.offsetWidth
        if (amContainerWidth < 670) {
          this.columnsLg = 24
        }
      },

      addCustomFieldsValidationRules: !AMELIA_LITE_VERSION ? function () {
        for (let i = 0; i < this.customFields.length; i++) {
          if (this.isCustomFieldVisible(this.customFields[i])) {
            this.$set(
              this.rules,
              'customFields.' + this.customFields[i].id + '.value',
              [{required: true, message: this.$root.labels.required_field, trigger: 'submit'}]
            )
          }
        }
      } : function () {}
    },

    computed: {
      selectedExtras: !AMELIA_LITE_VERSION ? function () {
        return this.appointment.bookings[0].extras.filter(extra => extra.selected === true)
      } : function () {
        return []
      },

      paymentOptions () {
        let paymentOptions = []

        if (this.$root.settings.payments.onSite === true) {
          paymentOptions.push({
            value: 'onSite',
            label: this.$root.labels.on_site
          })
        }

        if (this.$root.settings.payments.payPal.enabled) {
          paymentOptions.push({
            value: 'payPal',
            label: this.$root.labels.pay_pal
          })
        }

        if (this.$root.settings.payments.stripe.enabled) {
          paymentOptions.push({
            value: 'stripe',
            label: this.$root.labels.credit_card
          })
        }

        if (this.$root.settings.payments.wc.enabled) {
          paymentOptions.push({
            value: 'wc',
            label: this.$root.labels.wc
          })
        }

        return paymentOptions
      },

      showConfirmBookingButton () {
        return this.appointment.payment.gateway === 'onSite' ||
          this.appointment.payment.gateway === 'wc' ||
          this.appointment.payment.gateway === 'stripe' ||
          (this.appointment.payment.gateway === 'payPal' && this.getTotalPrice() === 0)
      }
    },

    components: {
      moment,
      PhoneInput,
      Card
    }
  }
</script>