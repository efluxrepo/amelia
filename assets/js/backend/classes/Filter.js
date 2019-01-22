class Filter {
  constructor (data, entities) {
    this.data = data
    this.entities = entities
  }

  /**
   * Filter all fields
   */
  process (selectionKey, selectionId, fields) {
    let $this = this

    this.data[selectionKey].selection = selectionId

    if (selectionId === '') {
      return
    }

    fields.forEach(function (name) {
      $this.filterOptions(
        $this.entities[name],
        $this.getIntersectedData(name, Object.keys($this.data[name].dependents)),
        selectionId
      )
    })
  }

  getIntersectedData (type, dependents) {
    let data = this.data
    let options = []

    // for each filter type, return possible values for selected filter if exist or empty array
    dependents.forEach(function (dependent) {
      let id = data[dependent].selection
      let object = data[dependent].dependents[type]

      options.push(
        (id && object[id]) ? object[id] : []
      )
    })

    let intersection = []

    // find intersected values
    options.forEach(function (options) {
      if (intersection.length && options.length) {
        intersection = options.filter(function (n) {
          return intersection.indexOf(n) > -1
        })
      } else {
        intersection = intersection.concat(options)
      }
    })

    return intersection
  }

  filterOptions (list, enabledItems, selection) {
    list.forEach(function (item) {
      item.disabled = enabledItems.indexOf(item.id) === -1 && selection !== ''
    })
  }
}

export default Filter
