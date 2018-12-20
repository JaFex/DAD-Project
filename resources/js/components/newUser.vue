<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row">
                <div class="col">
                    <b-jumbotron header="Create new user">
                        <hr class="my-4">
                        <div class="row justify-content-center">
                            <div class="col-6">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="showMessage">
                                    Please correct the errors!
                                    <button type="button" class="close" @click.prevent="dismissAlert">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <b-form>
                                    <b-form-group id="usernameLabel"
                                                label="Username:"
                                                label-for="usernameInput"
                                                >
                                        <b-form-input id="usernameInput"
                                                    type="text"
                                                    v-model="user.username"
                                                    >
                                        </b-form-input>
                                        <div class="invalid" v-show="!$v.user.username.required">
                                            Field is required.
                                        </div>
                                        <div class="invalid" v-if="!$v.user.username.minLength">
                                            Username must have at least {{$v.user.username.$params.minLength.min}} letters.
                                        </div>
                                        <div class="invalid" v-if="!$v.user.username.alphaNum">
                                            Username must be alpha numeric..
                                        </div>
                                    </b-form-group>
                                    <b-form-group id="emailLabel"
                                                label="Email:"
                                                label-for="emailInput"
                                                >
                                        <b-form-input id="emailInput"
                                                    type="email"
                                                    v-model="user.email"
                                                    >
                                        </b-form-input>
                                        <div class="invalid" v-show="!$v.user.email.required">
                                            Field is required.
                                        </div>
                                        <div class="invalid" v-if="!$v.user.email.email">
                                            The email is not valid.
                                        </div>
                                    </b-form-group>
                                    <b-form-group id="fullNameLabel"
                                                label="Full Name:"
                                                label-for="fullNameInput"
                                                >
                                        <b-form-input id="fullNameInput"
                                                    type="text"
                                                    v-model="user.name"
                                                    >
                                        </b-form-input>
                                        <div class="invalid" v-show="!$v.user.name.required">
                                            Field is required.
                                        </div>
                                        <div class="invalid" v-if="!$v.user.name.minLength">
                                            Name must have at least {{$v.user.name.$params.minLength.min}} letters.
                                        </div>
                                    </b-form-group>
                                    <b-form-group id="functionLabel"
                                                label="Function:"
                                                label-for="functionInput">
                                        <b-form-select id="functionInput" v-model="user.type" :options="functions"/>
                                        <div class="invalid" v-show="!$v.user.type.required">
                                            Field is required.
                                        </div>
                                    </b-form-group>
                                    <b-form-group id="photoLabel"
                                                label="Photo:"
                                                label-for="photoInput">
                                        <input type="file" ref="file" class="form-control-file" id="file">
                                    </b-form-group>
                                    <b-button variant="success" @click.prevent="createUser">Submit</b-button>
                                </b-form>
                            </div>
                        </div>
                    </b-jumbotron>
                </div>
            </div>
        </div> 
    </div>
</template>
<script>
import { required, minLength, alpha, email, alphaNum } from 'vuelidate/lib/validators'
export default {
    data() {
        return {
            showMessage: false,
            user: {},
            functions: [
                { text: 'Manager', value: 'manager' },
                { text: 'Cook', value: 'cook' },
                { text: 'Waiter', value: 'waiter' },
                { text: 'Cashier', value: 'cashier' }
            ]
        }
    },
    methods: {
        createUser(){
            if(this.$v.$invalid){
                this.showMessage = true;
            } else {
                let formData = new FormData();
                formData.append('file', this.$refs.file.files[0]);
                formData.append('username', this.user.username);
                formData.append('name', this.user.name);
                formData.append('email', this.user.email);
                formData.append('type', this.user.type);
                axios.post("api/users", formData, { headers: { 'Content-Type': 'multipart/form-data' } })
                    .then(response => {
                        this.$toasted.show('User created and confirmation email sent.', {
                            theme: "bubble",
                            position: "bottom-center",
                            duration: 5000,
                            className: ['success']
                        });
                        this.$router.push("/dashboard");
                    }).catch(error => {
                        console.log(error);
                    });
            } 
        },
        dismissAlert(){
            this.showMessage = false;
        }
    },
    validations: {
        user: {
            username: {
                required,
                minLength: minLength(2),
                alphaNum
            }, 
            email: {
                required,
                email
            },
            name: {
                required,
                minLength: minLength(3),
                alpha
            },
            type: {
                required
            },
        }
    },
    components: {
        'nav-bar': require('./dashboardnav.vue'),
    }
}
</script>
<style>
    .invalid{
        font-size: 80%;
        margin-top: .25rem;
        color: #dc3545;
    }
    .success{
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
</style>

