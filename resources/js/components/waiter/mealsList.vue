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
                <div v-if="showSuccess" class="alert alert-success alert-dismissible">
                    <a href="#" class="close" v-on:click='showSuccess = false'>&times;</a>
                    <strong>{{ messageTitle }}</strong> {{ message }}
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
                    <createOrder @clickTimeRunOutOrderConfirmed="timeRunOutOrderConfirmed" @clickReloadMealAndOrder="reloadMealAndOrder" :myMeals="meals" :meal="currentMealCreate"></createOrder>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title float-left">My current meals with orders 'pending' and 'confirmed'</h5>
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
                                <tr :key="meal.id">
                                    <td>{{meal.id}}</td>
                                    <td>{{meal.state}}</td>
                                    <td>{{meal.table_number}}</td>
                                    <td>{{meal.start}}</td>
                                    <td>{{meal.total_price_preview}}</td>
                                    <td>
                                        <button v-if="meal.total_price_preview > 0" type="button" class="btn btn-primary" @click.prevent="openOrders(meal);">See all orders</button>
                                        <button type="button" class="btn btn-primary" @click.prevent="showFormCreteOrder(meal)">add order</button>
                                        <button type="button" class="btn btn-danger" @click.prevent="terminateMeal(meal)">Terminate</button>
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
                                                <ordersMealList @clickReloadMealAndOrder="reloadMealAndOrder" @clickUpdateKitchen="updateKitchen" @clickUpdateOrder="updateOrder"  :method="loadOrders" :meal="currentMeal" :orders="orders" :links="linksOrders"></ordersMealList>
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
                    console.log("loadMeals-"+error);
                });
        },
        openOrders: function(meal) {
            this.currentMeal = meal;
            this.loadOrders('meals/'+meal.id+'/orders');
        },
        loadOrders: function(url){
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
                    console.log("loadOrders-"+error);
                });
        },
        loadTables: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.restaurantTables = response.data.data;
                    this.selectedRestaurantTables = this.restaurantTables[0].table_number;
                })
                .catch(function (error) {
                    console.log("loadTables-"+error);
                });
        },
        closeListOrders: function(){
            this.currentMeal = '';
        },
        creatMeal: function(table_number){
            this.closeListOrders();
            this.showSuccess = false;
            this.showErro = false;
            let self = this
            axios.post('/api/meals', {
                    table_number: table_number
                })
                .then(function (response) {
                    self.loadMeals('meals');
                    self.message = 'Meal as created successful to the table '+table_number;
                    self.messageTitle = 'Success!';
                    self.showSuccess = true;
                })
                .catch(function (error) {
                    console.log("creatMeal-"+error.response.data);
                    if(error.response.data.errors.table_number) {
                        console.log("creatMeal-"+error.response.data.errors.table_number[0]);
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
        reloadMealAndOrder: function() {
            this.loadMeals('meals');
            if(this.currentMeal && this.currentMealCreate && this.currentMeal.id == this.currentMealCreate.id) {
                this.loadOrders('meals/'+this.currentMeal.id+'/orders');
            }
        },
        updateOrder: function(order) {
            if(this.currentMeal && order && this.currentMeal.id == order.meal_id) {
                this.loadOrders('meals/'+this.currentMeal.id+'/orders');
            }
        },
        timeRunOutOrderConfirmed: function(order) {
            this.reloadMealAndOrder();
            let soft = this;
            setTimeout(function () {
                var update = {};
                update["state"] = 'confirmed';
                axios.put('/api/orders/'+order.id, update)
                        .then(response => {
                            order = response.data.data;
                            soft.messageTitle = "Order Confirmed!";
                            soft.message = "Order ("+order.id+") as been confirmed on the meal "+order.meal_id+"!";
                            soft.showSuccess = true;
                            soft.selectedItemType = '';
                            soft.selectedItem = null;
                            soft.updateOrder(order);
                            soft.$socket.emit('kitchen');
                        })
                        .catch(function (error) {
                            console.log("timeRunOutOrderConfirmed->"+error);
                            if (error.response.status === 404) {
                                console.log("timeRunOutOrderConfirmed->"+'Is 404 maybe order has deleted so he didn t find order');
                            } else {
                                soft.messageTitleErro = "Fail to confirm order!";
                                soft.messageErro = "Ops! Order"+order.id+" not confirmed";
                                soft.showErro = true;
                            }
                        });
            }, 5000);
        },
        updateKitchen: function() {
            this.$socket.emit('kitchen');
        },
        terminateMeal: function(meal){
            let soft = this;
            axios.get('/api/meals/'+meal.id+'/terminated')
                .then(response => {
                    var booleanCanBeTerminated = response.data['terminated'];
                    if(booleanCanBeTerminated == true) {
                        soft.sendUpdateToTerminateMeal(meal);
                    } else if(booleanCanBeTerminated == false) {
                        if (confirm('The meal ('+meal.id+') have not all orders been delivered yet, and will they be excluded from payment! Is it okay?')) {
                            soft.sendUpdateToTerminateMeal(meal);
                        } else {
                            soft.$toasted.show('The meal ('+meal.id+') was not been terminated', {
                                theme: "bubble",
                                position: "bottom-center",
                                duration: 5000,
                                className: ['success']
                            });
                        }
                    }
                })
                .catch(function (error) {
                    console.log("terminateMeal->"+error);
                });
        },
        sendUpdateToTerminateMeal: function(meal) {
            let soft = this;
            var update = {};
            update["state"] = 'terminated';
            axios.put('/api/meals/'+meal.id+'/terminated', update)
                    .then(response => {
                        meal = response.data.data;
                        soft.$toasted.show('The meal ('+meal.id+') was been terminated', {
                                theme: "bubble",
                                position: "bottom-center",
                                duration: 5000,
                                className: ['success']
                            });
                        soft.currentMeal = soft.currentMeal.id==meal.id?null:soft.currentMeal;
                        soft.reloadMealAndOrder();
                        soft.$socket.emit('kitchen');
                        soft.$socket.emit('cashier');
                    })
                    .catch(function (error) {
                        console.log("sendUpdateToTerminateMeal->"+error);
                        soft.$toasted.show('ERRO: The meal ('+meal.id+') was not been terminated', {
                                theme: "bubble",
                                position: "bottom-center",
                                duration: 5000,
                                className: ['error']
                            });
                    });
        }
    },
    computed: {
        
    },
    components:{
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
        'ordersMealList': require('./ordersMealList.vue'),
        'createOrder': require('./createOrder.vue')
    },
    created() {
        this.loadMeals('meals');
        this.loadTables('restaurantTables');
    },
    sockets:{
        privateUpdateConfirmed(received){
            var sourceUser = received[0];
            var order = received[1];
            this.updateOrder(order);
        },
    }
}
</script>