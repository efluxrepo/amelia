<template>
  <div class="am-wrap">
    <div id="am-email-notifications" class="am-body">

      <!-- Page Header -->
      <page-header></page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched">
        <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
      </div>

      <!-- Notification -->
      <div v-show="fetched">
        <el-row class="am-flexed">

          <!-- Tabs -->
          <el-col :md="8" class="">
            <div class="am-section am-gray-section">
              <el-tabs v-model="sendToTab" @tab-click="inlineSVG()">

                <!-- To Customer -->
                <el-tab-pane :label="$root.labels.to_customer" name="customer">
                  <div class="am-email-notification-buttons">

                    <!-- Customer's Notification Buttons -->
                    <div v-for="(item, index) in customerNotifications" class="am-button-checkbox">
                      <el-button
                          size="large"
                          :key="index"
                          @click="getNotification(item.id)"
                          :class="{ 'am-active': item.id === notification.id, 'am-lite-disabled': isDisabled('customer', item) }"
                          :disabled="isDisabled('customer', item)"
                      >
                        {{ item.niceName }}
                      </el-button>
                      <el-checkbox @change="changeNotificationStatus(item)" v-model="item.status" true-label="enabled"
                                   :disabled="isDisabled('customer', item)"
                                   false-label="disabled"></el-checkbox>
                      <el-tooltip v-if="item.time || item.timeBefore || item.timeAfter" class="item" effect="dark"
                                  :content="$root.labels.requires_scheduling_setup" placement="top">
                        <span class="am-cron-icon" :class="{ 'active': item.id === notification.id }">
                          <img class="svg" :src="$root.getUrl+'public/img/cron-job.svg'"/>
                        </span>
                      </el-tooltip>
                    </div>

                  </div>
                </el-tab-pane>

                <!-- To Employee -->
                <el-tab-pane :label="$root.labels.to_employee" name="employee">
                  <div class="am-email-notification-buttons">

                    <!-- Employees's Notification Buttons -->
                    <div v-for="(item, index) in employeeNotifications" class="am-button-checkbox">
                      <el-button
                          size="large"
                          :key="index"
                          @click="getNotification(item.id)"
                          :class="{ 'am-active': item.id === notification.id, 'am-lite-disabled': isDisabled('provider', item) }"
                          :disabled="isDisabled('provider', item)"
                      >
                        {{ item.niceName }}
                      </el-button>
                      <el-checkbox @change="changeNotificationStatus(item)" v-model="item.status" true-label="enabled"
                                   :disabled="isDisabled('provider', item)"
                                   false-label="disabled"></el-checkbox>
                      <el-tooltip v-if="item.time || item.timeBefore || item.timeAfter" class="item" effect="dark"
                                  :content="$root.labels.requires_scheduling_setup"
                                  placement="top">
                        <span class="am-cron-icon" :class="{ 'active': item.id === notification.id }">
                          <img class="svg" :src="$root.getUrl+'public/img/cron-job.svg'"/>
                        </span>
                      </el-tooltip>
                    </div>

                  </div>
                </el-tab-pane>

              </el-tabs>
            </div>
          </el-col>

          <el-col :md="16">

            <!-- Content Spinner -->
            <div class="am-spinner am-section" v-show="fetched && !fetchedUpdate">
              <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
            </div>

            <div class="am-section am-email-form-settings" v-show="fetchedUpdate">
              <transition name="fadeIn">
                <el-form :model="notification" ref="notification">

                  <!-- Name & Show Email Codes -->
                  <el-row :gutter="16">
                    <el-col :span="16">
                      <div>
                        <h2>{{ notification.niceName }}</h2>
                      </div>
                    </el-col>
                    <el-col :span="8">
                      <div class="align-right">
                        <p class="am-blue-link" @click="showDialogEmailCodes">{{ $root.labels.show_email_codes }}</p>
                      </div>
                    </el-col>
                  </el-row>

                  <el-row :gutter="16">

                    <!-- Subject -->
                    <el-col :span="notificationTimeBased ? 18 : 24">
                      <el-form-item :label="$root.labels.subject + ':'">
                        <el-input type="text" v-model="notification.subject"></el-input>
                      </el-form-item>
                    </el-col>

                    <!-- Time -->
                    <el-col v-if="notificationTime" :span="6">
                      <el-form-item :label="$root.labels.scheduled_for + ':'">
                        <el-time-select
                            v-model="notificationTime"
                            :picker-options="timeSelectOptions"
                            :clearable="false"
                        >
                        </el-time-select>
                      </el-form-item>
                    </el-col>

                    <!-- Time Before -->
                    <el-col v-if="notification.timeBefore" :span="6">
                      <el-form-item :label="$root.labels.scheduled_before + ':'">
                        <el-select v-model="notification.timeBefore">
                          <el-option
                              v-for="item in getPossibleDurationsInSeconds(notification.timeBefore, 86400)"
                              :key="item"
                              :label="secondsToNiceDuration(item)"
                              :value="item"
                          >
                          </el-option>
                        </el-select>
                      </el-form-item>
                    </el-col>

                    <!-- Time After -->
                    <el-col v-if="notification.timeAfter" :span="6">
                      <el-form-item :label="$root.labels.scheduled_after + ':'">
                        <el-select v-model="notification.timeAfter">
                          <el-option
                              v-for="item in getPossibleDurationsInSeconds(notification.timeAfter, 86400)"
                              :key="item"
                              :label="secondsToNiceDuration(item)"
                              :value="item"
                          >
                          </el-option>
                        </el-select>
                      </el-form-item>
                    </el-col>

                  </el-row>

                  <!-- Content -->
                  <el-form-item :label="$root.labels.message + ':'">
                    <quill-editor v-model="notification.content" :options="editorOption"></quill-editor>
                  </el-form-item>

                  <!-- Cron Message -->
                  <el-alert
                      v-if="notificationTimeBased === true"
                      class="am-alert"
                      :title="$root.labels.cron_instruction + ':'"
                      type="info"
                      :description="'*/15 * * * * ' + $root.getAjaxUrl + '/notifications/scheduled/send'"
                      show-icon
                      :closable="false">
                  </el-alert>

                  <!-- Cancel & Save -->
                  <el-row :gutter="16">
                    <el-col :span="12">
                      <div>
                        <el-button size="small" @click="testEmailModal = true">
                          {{ $root.labels.send_test_email }}
                        </el-button>
                      </div>
                    </el-col>
                    <el-col :span="12">
                      <div class="align-right">
                        <el-button @click="updateNotification()" size="small" type="primary">
                          {{ $root.labels.save }}
                        </el-button>
                      </div>
                    </el-col>
                  </el-row>

                </el-form>
              </transition>
            </div>
          </el-col>

        </el-row>
      </div>

      <!-- Dialog Email Codes -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-email-codes"
            :visible.sync="dialogEmailCodes"
            :show-close="false"
            v-if="dialogEmailCodes"
        >
          <dialog-email-codes
              :customFields="options.entities.customFields"
              @closeDialogEmailCodes="dialogEmailCodes=false"
          >
          </dialog-email-codes>
        </el-dialog>
      </transition>

      <!-- Test Email Modal -->
      <el-dialog :title="$root.labels.send_test_email" class="am-pop-modal" :visible.sync="testEmailModal">

        <!-- Configure Sender Email Warning -->
        <el-alert
            v-if="$root.settings.notifications.senderEmail === ''"
            type="warning"
            show-icon
            title=""
            :description="$root.labels.test_email_warning"
            :closable="false"
        >
        </el-alert>

        <el-form :model="testEmail" ref="testEmail" :rules="rules" label-position="top" @submit.prevent="sendTestEmail"
                 v-loading="testEmailLoading">

          <!-- Recipient Email -->
          <el-form-item :label="$root.labels.recipient_email" prop="recipientEmail">
            <el-input
                v-model="testEmail.recipientEmail"
                :placeholder="$root.labels.email_placeholder"
                auto-complete="off"
            >
            </el-input>
          </el-form-item>

          <!-- Email Template -->
          <el-form-item :label="$root.labels.email_template" prop="emailTemplate">
            <el-select v-model="testEmail.emailTemplate">
              <el-option
                  v-for="notification in notifications"
                  :key="notification.id"
                  :label="notification.sendTo === 'provider' ? $root.labels.employee + ' ' + notification.niceName : $root.labels.customer + ' ' + notification.niceName"
                  :value="notification.name"
              >
              </el-option>
            </el-select>
          </el-form-item>

        </el-form>

        <span slot="footer" class="dialog-footer">
          <el-button size="small" @click="testEmailModal = false">{{ $root.labels.cancel }}</el-button>
          <el-button size="small" type="primary" @click="sendTestEmail" :loading="testEmailLoading"
                     :disabled="!$root.settings.notifications.senderEmail">{{ $root.labels.send }}</el-button>
        </span>

      </el-dialog>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/notifications/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

    </div>
  </div>
</template>

<script>
  import PageHeader from '../parts/PageHeader.vue'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import DialogEmailCodes from './DialogEmailCodes.vue'
  import Form from 'form-object'
  import { quillEditor } from 'vue-quill-editor'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'

  export default {

    mixins: [imageMixin, notifyMixin, durationMixin],

    data () {
      return {
        dialogEmailCodes: false,
        editorOption: {
          modules: {
            toolbar: [
              ['bold', 'italic', 'underline', 'strike'],
              ['blockquote'],
              [{'list': 'ordered'}, {'list': 'bullet'}],
              [{'script': 'sub'}, {'script': 'super'}],
              [{'indent': '-1'}, {'indent': '+1'}],
              [{'direction': 'rtl'}],
              [{'size': ['small', false, 'large', 'huge']}],
              [{'header': [1, 2, 3, 4, 5, 6, false]}],
              [{'font': []}],
              [{'color': []}, {'background': []}],
              [{'align': []}],
              ['clean'],
              ['link']
            ]
          }
        },
        fetched: false,
        fetchedUpdate: true,
        form: new Form(),
        notification: {},
        notifications: [],
        options: {
          entities: {
            customFields: []
          },
          fetched: false
        },
        rules: {
          recipientEmail: [
            {required: true, message: this.$root.labels.enter_recipient_email_warning, trigger: 'submit'},
            {type: 'email', message: this.$root.labels.enter_valid_email_warning, trigger: 'submit'}
          ],
          emailTemplate: [
            {required: true, message: this.$root.labels.select_email_template_warning, trigger: 'submit'}
          ]
        },
        sendToTab: 'customer',
        testEmail: {
          recipientEmail: '',
          emailTemplate: 'customer_appointment_approved'
        },
        testEmailModal: false,
        testEmailLoading: false
      }
    },

    created: !AMELIA_LITE_VERSION ? function () {
      this.getEntities()
      this.inlineSVG()
    } : function () {
      this.options.fetched = true
      this.getNotifications()
    },

    mounted () {
      this.inlineSVG()
    },

    methods: {
      getNotifications () {
        this.fetched = false

        this.$http.get(`${this.$root.getAjaxUrl}/notifications`)
          .then(response => {
            this.notifications = response.data.data.notifications
            this.getNotification(null)

            this.fetched = true
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
          })
      },

      getEntities () {
        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: ['custom_fields']
          }
        }).then(response => {
          this.options.entities = response.data.data
          this.options.fetched = true
          this.getNotifications()
        }).catch(e => {
          console.log(e.message)
          this.fetched = true
          this.options.fetched = true
        })
      },

      getNotification (id) {
        if (id === null) {
          this.notification = this.notifications[0]
        } else {
          this.notification = this.notifications.find(notification => notification.id === id)
        }
      },

      updateNotification () {
        this.fetchedUpdate = false

        this.form.post(`${this.$root.getAjaxUrl}/notifications/${this.notification.id}`, this.notification)
          .then(() => {
            this.fetchedUpdate = true
            this.notify(this.$root.labels.success, this.$root.labels.notification_saved, 'success')
          })
          .catch(e => {
            this.fetchedUpdate = true
            this.notify(this.$root.labels.error, this.$root.labels.notification_not_saved, 'error')
          })
      },

      changeNotificationStatus (notification) {
        this.fetchedUpdate = false
        this.form.post(`${this.$root.getAjaxUrl}/notifications/status/${notification.id}`, notification)
          .then(() => {
            this.fetchedUpdate = true
          })
          .catch(e => {
            this.fetchedUpdate = true
          })
      },

      showDialogEmailCodes () {
        this.dialogEmailCodes = true
      },

      sendTestEmail () {
        this.$refs.testEmail.validate((valid) => {
          if (valid) {
            this.testEmailLoading = true
            this.form.post(`${this.$root.getAjaxUrl}/notifications/email/test`, this.testEmail)
              .then(() => {
                // Clear validation on inputs
                this.$refs.testEmail.clearValidate()
                // Close modal
                this.testEmailModal = false
                // Modal loader
                this.testEmailLoading = false
                // Reset testEmail object on default state
                this.testEmail = {
                  recipientEmail: '',
                  emailTemplate: 'customer_appointment_approved'
                }
                this.notify(this.$root.labels.success, this.$root.labels.test_email_success, 'success')
              })
              .catch(() => {
                this.testEmailLoading = false
                this.notify(this.$root.labels.error, this.$root.labels.test_email_error, 'error')
              })
          } else {
            return false
          }
        })
      },

      isDisabled: !AMELIA_LITE_VERSION ? function (type, item) {
        return false
      } : function (type, item) {
        item.status = (item.name !== (type + '_appointment_approved') && item.name !== (type + '_appointment_pending')) ? 'disabled' : item.status

        return this.$root.isLite ? (item.name !== (type + '_appointment_approved') && item.name !== (type + '_appointment_pending')) : false
      }
    },

    computed: {
      customerNotifications () {
        return this.notifications.filter(notification => notification.sendTo === 'customer')
      },

      employeeNotifications () {
        return this.notifications.filter(notification => notification.sendTo === 'provider')
      },

      notificationTime: {
        get () {
          if (this.notification.time !== null) {
            return this.$moment(this.notification.time, 'HH:mm:ss').format('HH:mm')
          }

          return null
        },
        set (selected) {
          this.notification.time = this.$moment(selected, 'HH:mm').format('HH:mm:ss')
        }
      },

      notificationTimeBased () {
        return this.notification.time !== null || this.notification.timeBefore !== null || this.notification.timeAfter !== null
      }
    },

    components: {
      PageHeader,
      DialogEmailCodes,
      quillEditor
    }
  }
</script>
