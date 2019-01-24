import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './routes'
import api from '../api'

Vue.use(VueRouter)

const router = new VueRouter({
  path: `/dashboard/`,
  //mode: 'history',
  routes
})

router.beforeEach((to, from, next) => {
  api.users.me().then(response => {
    if (response.status === 200) {
      router.app.$root.user = response.data.data
      next()
    }
  }).catch(error => {
    console.log(error)
    next()
  })
})

export default router
