<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row">
                <div class="col">
                    <b-jumbotron header="Dashboard">
                    </b-jumbotron>
                </div>
            </div>
        </div> 
    </div>
</template>
<script>
export default {
    methods: {
        
    },
    components: {
        'nav-bar': require('./dashboardnav.vue')
    }
}
</script>