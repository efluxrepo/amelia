export default {

  data: () => ({}),

  methods: {
    setEntitiesFilter () {
      let $this = this

      let entitiesChoices = {
        categories: {services: [], employees: [], locations: []},
        locations: {services: [], employees: [], categories: []},
        services: {categories: [], employees: [], locations: []},
        employees: {categories: [], services: [], locations: []}
      }

      for (let key in entitiesChoices) {
        this.options.entitiesChoices[key] = {
          selection: '',
          dependents: entitiesChoices[key]
        }
      }

      let setOptions = function (object, key, value) {
        if (!object[key]) {
          object[key] = []
        }

        if (object[key].indexOf(value) === -1) {
          object[key].push(value)
        }
      }

      this.options.entities.employees.forEach(function (empItem) {
        empItem.serviceList.forEach(function (empSerItem) {
          $this.options.entities.services.forEach(function (serItem) {
            if (serItem.id === empSerItem.id) {
              setOptions($this.options.entitiesChoices.categories.dependents.employees, serItem.categoryId, empItem.id)
              setOptions($this.options.entitiesChoices.categories.dependents.locations, serItem.categoryId, empItem.locationId)
              setOptions($this.options.entitiesChoices.categories.dependents.services, serItem.categoryId, serItem.id)
              setOptions($this.options.entitiesChoices.employees.dependents.categories, empItem.id, serItem.categoryId)
              setOptions($this.options.entitiesChoices.services.dependents.categories, serItem.id, serItem.categoryId)
              setOptions($this.options.entitiesChoices.locations.dependents.categories, empItem.locationId, serItem.categoryId)
            }
          })

          setOptions($this.options.entitiesChoices.employees.dependents.services, empItem.id, empSerItem.id)
          setOptions($this.options.entitiesChoices.services.dependents.employees, empSerItem.id, empItem.id)
          setOptions($this.options.entitiesChoices.services.dependents.locations, empSerItem.id, empItem.locationId)
          setOptions($this.options.entitiesChoices.locations.dependents.services, empItem.locationId, empSerItem.id)
        })

        setOptions($this.options.entitiesChoices.employees.dependents.locations, empItem.id, empItem.locationId)
        setOptions($this.options.entitiesChoices.locations.dependents.employees, empItem.locationId, empItem.id)
      })
    },

    resetEntitiesFilter () {
      let $this = this
      let entities = ['employees', 'services', 'locations', 'categories']

      entities.forEach(function (entityKey) {
        $this.options.entities[entityKey].forEach(function (item) {
          item.disabled = false
        })
      })
    },

    getLocationById (id) {
      return this.options.entities.locations.find(location => location.id === id) || null
    },

    getCustomerById (id) {
      return this.options.entities.customers.find(customer => customer.id === id) || null
    },

    getProviderById (id) {
      return this.options.entities.employees.find(employee => employee.id === id) || null
    },

    getServiceById (id) {
      return this.options.entities.services.find(service => service.id === id) || null
    },

    getServiceProviders (serviceId) {
      return this.options.entities.employees.filter(
        employee => employee.serviceList.map(
          service => service.id
        ).indexOf(serviceId) !== -1
      )
    },

    getServicesFromCategories () {
      let services = []

      this.options.entities.categories.map(category => category.serviceList).forEach(function (serviceList) {
        services = services.concat(serviceList)
      })

      return services
    },

    getCategoryServices (categoryId) {
      return this.options.entities.categories.find(category => category.id === categoryId).serviceList
    },

    getCustomerInfo (booking) {
      return booking.info ? JSON.parse(booking.info) : this.getCustomerById(booking.customerId)
    }
  },

  computed: {
    visibleLocations () {
      return this.options.entities.locations.filter(location => location.status === 'visible')
    },

    visibleEmployees () {
      return this.options.entities.employees.filter(employee => employee.status === 'visible')
    },

    visibleCustomers () {
      return this.options.entities.customers.filter(customer => customer.status === 'visible')
    },

    visibleServices () {
      return this.options.entities.services.filter(service => service.status === 'visible')
    }
  }

}
