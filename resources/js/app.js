
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

const axios = require('axios');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Vue.component('items-component', require('./components/index.vue'), {props: ['title', 'groupedItems', 'links']});

// const files = require.context('./', true, /\.vue$/i)

// files.keys().map(key => {
//     return Vue.component(_.last(key.split('/')).split('.')[0], files(key))
// })

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data: {
        title: "Restaurant",
        items: []
    },
    methods: {
        loadItems: function(){
            axios.get("/api/items")
                 .then(response => {
                    this.items = response.data.data;
                 })
                 .catch(function (error) {
                    console.log(error);
                });
        }
    },
    computed:{
        groupedItems() {
            var items = [];
            for (var i=0; i<this.items.length; i+=4) {
                items.push(this.items.slice(i,i+4));
           }  
            return items
          }
    },
    mounted(){
        this.loadItems();
    }
});
