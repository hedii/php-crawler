import 'babel-polyfill'
import 'bootstrap'
import Vue from 'vue'
import router from './router'
import './helpers/vue-filters'

new Vue({
  router,
  data () {
    return { user: {} }
  }
}).$mount('#app')
