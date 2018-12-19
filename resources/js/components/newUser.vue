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
                                <b-form>
                                    <b-form-group id="usernameLabel"
                                                label="Username:"
                                                label-for="usernameInput"
                                                >
                                        <b-form-input id="usernameInput"
                                                    type="text"
                                                    v-model="user.username"
                                                    required
                                                    >
                                        </b-form-input>
                                        <b-form-invalid-feedback></b-form-invalid-feedback>
                                    </b-form-group>
                                    <b-form-group id="emailLabel"
                                                label="Email:"
                                                label-for="emailInput"
                                                >
                                        <b-form-input id="emailInput"
                                                    type="email"
                                                    v-model="user.email"
                                                    required
                                                    >
                                        </b-form-input>
                                        <b-form-invalid-feedback></b-form-invalid-feedback>
                                    </b-form-group>
                                    <b-form-group id="fullNameLabel"
                                                label="Full Name:"
                                                label-for="fullNameInput"
                                                >
                                        <b-form-input id="fullNameInput"
                                                    type="text"
                                                    v-model="user.name"
                                                    required
                                                    >
                                        </b-form-input>
                                        <b-form-invalid-feedback></b-form-invalid-feedback>
                                    </b-form-group>
                                    <b-form-group id="functionLabel"
                                                label="Function:"
                                                label-for="functionInput">
                                        <b-form-select id="functionInput" v-model="user.type" :options="functions"/>
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
export default {
    data() {
        return {
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
            let formData = new FormData();
            formData.append('file', this.$refs.file.files[0]);
            formData.append('username', this.user.username);
            formData.append('name', this.user.name);
            formData.append('email', this.user.email);
            formData.append('type', this.user.type);
            axios.post("api/users", formData, { headers: { 'Content-Type': 'multipart/form-data' } })
                .then(response => {
                    console.log("USER CREATED");
                }).catch(error => {
                    console.log(error);
                });
        } 
    },
    components: {
        'nav-bar': require('./dashboardnav.vue'),
    }
}
</script>