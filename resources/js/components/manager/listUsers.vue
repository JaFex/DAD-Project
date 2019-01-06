<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row">
                <div class="col">
                    <b-jumbotron header="Users list">
                    </b-jumbotron>
                    <router-link tag="button" to="/users/create" class="btn btn-success mb-2 float-right">Create new user</router-link>
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Username</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Shift</th>
                            <th scope="col">Role</th>
                            <th scope="col">Blocked</th>
                            <th scope="col">Deleted</th>
                            <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users">
                                <td><img class="rounded img-fluid" :src="imagePath(user.photo_url)" alt="profile photo" style="width: 25px;"></td>
                                <td>{{user.username}}</td>
                                <td>{{user.name}}</td>
                                <td>{{user.email}}</td>
                                <td>{{user.shift_active === 1 ? 'Active' : 'Inactive'}}</td>
                                <td>{{user.type}}</td>
                                <td class="text-center">
                                    <i class="fas fa-check" v-if="user.blocked"></i>
                                    <i class="fas fa-times" v-else></i>
                                </td>
                                <td class="text-center">
                                    <i class="fas fa-check" v-if="user.deleted"></i>
                                    <i class="fas fa-times" v-else></i>
                                </td>
                                <td >
                                    <button v-if="user.id !== $store.state.user.id && !user.deleted && !user.blocked" type="button" class="btn btn-warning btn-sm" @click.prevent="edit(user)" data-toggle="modal" data-target="#exampleModalCenter"><i class="fas fa-user-edit"></i></button>
                                    <button v-if="user.id !== $store.state.user.id && !user.deleted && user.blocked" type="button" class="btn btn-success btn-sm" @click.prevent="unblock(user.id)"><i class="fas fa-lock-open"></i></button>
                                    <button v-if="user.id !== $store.state.user.id && !user.deleted && !user.blocked" type="button" class="btn btn-danger btn-sm" @click.prevent="block(user.id)"><i class="fas fa-lock"></i></button>
                                    <button v-if="user.id !== $store.state.user.id && user.deleted" type="button" class="btn btn-success btn-sm" @click.prevent="restoreUser(user.id)"><i class="fas fa-sync-alt"></i></button>
                                    <button v-if="user.id !== $store.state.user.id && !user.deleted" type="button" class="btn btn-danger btn-sm" @click.prevent="deleteUser(user.id)"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <pagination :method="loadUsers" :links="links"></pagination>
                </div>
            </div>
        </div>
        <modal-edit :user="currentUser"></modal-edit>
    </div>
</template>
<script>
export default {
    data() {
        return {
            users: {},
            links: {},
            currentUser: '',
        }
    },
    components: {
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
        'modal-edit': require('./modalUserEdit.vue')
    },
    methods: {
        loadUsers: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.users = response.data.data;
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
        deleteUser(id){
            axios.delete('api/users/'+id)
                .then(response => {
                    this.loadUsers('users');
                    //this.$socket.emit('msg_update_tables_from_client');
                    this.$toasted.show('User deleted successfully.', {
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
        restoreUser(id){
            axios.put('api/users/'+id+'/restore')
                .then(response => {
                    this.loadUsers('users');
                    //this.$socket.emit('msg_update_tables_from_client');
                    this.$toasted.show('User restored successfully.', {
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
        block(id){
            axios.put('api/users/'+id+'/block')
                .then(response => {
                    this.loadUsers('users');
                    //this.$socket.emit('msg_update_tables_from_client');
                    this.$toasted.show('User blocked successfully.', {
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
        unblock(id){
            axios.put('api/users/'+id+'/unblock')
                .then(response => {
                    this.loadUsers('users');
                    //this.$socket.emit('msg_update_tables_from_client');
                    this.$toasted.show('User unblocked successfully.', {
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
        imagePath(img) {
            return 'storage/profiles/'+img;
        },
        edit(user){
            this.currentUser = Object.assign({}, user);
        }
    },
    created() {
        this.loadUsers('users');
    },
    sockets:{
        msg_update_users_from_server(){
            this.loadUsers('users');
        }
    }
}
</script>
