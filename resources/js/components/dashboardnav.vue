<template>
    <b-navbar toggleable="md" type="dark" variant="dark" class="fixed-top">
            <router-link to="/" tag="a" class="navbar-brand">Restaurant</router-link>
            <b-navbar-nav>
                <li class="nav-item">
                    <router-link to="/dashboard" class="nav-link" tag="a">Dashboard</router-link>
                </li>
                <li v-if="isAuthWaiter()" class="nav-item">
                    <router-link to="/meals" class="nav-link" tag="a">Meals</router-link>
                </li>
                <li v-if="isAuthCashier()" class="nav-item">
                    <router-link to="/cashier" class="nav-link" tag="a">Cashier</router-link>
                </li>
                <li v-if="isAuthCook()" class="nav-item">
                    <router-link to="/orders" class="nav-link" tag="a">Orders</router-link>
                </li>
                <b-nav-item-dropdown v-if="isAuthManager()">
                    <template slot="button-content">Users</template>
                    <router-link to="/new-user" tag="a" role="manuitem" class="dropdown-item">New User</router-link>
                </b-nav-item-dropdown>
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
                })
                .catch(error => {
                    console.log(error);
                });
        },
        endShift() {
            axios.put('api/users/' + this.$store.state.user.id + '/end')
                .then(response => {
                    this.$store.commit('setUser',response.data.data);
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
    }
}
</script>

