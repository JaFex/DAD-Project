<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row">
                <div class="col">
                    <b-jumbotron header="Items list">
                    </b-jumbotron>
                    <button class="btn btn-success mb-2 float-right" data-toggle="modal" data-target="#modalCreate" @click.prevent="create">Create new item</button>
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Description</th>
                            <th scope="col">Price</th>
                            <th scope="col">Deleted</th>
                            <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items">
                                <td>{{item.name}}</td>
                                <td>{{item.type}}</td>
                                <td>{{item.shortDescription}}</td>
                                <td>{{item.price}}</td>
                                <td>
                                    <i class="fas fa-check" v-if="item.deleted"></i>
                                    <i class="fas fa-times" v-else></i>
                                </td>
                                <td v-if="!item.deleted">
                                    <button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-warning btn-sm" @click.prevent="edit(item)"><i class="fas fa-edit"></i></button>
                                    <button type="button" class="btn btn-danger btn-sm" @click.prevent="deleteItem(item.id)"><i class="fas fa-trash-alt"></i></button>
                                </td>
                                <td v-else>
                                    <button type="button" class="btn btn-success btn-sm" @click.prevent="restoreItem(item.id)"><i class="fas fa-sync-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <pagination :method="loadItems" :links="links"></pagination>
                </div>
            </div>
        </div>
        <modal-create v-show="showModalCreate"></modal-create>
        <modal-edit v-show="showModalEdit" :item="currentItem"></modal-edit>
    </div>
</template>
<script>
export default {
    data() {
        return {
            items: {},
            links: {},
            currentItem: '',
            showModalEdit: false,
            showModalCreate: false,
        }
    },
    components: {
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
        'modal-edit': require('./modalItemEdit.vue'),
        'modal-create': require('./modalItemCreate.vue')
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
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        deleteItem(id){
            axios.delete('/api/items/'+id)
                .then(response => {
                    this.loadItems('items/all');
                    //this.$socket.emit('msg_update_items_from_client');
                    this.$toasted.show('Item deleted successfully.', {
                            theme: "bubble",
                            position: "bottom-center",
                            duration: 5000,
                            className: ['success']
                    });
                })
                .catch(error => {
                    console.log(error);
                });
        },
        restoreItem(id){
            axios.put('api/items/'+id+'/restore')
                .then(response => {
                    this.loadItems('items/all');
                    //this.$socket.emit('msg_update_items_from_client');
                    this.$toasted.show('Item restored successfully.', {
                            theme: "bubble",
                            position: "bottom-center",
                            duration: 5000,
                            className: ['success']
                    });
                })
                .catch(error => {
                    console.log(error);
                });
        },
        edit(item){
            this.currentItem = Object.assign({}, item);
            this.showModalEdit = true;
        },
        create(){
            this.showModalCreatea = true;
        }
    },
    created() {
        this.loadItems('items/all');
    }
}
</script>
<style>
    .success{
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
</style>
