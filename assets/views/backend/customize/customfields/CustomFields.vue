<template>
  <div>

    <!-- Custom Fields -->
    <div class="am-custom-fields" id="qweqwe">

      <!-- Spinner -->
      <div class="am-spinner am-section" v-show="!fetched || !options.fetched">
        <img :src="$root.getUrl + 'public/img/spinner.svg'"/>
      </div>

      <!-- Empty State -->
      <div class="am-empty-state am-section" v-if="fetched && options.fetched && customFields.length === 0">
        <img :src="$root.getUrl + 'public/img/emptystate.svg'">
        <h2>{{ $root.labels.no_custom_fields_yet }}</h2>
        <p>{{ $root.labels.click_add_custom_field }}</p>
      </div>

      <!-- Custom Fields List -->
      <div class="am-custom-fields-list" v-if="fetched && options.fetched && customFields.length > 0">

        <!-- Custom Field Component -->
        <draggable v-model="customFields" :options="draggableOptions" @end="dropCustomField">
          <custom-field
              v-for="customField in customFields"
              :key="customField.id"
              :customField="customField"
              :categories="options.entities.categories"
              :services="options.entities.services"
              @deleteCustomField="deleteCustomField"
              @updateCustomField="updateCustomField"
          >
          </custom-field>
        </draggable>

      </div>

    </div>

    <!-- Dialog Custom Fields -->
    <transition name="slide">
      <el-dialog
          class="am-side-dialog am-dialog-custom-fields"
          :visible.sync="showDialog"
          :show-close="false" v-if="showDialog"
      >
        <dialog-custom-fields
            @closeDialogCustomFields="closeDialogCustomFields"
            @addCustomField="addCustomField"
        >
        </dialog-custom-fields>
      </el-dialog>
    </transition>

    <!-- Button New -->
    <div id="am-button-new" class="am-button-new">

      <!-- Popover -->
      <el-popover
          ref="popover"
          placement="top"
          width="160"
          v-model="popover"
          visible-arrow="false"
          popper-class="am-button-popover"
      >
        <div class="am-overlay" @click="popover = false; buttonNewItems = !buttonNewItems">
          <el-popover :disabled="!$root.isLite" ref="customFieldsPop" v-bind="$root.popLiteProps"><PopLite/></el-popover>
          <div class="am-button-new-items-custom-fields" v-popover:customFieldsPop>
            <transition name="el-zoom-in-bottom">
              <div v-show="buttonNewItems">
                <el-button
                    v-for="(type, index) in types"
                    :key="index" @click="addCustomField(type)"
                    :disabled="$root.isLite"
                >
                  {{ $root.labels[type] }}
                </el-button>
              </div>
            </transition>
          </div>
        </div>
      </el-popover>

      <!-- Button -->
      <el-button
          id="am-plus-symbol"
          v-popover:popover
          type="primary"
          icon="el-icon-plus"
          @click="buttonNewItems = !buttonNewItems"
      >
      </el-button>

    </div>

  </div>
</template>

<script>
  import CustomField from './CustomField'
  import DialogCustomFields from './DialogCustomFields.vue'
  import Draggable from 'vuedraggable'
  import notifyMixin from '../../../../js/backend/mixins/notifyMixin'
  import imageMixin from '../../../../js/common/mixins/imageMixin'
  import entitiesMixin from '../../../../js/common/mixins/entitiesMixin'

  export default {
    mixins: [notifyMixin, imageMixin, entitiesMixin],

    props: {
      dialogCustomFields: {
        default: false,
        type: Boolean
      }
    },

    data () {
      return {
        buttonNewItems: false,
        customFields: [],
        draggableOptions: {
          handle: '.am-drag-handle',
          animation: 150
        },
        fetched: false,
        options: {
          entities: {
            categories: [],
            services: []
          },
          fetched: false
        },
        popover: false,
        types: ['text', 'text-area', 'content', 'select', 'checkbox', 'radio']
      }
    },

    mounted: !AMELIA_LITE_VERSION ? function () {
      this.getCustomFields(false)
      this.getEntities()
    } : function () {
      this.fetched = true
      this.options.fetched = true
    },

    methods: {
      getCustomFields: !AMELIA_LITE_VERSION ? function (inlineSVG) {
        this.fetched = false
        this.$http.get(`${this.$root.getAjaxUrl}/fields`)
          .then(response => {
            this.customFields = response.data.data.customFields
            this.fetched = true
            if (inlineSVG) {
              setTimeout(() => {
                this.inlineSVG()
              }, 100)
            }
          })
          .catch(e => {
            console.log(e.message)
          })
      } : function () {},

      getEntities: !AMELIA_LITE_VERSION ? function () {
        this.options.fetched = false

        this.$http.get(`${this.$root.getAjaxUrl}/entities`, {
          params: {
            types: ['categories']
          }
        }).then(response => {
          this.options.entities = response.data.data
          this.options.fetched = true
          this.options.entities.services = this.getServicesFromCategories()
          setTimeout(() => {
            this.inlineSVG()
          }, 100)
        }).catch(() => {
          this.options.fetched = true
        })
      } : function () {},

      dropCustomField: !AMELIA_LITE_VERSION ? function (e) {
        if (e.newIndex !== e.oldIndex) {
          let $this = this
          this.customFields.forEach((customField) => {
            customField.position = $this.customFields.indexOf(customField) + 1
          })

          this.updateCustomFieldsPositions()
        }
      } : function () {},

      deleteCustomField: !AMELIA_LITE_VERSION ? function (customField) {
        this.fetched = false
        this.$http.delete(`${this.$root.getAjaxUrl}/fields/` + customField.id)
          .then(() => {
            this.notify(this.$root.labels.success, this.$root.labels.custom_fields_deleted, 'success')

            let index = this.customFields.indexOf(customField)
            this.customFields.splice(index, 1)

            // Update custom fields positions
            for (let i = 0; i < this.customFields.length; i++) {
              this.customFields[i].position = i + 1
            }
            this.updateCustomFieldsPositions()

            this.getCustomFields(true)
          })
          .catch(e => {
            this.notify(this.$root.labels.error, e.message, 'error')
          })
      } : function () {},

      updateCustomField: !AMELIA_LITE_VERSION ? function (customField) {
        let index = this.customFields.findIndex(field => field.id === customField.id)

        let $this = this
        this.customFields[index].options.forEach((option, optionIndex) => {
          if ($this.customFields[index].options[optionIndex].deleted === true) {
            $this.customFields[index].options.splice(optionIndex, 1)
          } else {
            $this.$set(option, 'id', typeof customField.options[optionIndex].id !== 'undefined' ? customField.options[optionIndex].id : null)
            $this.$set(option, 'edited', false)
            $this.$set(option, 'deleted', false)
            $this.$set(option, 'new', false)
          }
        })
      } : function () {},

      closeDialogCustomFields () {
        this.$emit('closeDialogCustomFields')
      },

      addCustomField: !AMELIA_LITE_VERSION ? function (type) {
        this.fetched = false
        this.showDialog = false

        let customField = {
          id: null,
          label: '',
          options: [],
          position: this.customFields.length + 1,
          required: true,
          services: this.options.entities.services,
          type: type
        }

        this.$http.post(`${this.$root.getAjaxUrl}/fields`, {
          customField: customField
        }).then(() => {
          this.notify(this.$root.labels.success, this.$root.labels.custom_fields_added, 'success')
          this.getCustomFields(true)
        }).catch(e => {
          this.notify(this.$root.labels.error, e.message, 'error')
        })
      } : function () {},

      updateCustomFieldsPositions: !AMELIA_LITE_VERSION ? function () {
        this.$http.patch(`${this.$root.getAjaxUrl}/fields/positions`, {
          customFields: this.customFields
        }).catch(() => {
          this.notify(this.$root.labels.error, this.$root.labels.custom_fields_positions_saved_fail, 'error')
        })
      } : function () {}
    },

    computed: {
      showDialog: {
        get () {
          return this.dialogCustomFields === true
        },
        set () {
          this.$emit('closeDialogCustomFields')
        }
      }
    },

    components: {
      CustomField,
      DialogCustomFields,
      Draggable
    }
  }
</script>