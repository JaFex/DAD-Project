<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row">
                <div class="col">
                    <b-jumbotron header="Statistics" lead="Restaurant statistics">
                    </b-jumbotron>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <b-card class="text-center">
                        <bar-chart></bar-chart>.
                    </b-card>
                </div>
                <div class="col-4">
                    <b-card class="text-center">
                        <bar-chart></bar-chart>.
                    </b-card>
                </div>
                <div class="col-4">
                    <b-card class="text-center">
                        <bar-chart></bar-chart>.
                    </b-card>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import barChart from './lineChart.vue';
export default {
    components: {
        'nav-bar': require('../dashboardnav.vue'),
        'bar-chart': barChart
    },
    data() {
        return {
            results: ''
        }
    },
    methods: {
        getMealsByWaiter(){
            axios.get('/api/statistics/meals')
                .then(response => {
                    console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    },
    mounted() {
        this.getMealsByWaiter();
    }
     
}
</script>
