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
                    <h5 class="card-title float-left">Summary Active Meals</h5>
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
                                        <button v-if="meal.total_price_preview > 0" type="button" class="btn btn-primary" @click.prevent="openSummarys(meal);">See all orders</button>
                                    </td>
                                </tr>
                                <tr v-if='currentMeal && meal.id == currentMeal.id' :key="'meal_'+meal.id">
                                    <td colspan="6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title float-left">Order of meal {{ meal.id }}</h5>
                                                <button type="button" class="btn btn-danger float-right" @click.prevent="closeListSummarys()">Close</button>
                                            </div>
                                            <div class="card-body">
                                                <summaryMealOrderList :method="loadSummarys" :summarys="summarys" :links="linkSummarys"></summaryMealOrderList>
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
            meals: {},
            summarys: {},
            links: {},
            linkSummarys: {},
            currentMeal: '',
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
        openSummarys: function(meal) {
            this.currentMeal = meal;
            this.loadSummarys('meals/'+meal.id+'/summarys');
        },
        loadSummarys: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.summarys = response.data.data;
                    this.linkSummarys = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log("loadSummarys-"+error);
                });
        },
        closeListSummarys: function(){
            this.currentMeal = '';
        }
    },
    computed: {
        
    },
    components:{
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
        'summaryMealOrderList': require('./summaryMealOrderList.vue'),
        'createOrder': require('./createOrder.vue')
    },
    created() {
        this.loadMeals('meals');
    }
}
</script>