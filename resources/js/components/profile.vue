<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container" style="margin-top: 30px;">
            <div class="row">
                <div class="col">
                    <b-jumbotron header="My Profile" class="mt-3">
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-4 text-center">
                                <b-img rounded height="220" :src="imagePath(this.$store.state.user.photo_url)" alt="Responsive image"/>
                                <br>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-3" @click.prevent="presentDialog">Change Image</button>
                                <h4 class="mt-3">
                                    Function: 
                                    <small class="text-muted">{{user.function}}</small>
                                </h4>
                                <h4 class="mt-3">
                                    Email: 
                                    <small class="text-muted">{{user.email}}</small>
                                </h4>
                            </div>
                            <div class="col">
                                <b-form>
                                    <b-form-group id="usernameInput"
                                                label="Username:"
                                                label-for="usernameInput"
                                                >
                                        <b-form-input id="usernameInput"
                                                    type="text"
                                                    required
                                                    v-model="user.username">
                                        </b-form-input>
                                        <b-form-invalid-feedback></b-form-invalid-feedback>
                                    </b-form-group>
                                    <b-form-group id="nameInput"
                                                label="Name:"
                                                label-for="nameInput"
                                                >
                                        <b-form-input id="nameInput"
                                                    type="text"
                                                    required
                                                    v-model="user.name">
                                        </b-form-input>
                                        <b-form-invalid-feedback></b-form-invalid-feedback>
                                    </b-form-group>
                                    <b-form-group id="newPasswordInput"
                                                label="New Password:"
                                                label-for="newPasswordInput"
                                                >
                                        <b-form-input id="newPasswordInput"
                                                    type="password"
                                                    v-model="newPassword"
                                                    @change="clickPasswordField()"
                                                    :class="passwordError ? 'is-invalid' : ''"
                                                    >
                                        </b-form-input>
                                        <b-form-invalid-feedback></b-form-invalid-feedback>
                                    </b-form-group>
                                    <b-form-group id="confirmPasswordInput"
                                                label="Confirm Password:"
                                                label-for="confirmPasswordInput">
                                        <b-form-input id="confirmPasswordInput"
                                                    type="password"
                                                    @change="clickPasswordField($event)"
                                                    v-model="confirmPassword"
                                                    :class="passwordError ? 'is-invalid' : ''">
                                        </b-form-input>
                                        <div class="invalid-feedback">
                                            Passwords don't match.
                                        </div>
                                    </b-form-group>
                                    <b-button variant="primary" @click.prevent="updateUser">Submit</b-button>
                                </b-form>
                            </div>
                        </div>
                    </b-jumbotron>
                </div>
            </div>
        </div>
        <div class="modal" tabindex="-1" style="display: block;" v-show="showDialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select your new photo</h5>
                    <button type="button" class="close" @click.prevent="dismissDialog">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <input type="file" ref="file" class="form-control-file" id="file">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click.prevent="uploadPhoto">Submit</button>
                </div>
                </div>
            </div>  
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            user: {},
            newPassword: '',
            confirmPassword: '',
            photo: '',
            passwordError: false,
            showDialog: false,
        }
    },
    methods: {
        loadUser: function() {
            Object.assign(this.user, this.$store.state.user);
        },
        updateUser() {
            if(this.newPassword != this.confirmPassword){
                this.passwordError = true;
                return;
            }
            if(this.newPassword != '' && this.confirmPassword != ''){
                this.user.password = this.newPassword
            }
            
            axios.put('api/users/' + this.user.id, this.user)
                .then(response => {
                    this.newPassword = '';
                    this.confirmPassword = '';
                    this.photo_url = '';
                    Object.assign(this.user, response.data.data);
                    Object.assign(this.$store.state.user, this.user);
                }).catch(error => {
                    console.log(error);
                });
        },
        uploadPhoto() {
            let formData = new FormData();
            formData.append('file', this.$refs.file.files[0]);
            formData.append('_method', 'put');
            formData.append('id', this.user.id);
            axios.post('api/users/' + this.user.id, formData, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(response => {
                    this.$store.commit('setUser',response.data.data);
                    this.user = this.$store.state.user;
                    this.clear();
                }).catch(error => {
                    console.log(error);
                });
            this.showDialog = false;
        },
        clickPasswordField: function(event) {
            this.passwordError = false;
        },
        handleFileUpload(){
            this.photo = this.$refs.file.files[0];
            console.log(this.user.photo);
        },
        presentDialog(){
            this.showDialog = true;
        },
        dismissDialog(){
            this.showDialog = false;
        },
        imagePath(img) {
            return 'storage/profiles/'+img;
        },
        clear () {
            const input = this.$refs.file;
            input.type = 'text';
            input.type = 'file';
        },
    },
    components: {
        'nav-bar': require('./dashboardnav.vue')
    },
    created() {
        this.loadUser();
    }
}
</script>
