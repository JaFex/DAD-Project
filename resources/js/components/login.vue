<template>
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center">
            <div class="col-4">
                <div class="alert alert-danger" v-if="showMessage">             
                    <button type="button" class="close" v-on:click="showMessage=false">&times;</button>
                    <strong>{{ message }}</strong>
                </div>
                <div class="card-header text-white bg-dark">
                    Login
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="inputEmail">Email address</label>
                            <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" v-model.trim="user.email">
                        </div>
                        <div class="form-group">
                            <label for="inputPassword">Password</label>
                            <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Password" v-model="user.password">
                        </div>
                        <button type="submit" class="btn btn-primary mr-3" v-on:click.prevent="login">Login</button>
                        <router-link tag="a" to="/">Cancel</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data: function(){
        return { 
                user: {
                    email:"",
                    password:"",
                    type:""
                },
                showMessage: false,
                message: "",
            }
    },
    methods: {
        login(){
            axios.post('api/login', this.user)
                    .then(response => {
                        this.$store.commit('setToken',response.data.access_token);
                        return axios.get('api/users/me');
                    })
                    .then(response => {
                        this.user.type = response.data.data.type;
                        this.$store.commit('setUser',response.data.data);
                        this.$toasted.show('Logged successfully.', {
                            theme: "bubble",
                            position: "bottom-center",
                            duration: 5000,
                            className: ['success']
                        });
                        if(this.$store.state.user.shift_active){
                            this.$socket.emit('user_enter', this.$store.state.user);
                        }
                        if(this.user.type === "manager"){
                            this.$router.push("/dashboardManagers");
                        }else{
                            this.$router.push("/dashboard");
                        }
                        
                    })
                    .catch(error => {
                        this.$store.commit('clearUserAndToken');
                        this.message = error.response.data.msg;
                        this.showMessage = true;
                        console.log(error);
                    })
        }
    }
}
</script>
<style>

    html, body, #app, .container-fluid, .row {
        height: 95%;
    }

    .success{
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }

</style>


