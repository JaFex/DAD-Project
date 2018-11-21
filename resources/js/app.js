require('./bootstrap');

import VueRouter from 'vue-router';

window.Vue = require('vue');
Vue.use(VueRouter);

const routes = [
    { path: '/', redirect: '/items' },
    { path: '/items', component: require('./pages/itemsList.vue') },
    { path: '/login', component: require('./pages/login.vue') },
]
const router = new VueRouter({
    routes:routes
 })

const app = new Vue({
    router,
    el: '#app'
});
