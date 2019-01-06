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
                <div class="col">
                    <b-card class="text-center" title="Waiters Average">
                        <bar-chart v-if="wloaded" :chartdata="waiterAvarage"></bar-chart>.
                    </b-card>
                </div>
                <div class="col">
                    <b-card class="text-center" title="Cooks Average">
                        <bar-chart v-if="cloaded" :chartdata="cooksAvarage"></bar-chart>.
                    </b-card>
                </div>
                <div class="col">
                    <b-card class="text-center" title="Monthly Meals">
                        <line-chart v-if="mloaded" :chartdata="mealsMonth"></line-chart>.
                    </b-card>
                </div>
                <div class="col">
                    <b-card class="text-center" title="Monthly Orders">
                        <line-chart v-if="oloaded" :chartdata="ordersMonth"></line-chart>.
                    </b-card>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import barChart from './barChart.vue';
import lineChart from './lineChart.vue';
export default {
    components: {
        'nav-bar': require('../dashboardnav.vue'),
        'bar-chart': barChart,
        'line-chart': lineChart
    },
    data() {
        return {
            wloaded: false,
            cloaded: false,
            mloaded: false,
            oloaded: false,
            waiterAvarage: {
                labels: [],
                datasets: [{
                    label: 'Waiters avarage',
                    data: [],
                    backgroundColor: 'rgba(0,123,255,.7)',
                }],
            },
            cooksAvarage: {
                labels: [],
                datasets: [{
                    label: 'Cooks avarage',
                    data: [],
                    backgroundColor: 'rgba(0,123,255,.7)',
                }],
            },
            mealsMonth: {
                labels: [],
                datasets: [{
                    label: 'Monthly Meals',
                    data: [],
                    backgroundColor: 'rgba(0,123,255,.7)',
                }],
            },
            ordersMonth: {
                labels: [],
                datasets: [{
                    label: 'Monthly Orders',
                    data: [],
                    backgroundColor: 'rgba(0,123,255,.7)',
                }]
            }
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
        },
        getMonthlyValues(){
            axios.get('/api/statistics/meals/monthly')
                .then(response => {
                    response.data.forEach(element => {
                            this.mealsMonth.labels.push(element['date']);
                            this.mealsMonth.datasets[0].data.push(element['total']);
                    });
                    this.mloaded = true;
                })
                .catch(function (error) {
                    console.log(error);
            });
        },
        getMonthlyOrders(){
            axios.get('/api/statistics/orders/monthly')
                    .then(response => {
                        response.data.forEach(element => {
                            this.ordersMonth.labels.push(element['date']);
                            this.ordersMonth.datasets[0].data.push(element['total']);
                        });
                        this.oloaded = true;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
        }
    },
    created() {
        this.getMealsByWaiter();
        this.getOrdersByCook();
        this.getMonthlyValues();
        this.getMonthlyOrders();
    }
     
}
</script>
