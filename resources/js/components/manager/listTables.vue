<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row">
                <div class="col">
                    <b-jumbotron header="Tables list">
                    </b-jumbotron>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" v-show="showMessage">
                        {{message}}
                        <button type="button" class="close" @click.prevent="dismissAlert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Table Number</span>
                        </div>
                        <input type="text" class="form-control" aria-label="Table number" aria-describedby="button-addon1" v-model="table.table_number">
                        <div class="input-group-append">
                            <button class="btn btn-success" type="button" id="button-addon1" @click.prevent="createTable">Create table</button>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">Number</th>
                            <th scope="col">Deleted</th>
                            <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="table in tables">
                                <td>{{table.table_number}}</td>
                                <td>
                                    <i class="fas fa-check" v-if="table.deleted"></i>
                                    <i class="fas fa-times" v-else></i>
                                </td>
                                <td >
                                    <button v-if="table.deleted" type="button" class="btn btn-success btn-sm" @click.prevent="restoreTable(table.table_number)">Restore</button>
                                    <button v-else type="button" class="btn btn-danger btn-sm" @click.prevent="deleteTable(table.table_number)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <pagination :method="loadTables" :links="links"></pagination>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            tables: {},
            table: {},
            links: {},
            message: '',
            showMessage: false
        }
    },
    components: {
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue')
    },
    methods: {
        loadTables: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.tables = response.data.data;
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
        createTable(){
            axios.post('/api/restaurantTables', this.table)
                .then(response => {
                    this.loadTables('restaurantTables/all');
                    this.$socket.emit('msg_update_tables_from_client');
                    this.$toasted.show('Table created successfully.', {
                            theme: "bubble",
                            position: "bottom-center",
                            duration: 5000,
                            className: ['success']
                        });
                    this.table.table_number = '';
                })
                .catch(error => {
                    console.log(error);
                    this.message = error.response.data.errors.table_number[0];
                    this.showMessage = true;
                });
        },
        deleteTable(id){
            axios.delete('api/restaurantTables/'+id)
                .then(response => {
                    this.loadTables('restaurantTables/all');
                    this.$socket.emit('msg_update_tables_from_client');
                    this.$toasted.show('Table deleted successfully.', {
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
        restoreTable(id){
            axios.put('api/restaurantTables/'+id)
                .then(response => {
                    this.loadTables('restaurantTables/all');
                    this.$socket.emit('msg_update_tables_from_client');
                    this.$toasted.show('Table restored successfully.', {
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
        dismissAlert(){
            this.showMessage = false;
        }
    },
    sockets:{
        msg_update_tables_from_server(){
            this.loadTables('restaurantTables/all');
        }
    },
    created() {
        this.loadTables('restaurantTables/all');
    }
}
</script>
<style>
    .success{
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
</style>