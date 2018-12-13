<template>
    <div>
        <nav-bar></nav-bar>
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>State</th>
                        <th>Item_id</th>
                        <!--<th>Meal_id</th>-->
                        <!--<th>Responsible_cook_id</th>-->
                        <th>Start</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders" :class="[!order.responsible_cook_id? 'table-warning' : 'table-success']">
                        <td>{{order.id}}</td>
                        <td>{{order.state}}</td>
                        <td><button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter" @click="seeInfoIteam(order.item_id)">Show me item</button></td>
                        <!--<td>{{order.meal_id}}</td>-->
                        <!--<td>{{order.responsible_cook_id}}</td>-->
                        <td>{{order.start}}</td>
                        <td>
                            <button v-if="order.state === 'in preparation'" type="button" class="btn btn-primary" @click.prevent="changeStateOrder(order, 'prepared')">I Finished Preparing</button>
                            <button v-else-if="order.state === 'confirmed'" type="button" class="btn btn-primary" @click.prevent="changeStateOrder(order, 'in preparation')">Start Preparation</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination :method="loadOrders" :links="links"></pagination>
        <modalItem :object="currentIteam"></modalItem>
    </div>
</template>
<script>
export default {
    data() {
        return {
            orders: {},
            links: {},
            currentOrder: '',
            currentIteam: '',
        }
    },
    methods: {
        loadOrders: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.orders = response.data.data;
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
        seeInfoIteam: function(item_id){
            axios.get('/api/items/'+item_id)
                .then(response => {
                    this.currentIteam = response.data.data[0];
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        changeStateOrder: function(order, state){
            order.state = state;
            order.responsible_cook_id = this.$store.state.user.id;
            var now = new Date;
            if(state === 'prepared') {
                order.end = now.getUTCFullYear()+"/"+now.getUTCMonth()+"/"+now.getUTCDate()+" "+now.getUTCHours()+":"+now.getUTCMinutes()+":"+now.getUTCSeconds();
            }
            axios.put('/api/orders/'+order.id, order)
                .then(response => {
                    order = response.data.data;
                    if(order.state === 'prepared') {
                        axios.get('/api/orders?page='+this.links.currentPage)
                            .then(response => {
                                this.orders = response.data.data;
                                this.links = {
                                    prev: response.data.links.prev,
                                    next: response.data.links.next,
                                    currentPage: response.data.meta.current_page,
                                    lastPage: response.data.meta.last_page,
                                    path: 'orders?page='
                                }
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
    },
    computed: {
    },
    components:{
        'nav-bar': require('./dashboardnav.vue'),
        'pagination': require('../components/pagination.vue'),
        'modalItem': require('../components/modalItem.vue')
    },
    created() {
        this.loadOrders('orders');
    }
}
</script>