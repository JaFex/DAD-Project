<template>
    <div>
        <nav-bar :title="title"></nav-bar>
        <div class="container">
            <div class="card-deck" v-for="groupItem in groupedItems">
                <div v-for="item in groupItem" class="card bg-light border-secondary mb-3">
                    <img class="card-img-top" v-bind:src="item.photoUrl" style="max-heigth:50px;">
                    <div class="card-body">
                        <h5 class="card-title">{{ item.name }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">{{item.price}}€</h6>
                        <p class="card-text">{{ item.shortDescription }}</p>
                        <a href="#" class="btn btn-primary">More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
Vue.component('navBar', require('./navbar.vue'), {props: ['title']});
export default {
    props: ['title', 'groupedItems', 'links']
}
</script>
