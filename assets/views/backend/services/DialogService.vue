<template>
  <div>
    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="18">
            <h2 v-if="service.id !== 0">{{ $root.labels.edit_service }}</h2>
            <h2 v-else>{{ $root.labels.new_service }}</h2>
          </el-col>
          <el-col :span="6" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form :model="service" ref="service" :rules="rulesService" label-position="top">
        <el-tabs v-model="serviceTabs">

          <!-- Service Details -->
          <el-tab-pane :label="$root.labels.service_details" name="service_details">

            <!-- Profile Photo -->
            <div class="am-service-profile">

              <picture-upload
                  :edited-entity="this.service"
                  :entity-name="'service'"
                  @pictureSelected="servicePictureSelected"
              >
              </picture-upload>
              <el-popover
                  ref="color-popover"
                  v-model="colorPopover"
                  placement="bottom"
                  width="160"
                  trigger="click"
                  popper-class="am-color-popover"
              >
                <span
                    v-for="color in serviceColors" :key="color"
                    class="am-service-color"
                    :class="{ 'color-active' : '#' + color === service.color}"
                    @click="changeServiceColor"
                    :data-color="'#'+color"
                    :style="'background-color:#'+color"
                >
                </span>

                <el-form-item :label="$root.labels.hex + ':'">
                  <el-input v-model="service.color" auto-complete="off"></el-input>
                </el-form-item>
                <div class="align-right">
                  <el-button type="primary" size="mini" @click="colorPopover = false">{{ $root.labels.ok }}</el-button>
                </div>

              </el-popover>
              <span class="am-service-color" :style="bgColor(service.color)" v-popover:color-popover></span>
              <h2>{{ service.name }}</h2>
            </div>

            <!-- Name -->
            <el-form-item :label="$root.labels.name + ':'" prop="name">
              <el-input v-model="service.name" auto-complete="off" @input="clearValidation()"></el-input>
            </el-form-item>

            <!-- Category -->
            <el-form-item :label="$root.labels.category + ':'" prop="categoryId">
              <el-select
                  v-model="service.categoryId"
                  placeholder=""
                  @change="clearValidation()"
              >
                <el-option
                    v-for="item in visibleCategories"
                    :disabled="item.disabled"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
                </el-option>
              </el-select>
              <img :src="$root.getUrl+'public/img/oval-spinner.svg'" class="svg is-spinner">
            </el-form-item>

            <!-- Duration & Price -->
            <el-row :gutter="24">

              <!-- Duration -->
              <el-col :span="12">
                <el-form-item :label="$root.labels.duration + ':'" prop="duration">
                  <el-select
                      v-model="service.duration"
                      placeholder=""
                      @change="clearValidation()"
                  >
                    <el-option
                        v-for="item in getPossibleDurationsInSeconds(service.duration)"
                        :key="item"
                        :label="secondsToNiceDuration(item)"
                        :value="item"
                    >
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>

              <!-- Price -->
              <el-col :span="12">
                <el-form-item :label="$root.labels.price + ':'" prop="price">
                  <div class="el-input">
                    <money v-model="service.price" v-bind="moneyComponentData" class="el-input__inner"></money>
                  </div>
                </el-form-item>
              </el-col>

            </el-row>

            <!-- Pending Time -->
            <el-row :gutter="24">

              <!-- Buffer Time Before -->
              <el-col :span="12">
                <el-popover :disabled="!$root.isLite" ref="bufferTimeBeforePop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                <el-form-item label="placeholder"  v-popover:bufferTimeBeforePop>
                  <label slot="label">
                    {{ $root.labels.service_buffer_time_before }}:
                    <el-tooltip placement="top">
                      <div slot="content" v-html="$root.labels.service_buffer_time_before_tooltip"></div>
                      <i class="el-icon-question am-tooltip-icon"></i>
                    </el-tooltip>
                  </label>
                  <el-select
                      v-model="service.timeBefore"
                      placeholder=""
                      clearable
                      @change="clearValidation()"
                      :disabled=$root.isLite
                  >
                    <el-option
                        v-for="item in getPossibleDurationsInSeconds(service.timeBefore)"
                        :key="item"
                        :label="secondsToNiceDuration(item)"
                        :value="item"
                    >
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>

              <!-- Buffer Time After -->
              <el-col :span="12">
                <el-popover :disabled="!$root.isLite" ref="bufferTimeAfterPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                <el-form-item label="placeholder" v-popover:bufferTimeAfterPop>
                  <label slot="label">
                    {{ $root.labels.service_buffer_time_after }}:
                    <el-tooltip placement="top">
                      <div slot="content" v-html="$root.labels.service_buffer_time_after_tooltip"></div>
                      <i class="el-icon-question am-tooltip-icon"></i>
                    </el-tooltip>
                  </label>
                  <el-select
                      v-model="service.timeAfter"
                      placeholder=""
                      clearable
                      @change="clearValidation()"
                      :disabled=$root.isLite
                  >
                    <el-option
                        v-for="item in getPossibleDurationsInSeconds(service.timeAfter)"
                        :key="item"
                        :label="secondsToNiceDuration(item)"
                        :value="item"
                    >
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>

            </el-row>

            <!-- Capacity -->
            <el-row :gutter="24">

              <!-- Minimum Capacity -->
              <el-col :span="12">
                <el-popover :disabled="!$root.isLite" ref="minimumCapacityPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                <el-form-item label="placeholder" v-popover:minimumCapacityPop>
                  <label slot="label">
                    {{ $root.labels.minimum_capacity }}:
                    <el-tooltip placement="top">
                      <div slot="content" v-html="$root.labels.minimum_capacity_tooltip"></div>
                      <i class="el-icon-question am-tooltip-icon"></i>
                    </el-tooltip>
                  </label>
                  <el-input-number
                      v-model="service.minCapacity" :min="1"
                      @input="checkCapacityLimits()"
                      :disabled=$root.isLite
                  >
                  </el-input-number>
                </el-form-item>
              </el-col>

              <!-- Maximum Capacity -->
              <el-col :span="12">
                <el-popover :disabled="!$root.isLite" ref="maximumCapacityPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                <el-form-item label="placeholder" v-popover:maximumCapacityPop>
                  <label slot="label">
                    {{ $root.labels.maximum_capacity }}:
                    <el-tooltip placement="top">
                      <div slot="content" v-html="$root.labels.maximum_capacity_tooltip"></div>
                      <i class="el-icon-question am-tooltip-icon"></i>
                    </el-tooltip>
                  </label>
                  <el-input-number
                      v-model="service.maxCapacity"
                      :min="service.minCapacity"
                      @input="clearValidation()"
                      :disabled=$root.isLite
                  >
                  </el-input-number>
                </el-form-item>
              </el-col>

            </el-row>

            <!-- Bringing Anyone -->
            <div class="am-setting-box am-switch-box" v-if="service.maxCapacity > 1 && !$root.isLite">
              <el-row type="flex" align="middle" :gutter="24">
                <el-col :span="20">
                  {{ $root.labels.bringing_anyone }}
                  <el-tooltip placement="top">
                    <div slot="content" v-html="$root.labels.bringing_anyone_tooltip"></div>
                    <i class="el-icon-question am-tooltip-icon"></i>
                  </el-tooltip>
                </el-col>
                <el-col :span="4" class="align-right">
                  <el-switch
                      v-model="service.bringingAnyone"
                      active-text=""
                      inactive-text=""
                  >
                  </el-switch>
                </el-col>
              </el-row>
            </div>

            <!-- Employees -->
            <el-form-item :label="$root.labels.employees + ':'" prop="providers">
              <el-select
                  v-model="service.providers"
                  value-key="id"
                  placeholder=""
                  multiple
                  collapse-tags
                  @change="changeEmployees"
              >
                <el-option
                    v-for="item in visibleEmployees"
                    :key="item.id"
                    :label="item.firstName + ' ' + item.lastName"
                    :value="item"
                >
                </el-option>
              </el-select>
            </el-form-item>

            <!-- Description -->
            <el-form-item :label="$root.labels.description + ':'">
              <el-input
                  type="textarea"
                  :autosize="{minRows: 4, maxRows: 6}"
                  placeholder=""
                  v-model="service.description"
                  @input="clearValidation()"
              >
              </el-input>
            </el-form-item>

          </el-tab-pane>

          <!-- Gallery -->
          <el-tab-pane :label="$root.labels.gallery" name="gallery">
            <BlockLite/>
            <div class="am-gallery-images">
              <el-row :gutter="24">
                <draggable v-model="service.gallery" :options="draggableOptions" @end="dropGalleryImage">
                  <el-col :sm="12" v-for="(galleryImage, index) in service.gallery" :key="index" v-if="!$root.isLite">
                    <div class="am-gallery-image-wrapper">
                      <div class="am-gallery-image"
                           :style="{'background-image': 'url(' + galleryImage.pictureFullPath + ')'}">
                        <i class="el-icon-delete" @click="deleteGalleryImage(index)"></i>
                      </div>
                      <div class="am-gallery-image-title">
                      <span class="am-drag-handle">
                        <img class="svg" width="20px" :src="$root.getUrl+'public/img/burger-menu.svg'">
                      </span>
                        <span>{{ galleryImage.pictureFullPath.substring(galleryImage.pictureFullPath.lastIndexOf('/')+1) }}</span>
                      </div>
                    </div>
                  </el-col>
                </draggable>

                <!-- Add Gallery -->
                <el-col :sm="12">
                  <div class="am-gallery-image-add" :class="{'am-lite-disabled': $root.isLite}">
                    <div>
                      <picture-upload
                          :multiple="true"
                          :edited-entity="null"
                          :entity-name="'gallery'"
                          @pictureSelected="galleryPictureSelected"
                          :disabled="$root.isLite"
                      >
                      </picture-upload>
                    </div>
                  </div>
                </el-col>

              </el-row>
            </div>

          </el-tab-pane>

          <!-- Extras -->
          <el-tab-pane :label="$root.labels.extras" name="extras">
            <BlockLite/>
            <div class="am-extra-list">
              <el-button @click="addExtra" size="large" type="primary" :class="{'am-dialog-create': true, 'am-lite-disabled': $root.isLite}" :disabled=$root.isLitelv>
                <i class="el-icon-plus"></i> <span class="button-text">{{ $root.labels.add_extra }}</span>
              </el-button>

              <draggable v-model="service.extras" :options="draggableOptions" @end="dropExtra">
                <transition-group name="list-complete">
                  <div class="am-extra-item"
                       v-loading="extraToDeleteLoading && index === service.extras.indexOf(extraToDelete)"
                       v-for="(extra, index) in service.extras" :key="index">

                    <!-- Extra Preview -->
                    <el-row type="flex" align="top" class="">

                      <el-col :span="2">
                        <span class="am-drag-handle">
                          <img class="svg" width="20px" :src="$root.getUrl+'public/img/burger-menu.svg'">
                        </span>
                      </el-col>

                      <el-col :span="22">

                        <el-row type="flex" align="middle">
                          <el-col :span="18">
                            <h3>{{ extra.name }}</h3>
                          </el-col>
                          <el-col v-show="index !== service.extras.indexOf(editedExtra) || editedExtraOld !== null"
                                  :span="6" class="extra-item-actions align-right">
                            <span @click="showEditExtraDialog(extra)">
                              <img class="svg edit" width="16px" :src="$root.getUrl+'public/img/edit.svg'">
                            </span>
                            <span
                                v-if="$root.settings.capabilities.canDelete === true"
                                @click="showDeleteExtraDialog(extra)"
                            >
                              <img class="svg" width="16px" :src="$root.getUrl+'public/img/delete.svg'">
                            </span>
                          </el-col>
                        </el-row>

                        <div class="am-extra-item-data"
                             v-show="(editedExtra === null || index !== service.extras.indexOf(editedExtra)) && (extraToDelete === null || index !== service.extras.indexOf(extraToDelete))">
                          <el-row :gutter="24">
                            <el-col :sm="7">
                              <span class="data-title">{{ $root.labels.duration }}:</span>
                              <span class="data-value">
                                {{ extra.duration ? secondsToNiceDuration(extra.duration) : '/'}}
                              </span>
                            </el-col>
                            <el-col :sm="7">
                              <span class="data-title">{{ $root.labels.price }}:</span>
                              <span class="data-value">{{ getFormattedPrice(extra.price) }}</span>
                            </el-col>
                            <el-col :sm="10">
                              <span class="data-title">{{ $root.labels.maximum_quantity }}:</span>
                              <span class="data-value">{{ extra.maxQuantity }}</span>
                            </el-col>
                          </el-row>
                        </div>

                      </el-col>
                    </el-row>

                    <!-- Extra Delete -->
                    <el-collapse-transition>
                      <div class="am-confirmation"
                           v-show="extraToDelete !== null && index === service.extras.indexOf(extraToDelete)">
                        <p>{{ $root.labels.delete_extra_confirmation }}?</p>
                        <div class="align-right">
                          <el-button size="small" @click="hideDeleteExtraDialog()">{{ $root.labels.cancel }}</el-button>
                          <el-button size="small" @click="deleteExtra(extra)" type="primary">{{ $root.labels.delete }}
                          </el-button>
                        </div>
                      </div>
                    </el-collapse-transition>

                    <!-- Extra Edit -->
                    <el-collapse-transition>
                      <div
                          v-show="extraEditDialog === true && index === service.extras.indexOf(editedExtra)">
                        <el-form :model="extra" ref="extra" :rules="rulesExtra" label-position="top">

                          <!-- Extra Name -->
                          <el-form-item :label="$root.labels.name + ':'" prop="name">
                            <el-input v-model="extra.name" auto-complete="off"></el-input>
                          </el-form-item>

                          <!-- Duration & Price -->
                          <el-row :gutter="24">

                            <!-- Duration -->
                            <el-col :sm="8" :xs="12">
                              <el-form-item :label="$root.labels.duration + ':'">
                                <el-select
                                    v-model="extra.duration"
                                    clearable
                                    placeholder=""
                                >
                                  <el-option
                                      v-for="item in getPossibleDurationsInSeconds(extra.duration)"
                                      :key="item"
                                      :label="secondsToNiceDuration(item)"
                                      :value="item"
                                  >
                                  </el-option>
                                </el-select>
                              </el-form-item>
                            </el-col>

                            <!-- Price -->
                            <el-col :sm="8" :xs="12">
                              <el-form-item :label="$root.labels.price + ':'" prop="price">
                                <div class="el-input">
                                  <money v-model="extra.price" v-bind="moneyComponentData"
                                         class="el-input__inner"></money>
                                </div>
                              </el-form-item>
                            </el-col>

                            <!-- Maximum Capacity -->
                            <el-col :sm="8" :xs="24">
                              <el-form-item :label="$root.labels.maximum_quantity + ':'">
                                <el-input-number v-model="extra.maxQuantity" :min="1"></el-input-number>
                              </el-form-item>
                            </el-col>

                          </el-row>

                          <!-- Description -->
                          <el-form-item :label="$root.labels.description + ':'">
                            <el-input
                                type="textarea"
                                :autosize="{ minRows: 4, maxRows: 6}"
                                placeholder=""
                                v-model="extra.description">
                            </el-input>
                          </el-form-item>

                          <!-- Cancel & Save -->
                          <div class="align-right">
                            <el-button @click="cancelExtra(extra)" size="small">{{ $root.labels.cancel }}
                            </el-button>
                            <el-button size="small" @click="saveExtra(extra)" type="primary">{{ $root.labels.save }}
                            </el-button>
                          </div>

                        </el-form>
                      </div>
                    </el-collapse-transition>

                  </div>
                </transition-group>
              </draggable>

            </div>
          </el-tab-pane>

        </el-tabs>
      </el-form>
    </div>

    <dialog-actions
        formName="service"
        urlName="services"
        :isNew="service.id === 0"
        :entity="service"
        :getParsedEntity="getParsedEntity"
        :haveSaveConfirmation="haveSaveConfirmation"
        :isDisabledDuplicate="isDisabledDuplicate"

        :action="{
          haveAdd: true,
          haveEdit: true,
          haveStatus: true,
          haveRemove: $root.settings.capabilities.canDelete === true,
          haveRemoveEffect: true,
          haveDuplicate: true,
          haveConfirmSave: true
        }"

        :message="{
          success: {
            save: $root.labels.service_saved,
            remove: $root.labels.service_deleted,
            show: $root.labels.service_visible,
            hide: $root.labels.service_hidden
          },
          confirm: {
            save: $root.labels.confirm_global_change_service,
            remove: $root.labels.confirm_delete_service,
            show: $root.labels.confirm_show_service,
            hide: $root.labels.confirm_hide_service,
            duplicate: $root.labels.confirm_duplicate_service
          },
        }"

        :buttonTextYes="$root.labels.update_for_all"
        :buttonTextNo="$root.labels.no"
    >
    </dialog-actions>

  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import PictureUpload from '../parts/PictureUpload.vue'
  import Form from 'form-object'
  import Draggable from 'vuedraggable'
  import { Money } from 'v-money'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import DialogActions from '../parts/DialogActions.vue'

  export default {
    mixins: [imageMixin, dateMixin, durationMixin, priceMixin, notifyMixin],

    props: {
      categories: null,
      passedService: null,
      employees: null,
      services: null,
      futureAppointments: null,
      isDisabledDuplicate: null
    },

    data () {
      let validateNonNegativePrice = (rule, price, callback) => {
        if (price < 0) {
          callback(new Error(this.$root.labels.enter_non_negative_price_warning))
        } else {
          callback()
        }
      }

      return {
        appointmentsEmployees: [],
        colorPopover: false,
        draggableOptions: {
          animation: 150,
          group: 'people',
          handle: '.am-drag-handle'
        },
        editedExtra: null,
        editedExtraOld: null,
        extraEditDialog: false,
        extraToDelete: null,
        extraToDeleteLoading: false,
        form: new Form(),
        rulesExtra: {
          name: [
            {required: true, message: this.$root.labels.enter_extra_name_warning, trigger: 'submit'}
          ],
          price: [
            {validator: validateNonNegativePrice, trigger: 'submit'},
            {required: true, message: this.$root.labels.enter_extra_price_warning, trigger: 'submit', type: 'number'}
          ]
        },
        rulesService: {
          name: [
            {required: true, message: this.$root.labels.enter_service_name_warning, trigger: 'submit'}
          ],
          categoryId: [
            {
              required: true,
              message: this.$root.labels.select_service_category_warning,
              trigger: 'submit',
              type: 'number'
            }
          ],
          duration: [
            {
              required: true,
              message: this.$root.labels.select_service_duration_warning,
              trigger: 'submit',
              type: 'number'
            }
          ],
          price: [
            {validator: validateNonNegativePrice, trigger: 'submit'},
            {required: true, message: this.$root.labels.enter_service_price_warning, trigger: 'submit', type: 'number'}
          ],
          providers: [
            {required: true, message: this.$root.labels.select_service_employee_warning, trigger: 'submit'}
          ]
        },
        service: {},
        serviceColors: [
          '1788FB',
          '4BBEC6',
          'FBC22D',
          'FA3C52',
          'D696B8',
          '689BCA',
          '26CC2B',
          'FD7E35',
          'E38587',
          '774DFB'
        ],
        serviceTabs: 'service_details',
        style: ''
      }
    },

    created () {
      this.service = JSON.parse(JSON.stringify(this.passedService))

      // If service is duplicated use duplicated service employees
      if (this.service.duplicated !== true) {
        this.$set(this.service, 'providers', this.selectedVisibleEmployees)
      }

      // Set extra duration to empty string if duration is null, because of element-ui select clearable button
      for (let i = 0; i < this.service.extras.length; i++) {
        if (this.service.extras[i].duration === null) {
          this.service.extras[i].duration = ''
        }
      }

      if (this.service.id in this.futureAppointments) {
        this.appointmentsEmployees = this.futureAppointments[this.service.id]
      }
    },

    methods: {
      closeDialog () {
        this.$emit('closeDialog')
      },

      getParsedEntity: !AMELIA_LITE_VERSION ? function (applyGlobally) {
        if (typeof this.$refs.extra !== 'undefined') {
          for (let i = 0; i < this.$refs.extra.length; i++) {
            this.$refs.extra[i].validate((valid) => {
              if (valid) {
                this.$refs.extra[i].clearValidate()
                return true
              } else {
                this.serviceTabs = 'extras'
                return false
              }
            })
          }
        }

        this.service.providers = this.service.providers.map(employee => employee.id)
        this.service.applyGlobally = applyGlobally

        return this.service
      } : function (applyGlobally) {
        this.service.minCapacity = 1
        this.service.maxCapacity = 1
        this.service.timeAfter = ''
        this.service.timeBefore = ''
        this.service.providers = this.service.providers.map(employee => employee.id)
        this.service.applyGlobally = applyGlobally

        if (this.categories[0].serviceList.length > 9 - 5 || this.service.categoryId !== this.categories[0].id) {
          return null
        }

        return this.service
      },

      haveSaveConfirmation () {
        return this.service.id && (this.passedService.price !== this.service.price || this.passedService.minCapacity !== this.service.minCapacity || this.passedService.maxCapacity !== this.service.maxCapacity)
      },

      bgColor (color) {
        return {'background-color': color}
      },

      servicePictureSelected (pictureFullPath, pictureThumbPath) {
        this.service.pictureFullPath = pictureFullPath
        this.service.pictureThumbPath = pictureThumbPath
      },

      galleryPictureSelected: !AMELIA_LITE_VERSION ? function (pictureFullPath, pictureThumbPath) {
        this.clearValidation()
        this.service.gallery.push({
          id: 0,
          pictureFullPath: pictureFullPath,
          pictureThumbPath: pictureThumbPath,
          position: this.service.gallery.length + 1
        })
      } : function () {},

      deleteGalleryImage: !AMELIA_LITE_VERSION ? function (index) {
        this.service.gallery.splice(index, 1)

        for (let i = 0; i < this.service.gallery.length; i++) {
          this.service.gallery[i].position = i + 1
        }
      } : function () {},

      dropGalleryImage: !AMELIA_LITE_VERSION ? function (e) {
        if (e.newIndex !== e.oldIndex) {
          let that = this
          this.service.gallery.forEach((image) => {
            image.position = that.service.gallery.indexOf(image) + 1
          })
        }
      } : function () {},

      changeServiceColor (e) {
        let siblings = Array.from(e.target.parentNode.children)
        siblings.forEach(function (sib) {
          if (sib.className.includes('color-active')) {
            sib.classList.remove('color-active')
          }
        })
        e.target.className = e.target.className + ' color-active'
        this.service.color = e.target.getAttribute('data-color')
      },

      checkCapacityLimits () {
        this.clearValidation()
        if (this.service.minCapacity > this.service.maxCapacity) {
          this.service.maxCapacity = this.service.minCapacity
        }
      },

      showEditExtraDialog: !AMELIA_LITE_VERSION ? function (extra) {
        this.handleExtraForm()
        this.editedExtra = extra
        this.editedExtraOld = Object.assign({}, extra)
        this.extraToDelete = null
        this.extraEditDialog = true
      } : function () {},

      cancelExtra: !AMELIA_LITE_VERSION ? function (extra) {
        if (this.editedExtraOld !== null) {
          extra.name = this.editedExtraOld.name
          extra.duration = this.editedExtraOld.duration
          extra.price = this.editedExtraOld.price
          extra.maxQuantity = this.editedExtraOld.maxQuantity
          extra.description = this.editedExtraOld.description
        }
        this.editedExtra = null
        this.editedExtraOld = null
        this.extraEditDialog = false

        // Remove newly created extra if it is not saved
        if (typeof extra.id === 'undefined') {
          this.service.extras.splice(-1, 1)
        }
      } : function () {},

      addExtra: !AMELIA_LITE_VERSION ? function () {
        this.handleExtraForm()
        this.extraEditDialog = true
        this.editedExtra = {
          name: '',
          duration: '',
          price: '',
          maxQuantity: 1,
          description: '',
          serviceId: this.service.id,
          position: this.service.extras.length + 1
        }
        this.service.extras.push(this.editedExtra)
      } : function () {},

      saveExtra: !AMELIA_LITE_VERSION ? function (extra) {
        let index = this.service.extras.indexOf(extra)

        this.$refs.extra[index].validate((valid) => {
          if (valid) {
            this.editedExtra = null
            this.editedExtraOld = null
            this.extraEditDialog = false
            this.$refs.extra[index].clearValidate()
          } else {
            return false
          }
        })
      } : function () {},

      showDeleteExtraDialog: !AMELIA_LITE_VERSION ? function (extra) {
        this.handleExtraForm()
        this.extraToDelete = extra
        this.editedExtra = null
        this.extraEditDialog = false
      } : function () {},

      handleExtraForm: !AMELIA_LITE_VERSION ? function () {
        // If new extra form is active, remove this extra
        if (this.editedExtra !== null && typeof this.editedExtra.id === 'undefined') {
          this.service.extras.splice(-1, 1)
        }
        // If edit extra form is active, revert extra to old state
        if (this.editedExtraOld !== null) {
          this.cancelExtra(this.editedExtra)
        }
      } : function () {},

      hideDeleteExtraDialog: !AMELIA_LITE_VERSION ? function () {
        this.extraToDelete = null
        this.extraEditDialog = true
      } : function () {},

      deleteExtra: !AMELIA_LITE_VERSION ? function (extra) {
        if (typeof extra.id === 'undefined') {
          let index = this.service.extras.indexOf(extra)
          this.service.extras.splice(index, 1)
        } else {
          this.extraToDeleteLoading = true
          this.$http.delete(`${this.$root.getAjaxUrl}/extras/` + extra.id)
            .then(() => {
              let index = this.service.extras.indexOf(extra)
              this.service.extras.splice(index, 1)
              this.extraToDeleteLoading = false
            })
            .catch(() => {
              this.extraToDeleteLoading = false
              this.notify(this.$root.labels.error, this.$root.labels.extra_delete_fail, 'error')
            })
        }

        for (let i = 0; i < this.service.extras.length; i++) {
          this.service.extras[i].position = i + 1
        }
      } : function () {},

      dropExtra: !AMELIA_LITE_VERSION ? function (e) {
        if (e.newIndex !== e.oldIndex) {
          let that = this
          this.service.extras.forEach((extra) => {
            extra.position = that.service.extras.indexOf(extra) + 1
          })
        }
      } : function () {},

      clearValidation () {
        if (typeof this.$refs.service !== 'undefined') {
          this.$refs.service.clearValidate()
        }
      },

      changeEmployees () {
        let selectedEmployees = this.service.providers.map(employee => employee.id)

        let intersection = this.appointmentsEmployees.filter(x => !selectedEmployees.includes(x))

        if (intersection.length) {
          let $this = this
          let unselectedEmployees = []

          intersection.forEach(function (employeeId) {
            unselectedEmployees.push($this.visibleEmployees.filter(employee => employee.id === employeeId)[0])
            $this.notify($this.$root.labels.error, $this.$root.labels.service_provider_remove_fail, 'error')
          })

          this.service.providers = this.service.providers.concat(unselectedEmployees).sort(function (a, b) {
            return (a.firstName + ' ' + a.lastName).localeCompare((b.firstName + ' ' + b.lastName))
          })
        }
      }
    },

    computed: {
      visibleCategories () {
        return this.categories.filter(category => category.status === 'visible')
      },

      visibleEmployees () {
        return this.employees.filter(employee =>
          (employee.status === 'visible') ||
          (employee.status === 'hidden' && employee.serviceList.map(service => service.id).indexOf(this.service.id) !== -1)
        )
      },

      selectedVisibleEmployees () {
        return this.employees.filter(employee =>
          employee.serviceList.map(service => service.id).indexOf(this.service.id) !== -1
        )
      }
    },

    watch: {
      'service.price' () {
        this.clearValidation()
      }
    },

    components: {
      PictureUpload,
      Draggable,
      Money,
      DialogActions
    }
  }
</script>