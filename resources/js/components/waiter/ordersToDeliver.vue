<template>
    <div>
        <nav-bar></nav-bar>
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title float-left">My meals and order need to be deliver</h5>
                </div>
                <div class="card-body">
                    <div v-if="showSuccess" class="alert alert-success alert-dismissible">
                        <a href="#" class="close" v-on:click='showSuccess = false'>&times;</a>
                        <strong>{{ messageTitle }}</strong> {{ message }}
                    </div>
                    <div v-if="showErro" class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" v-on:click='showErro = false'>&times;</a>
                        <strong>{{ messageTitle }}</strong> {{ message }}
                    </div>
                    <table class="table">
                        <thead>
                            <tr class="table-active">
                                <th>ID</th>
                                <th>State</th>
                                <th>Table number</th>
                                <th>Start</th>
                                <th>Total price preview</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template  v-for="meal in meals">
                                <tr :key="meal.id">
                                    <td>{{meal.id}}</td>
                                    <td>{{meal.state}}</td>
                                    <td>{{meal.table_number}}</td>
                                    <td>{{meal.start}}</td>
                                    <td>{{meal.total_price_preview}}</td>
                                    <td>
                                        <button v-if="meal.total_price_preview > 0" type="button" class="btn btn-primary" @click.prevent="openOrders(meal);">See all orders</button>
                                    </td>
                                </tr>
                                <tr v-if='currentMeal && meal.id == currentMeal.id' :key="'meal_'+meal.id">
                                    <td colspan="6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title float-left">Order of meal {{ meal.id }}</h5>
                                                <button type="button" class="btn btn-danger float-right" @click.prevent="closeListOrders()">Close</button>
                                            </div>
                                            <div class="card-body">
                                                <ordersMealList  :method="loadOrders" :meal="currentMeal" :orders="orders" :links="linksOrders"></ordersMealList>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template >
                        </tbody>
                    </table>
                    <pagination :method="loadMeals" :links="links"></pagination>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            showSuccess: false,
            showErro: false,
            message: '',
            messageTitle: '',
            meals: {},
            orders: {},
            links: {},
            linksOrders: {},
            restaurantTables: {},
            selectedRestaurantTables: 0,
            currentMeal: '',
            showCreteOrder: false,
            currentMealCreate: '',
        }
    },
    methods: {
        loadMeals: function(url){
            let soft = this;
            axios.get('/api/'+url)
                .then(response => {
                    soft.meals = response.data.data;
                    soft.links = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                    if(soft.$route.query.meal_id) {
                        var meal_id = soft.$route.query.meal_id;
                        soft.meals.forEach(function(meal) {
                            if(meal_id+'' === meal.id+'') {
                                soft.openOrders(meal);
                            }
                        });
                    }
                })
                .catch(function (error) {
                    console.log("loadMeals->"+error);
                });
        },
        openOrders: function(meal) {
            this.currentMeal = meal;
            this.loadOrders('meals/'+meal.id+'/delived');
        },
        loadOrders: function(url){
            var res = url.split("?");
            if(res.length > 2){
                url = res[0]+"?"+res[2];
            }
            axios.get('/api/'+url)
                .then(response => {
                    this.orders = response.data.data;
                    this.linksOrders = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log("loadOrders->"+error);
                });
        },
        closeListOrders: function(){
            this.currentMeal = '';
        },
        reloadMealAndOrder: function() {
            this.loadMeals('meals');
            if(this.currentMeal && this.currentMealCreate && this.currentMeal.id == this.currentMealCreate.id) {
                this.loadOrders('meals/'+this.currentMeal.id+'/delived');
            }
        },
        updateOrder: function(order) {
            if(this.currentMeal && order && this.currentMeal.id == order.meal_id) {
                this.loadOrders('meals/'+this.currentMeal.id+'/delived');
            }
        }
    },
    computed: {
        
    },
    components:{
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
        'ordersMealList': require('./ordersMealListDelived.vue')
    },
    created() {
        this.loadMeals('meals');
    }
}
</script>