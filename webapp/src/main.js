import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import store from './store'
import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import VueResource from 'vue-resource';
import vuetify from './plugins/vuetify';
import VueSidebarMenu from 'vue-sidebar-menu'
import 'vue-sidebar-menu/dist/vue-sidebar-menu.css'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faTrashAlt, faPlusCircle } from '@fortawesome/free-solid-svg-icons'
import { faCarrot } from '@fortawesome/free-solid-svg-icons'
import { faExclamationCircle } from '@fortawesome/free-solid-svg-icons'
import { faMapMarkerAlt } from '@fortawesome/free-solid-svg-icons'
import { faHome } from '@fortawesome/free-solid-svg-icons'
import { faUser } from '@fortawesome/free-solid-svg-icons'
import { faSignOutAlt } from '@fortawesome/free-solid-svg-icons'
import { faHeart } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import VueSweetalert2 from 'vue-sweetalert2';
import VueCookies from 'vue-cookies'

// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';

library.add(faTrashAlt)
library.add(faCarrot)
library.add(faExclamationCircle)
library.add(faMapMarkerAlt)
library.add(faHome)
library.add(faUser)
library.add(faSignOutAlt)
library.add(faHeart)
library.add(faPlusCircle)

Vue.use(require('vue-moment'))
Vue.use(VueCookies)
Vue.use(VueSweetalert2);
Vue.use(VueSidebarMenu)
Vue.use(VueResource)

Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('l-map', LMap);
Vue.component('l-tile-layer', LTileLayer);
Vue.component('l-marker', LMarker);

Vue.config.productionTip = false
delete L.Icon.Default.prototype._getIconUrl

// eslint-disable-next-line  
L.Icon.Default.mergeOptions({  
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),  
  iconUrl: require('leaflet/dist/images/marker-icon.png'),  
  shadowUrl: require('leaflet/dist/images/marker-shadow.png')  
})
Vue.prototype.$user = ''

// // Request Permission of Notifications
// messaging.requestPermission().then(() => {
//   console.log('Notification permission granted.');
//   // Get Token
//   messaging.getToken().then((token) => {
//     localStorage.setItem('notificationToken', token)
//     Vue.prototype.$notify = token
//   })
// }).catch((err) => {
//   console.log('Unable to get permission to notify.', err);
// });

//Cookie config
Vue.$cookies.config('30d')

new Vue({
  data: {
    user: '',
  },
  router,
  store,
  vuetify,
  render: function (h) { return h(App) }
}).$mount('#app')
