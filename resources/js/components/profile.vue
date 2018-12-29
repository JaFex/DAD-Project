<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container-fuid" style="margin-top: 50px;">
            <b-jumbotron header="My Profile">
                <hr class="my-4">
                <div class="row justify-content-center">
                    <div class="col-8">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="showMessage">
                            {{message}}
                            <button type="button" class="close" @click.prevent="dismissAlert">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-4 text-center">
                                <b-img rounded height="220" :src="imagePath(this.$store.state.user.photo_url)" alt="Responsive image"/>
                                <br>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-3" @click.prevent="presentDialog">Change Image</button>
                                <h4 class="mt-3">
                                    Function: 
                                    <small class="text-muted">{{user.type}}</small>
                                </h4>
                                <h4 class="mt-3">
                                    Email: 
                                    <small class="text-muted">{{user.email}}</small>
                                </h4>
                            </div>
                            <div class="col">
                                <b-form>
                                    <div class="form-group">
                                        <label for="usernameInput">Username:</label>
                                        <input class="form-control" id="usernameInput"
                                                    type="text"
                                                    v-model.trim="user.username"
                                                    :class="!$v.user.username.required || !$v.user.username.minLength ? 'is-invalid' : ''" />
                                        <div class="invalid" v-if="!$v.user.username.required">
                                            Field is required.
                                        </div>
                                        <div class="invalid" v-if="!$v.user.username.minLength">
                                            Username must have at least {{$v.user.username.$params.minLength.min}} letters.
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nameInput">Name:</label>
                                        <input class="form-control" id="nameInput" type="text" v-model.trim="user.name">
                                        <div class="invalid" v-show="!$v.user.name.required">
                                            Field is required.
                                        </div>
                                    </div>
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
                    </div>
                </div> 
            </b-jumbotron>
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
import { required, minLength, alphaNum, alpha } from 'vuelidate/lib/validators'
export default {
    data() {
        return {
            //variaveis que existem neste componente
            message: '',
            showMessage: false,
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

            if(this.$v.$invalid){
                this.message = "Please correct the errors."
                this.showMessage = true;
            } else {
                axios.put('api/users/' + this.user.id, this.user)
                    .then(response => {
                        this.newPassword = '';
                        this.confirmPassword = '';
                        this.photo_url = '';
                        Object.assign(this.user, response.data.data);
                        this.$store.commit('setUser',response.data.data);
                        this.passwordError = false;
                        this.showMessage = false;
                    }).catch(error => {
                        console.log(error);
                        this.message = '';
                        this.message += error.response.data.errors.name !== undefined ? error.response.data.errors.name[0] + ' ' : '';
                        this.message += error.response.data.errors.username !== undefined ? error.response.data.errors.username[0] + ' ' : '';
                        this.message += error.response.data.errors.password !== undefined ? error.response.data.errors.password[0] : '';
                        this.showMessage = true; 
                    });
            }
        },
        uploadPhoto() {
            if(this.$refs.file.files[0] === undefined){
                this.message = 'Please choose a profile image.';
                this.showMessage = true; 
            } else {
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
                        this.message = '';
                        this.message = error.response.data.errors.file !== undefined ? error.response.data.errors.file[0] + ' ' : '';
                        this.showMessage = true; 

                    });
            }
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
        dismissAlert(){
            this.showMessage = false;
        }
    },
    components: {
        'nav-bar': require('./dashboardnav.vue')
    },
    validations: {
        user: {
            username: {
                required,
                minLength: minLength(2),
                alphaNum
            },
            name: {
                required,
                minLength: minLength(3),
            }
        },
    },
    created() {
        this.loadUser();
    }
}
</script>
