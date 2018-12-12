
<template>
    <div>
        <nav-bar></nav-bar>
        <br>
        <div class="container">
            <div class="card-deck" v-for="groupItem in groupedItems">
                <div v-for="item in groupItem" class="card bg-light border-secondary mb-3">
                    <img class="card-img-top" v-bind:src="item.photoUrl" style="max-heigth:50px;">
                    <div class="card-body">
                        <h5 class="card-title">{{ item.name }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{item.price}}€</h6>
                        <p class="card-text">{{ item.shortDescription }}</p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter" @click="moreInfo(item)">More Info</button>
                    </div>
                </div>
            </div>
        </div>
        <pagination :method="loadItems" :links="links"></pagination>
        <modal :object="currentItem"></modal>
    </div>
</template>
<script>
export default {
    data() {
        return {
            items: {},
            links: {},
            currentItem: '',
        }
    },
    methods: {
        loadItems: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.items = response.data.data;
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
        },
        moreInfo: function(order){
            this.currentOrder = order;
        }
    },
    computed: {
        groupedOrders() {
            var orders = [];
            for (var i=0; i<this.orders.length; i+=4) {
                orders.push(this.orders.slice(i,i+4));
           }  
            return orders
          }
    },
    components:{
        'nav-bar': require('../components/navbar.vue'),
        'pagination': require('../components/pagination.vue'),
        'modal': require('../components/modal.vue')
    },
    created() {
        this.loadItems('orders');
    }
}
</script>