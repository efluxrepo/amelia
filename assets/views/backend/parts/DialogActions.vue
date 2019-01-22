<template>
  <div>
    <div class="am-dialog-footer">
      <div class="am-dialog-footer-actions">

        <!-- Dialog Delete Confirmation -->
        <transition name="slide-vertical">
          <div class="am-dialog-confirmation" v-show="showDeleteConfirmation">
            <h3 v-show="allowedDelete">{{ message.confirm.remove }}</h3>
            <el-alert
                v-if="action.haveRemoveEffect && deleteEffectMessage"
                title=""
                type="warning"
                :description="deleteEffectMessage"
                show-icon
                :closable="false"
            >
            </el-alert>
            <div class="align-left">
              <el-button size="small" v-if="allowedDelete" @click="showDeleteConfirmation = !showDeleteConfirmation">
                {{ $root.labels.cancel }}
              </el-button>
              <el-button size="small" v-if="allowedDelete" @click="deleteEntity" type="primary">
                {{ $root.labels.delete }}
              </el-button>
              <el-button size="small" v-if="!allowedDelete" @click="showDeleteConfirmation = !showDeleteConfirmation">
                {{ $root.labels.close }}
              </el-button>
            </div>
          </div>
        </transition>

        <!-- Dialog Visibility Confirmation -->
        <transition name="slide-vertical">
          <div class="am-dialog-confirmation" v-show="showVisibilityConfirmation">
            <h3 v-if="entity.status === 'visible'">{{ message.confirm.hide }}</h3>
            <h3 v-else-if="entity.status === 'hidden'">{{ message.confirm.show }}</h3>
            <div class="align-left">
              <el-button size="small" @click="showVisibilityConfirmation = !showVisibilityConfirmation">
                {{ $root.labels.cancel }}
              </el-button>
              <el-button size="small" v-if="entity.status === 'visible'" @click="hideEntity" type="primary">
                {{ $root.labels.visibility_hide}}
              </el-button>
              <el-button size="small" v-else="entity.status === 'hidden'" @click="showEntity" type="primary">
                {{$root.labels.visibility_show }}
              </el-button>
            </div>
          </div>
        </transition>

        <!-- Dialog Duplicate Confirmation -->
        <transition name="slide-vertical">
          <div class="am-dialog-confirmation" v-show="showDuplicateConfirmation">
            <h3>{{ message.confirm.duplicate }}</h3>
            <div class="align-left">
              <el-button size="small" @click="showDuplicateConfirmation = !showDuplicateConfirmation">
                {{ $root.labels.cancel }}
              </el-button>
              <el-button size="small" @click="duplicateEntity" type="primary">
                {{ $root.labels.duplicate }}
              </el-button>
            </div>
          </div>
        </transition>

        <!-- Dialog Save Confirm -->
        <transition name="slide-vertical">
          <div class="am-dialog-confirmation" v-show="showSaveConfirmation">
            <h3>{{ message.confirm.save }}</h3>
            <div class="align-left">
              <el-button type="primary" size="small" @click="onSubmit(false)">
                {{ getButtonText().no }}
              </el-button>
              <el-button type="primary" size="small" @click="onSubmit(true)">
                {{ getButtonText().yes }}
              </el-button>
            </div>
          </div>
        </transition>

        <!-- Dialog Buttons -->
        <el-row v-if="!isNew">
          <el-col :sm="12" class="align-left">
            <el-button
                v-if="action.haveDuplicate"
                :disabled="isDisabledDuplicate"
                :class="{'am-button-icon': true, 'am-lite-disabled': isDisabledDuplicate}"
                @click="handleDuplicateConfirmation">
              <img class="svg" :alt="$root.labels.duplicate" :src="$root.getUrl+'public/img/copy.svg'"/>
            </el-button>
            <el-button
                v-if="action.haveStatus"
                class="am-button-icon"
                @click="handleVisibilityConfirmation">
              <img class="svg" :alt="$root.labels.hide"
                   :src="$root.getUrl+ (entity.status === 'hidden' ? 'public/img/unhide.svg' : 'public/img/hide.svg')"/>
            </el-button>
            <el-button
                v-if="action.haveRemove"
                class="am-button-icon"
                @click="handleDeleteConfirmation">
              <img class="svg" :alt="$root.labels.delete" :src="$root.getUrl+'public/img/delete.svg'"/>
            </el-button>
          </el-col>
          <el-col
              :sm="(action.haveRemove === false && action.haveStatus === false && action.haveDuplicate === false) ? 24 : 12"
              class="align-right">
            <el-button
                v-if="action.haveAdd && action.haveEdit"
                type="primary" @click="handleSaveClick" class="am-dialog-create">
              {{ $root.labels.save }}
            </el-button>
          </el-col>
        </el-row>
        <el-row v-else>
          <el-col :sm="24" class="align-right">
            <el-button type="" @click="closeDialog" class="">
              {{ $root.labels.cancel }}
            </el-button>
            <el-button
                v-if="action.haveAdd && action.haveEdit"
                type="primary" @click="handleSaveClick" class="am-dialog-create">
              {{ $root.labels.save }}
            </el-button>
          </el-col>
        </el-row>
      </div>
    </div>

    <!-- Dialog Loader -->
    <div class="am-dialog-loader" v-show="dialogLoading">
      <div class="am-dialog-loader-content">
        <img :src="$root.getUrl+'public/img/spinner.svg'" class="">
        <p>{{ $root.labels.loader_message }}</p>
      </div>
    </div>

  </div>
</template>

<script>
  import Form from 'form-object'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'

  export default {

    mixins: [notifyMixin, dateMixin],

    props: {
      isDisabledDuplicate: false,
      formName: null,
      urlName: null,
      isNew: null,
      entity: null,
      getParsedEntity: null,
      haveSaveConfirmation: null,

      action: {
        haveAdd: false,
        haveEdit: false,
        haveStatus: false,
        haveRemove: false,
        haveRemoveEffect: false,
        ignoreDeleteEffect: false,
        haveDuplicate: false
      },

      message: {
        success: {
          save: null,
          remove: null,
          show: null,
          hide: null
        },
        confirm: {
          remove: null,
          show: null,
          hide: null,
          duplicate: null,
          save: null
        }
      },

      buttonTextYes: null,
      buttonTextNo: null
    },

    data () {
      return {
        form: new Form(),

        allowedDelete: true,

        showDeleteConfirmation: false,
        showVisibilityConfirmation: false,
        showDuplicateConfirmation: false,
        showSaveConfirmation: false,

        deleteEffectMessage: null,

        dialogLoading: false
      }
    },

    methods: {
      closeDialog () {
        this.$parent.$emit('closeDialog')
      },

      onSubmit (applyGlobally) {
        let $this = this.$root

        this.$parent.$refs[this.formName].validate((valid, errors) => {
          if (valid) {
            this.dialogLoading = true

            if (this.isNew) {
              this.addEntity(applyGlobally)
            } else {
              this.editEntity(applyGlobally)
            }
          } else {
            // For Appointment dialog, change tab to "Custom Fields" if validation error only there
            if (this.formName === 'appointment') {
              this.handleAppointmentDialogTabChange(errors)
            }

            this.$emit('validationFailCallback')
            return false
          }

          this.$root = $this
        })
      },

      onSuccess (title, message, response) {
        this.$parent.$emit('saveCallback', response)
        setTimeout(() => {
          this.dialogLoading = false
          this.$parent.$emit('closeDialog')
        }, 300)
        this.notify(title, message, 'success')
      },

      onError (title, responseData) {
        this.dialogLoading = false
        this.$emit('errorCallback', responseData)
      },

      addEntity (applyGlobally) {
        this.form.post(this.$root.getAjaxUrl + '/' + this.urlName, this.getParsedEntity(applyGlobally))
          .then(response => {
            this.onSuccess(this.$root.labels.success, this.message.success.save, response.data)
          })
          .catch(error => {
            if (error.response) {
              this.onError('', error.response.data)
            }
          })
      },

      editEntity (applyGlobally) {
        this.form.post(this.$root.getAjaxUrl + '/' + this.urlName + '/' + this.entity.id, this.getParsedEntity(applyGlobally))
          .then(response => {
            this.onSuccess(this.$root.labels.success, this.message.success.save, response.data)
          })
          .catch(error => {
            if (error.response) {
              this.onError('', error.response.data)
            }
          })
      },

      deleteEntity () {
        this.dialogLoading = true
        this.form.delete(this.$root.getAjaxUrl + '/' + this.urlName + '/' + this.entity.id)
          .then(response => {
            this.onSuccess(this.$root.labels.success, this.message.success.remove, response.data)
          })
          .catch(error => {
            if (error.response) {
              this.onError('', error.response.data.message)
            }
          })
      },

      showEntity () {
        this.dialogLoading = true
        this.form.post(this.$root.getAjaxUrl + '/' + this.urlName + '/status/' + this.entity.id, {
          'status': 'visible'
        })
          .then(response => {
            this.onSuccess(this.$root.labels.success, this.message.success.show, null)
          })
          .catch(error => {
            if (error.response) {
              this.onError('', error.response.data.message)
            }
          })
      },

      hideEntity () {
        this.dialogLoading = true
        this.form.post(this.$root.getAjaxUrl + '/' + this.urlName + '/status/' + this.entity.id, {
          'status': 'hidden'
        }).then(() => {
          this.onSuccess(this.$root.labels.success, this.message.success.hide, null)
        }).catch(error => {
          if (error.response) {
            this.onError('', error.response.data.message)
          }
        })
      },

      duplicateEntity () {
        this.dialogLoading = true
        this.$parent.$emit('closeDialog', true)
        this.$parent.$emit('duplicateCallback', this.entity)
      },

      handleSaveConfirmation () {
        this.showSaveConfirmation = !this.showSaveConfirmation
        this.showVisibilityConfirmation = false
        this.showDeleteConfirmation = false
        this.showDuplicateConfirmation = false
      },

      handleDuplicateConfirmation () {
        this.showDuplicateConfirmation = !this.showDuplicateConfirmation
        this.showSaveConfirmation = false
        this.showDeleteConfirmation = false
        this.showVisibilityConfirmation = false
      },

      handleVisibilityConfirmation () {
        this.showVisibilityConfirmation = !this.showVisibilityConfirmation
        this.showSaveConfirmation = false
        this.showDeleteConfirmation = false
        this.showDuplicateConfirmation = false
      },

      handleDeleteConfirmation () {
        this.showVisibilityConfirmation = false
        this.showDuplicateConfirmation = false
        this.showSaveConfirmation = false

        if (this.action.haveRemoveEffect) {
          if (this.showDeleteConfirmation === false) {
            this.$http.get(this.$root.getAjaxUrl + '/' + this.urlName + '/effect/' + this.entity.id)
              .then(response => {
                this.allowedDelete = response.data.data.valid || this.ignoreDeleteEffect
                this.deleteEffectMessage = response.data.data.message
                this.showDeleteConfirmation = !this.showDeleteConfirmation
              })
              .catch(e => {
                this.showDeleteConfirmation = !this.showDeleteConfirmation
                this.deleteEffectMessage = ''
              })
          }
        } else {
          this.showDeleteConfirmation = !this.showDeleteConfirmation
        }
      },

      handleSaveClick () {
        if (this.haveSaveConfirmation && this.haveSaveConfirmation()) {
          this.handleSaveConfirmation()
        } else {
          this.onSubmit(false)
        }
      },

      getButtonText () {
        return {
          yes: this.buttonTextYes !== null ? this.buttonTextYes : this.$root.labels.yes,
          no: this.buttonTextNo !== null ? this.buttonTextNo : this.$root.labels.no
        }
      },

      handleAppointmentDialogTabChange (errors) {
        let errorKeys = Object.keys(errors)
        let numberOfBookingErrors = 0
        for (let i = 0; i < errorKeys.length; i++) {
          numberOfBookingErrors = errorKeys[i].startsWith('bookings.') ? numberOfBookingErrors + 1 : numberOfBookingErrors
        }

        if (numberOfBookingErrors === errorKeys.length) {
          this.$emit('validationBookingsFailCallback')
        }
      }
    },

    components: {}

  }
</script>