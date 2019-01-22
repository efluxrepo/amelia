/* eslint-disable no-undef */
import Vue from 'vue'
import axios from 'axios'
import ElementUI from 'element-ui'
import locale from 'element-ui/lib/locale/lang/en'
import VCalendar from 'v-calendar'
import 'babel-polyfill'
import moment from 'moment/moment'

// eslint-disable-next-line no-undef, camelcase
__webpack_public_path__ = wpAmeliaUrls.wpAmeliaPluginURL + '/public/'

let components = null

const Booking = () => import(/* webpackChunkName: "booking" */ '../../views/frontend/booking/Booking.vue')

if (!AMELIA_LITE_VERSION) {
  const Search = () => import(/* webpackChunkName: "search" */ '../../views/frontend/search/Search.vue')
  const Catalog = () => import(/* webpackChunkName: "catalog" */ '../../views/frontend/catalog/Catalog.vue')
  const Category = () => import(/* webpackChunkName: "category" */ '../../views/frontend/catalog/Category.vue')
  const Service = () => import(/* webpackChunkName: "service" */ '../../views/frontend/catalog/Service.vue')
  components = {Booking, Search, Catalog, Category, Service}
} else {
  components = {Booking}
}

Vue.prototype.$http = axios
Vue.prototype.$http.defaults.headers.common = {
  'X-Requested-With': 'XMLHttpRequest'
}

Vue.use(ElementUI, {locale})
Vue.use(VCalendar, {
  firstDayOfWeek: window.wpAmeliaSettings.wordpress.startOfWeek + 1,
  locale: window.localeLanguage.replace('_', '-')
})

function ameliaLoading () {
  // eslint-disable-next-line no-new
  var ameliaContainers = document.getElementsByClassName('amelia-frontend')
  var ameliaVueInstances = []

  for (var i = 0; i < ameliaContainers.length; i++) {
    ameliaVueInstances.push(
      new Vue({
        el: ameliaContainers[i],

        components: components,

        data: {
          getAjaxUrl: wpAmeliaUrls.wpAmeliaPluginAjaxURL,
          getUrl: wpAmeliaUrls.wpAmeliaPluginURL,
          isLite: AMELIA_LITE_VERSION,
          labels: window.wpAmeliaLabels,
          settings: window.wpAmeliaSettings,
          shortcodeData: {
            enabled: false,
            booking: {
              category: '',
              service: '',
              employee: '',
              location: ''
            },
            search: {
              today: ''
            },
            hasCategoryShortcode: '',
            hasBookingShortcode: '',
            counter: ''
          }
        },

        mounted () {
          moment.locale(window.localeLanguage)

          let bookingData = typeof window.bookingEntitiesIds !== 'undefined' ? window.bookingEntitiesIds.shift() : null

          this.shortcodeData.booking = bookingData ? {
            category: 'category' in bookingData ? parseInt(bookingData.category) : null,
            service: 'service' in bookingData ? parseInt(bookingData.service) : null,
            employee: 'employee' in bookingData ? parseInt(bookingData.employee) : null,
            location: 'location' in bookingData ? parseInt(bookingData.location) : null
          } : null

          this.shortcodeData.searchToday = typeof window.searchToday !== 'undefined' ? window.searchToday : null

          this.shortcodeData.counter = (bookingData ? bookingData.counter : '')
          this.shortcodeData.hasCategoryShortcode = typeof window.hasCategoryShortcode !== 'undefined' ? window.hasCategoryShortcode : ''
          this.shortcodeData.hasBookingShortcode = typeof window.hasBookingShortcode !== 'undefined' ? window.hasBookingShortcode : ''
          this.shortcodeData.enabled = this.shortcodeData.booking !== null && !Object.values(this.shortcodeData.booking).every(x => (x === null || x === '' || isNaN(x)))
        }
      })
    )
  }
}

if (document.getElementsByClassName('amelia-frontend').length === 0) {
  document.addEventListener('DOMContentLoaded', function () {
    ameliaLoading()
  })
} else {
  ameliaLoading()
}
