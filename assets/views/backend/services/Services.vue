<template>
  <div class="am-wrap">
    <div id="am-services" class="am-body">

      <!-- Page Header -->
      <page-header
          :servicesTotal="services.length"
          :categoriesTotal="categories.length"
          @newServiceBtnClicked="showDialogNewService()"
      >
      </page-header>

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched">
        <img :src="$root.getUrl+'public/img/spinner.svg'"/>
      </div>

      <!-- Services & Categories -->
      <div v-if="fetched" class="am-services-categories">
        <el-row class="am-flexed">
          <el-col :md="6" class="">
            <div class="am-categories-column am-section">
              <h2>{{ $root.labels.categories }}</h2>
              <el-popover :disabled="!($root.isLite && categories.length > 0)" ref="addCategoryPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
              <div v-popover:addCategoryPop>
                <el-button @click="addCategory" size="large" type="primary" :class="{'am-dialog-create': true, 'am-lite-disabled': ($root.isLite && categories.length > 0)}"
                           :disabled="$root.isLite && categories.length > 0"
                           :loading="loadingAddCategory">
                  <i class="el-icon-plus"></i> <span class="button-text">{{ $root.labels.add_category }}</span>
                </el-button>
              </div>

              <!-- All Services Filter -->
              <div class="am-category-item">
                <h3>
                  <span
                      class="am-category-title"
                      :class="{ active: Object.keys(activeCategory).length === 0 }"
                      @click="filterServices({})"
                  >
                    {{ $root.labels.all_services }}
                  </span>
                </h3>
              </div>

              <!-- Categories -->
              <draggable v-model="categories" :options="draggableOptions" @end="dropCategory">
                <transition-group name="list-complete">
                  <div class="am-category-item" v-for="category in categories" :key="category.id">

                    <!-- Reorder & Title -->
                    <h3 class="am-three-dots">

                      <!-- Reorder Button -->
                      <span class="am-drag-handle">
                        <img class="svg" width="20px" :src="$root.getUrl + 'public/img/burger-menu.svg'">
                      </span>

                      <!-- Title Input -->
                      <input
                          ref="input"
                          class="am-category-title"
                          :class="{ hidden: editedCategoryId !== category.id }"
                          v-model="editedCategoryName"
                          @keyup.enter="editCategoryName(category, $event)"
                      />

                      <!-- Title Text -->
                      <span class="am-category-title"
                            :class="{ hidden: editedCategoryId === category.id, active: activeCategory.id === category.id }"
                            v-model="category.name"
                            @click="filterServices(category)"
                      >
                        {{ category.name }}
                      </span>
                    </h3>

                    <div class="am-category-item-footer">
                      <el-row type="flex" align="middle">

                        <!-- Number of Services -->
                        <el-col :span="12">
                          <span class="service-count"> {{ category.serviceList.length }} {{ category.serviceList.length === 1 ? $root.labels.service : $root.labels.services }}</span>
                        </el-col>

                        <!-- Category Actions -->
                        <el-col :span="12" class="align-right category-actions">

                          <!-- Edit Category Name -->
                          <span
                              :class="{active: editedCategoryId === category.id }"
                              @click="editCategoryName(category, $event)"
                          >
                            <img class="svg edit" width="16px" :src="$root.getUrl+'public/img/edit.svg'">
                            <i class="el-icon-circle-check done"></i>
                          </span>

                          <!-- Duplicate Category -->
                          <el-popover :disabled="!$root.isLite" ref="duplicateCategoryPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
                          <span v-popover:duplicateCategoryPop @click="($root.isLite && categories.length > 0) ? false : duplicateCategory(category)" :class="($root.isLite && categories.length > 0) ? 'am-lite-disabled' : ''">
                            <img class="svg" width="16px" :src="$root.getUrl+'public/img/copy.svg'">
                          </span>

                          <!-- Delete Category -->
                          <span
                              v-if="$root.settings.capabilities.canDelete === true"
                              @click="handleCategoryDeleteConfirmation(category)"
                          >
                            <img class="svg" width="16px" :src="$root.getUrl+'public/img/delete.svg'">
                          </span>

                        </el-col>
                      </el-row>

                      <!-- Delete Confirmation -->
                      <el-collapse-transition>
                        <div class="am-confirmation" v-show="deleteConfirmation && categoryToDeleteId === category.id">
                          <p>{{ $root.labels.delete_category_confirmation }}?</p>
                          <el-alert
                              title=""
                              type="warning"
                              description=""
                              :closable="false"
                          >
                          </el-alert>
                          <div class="align-right">
                            <el-button size="small" @click="hideDeleteCategoryDialog()">
                              {{ $root.labels.cancel }}
                            </el-button>
                            <el-button size="small" @click="deleteCategory(category)" type="primary"
                                       :loading="loadingDeleteCategory">
                              {{ $root.labels.delete }}
                            </el-button>
                          </div>
                        </div>
                      </el-collapse-transition>

                    </div>

                  </div>
                </transition-group>
              </draggable>

            </div>
          </el-col>

          <el-col :md="18">
            <div class="am-services-column am-section">
              <h2 v-show="displayedServices.length !== 0 && Object.keys(activeCategory).length === 0">{{
                $root.labels.all_services }}</h2>
              <h2 v-show="displayedServices.length !== 0 && Object.keys(activeCategory).length !== 0">
                {{ activeCategory.name }}
              </h2>

              <!-- Empty State For Categories -->
              <div class="am-empty-state am-section" v-show="fetched && categories.length === 0">
                <img :src="$root.getUrl+'public/img/emptystate.svg'">
                <h2>{{ $root.labels.no_categories_yet }}</h2>
                <p>{{ $root.labels.click_add_category }}</p>
              </div>

              <!-- Empty State For Services -->
              <div class="am-empty-state am-section"
                   v-show="fetched && categories.length !== 0 && displayedServices.length === 0">
                <img :src="$root.getUrl+'public/img/emptystate.svg'">
                <h2>{{ $root.labels.no_services_yet }}</h2>
                <p>{{ $root.labels.click_add_service }}</p>
              </div>


              <!-- Services -->
              <div v-show="fetched && categories.length !== 0" class="am-services-grid">
                <el-row :gutter="16">
                  <template v-for="(service, index) in displayedServices">
                    <transition name="el-fade-in-linear">
                      <el-col :xl="8" :lg="12" :md="24">
                        <div class="am-service-card" @click="showDialogEditService(index)"
                             :class="{'am-hidden-entity' : service.status === 'hidden'}"
                        >
                          <div class="am-service-photo">
                            <img :src="pictureLoad(service, false)" @error="imageLoadError(service, false)"/>
                            <span class="am-service-color" :style="bgColor(service.color)"></span>
                          </div>
                          <div class="">
                            <h4>{{ service.name }}</h4>
                            <p>{{ $root.labels.duration }}: {{ secondsToNiceDuration(service.duration) }}</p>
                            <p>{{ $root.labels.price }}: {{ getFormattedPrice(service.price) }}</p>
                          </div>
                        </div>
                      </el-col>
                    </transition>
                  </template>
                </el-row>
              </div>

            </div>
          </el-col>

        </el-row>
      </div>

      <!-- Button New -->
      <div v-if="categories.length > 0 && $root.settings.capabilities.canWrite === true"
           id="am-button-new"
           class="am-button-new"
           v-popover:addServicePlusPop
      >
        <el-popover :disabled="!($root.isLite && services.length > 3)" ref="addServicePlusPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
        <el-button id="am-plus-symbol"
           type="primary"
           icon="el-icon-plus"
           @click="showDialogNewService()"
           :class="{'am-lite-disabled': ($root.isLite && services.length > 3)}"
           :disabled="$root.isLite && services.length > 3"
        >
        </el-button>
      </div>

      <!-- Dialog Service -->
      <transition name="slide">
        <el-dialog
            class="am-side-dialog am-dialog-service"
            :visible.sync="dialogService"
            :show-close="false"
            v-if="dialogService"
        >
          <dialog-service
              :categories="categories"
              :passedService="service"
              :employees=options.employees
              :futureAppointments="futureAppointments"
              @saveCallback="saveServiceCallback"
              @duplicateCallback="duplicateServiceCallback"
              @closeDialog="dialogService = false"
              :isDisabledDuplicate="$root.isLite && services.length > 3"
          >
          </dialog-service>
        </el-dialog>
      </transition>

      <!-- Help Button -->
      <el-col :md="6" class="">
        <a class="am-help-button" href="https://wpamelia.com/services-and-categories/" target="_blank">
          <i class="el-icon-question"></i> {{ $root.labels.need_help }}?
        </a>
      </el-col>

      <DialogLite/>

    </div>
  </div>
</template>

<script>
  import Form from 'form-object'
  import DialogService from './DialogService.vue'
  import PageHeader from '../parts/PageHeader.vue'
  import Draggable from 'vuedraggable'
  import liteMixin from '../../../js/common/mixins/liteMixin'
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'
  import durationMixin from '../../../js/common/mixins/durationMixin'
  import priceMixin from '../../../js/common/mixins/priceMixin'
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'

  export default {

    mixins: [liteMixin, imageMixin, dateMixin, durationMixin, priceMixin, notifyMixin],

    data () {
      return {
        activeCategory: {},
        categories: [],
        categoryToDeleteId: null,
        count: 0,
        dialogService: false,
        displayedServices: [],
        deleteConfirmation: false,
        draggableOptions: {
          handle: '.am-drag-handle',
          animation: 150
        },
        editedCategoryId: 0,
        editedCategoryName: '',
        editedCategoryOldName: '',
        fetched: false,
        form: new Form(),
        futureAppointments: {},
        loadingAddCategory: false,
        loadingDeleteCategory: false,
        options: {
          employees: []
        },
        service: null,
        services: [],
        svgLoaded: false
      }
    },

    created () {
      this.getOptions()
    },

    mounted () {

    },

    updated () {
      if (this.svgLoaded) this.inlineSVG()
      this.svgLoaded = true
    },

    methods: {
      getOptions () {
        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {params: {types: ['employees', 'appointments', 'categories']}})
          .then(response => {
            this.filterResponseData(response)

            this.services = []

            this.parseOptions(response)

            for (let i = 0; i < this.categories.length; i++) {
              this.services = this.services.concat(this.categories[i].serviceList)
            }

            this.filterServices(this.activeCategory)

            let appointments = response.data.data.appointments['futureAppointments']

            for (let key in appointments) {
              let serviceId = appointments[key].serviceId
              let providerId = appointments[key].providerId

              if (!(serviceId in this.futureAppointments)) {
                this.futureAppointments[serviceId] = []
                this.futureAppointments[serviceId].push(providerId)
              } else if (this.futureAppointments[serviceId].indexOf(providerId) === -1) {
                this.futureAppointments[serviceId].push(providerId)
              }
            }

            this.fetched = true
          })
          .catch(e => {
            console.log(e.message)
            this.fetched = true
          })
      },

      parseOptions: !AMELIA_LITE_VERSION ? function (response) {
        this.options.employees = response.data.data.employees
        this.categories = response.data.data.categories
      } : function (response) {
        this.options.employees = response.data.data.employees.slice(0, 1)
        this.categories = response.data.data.categories.slice(0, 1)

        if (this.categories.length) {
          this.categories[0].serviceList = this.categories[0].serviceList.slice(0, 4)
        }
      },

      addCategory () {
        this.loadingAddCategory = true

        let newCategory = {
          status: 'visible',
          name: this.$root.labels.new_category,
          position: this.categories.length + 1
        }

        this.form.post(`${this.$root.getAjaxUrl}/categories`, newCategory)
          .then(response => {
            this.categories.push(response.data.category)
            this.editedCategoryId = response.data.category.id
            this.editedCategoryName = response.data.category.name
            this.loadingAddCategory = false

            let that = this
            window.setTimeout(function () {
              that.$refs.input[that.categories.indexOf(response.data.category)].focus()
            }, 0)
          })
          .catch(e => {
            this.notify(this.$root.labels.error, this.$root.labels.category_add_fail, 'error')
            this.loadingAddCategory = false
          })
      },

      updateCategory (category) {
        this.form.post(`${this.$root.getAjaxUrl}/categories/${category.id}`, category)
          .then(() => {
            this.notify(this.$root.labels.success, this.$root.labels.category_saved, 'success')
          })
          .catch(() => {
            category.name = this.editedCategoryOldName
            this.notify(this.$root.labels.error, this.$root.labels.category_saved_fail, 'error')
          })
      },

      updateCategoriesPositions (notify) {
        this.$http.patch(`${this.$root.getAjaxUrl}/categories/positions`, {
          categories: this.categories
        }).then(() => {
          if (notify) {
            this.notify(this.$root.labels.success, this.$root.labels.categories_positions_saved, 'success')
          }
        }).catch(() => {
          this.notify(this.$root.labels.error, this.$root.labels.categories_positions_saved_fail, 'error')
        })
      },

      editCategoryName (category, event) {
        this.hideDeleteCategoryDialog()
        // If edit or save button is clicked
        if (event.currentTarget.className === '') {
          this.editedCategoryId = category.id
          this.editedCategoryName = category.name
          // Focus category name input
          let that = this
          window.setTimeout(function () {
            that.$refs.input[that.categories.indexOf(category)].focus()
          }, 0)
        } else {
          this.editedCategoryOldName = category.name
          if (category.name !== this.editedCategoryName) {
            category.name = this.editedCategoryName
            this.updateCategory(category)
          }
          this.editedCategoryId = this.editedCategoryName = null
        }
      },

      dropCategory (e) {
        if (e.newIndex !== e.oldIndex) {
          let that = this
          this.categories.forEach((category) => {
            category.position = that.categories.indexOf(category) + 1
          })
          this.updateCategoriesPositions(true)
        }
      },

      duplicateCategory: !AMELIA_LITE_VERSION ? function (category) {
        let newCategory = Object.assign({}, category)
        delete newCategory.id
        newCategory.position = this.categories.length + 1
        this.svgLoaded = false

        this.form.post(`${this.$root.getAjaxUrl}/categories`, newCategory)
          .then(response => {
            this.categories.push(response.data.category)
            this.services = this.services.concat(response.data.category.serviceList)
            if (Object.keys(this.activeCategory).length === 0) { this.displayedServices = this.services }
            this.notify(this.$root.labels.success, this.$root.labels.category_duplicated, 'success')
          })
          .catch(e => {
            this.notify(this.$root.labels.error, this.$root.labels.category_add_fail, 'error')
          })
      } : function () {},

      handleCategoryDeleteConfirmation (category) {
        this.categoryToDeleteId = category.id
        this.deleteConfirmation = true
        // Remove category name editing if it is enabled
        this.editedCategoryId = null
        this.editedCategoryName = ''
      },

      hideDeleteCategoryDialog () {
        this.categoryToDeleteId = null
        this.deleteConfirmation = false
      },

      deleteCategory (category) {
        this.loadingDeleteCategory = true

        this.$http.delete(`${this.$root.getAjaxUrl}/categories/` + this.categoryToDeleteId)
          .then(() => {
            // Delete category
            let index = this.categories.indexOf(category)
            this.categories.splice(index, 1)
            // Delete services
            this.services = this.services.filter(service => service.categoryId !== this.categoryToDeleteId)
            // Refresh displayed services if active category is deleted
            if (Object.keys(this.activeCategory).length === 0) {
              this.displayedServices = this.services
            } else if (this.activeCategory.id === this.categoryToDeleteId) {
              this.activeCategory = {}
              this.displayedServices = this.services
            }

            // Update categories positions
            for (let i = 0; i < this.categories.length; i++) {
              this.categories[i].position = i + 1
            }

            this.updateCategoriesPositions(false)
            this.loadingDeleteCategory = false
            this.notify(this.$root.labels.success, this.$root.labels.category_deleted, 'success')
          })
          .catch(() => {
            this.loadingDeleteCategory = false
            this.notify(this.$root.labels.error, this.$root.labels.categories_delete_fail, 'error')
          })
      },

      filterServices (category) {
        this.activeCategory = category
        this.displayedServices = Object.keys(category).length === 0 ? this.services : this.services.filter(service => service.categoryId === category.id)
      },

      showDialogNewService: !AMELIA_LITE_VERSION ? function () {
        this.service = this.getInitServiceObject()
        this.dialogService = true
      } : function () {
        if (this.categories[0].serviceList.length < 8 - 4) {
          this.service = this.getInitServiceObject()
          this.dialogService = true
        }
      },

      showDialogEditService (index) {
        this.service = this.displayedServices[index]

        if (this.service.timeBefore === null) { this.service.timeBefore = '' }
        if (this.service.timeAfter === null) { this.service.timeAfter = '' }

        this.dialogService = true
      },

      duplicateServiceCallback (service) {
        this.service = service
        this.service.id = 0
        this.service.duplicated = true

        setTimeout(() => {
          this.dialogService = true
        }, 300)
      },

      bgColor (color) {
        return {'background-color': color}
      },

      saveServiceCallback () {
        this.dialogService = false
        this.getOptions()
      },

      getInitServiceObject () {
        return {
          id: 0,
          categoryId: '',
          color: '#1788FB',
          description: '',
          duration: '',
          providers: [],
          extras: [],
          maxCapacity: 1,
          minCapacity: 1,
          name: '',
          pictureFullPath: '',
          pictureThumbPath: '',
          price: 0,
          status: 'visible',
          timeAfter: '',
          timeBefore: '',
          bringingAnyone: true,
          applyGlobally: false,
          gallery: []
        }
      }

    },

    components: {
      PageHeader,
      Draggable,
      DialogService
    }

  }
</script>
