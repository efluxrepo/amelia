export default {

  data: () => ({}),

  methods: {
    getUrlQueryParams (url) {
      let queryString = url.indexOf('#') ? url.substring(0, url.indexOf('#')).split('?')[1] : url.split('?')[1]
      let keyValuePairs = queryString.split('&')
      let keyValue = []
      let queryParams = {}
      keyValuePairs.forEach(function (pair) {
        keyValue = pair.split('=')
        queryParams[keyValue[0]] = decodeURIComponent(keyValue[1]).replace(/\+/g, ' ')
      })
      return queryParams
    },

    removeURLParameter (url, parameter) {
      let urlParts = url.split('?')
      if (urlParts.length >= 2) {
        let prefix = encodeURIComponent(parameter) + '='
        let pars = urlParts[1].split(/[&;]/g)

        for (let i = pars.length; i-- > 0;) {
          if (pars[i].lastIndexOf(prefix, 0) !== -1) {
            pars.splice(i, 1)
          }
        }

        url = urlParts[0] + (pars.length > 0 ? '?' + pars.join('&') : '')
        return url
      } else {
        return url
      }
    },

    capitalizeFirstLetter (string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    }
  }
}
