export default {
  data: () => ({}),

  methods: {
    filterResponseData: AMELIA_LITE_VERSION ? function (response) {
      response.data.data.employees = response.data.data.employees.slice(0, 1)
      response.data.data.categories = response.data.data.categories.slice(0, 1)

      let availableEmployeeServices = []

      if (response.data.data.categories.length) {
        response.data.data.categories[0].serviceList = response.data.data.categories[0].serviceList.slice(0, 4)

        response.data.data.categories[0].serviceList.forEach(function (serItem) {
          serItem.maxCapacity = 1
          serItem.minCapacity = 1
          serItem.timeAfter = ''
          serItem.timeBefore = ''
        })

        availableEmployeeServices = response.data.data.categories[0].serviceList.map(service => service.id)
      }

      if (response.data.data.employees.length) {
        response.data.data.employees[0].serviceList = response.data.data.employees[0].serviceList.filter(
          service => availableEmployeeServices.indexOf(service.id) !== -1
        )

        response.data.data.employees[0].locationId = null

        response.data.data.employees[0].serviceList.forEach(function (serItem) {
          serItem.maxCapacity = 1
          serItem.minCapacity = 1
          serItem.timeAfter = ''
          serItem.timeBefore = ''
        })
      }
    } : function (response) {
    }
  }
}
