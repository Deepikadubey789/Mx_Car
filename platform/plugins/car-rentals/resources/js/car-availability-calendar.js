import CarAvailabilityCalendarComponent from './components/CarAvailabilityCalendarComponent.vue'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.component('car-availability-calendar-component', CarAvailabilityCalendarComponent)
    })
}
