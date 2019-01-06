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
                    <h5 class="card-title float-left">Meals</h5>
                </div>
                <div class="card-body">
                    <form>
                        <h6 class="card-title float-left">Search:</h6>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Waiter: </span>
                            </div>
                            <select class="form-control" aria-describedby="basic-addon2" v-model="selectedWaiter">
                                <option :value="0">No Waiter</option>
                                <option v-for="waiter in waiters" :value="waiter.id" :key="waiter.id">{{"("+waiter.id+")-"+waiter.name}}</option>
                            </select>
                        </div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">State:  </span>
                            </div>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck1" v-model="selectedActive">
                                    <label class="form-check-label" for="gridCheck1">Active</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck2" v-model="selectedTerminated">
                                    <label class="form-check-label" for="gridCheck1">Terminated</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck3" v-model="selectedPaid">
                                    <label class="form-check-label" for="gridCheck1">Paid</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck4" v-model="selectedNotPaid">
                                    <label class="form-check-label" for="gridCheck1">Not Paid</label>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Start Date: </span>
                                <div class="input-group-text">
                                    <input type="checkbox" aria-label="Checkbox for following text input" v-model="selectedDateShow">
                                </div>
                            </div>
                            <input class="form-control" type="date" v-model="selectedDate" id="example-date-input" :disabled="selectedDateShow==false">
                        </div>
                        <button type="button" class="btn btn-primary float-right" @click.prevent="search()">Search</button>
                        <button type="button" class="btn btn-danger float-right" @click.prevent="clear()">Clear</button>
                    </form>
                    <table class="table">
                        <thead>
                            <tr class="table-active">
                                <th>ID</th>
                                <th>State</th>
                                <th>Table number</th>
                                <th>Waiter</th>
                                <th>Start</th>
                                <th>End</th>
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
                                    <td>{{'('+meal.responsible_waiter_id+')-'+ meal.responsible_waiter_name}}</td>
                                    <td>{{meal.start}}</td>
                                    <td>{{meal.end}}</td>
                                    <td>{{meal.total_price_preview}}</td>
                                    <td>
                                        <button v-if="meal.total_price_preview > 0" type="button" class="btn btn-primary" @click.prevent="openOrders(meal);">See all orders</button>
                                    </td>
                                </tr>
                                <tr v-if='currentMeal && meal.id == currentMeal.id' :key="'meal_'+meal.id">
                                    <td colspan="10">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title float-left">Order of meal {{ meal.id }}</h5>
                                                <button type="button" class="btn btn-danger float-right" @click.prevent="closeListOrders()">Close</button>
                                            </div>
                                            <div class="card-body">
                                                <ordersListMeal :method="loadOrders" :meal="currentMeal" :orders="orders" :links="linksOrders"></ordersListMeal>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template >
                        </tbody>
                    </table>
                </div>
                <pagination :method="loadMeals" :links="links"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            meals: {},
            orders: {},
            links: {},
            linksOrders: {},
            waiters: {},
            selectedWaiter: 0,
            selectedActive: true,
            selectedTerminated: true,
            selectedPaid: false,
            selectedNotPaid: false,
            selectedDateShow: false,
            selectedDate: '',
            currentMeal: '',
        }
    },
    methods: {
        loadMeals: function(url){
            if(this.selectedActive || this.selectedTerminated || this.selectedPaid || this.selectedNotPaid) {
                var info = {};
                if(this.selectedActive) {
                    info["active"] = this.selectedActive;
                }
                if(this.selectedTerminated) {
                    info["terminated"] = this.selectedTerminated;
                }
                if(this.selectedPaid) {
                    info["paid"] = this.selectedPaid;
                }
                if(this.selectedNotPaid) {
                    info["not_paid"] = this.selectedNotPaid;
                }
                if(this.selectedWaiter != 0) {
                    info["waiter"] = this.selectedWaiter;
                }
                if(this.selectedDateShow) {
                    info["date"] = this.selectedDate;
                }

                axios.get('/api/'+url, {
                            params: info
                        }
                    ).then(response => {
                        this.meals = response.data.data;
                        if(this.meals.length <= 0) {
                            this.$toasted.show('No meals found!', {
                                theme: "bubble",
                                position: "bottom-center",
                                duration: 5000,
                                className: ['error']
                            });
                        }
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
            }
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
        loadWaiters: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.waiters = response.data.data;
                })
                .catch(function (error) {
                    console.log("loadWaiters-"+error);
                });
        },
        openOrders: function(meal) {
            this.currentMeal = meal;
            this.loadOrders('meals/'+meal.id+'/orders/all');
        },
        closeListOrders: function(){
            this.currentMeal = '';
        },
        search: function(){
            this.currentMeal = '';
            this.loadMeals('meals/filter');
        },
        clear: function(){
            this.selectedWaiter = 0;
            this.selectedActive = true;
            this.selectedTerminated = true;
            this.selectedPaid = false;
            this.selectedNotPaid = false;
            this.selectedDateShow = false;
            var date = new Date();
            this.selectedDate = date.getFullYear()+'-'+((date.getMonth()+1)<10?'0':'')+''+(date.getMonth()+1)+'-'+(date.getDate()<10?'0':'')+date.getDate();

            this.currentMeal = '';
            this.loadMeals('meals/filter');
        }
    },
    computed: {
        
    },
    components:{
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
        'ordersListMeal': require('./ordersListMeal.vue')
    },
    created() {
        this.loadMeals('meals/filter');
        this.loadWaiters('users/search/waiter');
        var date = new Date();
        this.selectedDate = date.getFullYear()+'-'+((date.getMonth()+1)<10?'0':'')+''+(date.getMonth()+1)+'-'+(date.getDate()<10?'0':'')+date.getDate();
    },
    sockets:{

    }
}
</script>