require('./bootstrap');

import VueRouter from 'vue-router';
import BootstrapVue from 'bootstrap-vue';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

window.Vue = require('vue');
Vue.use(VueRouter);
Vue.use(BootstrapVue);

import store from './stores/global-store';

const home = Vue.component('home', require('./components/index.vue'));
const dashboard = Vue.component('dashboard', require('./components/dashboard.vue'));
const login = Vue.component('login', require('./components/login.vue'));
const profile = Vue.component('profile', require('./components/profile.vue'));
const newUser = Vue.component('newUser', require('./components/newUser.vue'));
const orders = Vue.component('orders', require('./components/ordersList.vue'));
const meals = Vue.component('meals', require('./components/mealsList.vue'));
const notfound = Vue.component('notfound', require('./components/erro/404.vue'));

const routes = [
    { path: '/', meta: { title: 'Restaurante' }, redirect: '/home', name: 'root' },
    { path: '/home', meta: { title: 'Restaurante' }, component:  home, name: 'home' },
    { path: '/login', meta: { title: 'Restaurante' }, component:  login, name: 'login' },
    { path: '/dashboard', meta: { title: 'Restaurante' }, component: dashboard, name: 'dashboard' },
    { path: '/profile', meta: { title: 'Restaurante' }, component: profile, name: 'profile' },
    { path: '/new-user', meta: { title: 'Restaurante' }, component: newUser, name: 'newUser' },
    { path: '/orders', meta: { title: 'Restaurante' }, component: orders, name: 'Cooks' },
    { path: '/meals', meta: { title: 'Restaurante' }, component: meals, name: 'Waters' },
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
        console.log('-----');
        console.log(this.$store.state.user);
        this.$store.commit('loadTokenAndUserFromSession');
        console.log(this.$store.state.user);
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
