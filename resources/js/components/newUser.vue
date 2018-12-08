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
                                                    required
                                                    >
                                        </b-form-input>
                                        <b-form-invalid-feedback></b-form-invalid-feedback>
                                    </b-form-group>
                                    <b-form-group id="functionLabel"
                                                label="Function:"
                                                label-for="functionInput">
                                        <b-form-select id="functionInput" :options="this.functions"/>
                                    </b-form-group>
                                    <b-form-group id="photoLabel"
                                                label="Photo:"
                                                label-for="photoInput">
                                        <b-form-file id="photoInput" />
                                    </b-form-group>
                                    <b-button variant="success">Submit</b-button>
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
            functions: ['Manager', 'Cook', 'Waiter', 'Cashier']
        }
    },
    methods: {
        
    },
    components: {
        'nav-bar': require('./dashboardnav.vue'),
    }
}
</script>