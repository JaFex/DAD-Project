<template>
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit user</h5>
                    <button type="button" class="close" data-dismiss="modal" data-target="#exampleModalCenter" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="showMessage">
                        {{message}}
                        <button type="button" class="close" @click.prevent="dismissAlert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="form-group">
                        <label for="inputName">Name</label>
                        <input id="inputName" type="text" class="form-control" v-model="user.name">
                    </div>
                    <div class="form-group">
                        <label for="inputUsername">Username</label>
                        <input id="inputUsername" type="text" class="form-control" v-model="user.username">
                    </div>
                    <div class="form-group">
                        <label for="inputUsername">Email</label>
                        <input id="inputUsername" type="text" class="form-control" v-model="user.email">
                    </div>
                    <div class="form-group">
                        <label for="inputPhoto">Photo</label>
                        <input type="file" class="form-control-file" id="inputPhoto" ref="file">
                    </div>
                    <button type="submit" class="btn btn-primary" @click.prevent="editUser">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user'],
    data() {
        return {
            message: '',
            showMessage: false,
        }
     },
    methods: {
        editUser(){
            let formData = new FormData();
            formData.append('file', this.$refs.file.files[0]);
            formData.append('_method', 'put');
            formData.append('id', this.user.id);
            formData.append('name', this.user.name);
            formData.append('username', this.user.username);
            formData.append('email', this.user.email);
            axios.post('/api/users/' + this.user.id+'/edit', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(response => {
                    this.$toasted.show('User edited successfully.', {
                        theme: "bubble",
                        position: "bottom-center",
                        duration: 5000,
                        className: ['success']
                    });
                    this.$socket.emit('msg_update_users_from_client');
                }).catch(error => {
                    console.log(error);
                    this.message = '';
                    this.message += error.response.data.errors.name !== undefined ? error.response.data.errors.name[0] + ' ': '';
                    this.message += error.response.data.errors.username !== undefined ? error.response.data.errors.username[0] + ' ': '';
                    this.message += error.response.data.errors.email !== undefined ? error.response.data.errors.email[0] + ' ': '';
                    this.message += error.response.data.errors.file !== undefined ? error.response.data.errors.file[0] : '';
                    this.showMessage = true;
                });
        },
        dismissAlert(){
            this.showMessage = false;
        }
    }
}
</script>
<style>
    .success{
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
</style>

