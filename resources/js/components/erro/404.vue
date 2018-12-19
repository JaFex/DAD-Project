<template>
    <div>
        <nav v-if="!isAuth()" class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="#">Restaurant</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        
                    </li>
                </ul>

                <router-link v-show="!this.$store.state.user" to="/login" tag="a" class="btn btn-outline-light">Login</router-link>
                <router-link v-show="this.$store.state.user" to="/dashboard" tag="a" class="btn btn-outline-light mr-3">Dashboard</router-link>
                <button v-show="this.$store.state.user" class="btn btn-outline-light" @click.prevent="logout">Logout</button>
            </div>
        </nav>
        <nav-bar v-if="isAuth()"></nav-bar>
        <div id="notfound">
            <div class="notfound">
                <div class="notfound-404">
                    <h1>4<span></span>4</h1>
                </div>
                <h2>Oops! Page Not Be Found</h2>
                <p>Sorry but the page you are looking for does not exist, have been removed. name changed or is temporarily unavailable</p>
                <router-link to="/" tag="a" class="navbar-brand">Back to homepage</router-link>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
        }
    },
    methods: {
        isAuth() {
            return this.$store.state.user !== null;
        }
    },
    computed: {
    },
    components:{
        'nav-bar': require('../dashboardnav.vue')
    }
}
</script>