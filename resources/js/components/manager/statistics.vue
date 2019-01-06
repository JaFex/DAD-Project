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
                        <bar-chart v-if="wloaded" :chartdata="waiterAvarage"></bar-chart>.
                    </b-card>
                </div>
                <div class="col-4">
                    <b-card class="text-center">
                        <bar-chart v-if="cloaded" :chartdata="cooksAvarage"></bar-chart>.
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
            wloaded: false,
            cloaded: false,
            waiterAvarage: {
                labels: [],
                datasets: [{
                    label: 'Waiters avarage',
                    data: [],
                    backgroundColor: 'rgba(54,73,93,.5)',
                }],
            },
            cooksAvarage: {
                labels: [],
                datasets: [{
                    label: 'Cooks avarage',
                    data: [],
                    backgroundColor: 'rgba(54,73,93,.5)',
                }],
            },
        }
    },
    methods: {
        getMealsByWaiter(){
            axios.get('/api/statistics/meals')
                .then(response => {
                    response.data.forEach(element => {
                        this.waiterAvarage.labels.push(element['responsible_waiter_id']);
                        this.waiterAvarage.datasets[0].data.push(element['average'])
                    });
                    this.wloaded = true;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        getOrdersByCook(){
            axios.get('/api/statistics/orders')
                .then(response => {
                    response.data.forEach(element => {
                        if(element['responsible_cook_id'] !== null){
                            this.cooksAvarage.labels.push(element['responsible_cook_id']);
                            this.cooksAvarage.datasets[0].data.push(element['average']);
                        }
                    });
                    this.cloaded = true;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    },
    created() {
        this.getMealsByWaiter();
        this.getOrdersByCook();
    }
     
}
</script>
