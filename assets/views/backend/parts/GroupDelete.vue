<template>
  <!-- Selected Popover Delete -->
  <transition name="slide-vertical">
    <div class="am-bottom-popover" v-show="checkGroupData.toaster">
      <transition name="fade">
        <el-button
            class="am-button-icon"
            @click="showDeleteConfirmation = !showDeleteConfirmation"
            v-show="!showDeleteConfirmation"
        >
          <img class="svg" :alt="$root.labels.delete" :src="$root.getUrl+'public/img/delete.svg'"/>
        </el-button>
      </transition>
      <transition name="slide-vertical">
        <div class="am-bottom-popover-confirmation" v-show="showDeleteConfirmation">
          <el-row type="flex" justify="start" align="middle">
            <h3>{{ confirmDeleteMessage }}</h3>
            <div class="align-left">
              <el-button size="small" @click="showDeleteConfirmation = !showDeleteConfirmation">
                {{ $root.labels.cancel }}
              </el-button>
              <el-button
                  size="small"
                  @click="deleteEntities()"
                  type="primary"
                  :loading="deleteGroupLoading"
              >
                {{ $root.labels.delete }}
              </el-button>
            </div>
          </el-row>

        </div>
      </transition>
    </div>
  </transition>
</template>

<script>
  import notifyMixin from '../../../js/backend/mixins/notifyMixin'
  import checkMixin from '../../../js/backend/mixins/checkMixin'
  import Form from 'form-object'

  export default {

    mixins: [notifyMixin, checkMixin],

    props: {
      name: null,
      entities: null,
      checkGroupData: {
        toaster: false,
        allChecked: false
      },
      confirmDeleteMessage: '',
      successMessage: {single: '', multiple: ''},
      errorMessage: {single: '', multiple: ''}
    },

    data () {
      return {
        count: {
          success: 0,
          error: 0
        },
        deleteGroupLoading: false,
        form: new Form(),
        showDeleteConfirmation: false
      }
    },

    methods: {
      deleteEntities () {
        this.deleteGroupLoading = true
        let $this = this
        let selectedEntities = this.entities.filter(entity => entity.checked).map(entity => entity.id)

        selectedEntities.forEach(function (value) {
          $this.form.delete(`${$this.$root.getAjaxUrl}/` + $this.name + '/' + value)
            .then(() => {
              $this.deleteEntityCallback(selectedEntities, true)
            })
            .catch(() => {
              $this.deleteEntityCallback(selectedEntities, false)
            })
        })
      },

      deleteEntityCallback (selectedEntities, result) {
        selectedEntities.pop()

        if (result) {
          this.count.success++
        } else {
          this.count.error++
        }

        if (selectedEntities.length === 0) {
          if (this.count.success) {
            this.notify(
              this.$root.labels.success,
              this.count.success + ' ' + (this.count.success > 1 ? this.successMessage.multiple : this.successMessage.single),
              'success')
          }

          if (this.count.error) {
            this.notify(
              this.$root.labels.error,
              this.count.error + ' ' + (this.count.error > 1 ? this.errorMessage.multiple : this.errorMessage.single),
              'error')
          }

          this.count.success = 0
          this.count.error = 0

          this.showDeleteConfirmation = false
          this.deleteGroupLoading = false
          this.$emit('groupDeleteCallback')
        }
      }

    },

    computed: {},

    watch: {},

    components: {}

  }
</script>
