import moment from 'moment'

export default {

  data: () => ({
    formatPHPtoMomentMap: {
      d: 'DD',
      D: 'ddd',
      j: 'D',
      l: 'dddd',
      N: 'E',
      w: 'd',
      W: 'W',
      F: 'MMMM',
      m: 'MM',
      M: 'MMM',
      n: 'M',
      o: 'GGGG',
      Y: 'YYYY',
      y: 'YY',
      a: 'a',
      A: 'A',
      g: 'h',
      G: 'H',
      h: 'hh',
      H: 'HH',
      i: 'mm',
      s: 'ss',
      O: 'ZZ',
      P: 'Z',
      c: 'YYYY-MM-DD[T]HH:mm:ssZ',
      r: 'ddd, DD MMM YYYY HH:mm:ss ZZ',
      U: 'X',
      S: 'o'
    },

    formatPHPtoDatePickerMap: {
      d: 'dd',
      j: 'd',
      M: 'MMM',
      F: 'MMMM',
      m: 'MM',
      n: 'M',
      y: 'yy',
      Y: 'yyyy',
      g: 'HH',
      H: 'HH',
      i: 'mm',
      a: 'A',
      A: 'A',
      s: 'ss'
    },

    formatEx: /[dDjlNwWFmMntoYyaAgGhHisOPcrUS]/g
  }),

  methods: {
    getDate (date) {
      return moment(date, 'YYYY-MM-DD').toDate()
    },

    getDatabaseFormattedDate (date) {
      return moment(date, 'YYYY-MM-DD').format('YYYY-MM-DD')
    },

    getFrontedFormattedDate (date) {
      return moment(date, 'YYYY-MM-DD').format(this.momentDateFormat)
    },

    getFrontedFormattedDateTime (datetime) {
      return moment(datetime, 'YYYY-MM-DD HH:mm:ss').format(
        this.momentDateFormat + ' ' + this.momentTimeFormat
      )
    },

    getFrontedFormattedTime (time) {
      return moment(time, 'HH:mm:ss').format(this.momentTimeFormat)
    },

    getDatePickerFirstDayOfWeek () {
      // Sunday index on WordPress is 0 and in DatePicker is 1
      return this.$root.settings.wordpress.startOfWeek + 1
    },

    getWordPressFirstDayOfWeek () {
      return this.$root.settings.wordpress.startOfWeek
    },

    getTimeSlotLength () {
      return this.$root.settings.general.timeSlotLength
    },

    getDatePickerInitRange () {
      return {
        start: moment().toDate(),
        end: moment().add(6, 'days').toDate()
      }
    },

    getFrontedFormattedTimeIncreased (time, seconds) {
      return moment(time, 'HH:mm:ss').add(seconds, 'seconds').format(this.momentTimeFormat)
    },

    getTime (datetime) {
      return moment(datetime, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')
    }
  },

  computed: {
    momentTimeFormat () {
      let that = this

      // Fix for French "G \h i \m\i\n" format
      if (this.$root.settings.wordpress.timeFormat === 'G \\h i \\m\\i\\n') {
        return 'HH:mm'
      }

      return this.$root.settings.wordpress.timeFormat.replace(this.formatEx, function (phpStr) {
        return that.formatPHPtoMomentMap[phpStr]
      })
    },

    momentDateFormat () {
      let that = this
      return this.$root.settings.wordpress.dateFormat.replace(this.formatEx, function (phpStr) {
        return that.formatPHPtoMomentMap[phpStr]
      })
    },

    vCalendarFormats () {
      return {
        input: [this.momentDateFormat, 'YYYY-MM-DD', 'YYYY/MM/DD']
      }
    }
  }

}
