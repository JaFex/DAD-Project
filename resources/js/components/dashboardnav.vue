<template>
    <b-navbar toggleable="md" type="dark" variant="dark" class="fixed-top">
            <router-link to="/" tag="a" class="navbar-brand">Restaurant</router-link>
            <b-navbar-nav>
                <li class="nav-item">
                    <router-link to="/dashboard" class="nav-link" tag="a">Dashboard</router-link>
                </li>
                <template v-if="isAuthWaiter()">
                    <li class="nav-item">
                        <router-link to="/meals" class="nav-link" tag="a">Meals</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/orders-to-deliver" class="nav-link" tag="a">Meals To Deliver</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/summary" class="nav-link" tag="a">Summary Meals</router-link>
                    </li>
                </template>
                <template v-if="isAuthCashier()">
                    <li class="nav-item">
                        <router-link to="/invoices" class="nav-link" tag="a">Invoices</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/invoicesPDF" class="nav-link" tag="a">Invoices Download</router-link>
                    </li>
                </template>
                
                
                <li v-if="isAuthCook()" class="nav-item">
                    <router-link to="/orders" class="nav-link" tag="a">Orders</router-link>
                </li>
                <template v-if="isAuthManager()">
                    <li class="nav-item">
                        <router-link to="/tables" class="nav-link" tag="a">Tables</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/items" class="nav-link" tag="a">Items</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/meals/filter" class="nav-link" tag="a">Meals</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/invoices/filter" class="nav-link" tag="a">Invoices</router-link>
                    </li>
                    <b-nav-item-dropdown >
                        <template slot="button-content">Users</template>
                        <router-link to="/users/create" tag="a" role="menuitem" class="dropdown-item">New User</router-link>
                        <router-link to="/users" tag="a" role="menuitem" class="dropdown-item">List</router-link>
                    </b-nav-item-dropdown>
                </template>
                <li class="nav-item">
                    <router-link to="/message" class="nav-link" tag="a">Send Message</router-link>
                </li>
            </b-navbar-nav>
            <b-navbar-nav class="ml-auto">
                <b-nav-item v-if="this.$store.state.user.shift_active === 1" class="mr-2" disabled>Working</b-nav-item>
                <b-nav-item v-else class="mr-2" disabled>Not working</b-nav-item>
                <b-img rounded width="30" height="30" alt="photo" class="m-1" :src="imagePath(this.$store.state.user.photo_url)" />
                <b-nav-item-dropdown right>
                    <template slot="button-content">
                        <em>{{this.$store.state.user.name}}</em>
                    </template>
                    <router-link to="/profile" tag="a" role="manuitem" class="dropdown-item">Profile</router-link>
                    <b-dropdown-item v-if="this.$store.state.user.shift_active === 1" @click.prevent="endShift">End Shift</b-dropdown-item>
                    <b-dropdown-item v-else @click.prevent="startShift">Start Shift</b-dropdown-item>
                    <b-dropdown-item v-on:click.prevent="logout">Logout</b-dropdown-item>
                </b-nav-item-dropdown>
            </b-navbar-nav>
        </b-navbar>
</template>
<script>
export default {
    methods: {
        logout() {
            axios.post('api/logout')
                .then(response => {
                    if(this.$store.state.user.shift_active){
                        this.$socket.emit('user_exit', this.$store.state.user);
                    }
                    this.$store.commit('clearUserAndToken');
                    this.$router.push("/");
                })
                .catch(error => {
                    this.$store.commit('clearUserAndToken');
                    console.log(error);
                })            
        },
        startShift() {
            axios.put('api/users/' + this.$store.state.user.id + '/start')
                .then(response => {
                    this.$store.commit('setUser',response.data.data);
                    this.$socket.emit('user_enter', response.data.data);
                })
                .catch(error => {
                    console.log(error);
                });
        },
        endShift() {
            axios.put('api/users/' + this.$store.state.user.id + '/end')
                .then(response => {
                    this.$store.commit('setUser',response.data.data);
                    this.$socket.emit('user_exit', this.$store.state.user);
                })
                .catch(error => {
                    console.log(error);
                });
        },
        imagePath(img) {
            return 'storage/profiles/'+img;
        },
        isAuthCook() {
            return this.$store.state.user.type  === 'cook';
        },
        isAuthWaiter() {
            return this.$store.state.user.type  === 'waiter';
        },
        isAuthManager() {
            return this.$store.state.user.type  === 'manager';
        },
        isAuthCashier() {
            return this.$store.state.user.type  === 'cashier';
        }
    },
    sockets:{
        privateUpdatePrepared(received){
            if(this.isAuthWaiter()) {
            var sourceUser = received[0];
            var order = received[1];
            this.$toasted.show('New order('+order.id+') is prepared on meal "'+order.meal_id+'" from "' + sourceUser.name + '!', {
                    theme: "bubble",
                    position: "bottom-center",
                    duration: 5000,
                    className: ['show'],
                    action : {
                        text : 'Show me',
                        onClick : (e, toastObject) => {
                            toastObject.goAway(0);
                            e.preventDefault();
                            // in here redirect the user via $router
                            this.$router.push({ path: 'orders-to-deliver', query: { meal_id: order.meal_id } });
                        }
                    }
                });
            }
        },
        update() {
            if(this.isAuthCook()) {
                this.$toasted.show('Orders was changed!!', {
                    theme: "bubble",
                    position: "bottom-center",
                    duration: 5000,
                    className: ['show'],
                    action : {
                        text : 'Show me',
                        onClick : (e, toastObject) => {
                            toastObject.goAway(0);
                            e.preventDefault();
                            // in here redirect the user via $router
                            this.$router.push({ path: 'orders'});
                        }
                    }
                });
            } else if(this.isAuthCashier()) {
                this.$toasted.show('Invoices was changed!', {
                    theme: "bubble",
                    position: "bottom-center",
                    duration: 5000,
                    className: ['show'],
                    action : {
                        text : 'Show me',
                        onClick : (e, toastObject) => {
                            toastObject.goAway(0);
                            e.preventDefault();
                            // in here redirect the user via $router
                            this.$router.push({ path: 'invoices'});
                        }
                    }
                });
            }
        }
    }
}
</script>
<style>
    .info{
        color: #004085 !important;
        background-color: #cce5ff !important;
        border-color: #b8daff !important;
        border-radius: .25rem !important;
    }
</style>


