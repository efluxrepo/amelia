<template>
  <div>

    <div class="am-dialog-scrollable">

      <!-- Dialog Header -->
      <div class="am-dialog-header">
        <el-row>
          <el-col :span="14">
            <h2>{{ $root.labels.export }}</h2>
          </el-col>
          <el-col :span="10" class="align-right">
            <el-button @click="closeDialog" class="am-dialog-close" size="small" icon="el-icon-close"></el-button>
          </el-col>
        </el-row>
      </div>

      <!-- Form -->
      <el-form label-position="top">

        <!-- CSV Delimiters -->
        <el-form-item :label="$root.labels.csv_delimiter + ':'">
          <el-select :placeholder="$root.labels.csv_delimiter" v-model="delimiter" @change="changeFields">
            <el-option
                v-for="item in delimiters"
                :key="item.value"
                :label="item.label"
                :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>

        <!-- Export Columns -->
        <template v-for="field in data.fields">
          <el-checkbox v-model="field.checked" checked :label="field.label" border @change="changeFields"></el-checkbox>
        </template>
      </el-form>

    </div>

    <!-- Dialog Footer -->
    <div class="am-dialog-footer">
      <div class="am-dialog-footer-actions">
        <el-row>
          <el-col :sm="24" class="align-right">
            <el-button
                type=""
                @click="closeDialog"
                class=""
            >
              {{ $root.labels.cancel }}
            </el-button>
            <el-button
                type="primary"
                class="am-dialog-create"
                @click="closeDialog"
                native-type='submit'
            >
              {{ $root.labels.export }}
            </el-button>
          </el-col>
        </el-row>
      </div>
    </div>

  </div>
</template>

<script>
  import imageMixin from '../../../js/common/mixins/imageMixin'
  import dateMixin from '../../../js/common/mixins/dateMixin'

  export default {

    mixins: [imageMixin, dateMixin],

    props: {
      data: null,
      action: null
    },

    data () {
      return {
        delimiter: ',',
        delimiters: [
          {
            label: this.$root.labels.csv_delimiter_comma,
            value: ','
          },
          {
            label: this.$root.labels.csv_delimiter_semicolon,
            value: ';'
          }
        ]
      }
    },

    updated: !AMELIA_LITE_VERSION ? function () {
      this.inlineSVG()
    } : function () {},

    mounted: !AMELIA_LITE_VERSION ? function () {
      this.$emit('updateAction', this.getAction())
      this.inlineSVG()
    } : function () {},

    methods: {
      changeFields: !AMELIA_LITE_VERSION ? function () {
        this.$emit('updateAction', this.getAction())
      } : function () {},

      closeDialog: !AMELIA_LITE_VERSION ? function () {
        this.$emit('closeDialogExport')
      } : function () {},

      getAction: !AMELIA_LITE_VERSION ? function () {
        let params = []

        for (let paramKey in this.data) {
          if (this.data.hasOwnProperty(paramKey)) {
            if (this.data[paramKey] instanceof Array || this.data[paramKey] instanceof Object) {
              let arrayParams = Object.keys(this.data[paramKey]).map(key => this.data[paramKey][key])

              for (let index in arrayParams) {
                if (arrayParams[index] !== '') {
                  let value = ''

                  if ((arrayParams[index] instanceof Date)) {
                    // Report dates
                    value = (arrayParams[index] instanceof Date) ? this.getDatabaseFormattedDate(arrayParams[index]) : arrayParams[index]
                  } else if ((arrayParams[index] instanceof Object) && arrayParams[index]['checked'] === true) {
                    // Report params
                    value = arrayParams[index]['value']
                  } else {
                    value = arrayParams[index]
                  }

                  if (value !== '') {
                    params.push(paramKey + '[' + index + ']' + '=' + encodeURIComponent(value))
                  }
                }
              }
            } else {
              if (this.data[paramKey] !== '') {
                params.push(paramKey + '=' + encodeURIComponent(this.data[paramKey]))
              }
            }
          }
        }

        return this.action + '&' + params.join('&') + '&delimiter=' + this.delimiter
      } : function () {}
    },

    components: {}
  }
</script>