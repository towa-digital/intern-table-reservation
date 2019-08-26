import Vue from 'vue'
import App from './App.vue'
import VueResource from 'vue-resource'
import store from './store'
import VueMq from 'vue-mq'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faMinus} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

Vue.use(VueMq, {
  breakpoints: {
    mobile: 450,
    tablet: 1100,
    laptop: 1250,
    lg: Infinity,
  }
})

Vue.use(VueResource);


library.add(faMinus)
 
Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.http.options.root = 'http://localhost/wordpress/wp-json';

Vue.config.productionTip = false

new Vue({
  store,
  render: h => h(App),
}).$mount('#app')
