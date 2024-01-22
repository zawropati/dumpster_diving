import Vue from 'vue'
import VueRouter from 'vue-router'
import About from '../views/About.vue'
import Signup from '../views/Signup.vue'
import Login from '../views/Login.vue'
import Profile from '../views/Profile.vue'
import NotFound from '../components/notFound.vue'
import NewLocation from '../views/NewLocation.vue'

Vue.use(VueRouter)

  const routes = [
  {
    path: '/about',
    name: 'About',
    component: About
  },
  {
    path: '/newLocation',
    name: 'newLocation',
    component: NewLocation
  },
  {
    path: '/signup',
    name: 'Signup',
    component: Signup,
    beforeEnter(to, from, next) {
      if (localStorage.getItem('userId')) {
        next('/') 
      } else {
        next()
      }
    }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    beforeEnter(to, from, next) {
      if (localStorage.getItem('userId')) {
        next() 
      } else {
        next('/notFound')
      }
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    beforeEnter(to, from, next) {
      if (localStorage.getItem('userId')) {
        next('/') 
      } else {
        next()
      }
    }
  },
  {
    path :'*',
    component: NotFound
  },
  {
    path: '/',
    name: 'Dumpsters',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: function () {
      return import(/* webpackChunkName: "about" */ '../views/Dumpsters.vue')
    }
  }
]

const router = new VueRouter({
  routes
})

export default router
