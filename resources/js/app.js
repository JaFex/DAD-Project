/*jshint esversion: 6 */
require('./bootstrap');

import VueRouter from 'vue-router';
import BootstrapVue from 'bootstrap-vue';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Toasted from 'vue-toasted';
import Vuelidate from 'vuelidate';
import VueSocketio from 'vue-socket.io';
import store from './stores/global-store';


window.Vue = require('vue');
Vue.use(VueRouter);
Vue.use(BootstrapVue);
Vue.use(Toasted);
Vue.use(Vuelidate);
Vue.use(new VueSocketio({
    debug: true,
    connection: 'http://192.168.10.10:8080'
}));

const home = Vue.component('home', require('./components/index.vue'));
const dashboard = Vue.component('dashboard', require('./components/dashboard.vue'));
const login = Vue.component('login', require('./components/login.vue'));
const profile = Vue.component('profile', require('./components/profile.vue'));
const newUser = Vue.component('newUser', require('./components/newUser.vue'));
const orders = Vue.component('orders', require('./components/ordersList.vue'));
const meals = Vue.component('meals', require('./components/mealsList.vue'));
const message = Vue.component('message', require('./components/message.vue'));
const notfound = Vue.component('notfound', require('./components/erro/404.vue'));

const routes = [
    { path: '/', meta: { title: 'Restaurant' }, redirect: '/home', name: 'root' },
    { path: '/home', meta: { title: 'Restaurant' }, component:  home, name: 'home' },
    { path: '/login', meta: { title: 'Restaurant' }, component:  login, name: 'login' },
    { path: '/dashboard', meta: { title: 'Restaurant' }, component: dashboard, name: 'dashboard' },
    { path: '/profile', meta: { title: 'Restaurant' }, component: profile, name: 'profile' },
    { path: '/new-user', meta: { title: 'Restaurant' }, component: newUser, name: 'newUser' },
    { path: '/orders', meta: { title: 'Restaurant' }, component: orders, name: 'Cooks' },
    { path: '/meals', meta: { title: 'Restaurant' }, component: meals, name: 'Waters' },
    { path: '/message', meta: { title: 'Restaurant' }, component: message, name: 'Message' },
    { path: '/404', meta: { title: 'Not Found 404' }, component: notfound, name: 'NotFound' },
    { path: '*', redirect: '/404' }
];

const router = new VueRouter({
    routes:routes
});

const app = new Vue({
    router,
    store,
    data: {},
    created() {
        //console.log('-----');
        //console.log(this.$store.state.user);
        this.$store.commit('loadTokenAndUserFromSession');
        console.log(this.$store.state.user);
    },
    sockets:{
        connect(){
            console.log('socket connected (socket ID = '+this.$socket.id+')');
            if(this.$store.state.user != null && this.$store.state.user.shift_active){
                this.$socket.emit('user_enter', this.$store.state.user);
            }
        },
        msg_to_managers_from_server(dataFromServer){
            this.$toasted.show(dataFromServer, { 
                position: "top-right", 
                duration: 15000,
                className: ['info']
           });
        }
    }
}).$mount('#app');

router.beforeEach((to, from, next) => {
    let protectedRoutes = ['dashboard', 'profile', 'newUser'];
    if ((protectedRoutes.includes(to.name))) {
        if (!store.state.user) {
            next("/login");
            return;
        }
    }
    document.title = to.meta.title
    next();
});
