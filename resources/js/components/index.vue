
<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Restaurant</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        
                    </li>
                </ul>

                <router-link v-show="!this.$store.state.user" to="/login" tag="a" class="btn btn-outline-light">Login</router-link>
                <router-link v-show="this.$store.state.user" to="/dashboard" tag="a" class="btn btn-outline-light mr-3">Dashboard</router-link>
                <button v-show="this.$store.state.user" class="btn btn-outline-light" @click.prevent="logout">Logout</button>
            </div>
        </nav>
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
        <modal-item :object="currentItem"></modal-item>
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
                        path: 'items?page='
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        logout() {
            this.showMessage = false;
            axios.post('api/logout')
                .then(response => {
                    this.$store.commit('clearUserAndToken');
                    this.$router.push("/");
                })
                .catch(error => {
                    this.$store.commit('clearUserAndToken');
                    console.log(error);
                })            
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
    components:{
        'modal-item': require('./modalItem.vue'),
        'pagination': require('./pagination.vue'),
    },
    mounted() {
        this.loadItems('items');
    }
}
</script>
