<template>
    <div>
        <div class="container">
            <div v-if="showSuccess" class="alert alert-success alert-dismissible">
                <a href="#" class="close" v-on:click='showSuccess = false'>&times;</a>
                <strong>{{ messageTitle }}</strong> {{ message }}
            </div>
            <div v-if="showErro" class="alert alert-danger alert-dismissible">
                <a href="#" class="close" v-on:click='showErro = false'>&times;</a>
                <strong>{{ messageTitleErro }}</strong> {{ messageErro }}
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>State</th>
                        <th>Item</th>
                        <th>Start</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders">
                        <td>{{order.id}}</td>
                        <td>{{order.state}}</td>
                        <td><button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter" @click="seeInfoIteam(order.item_id)">Show me item</button></td>
                        <td>{{order.start}}</td>
                        <td>
                            <button v-if="order.state === 'prepared'" type="button" class="btn btn-primary" @click.prevent="changeStateOrder(order, 'delivered')">Delivered</button>
                            <button v-if="order.state === 'prepared'" type="button" class="btn btn-danger" @click.prevent="changeStateOrder(order, 'not delivered')">Not Delivered</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination :method="method" :links="links"></pagination>
        <modalItem :object="currentIteam"></modalItem>
    </div>
</template>
<script>
export default {
    props: ['method', 'meal', 'orders', 'links'],
    data() {
        return {
            currentOrder: '',
            currentIteam: '',
            showSuccess: false,
            showErro: false,
            messageTitle: '',
            message: '',
            messageTitleErro: '',
            messageErro: ''
        }
    },
    methods: {
        seeInfoIteam: function(item_id){
            axios.get('/api/items/'+item_id)
                .then(response => {
                    this.currentIteam = response.data.data;
                })
                .catch(function (error) {
                    console.log("seeInfoIteam->"+error);
                });
        },
        loadOrdersSamePage: function(url, currentPage){
            axios.get('/api/'+url+'?page='+currentPage)
                .then(response => {
                    this.currentMeal = meal;
                    this.orders = response.data.data;
                    this.linksOrders = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: currentPage,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log("loadOrders->"+error);
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
                    console.log(soft.currentMeal.id);
                    soft.loadOrdersSamePage(soft.currentMeal.id+'/delived', soft.links.currentPage);
                })
                .catch(function (error) {
                    console.log("changeStateOrder-"+error);
                });
        }
    },
    computed: {
    },
    components:{
        'pagination': require('../components/pagination.vue'),
        'modalItem': require('../components/modalItem.vue')
    },
    created() {
    },
    sockets:{
        privateUpdate(received){
            var sourceUser = received[0];
            var order = received[1];
            
            if(this.currentMeal.id == order.meal_id) {
                console.log("Received "+order.meal_id);
                this.loadOrdersSamePage(this.currentMeal.id+'/delived', this.links.currentPage);
            }
        },
    }
}
</script>