<template>
    <b-navbar toggleable="md" type="dark" variant="dark" class="fixed-top">
            <router-link to="/" tag="a" class="navbar-brand">Restaurant</router-link>
            <b-navbar-nav>
                <li class="nav-item">
                    <router-link to="/dashboard" class="nav-link" tag="a">Dashboard</router-link>
                </li>
                <b-nav-item href="#">Items</b-nav-item>
                <b-nav-item href="#">Tables</b-nav-item>
                <li class="nav-item">
                    <router-link to="/orders" class="nav-link" tag="a">Orders</router-link>
                </li>
                <b-nav-item-dropdown>
                    <template slot="button-content">Users</template>
                    <router-link to="/new-user" tag="a" role="manuitem" class="dropdown-item">New User</router-link>
                </b-nav-item-dropdown>
            </b-navbar-nav>
            <b-navbar-nav class="ml-auto">
                <b-img rounded width="30" height="30" alt="photo" class="m-1" :src="imagePath(this.$store.state.user.photo_url)" />
                <b-nav-item-dropdown right>
                    <template slot="button-content">
                        <em>{{this.$store.state.user.name}}</em>
                    </template>
                    <router-link to="/profile" tag="a" role="manuitem" class="dropdown-item">Profile</router-link>
                    <b-dropdown-item v-on:click.prevent="logout">Logout</b-dropdown-item>
                </b-nav-item-dropdown>
            </b-navbar-nav>
        </b-navbar>
</template>
<script>
export default {
    methods: {
        logout() {
            this.showMessage = false;
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
        imagePath(img) {
            return 'storage/profiles/'+img;
        }
    }
}
</script>

