<template>
  <div class="am-pagination am-section" v-show="visible">
    <el-row>

      <!-- Message -->
      <el-col :sm="8">
        <p>{{ paginationMessage }}</p>
      </el-col>

      <!-- Pagination -->
      <el-col :sm="16">
        <el-pagination
            layout="prev, pager, next"
            :page-size=$root.settings.general.itemsPerPage
            :total=count
            :current-page.sync=params.page
            @current-change="change"
        >
        </el-pagination>
      </el-col>

    </el-row>
  </div>
</template>

<script>
  export default {
    mixins: [],

    props: {
      params: null,
      visible: false,
      label: '',
      count: 0
    },

    data () {
      return {
        currentPage: null
      }
    },

    methods: {
      change () {
        this.$emit('change')
      }
    },

    computed: {
      paginationMessage () {
        return this.$root.labels.showing + ' ' +
          (this.params.page * this.$root.settings.general.itemsPerPage - this.$root.settings.general.itemsPerPage + 1) +
          ' ' + this.$root.labels.to + ' ' + (this.params.page * this.$root.settings.general.itemsPerPage >
          this.count ? this.count : this.params.page * this.$root.settings.general.itemsPerPage) +
          ' ' + this.$root.labels.of + ' ' + this.count + ' ' + this.label
      }
    }
  }
</script>