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

const routes = [
    { path: '/', redirect: '/home', name: 'root' },
    { path: '/home', component:  home, name: 'home' },
    { path: '/login', component:  login, name: 'login' },
    { path: '/dashboard', component: dashboard, name: 'dashboard' },
    { path: '/profile', component: profile, name: 'profile' },
    { path: '/new-user', component: newUser, name: 'newUser' }
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
    next();
});
