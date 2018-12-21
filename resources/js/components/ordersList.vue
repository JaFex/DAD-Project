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
                        <th>Item</th>
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
                    console.log("loadOrders-"+error);
                });
        },
        loadOrdersSamePage: function(url, currentPage){
            axios.get('/api/'+url+'?page='+currentPage)
                .then(response => {
                    this.orders = response.data.data;
                    this.links = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: currentPage,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log("loadOrdersSamePage-"+error);
                });
        },
        seeInfoIteam: function(item_id){
            axios.get('/api/items/'+item_id)
                .then(response => {
                    this.currentIteam = response.data.data[0];
                })
                .catch(function (error) {
                    console.log("seeInfoIteam-"+error);
                });
        },
        changeStateOrder: function(order, state){
            order.state = state;
            let soft = this;
            var update = {};
            update["state"] = state;
            axios.put('/api/orders/'+order.id, update)
                .then(response => {
                    order = response.data.data;
                    if(order.state === 'prepared') {
                        axios.get('/api/orders/'+order.id+'/waiter')
                            .then(response => {
                                var waiter = response.data.data;
                                soft.$socket.emit('privateUpdate', this.$store.state.user, waiter, order);
                            })
                            .catch(function (error) {
                                console.log("loadOrdersSamePage-"+error);
                            });
                    } else if(order.state === "in preparation") {
                        soft.$socket.emit('kitchenWichoutMe');
                        soft.$socket.emit('waiter');
                    }
                    soft.loadOrdersSamePage('orders', this.links.currentPage);
                })
                .catch(function (error) {
                    console.log("changeStateOrder-"+error);
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
    },
    sockets:{
        update() {
            console.log('---SOCKETS TELL TO UPDATE---');
            this.loadOrdersSamePage('orders', this.links.currentPage);
        },
        privateUpdate_unavailable(destUser){
            this.$toasted.show('The waiter "' + destUser.name + '" is not available', {
                            theme: "bubble",
                            position: "bottom-center",
                            duration: 5000,
                            className: ['error']
                        });
        },
        privateUpdate_sent(destUser){
            this.$toasted.show('The waiter "' + destUser.name + '" was warned that was order preperad', {
                            theme: "bubble",
                            position: "bottom-center",
                            duration: 5000,
                            className: ['success']
                        });
        }
    },
}
</script>