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
    connection: 'http://192.168.10.1:8080'
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
const mealsManager = Vue.component('mealsManager', require('./components/manager/listMeals.vue'));
const invoicesManager = Vue.component('invoicesManager', require('./components/manager/listInvoices.vue'));

const notAllowed = Vue.component('notAllowed', require('./components/erro/401.vue'));
const notfound = Vue.component('notfound', require('./components/erro/404.vue'));

const routes = [
    { path: '/', redirect: '/home', name: 'Root' },
    { path: '/home', component:  home, name: 'Home' },
    { path: '/login', component:  login, name: 'Login' },
    { path: '/dashboard', component: dashboard, name: 'User Dashboard' },
    { path: '/profile', component: profile, name: 'User Profile' },
    { path: '/users/create', component: newUser, name: 'Manager New User' },
    { path: '/orders', component: orders, name: 'Cooks Orders' },
    { path: '/meals', component: meals, name: 'Waiter Meals' },
    { path: '/summary', component: summaryMealList, name: 'Waiter Summary' },
    { path: '/orders-to-deliver', component: ordersToDeliver, name: 'Waiter Deliver' },
    { path: '/invoices', component: invoices, name: 'Cashier Invoices' },
    { path: '/invoicesPDF', component: invoicesListFile, name: 'Cashier Invoices PDF' },
    { path: '/message', component: message, name: 'User Message' },
    { path: '/tables', component: tables, name: 'Manager Tables' },
    { path: '/items', component: items, name: 'Manager Items' },
    { path: '/users', component: users, name: 'Manager Users' },
    { path: '/meals/filter', component: mealsManager, name: 'Manager Meals' },
    { path: '/invoices/filter', component: invoicesManager, name: 'Manager Invoices' },
    { path: '/401', meta: { title: 'Not Allowed 401' }, component: notAllowed, name: 'NotAllowed' },
    { path: '/404', meta: { title: 'Not Found 404' }, component: notfound, name: 'NotFound' },
    { path: '*', redirect: '/404' }
];

const router = new VueRouter({
    mode: 'history',
    routes:routes
});

router.beforeEach((to, from, next) => {
    console.log('---------------------------------------------------------------------------------------------------------------');
    //Routes for not auth
    let notAuthRoutes = ['Root', 'Home', 'Login'];

    //Routes for all workers
    let protectedRoutes = ['User Dashboard', 'User Profile', 'User Message'];

    //Routes only for cooks
    let cooksRoutes = ['Cooks Orders']; 

    //Routes only for waiters
    let waitersRoutes = ['Waiter Meals', 'Waiter Summary', 'Waiter Deliver'];

    //Routes only for cashiers
    let cashiersRoutes = ['Cashier Invoices', 'Cashier Invoices PDF'];

    //Routes only for managers
    let managersRoutes = ['Manager New User', 'Manager Users', 'Manager Tables', 'Manager Items', 'Manager Meals', 'Manager Invoices'];

    console.log(to.name+'-----------------------'+!store.state.user+'-----------------------'+!notAuthRoutes.includes(to.name));
    //Check worker login
    if (!store.state.user) {
        if (!notAuthRoutes.includes(to.name)) {
            next("/login");
            return;
        }
        document.title = to.meta.title !== undefined ? to.meta.title : 'Restaurant';
        next();
        return;
    }
    if(store.state.user && to.name === 'Login') {
        next("/dashboard");
        return;
    }

    //Check if its cook
    if(cooksRoutes.includes(to.name)){
        if(store.state.user.type !== 'cook'){
            next("/401");
            return;
        }
    }

    //Check if its waiter
    if(waitersRoutes.includes(to.name)){
        if(store.state.user.type !== 'waiter'){
            next("/401");
            return;
        }
    }

    //Check if its cashier
    if(cashiersRoutes.includes(to.name)){
        if(store.state.user.type !== 'cashier'){
            next("/401");
            return;
        }
    }

    //Check if its manager
    if(managersRoutes.includes(to.name)){
        if(store.state.user.type !== 'manager'){
            next("/401");
            return;
        }
    }

    document.title = to.meta.title !== undefined ? to.meta.title : 'Restaurant';
    next();
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


