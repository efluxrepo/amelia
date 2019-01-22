/* eslint-disable */
import VueRouter from 'vue-router'

let routes = [
  {
    path: '/dashboard',
    name: 'wpamelia-dashboard',
    meta: {title: wpAmeliaLabels.dashboard},
    component: () => import(/* webpackChunkName: "dashboard" */ '../../views/backend/dashboard/Dashboard')
  },

  {
    path: '/calendar',
    name: 'wpamelia-calendar',
    meta: {title: wpAmeliaLabels.calendar},
    component: () => import(/* webpackChunkName: "calendar" */ '../../views/backend/calendar/Calendar')
  },

  {
    path: '/appointments',
    name: 'wpamelia-appointments',
    meta: {title: wpAmeliaLabels.appointments},
    component: () => import(/* webpackChunkName: "appointments" */ '../../views/backend/appointments/Appointments')
  },

  {
    path: '/employees',
    name: 'wpamelia-employees',
    meta: {title: wpAmeliaLabels.employees},
    component: () => import(/* webpackChunkName: "employees" */ '../../views/backend/employees/Employees')
  },

  {
    path: '/services',
    name: 'wpamelia-services',
    meta: {title: wpAmeliaLabels.services},
    component: () => import(/* webpackChunkName: "services" */ '../../views/backend/services/Services')
  },

  {
    path: '/locations',
    name: 'wpamelia-locations',
    meta: {title: wpAmeliaLabels.locations},
    component: () => import(/* webpackChunkName: "locations" */ '../../views/backend/locations/Locations')
  },

  {
    path: '/customers',
    name: 'wpamelia-customers',
    meta: {title: wpAmeliaLabels.customers},
    component: () => import(/* webpackChunkName: "customers" */ '../../views/backend/customers/Customers')
  },

  {
    path: '/email-notifications',
    name: 'wpamelia-emailnotifications',
    meta: {title: wpAmeliaLabels.email_notifications},
    component: () => import(/* webpackChunkName: "emailnotifications" */ '../../views/backend/emailnotifications/EmailNotifications')
  },

  {
    path: '/customize',
    name: 'wpamelia-customize',
    meta: {title: wpAmeliaLabels.customize},
    component: () => import(/* webpackChunkName: "customize" */ '../../views/backend/customize/Customize')
  },
  {
    path: '/finance',
    name: 'wpamelia-finance',
    meta: {title: wpAmeliaLabels.finance},
    component: () => import(/* webpackChunkName: "finance" */ '../../views/backend/finance/Finance')
  },
  {
    path: '/settings',
    name: 'wpamelia-settings',
    meta: {title: wpAmeliaLabels.settings},
    component: () => import(/* webpackChunkName: "settings" */ '../../views/backend/settings/Settings')
  }
]

export default new VueRouter({
  routes,
  linkActiveClass: 'is-active'
})
