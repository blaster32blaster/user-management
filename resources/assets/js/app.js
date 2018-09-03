
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
// require("https://unpkg.com/vue-toasted");

window.Vue = require('vue');
import VModal from 'vue-js-modal'

Vue.use(VModal)
// Vue.use(Toasted)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('auth-clients', require('./components/passport/AuthorizedClients.vue'));
Vue.component('clients', require('./components/passport/Clients.vue'));
Vue.component('client', require('./components/passport/Client.vue'));
Vue.component('role', require('./components/passport/Role.vue'));
Vue.component('access-tokens', require('./components/passport/PersonalAccessTokens.vue'));
Vue.component('invitation-modal', require('./components/general/InvitationModal'));

const app = new Vue({
    el: '#app'
});
