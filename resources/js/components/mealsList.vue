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
            </form>
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
                    <tr v-for="meal in meals">
                        <td>{{meal.id}}</td>
                        <td>{{meal.state}}</td>
                        <td>{{meal.table_number}}</td>
                        <td>{{meal.start}}</td>
                        <td>{{meal.total_price_preview}}</td>
                        <td>
                            <!--<button v-if="meal.state === 'in preparation'" type="button" class="btn btn-primary" @click.prevent="changeStateOrder(order, 'prepared')">I Finished Preparing</button>
                            <button v-else-if="meal.state === 'confirmed'" type="button" class="btn btn-primary" @click.prevent="changeStateOrder(order, 'in preparation')">Start Preparation</button>-->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination :method="loadMeals" :links="links"></pagination>
    </div>
</template>
<script>
export default {
    data() {
        return {
            meals: {},
            links: {},
            restaurantTables: {},
            selectedRestaurantTables: 0,
            currentMeal: ''
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
        creatMeal: function(table_number){
            var now = new Date;
            axios.post('/api/meals', {
                table_number: table_number,
                start: now.getUTCFullYear()+"/"+now.getUTCMonth()+"/"+now.getUTCDate()+" "+now.getUTCHours()+":"+now.getUTCMinutes()+":"+now.getUTCSeconds()
                })
                .then(function (response) {
                    this.loadMeals('meals');
                    console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
            this.loadMeals('meals');
                
        },
    },
    computed: {
    },
    components:{
        'nav-bar': require('./dashboardnav.vue'),
        'pagination': require('../components/pagination.vue')
    },
    created() {
        this.loadMeals('meals');
        this.loadTables('restaurantTables');
    }
}
</script>