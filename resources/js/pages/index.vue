<template>
    <div>
        <nav-bar></nav-bar>
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
Vue.component('navBar', require('../components/navbar.vue'));
Vue.component('pagination', require('../components/pagination.vue'));
Vue.component('modal', require('../components/modal.vue'));
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
                        path: 'items?page='
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },

        moreInfo: function(item){
            this.currentItem = item;
        }
    },
    computed: {
        groupedItems() {
            var items = [];
            for (var i=0; i<this.items.length; i+=4) {
                items.push(this.items.slice(i,i+4));
           }  
            return items
          }
    },
    created() {
        this.loadItems('items');
    }
}
</script>
