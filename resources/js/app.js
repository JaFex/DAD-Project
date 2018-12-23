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
const newUser = Vue.component('newUser', require('./components/manager/newUser.vue'));
const orders = Vue.component('orders', require('./components/cook/ordersList.vue'));
const meals = Vue.component('meals', require('./components/waiter/mealsList.vue'));
const summaryMealList = Vue.component('summaryMealList', require('./components/waiter/summaryMealList.vue'));
const ordersToDeliver = Vue.component('ordersToDeliver', require('./components/waiter/ordersToDeliver.vue'));
const invoices = Vue.component('invoices', require('./components/cashier/invoicesList.vue'));
const invoicesListFile = Vue.component('invoices', require('./components/cashier/invoicesListFile.vue'));
const message = Vue.component('message', require('./components/message.vue'));
const tables = Vue.component('tables', require('./components/manager/listTables.vue'));
const items = Vue.component('items', require('./components/manager/listItems.vue'));
const users = Vue.component('users', require('./components/manager/listUsers.vue'));
const notfound = Vue.component('notfound', require('./components/erro/404.vue'));

const routes = [
    { path: '/', redirect: '/home', name: 'root' },
    { path: '/home', component:  home, name: 'home' },
    { path: '/login', component:  login, name: 'login' },
    { path: '/dashboard', component: dashboard, name: 'dashboard' },
    { path: '/profile', component: profile, name: 'profile' },
    { path: '/users/create', component: newUser, name: 'newUser' },
    { path: '/orders', component: orders, name: 'Cooks' },
    { path: '/meals', component: meals, name: 'Waiters' },
    { path: '/summary', component: summaryMealList, name: 'Waiters Summary' },
    { path: '/orders-to-deliver', component: ordersToDeliver, name: 'Waiters Deliver' },
    { path: '/invoices', component: invoices, name: 'Cashier' },
    { path: '/invoicesPDF', component: invoicesListFile, name: 'Invoice PDF' },
    { path: '/message', component: message, name: 'Message' },
    { path: '/tables', component: tables, name: 'Tables' },
    { path: '/items', component: items, name: 'Items' },
    { path: '/users', component: users, name: 'Users' },

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
        //console.log(this.$store.state.user);
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

    //Routes for all workers
    let protectedRoutes = ['dashboard', 'profile', 'message'];

    //Routes only for cooks
    let cooksRoutes = ['orders']; 

    //Routes only for waiters
    let waitersRoutes = ['meals'];

    //Routes only for cashiers
    let cashiersRoutes = ['invoices', 'invoicesPDF'];

    //Routes only for managers
    let managersRoutes = ['newUser', 'users', 'tables', 'items'];

    //Check worker login
    if (protectedRoutes.includes(to.name) || managersRoutes.includes(to.name) || cashiersRoutes.includes(to.name) || waitersRoutes.includes(to.name) || cooksRoutes.includes(to.name)) {
        if (!store.state.user) {
            next("/login");
            return;
        }
    }

    //Check if its cook
    if(cooksRoutes.includes(to.name)){
        if(store.state.user.type !== 'cook'){
            next("/dashboard");
            return;
        }
    }

    //Check if its waiter
    if(waitersRoutes.includes(to.name)){
        if(store.state.user.type !== 'waiter'){
            next("/dashboard");
            return;
        }
    }

    //Check if its cashier
    if(cashiersRoutes.includes(to.name)){
        if(store.state.user.type !== 'cashier'){
            next("/dashboard");
            return;
        }
    }

    //Check if its manager
    if(managersRoutes.includes(to.name)){
        if(store.state.user.type !== 'manager'){
            next("/dashboard");
            return;
        }
    }

    document.title = to.meta.title !== undefined ? to.meta.title : 'Restaurant';
    next();
});
