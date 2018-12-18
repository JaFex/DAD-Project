<template>
    <div>
        <nav-bar></nav-bar>
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="container">
            <form>
                <div class="input-group  mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">Table Number: </span>
                    </div>
                    <select class="form-control" aria-describedby="basic-addon2" v-model="selectedRestaurantTables">
                        <option v-for="restaurantTable in restaurantTables">{{restaurantTable.table_number}}</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-primary" type="button" @click.prevent="creatMeal(selectedRestaurantTables)">Create a meal for table {{ selectedRestaurantTables }}</button>
                    </div>
                </div>
                <div v-if="showErro" class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" v-on:click='showErro = false'>&times;</a>
                    <strong>{{ messageTitle }}</strong> {{ message }}
                </div>
            </form>
            <div v-if="showCreteOrder" class="card">
                <div class="card-header">
                    <h5 class="card-title float-left">Add Order</h5>
                    <button type="button" class="btn btn-danger float-right"  @click.prevent="showCreteOrder = false">Close</button>
                </div>
                <div class="card-body">
                    <createOrder :myMeals="meals" :meal="currentMealCreate"></createOrder>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title float-left">My current meals</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
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
                                <tr>
                                    <td>{{meal.id}}</td>
                                    <td>{{meal.state}}</td>
                                    <td>{{meal.table_number}}</td>
                                    <td>{{meal.start}}</td>
                                    <td>{{meal.total_price_preview}}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary" @click.prevent="loadOrders(meal)">See all orders</button>
                                        <button  type="button" class="btn btn-primary" @click.prevent="showFormCreteOrder(meal)">add order</button>
                                    </td>
                                </tr>
                                <tr v-if='currentMeal && meal.id == currentMeal.id'>
                                    <td colspan="6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title float-left">Order of meal {{ meal.id }}</h5>
                                                <button type="button" class="btn btn-danger float-right" @click.prevent="closeListOrders()">Close</button>
                                            </div>
                                            <div class="card-body">
                                                <ordersMealList :method="loadOrders" :meal="currentMeal" :orders="orders" :links="linksOrders"></ordersMealList>
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
            axios.get('/api/'+url)
                .then(response => {
                    this.meals = response.data.data;
                    this.links = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        loadOrders: function(meal){
            axios.get('/api/meals/'+meal.id+'/orders')
                .then(response => {
                    this.currentMeal = meal;
                    this.orders = response.data.data;
                    this.linksOrders = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: 'meals/'+meal.id+'/orders?page='
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        loadTables: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.restaurantTables = response.data.data;
                    this.selectedRestaurantTables = this.restaurantTables[0].table_number;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        closeListOrders: function(){
            this.currentMeal = '';
        },
        creatMeal: function(table_number){
            this.closeListOrders();
            this.showErro = false;
            var now = new Date;
            let self = this
            axios.post('/api/meals', {
                table_number: table_number,
                start: now.getUTCFullYear()+"/"+now.getUTCMonth()+"/"+now.getUTCDate()+" "+now.getUTCHours()+":"+now.getUTCMinutes()+":"+now.getUTCSeconds()
                })
                .then(function (response) {
                    console.log(response);
                    self.loadMeals('meals');
                })
                .catch(function (error) {
                    console.log(error.response.data);
                    if(error.response.data.errors.table_number) {
                        console.log(error.response.data.errors.table_number[0]);
                        self.message = error.response.data.errors.table_number[0];
                        self.messageTitle = 'Invalid!';
                    } else {
                        self.message = 'Something went wrong';
                        self.messageTitle = 'Ops!';
                    }
                    self.showErro = true;
                });      
        },
        showFormCreteOrder: function(meal){
            this.currentMealCreate = meal;
            this.showCreteOrder = true;
        },
        createOrder: function(meal, item){
            this.showErro = false;
            var now = new Date;
            let self = this
            axios.post('/api/orders', {
                meal_id: meal.id,
                item: item.id,
                start: now.getUTCFullYear()+"/"+now.getUTCMonth()+"/"+now.getUTCDate()+" "+now.getUTCHours()+":"+now.getUTCMinutes()+":"+now.getUTCSeconds()
                })
                .then(function (response) {
                    console.log(response);
                    self.loadOrders('meals');
                })
                .catch(function (error) {
                    console.log(error.response.data);
                    if(error.response.data.errors.table_number) {
                        console.log(error.response.data.errors.table_number[0]);
                        self.message = error.response.data.errors.table_number[0];
                        self.messageTitle = 'Invalid!';
                    } else {
                        self.message = 'Something went wrong';
                        self.messageTitle = 'Ops!';
                    }
                    self.showErro = true;
                });      
        },
    },
    computed: {
    },
    components:{
        'nav-bar': require('./dashboardnav.vue'),
        'pagination': require('../components/pagination.vue'),
        'ordersMealList': require('../components/ordersMealList.vue'),
        'createOrder': require('../components/createOrder.vue')
    },
    created() {
        this.loadMeals('meals');
        this.loadTables('restaurantTables');
    }
}
</script>