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
            <h2>{{ $root.labels.payment_details }}</h2>
          </el-col>
          <el-col :span="6" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <div class="am-payment-details">
        <el-row class="am-payment-details-row">
          <h4>{{ $root.labels.customer }}</h4>
          <el-col :span="24">
            <h3>{{ modalData.customer ? modalData.customer.firstName + ' ' + modalData.customer.lastName : '' }}</h3>
            <p>{{ modalData.customer ? modalData.customer.email : '' }}</p>
          </el-col>
        </el-row>

        <el-row class="am-payment-details-row">
          <h4>{{ $root.labels.payment }}</h4>
          <el-col :span="12">
            <p>{{ $root.labels.date }}</p>
            <p>{{ $root.labels.payment_method }}</p>
            <p>{{ $root.labels.status }}</p>
          </el-col>
          <el-col :span="12">
            <p class="am-semi-strong">{{ getFrontedFormattedDate(payment.dateTime) }}</p>
            <p class="am-semi-strong">
              <img class="svg" width="14px" :src="$root.getUrl + 'public/img/payments/' + payment.gateway + '.svg'">
              {{ getPaymentGatewayNiceName() }}
            </p>
            <p class="am-semi-strong">
              <i :class="{'el-icon-circle-check':payment.status === 'paid','el-icon-refresh':payment.status !== 'paid'}"></i>
              <span>{{ getPaymentStatus(payment.status) }}</span>
            </p>
          </el-col>
        </el-row>

        <el-row class="am-payment-details-row">
          <h4>{{ $root.labels.appointment_info }}</h4>
          <el-col :span="12">
            <p>{{ $root.labels.service }}</p>
            <p>{{ $root.labels.date }}</p>
            <p>{{ $root.labels.employee }}</p>
          </el-col>
          <el-col :span="12">
            <p class="am-semi-strong">{{ modalData.service ? modalData.service.name : '' }}</p>
            <p class="am-semi-strong">{{ getFrontedFormattedDateTime(modalData.appointment.bookingStart) }}</p>
            <p class="am-semi-strong">
              <img class="am-employee-photo" :src="pictureLoad(modalData.provider, true)"
                   @error="imageLoadError(modalData.provider, true)"/>
              {{ modalData.provider ? modalData.provider.firstName + ' ' + modalData.provider.lastName : '' }}
            </p>
          </el-col>
        </el-row>

        <el-row class="am-payment-details-row am-payment-summary">
          <el-col :span="12">
            <p>{{ $root.labels.service_price }}</p>
            <p>{{ $root.labels.extras }}</p>
            <p>{{ $root.labels.subtotal }}</p>
            <p>{{ $root.labels.discount_amount }}</p>
            <p>{{ $root.labels.paid }}</p>
            <p>{{ $root.labels.due }}</p>
            <p class="am-payment-total">{{ $root.labels.total }}</p>

          </el-col>
          <el-col :span="12">
            <p class="am-semi-strong">{{ getFormattedPrice(finance.servicePriceTotal) }}</p>
            <p class="am-semi-strong">{{ getFormattedPrice(finance.extrasPriceTotal) }}</p>
            <p class="am-semi-strong">{{ getFormattedPrice(finance.subTotal) }}</p>
            <p class="am-semi-strong">{{ getFormattedPrice(finance.discountTotal) }}</p>
            <p class="am-semi-strong">{{ getFormattedPrice(payment.amount) }}</p>
            <p class="am-semi-strong">{{ getFormattedPrice(finance.due) }}</p>
            <p class="am-semi-strong am-payment-total">{{ getFormattedPrice(finance.total) }}</p>
          </el-col>
        </el-row>

      </div>

    </div>

    <!-- Dialog Footer -->
    <div class="am-dialog-footer" v-if="$root.settings.capabilities.canWriteOthers === true && !dialogLoading">
      <div class="am-dialog-footer-actions">

        <!-- Dialog Delete Confirmation -->
        <transition name="slide-vertical">
          <div class="am-dialog-confirmation" v-show="showDeleteConfirmation">
            <h3>{{ $root.labels.confirm_delete_payment }}</h3>
            <div class="align-left">
              <el-button
                  size="small"
                  @click="showDeleteConfirmation = !showDeleteConfirmation; showDeleteConfirmation = false">
                {{ $root.labels.cancel }}
              </el-button>
              <el-button size="small" @click="deletePayment()" type="primary">
                {{ $root.labels.delete }}
              </el-button>
            </div>
          </div>
        </transition>

        <!-- Dialog Update Confirmation -->
        <transition name="slide-vertical">
          <div class="am-dialog-confirmation" v-show="showUpdatePaymentAmount">
            <el-form label-position="top">

              <h3>{{ $root.labels.enter_new_payment_amount }}</h3>
              <el-row class="am-no-padding" :gutter="24">
                <el-col :span="12">
                  <el-form-item :label="$root.labels.payment + ':'">
                    <money v-model="payment.amount" v-bind="moneyComponentData" class="el-input__inner"></money>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$root.labels.status + ':'">
                    <el-select v-model="payment.status">
                      <el-option
                          v-for="item in paymentStatuses"
                          :key="item.value"
                          :label="item.label"
                          :value="item.value"
                          class="am-appointment-status-option"
                      >
                  <span class="am-appointment-status-symbol"
                        :class="item.value">
                          </span>
                        <span>{{ item.label }}</span>
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
              </el-row>


              <div class="align-left">
                <el-button size="small"
                           @click="showUpdatePaymentAmount = !showUpdatePaymentAmount"
                >
                  {{ $root.labels.close }}
                </el-button>
              </div>
            </el-form>
          </div>
        </transition>

        <el-row>

          <!-- Delete & Edit -->
          <el-col :sm="6" class="align-left">

            <!-- Delete -->
            <el-button
                v-if="$root.settings.capabilities.canDelete === true"
                class="am-button-icon"
                @click="showDeleteConfirmation = !showDeleteConfirmation; showUpdatePaymentAmount = false">
              <img class="svg" :alt="$root.labels.delete" :src="$root.getUrl + 'public/img/delete.svg'"/>
            </el-button>

            <!-- Edit -->
            <el-button
                class="am-button-icon"
                @click="showUpdatePaymentAmount = !showUpdatePaymentAmount; showDeleteConfirmation = false">
              <img class="svg" :alt="$root.labels.edit" :src="$root.getUrl + 'public/img/edit.svg'"/>
            </el-button>

          </el-col>

          <!-- Cancel & Save -->
          <el-col :sm="18" class="align-right">

            <!-- Cancel -->
            <el-button type="" @click="closeDialog" class="">
              {{ $root.labels.cancel }}
            </el-button>

            <!-- Save -->
            <el-button
                type="primary"
                @click="updatePayment()"
                class="am-dialog-create"
            >
              {{ $root.labels.save }}
            </el-button>

          </el-col>
        </el-row>

      </div>
    </div>

  </div>
</template>

<script>
  import Form from 'form-object'
  import { Money } from 'v-money'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'

  export default {

    mixins: [imageMixin, dateMixin, notifyMixin, priceMixin],

    props: {
      modalData: null,
      appointmentFetched: false
    },

    data () {
      return {
        booking: {},
        dialogLoading: true,
        finance: {
          servicePriceTotal: 0,
          extrasPriceTotal: 0,
          discountTotal: 0,
          subTotal: 0,
          due: 0
        },
        form: new Form(),
        payment: {},
        paymentStatuses: [
          {
            value: 'pending',
            label: this.$root.labels.pending
          },
          {
            value: 'paid',
            label: this.$root.labels.paid
          }
        ],
        showDeleteConfirmation: false,
        showUpdatePaymentAmount: false
      }
    },

    created () {
      if (this.appointmentFetched) {
        this.setFinance()
        this.dialogLoading = false
      }
    },

    updated () {
      this.$nextTick(function () {
        let $this = this
        setTimeout(function () {
          $this.inlineSVG()
        }, 5)
      })
    },

    methods: {
      instantiateDialog () {
        if (this.modalData.appointment !== null) {
          this.setFinance()
          this.dialogLoading = false
        }
      },

      setFinance () {
        let $this = this

        $this.modalData.appointment.bookings.forEach(function (bookItem) {
          bookItem.payments.forEach(function (payItem) {
            if (payItem.id === $this.modalData.paymentId) {
              $this.payment = payItem
              $this.booking = bookItem

              $this.finance.extrasPriceTotal = 0

              bookItem.extras.forEach(function (extItem) {
                $this.finance.extrasPriceTotal += extItem.price * extItem.quantity * bookItem.persons
              })

              $this.finance.servicePriceTotal = bookItem.price * bookItem.persons
              $this.finance.subTotal = $this.finance.servicePriceTotal + $this.finance.extrasPriceTotal
              $this.finance.discountTotal = ($this.finance.subTotal / 100 * (bookItem.coupon ? bookItem.coupon.discount : 0)) + (bookItem.coupon ? bookItem.coupon.deduction : 0)
              $this.finance.total = $this.finance.subTotal - $this.finance.discountTotal
              $this.finance.total = $this.finance.total >= 0 ? $this.finance.total : 0
              $this.finance.due = ($this.finance.total - $this.payment.amount) >= 0 ? $this.finance.total - $this.payment.amount : 0
            }
          })
        })
      },

      closeDialog () {
        this.$emit('closeDialogPayment')
      },

      getPaymentStatus (status) {
        let statusLabel = ''

        this.paymentStatuses.forEach(function (statItem) {
          if (statItem.value === status) {
            statusLabel = statItem.label
          }
        })

        return statusLabel
      },

      deletePayment () {
        this.dialogLoading = true

        this.$http.delete(`${this.$root.getAjaxUrl}/payments/` + this.payment.id)
          .then(response => {
            this.dialogLoading = false
            if (!response.data) {
              return
            }

            this.$emit('updatePaymentCallback', this.payment.id)
            this.showDeleteConfirmation = !this.showDeleteConfirmation
            this.notify(this.$root.labels.success, this.$root.labels.payment_deleted, 'success')
          })
          .catch(e => {
            this.dialogLoading = false
            this.errorMessage = e.message
          })
      },

      updatePayment () {
        this.dialogLoading = true

        this.form.post(`${this.$root.getAjaxUrl}/payments/${this.payment.id}`, this.payment)
          .then(() => {
            this.showUpdatePaymentAmount = !this.showUpdatePaymentAmount

            this.setFinance()
            this.notify(this.$root.labels.success, this.$root.labels.payment_saved, 'success')
            this.$emit('updatePaymentCallback')
            this.dialogLoading = false
          })
          .catch(e => {
            this.dialogLoading = false
            this.errorMessage = e.message
          })
      },

      getPaymentGatewayNiceName () {
        if (this.payment.gateway === 'onSite') {
          return this.$root.labels.on_site
        }

        if (this.payment.gateway === 'wc') {
          return this.payment.gatewayTitle
        }

        if (this.payment.gateway) {
          return this.payment.gateway.charAt(0).toUpperCase() + this.payment.gateway.slice(1)
        }
      }
    },

    watch: {
      'appointmentFetched' () {
        if (this.appointmentFetched === true) {
          this.instantiateDialog()
        }
      }
    },

    components: {
      Money
    }
  }
</script>