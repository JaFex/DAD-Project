<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row">
                <div class="col">
                    <b-jumbotron header="Dashboard" :lead="'Welcome, ' + this.$store.state.user.name">
                        <hr class="my-4">
                        <div>
                            <h4 class="mb-3">My shift informations</h4>
                            <template v-if="this.$store.state.user.shift_active == 0">
                                <p>Status: Inactive</p>
                                <p v-if="this.$store.state.user.last_shift_end">Last shift: {{ endDate }}</p>
                                <p v-if="this.$store.state.user.last_shift_end">Time since last shift: {{ endDateDif }}</p> 
                            </template>
                            <template v-else >
                                <p>Shift: Active</p>
                                <p v-if="this.$store.state.user.last_shift_start">Start at: {{ startDate }}</p>
                                <p v-if="this.$store.state.user.last_shift_start">Time since start of shift: {{ startDateDif }}</p>
                            </template> 
                        </div>
                           
                    </b-jumbotron>
                </div>
            </div>
        </div>
        <dashboardManagers v-if="this.$store.state.user.type === 'manager'"></dashboardManagers>
    </div>
</template>
<script>
import moment from 'moment';
export default {
    methods: {

    },
    components: {
        'nav-bar': require('./dashboardnav.vue'),
        'dashboardManagers': require('./manager/dashboardManagers.vue')
    },
    computed: {
        startDate() {
            let date = new moment(this.$store.state.user.last_shift_start);
            return date.format("DD/MM/YYYY HH:MM");
        },
        endDate() {
            let date = new moment(this.$store.state.user.last_shift_end);
            return date.format("DD/MM/YYYY HH:MM");
        },
        endDateDif() {
            let date = new moment(this.$store.state.user.last_shift_end);
            let now = new moment();
            let diff = moment.duration(now.diff(date));
            return diff.humanize();
        },
        startDateDif() {
            let date = new moment(this.$store.state.user.last_shift_start);
            let now = new moment();
            return moment.duration(now.diff(date)).humanize();
        },
    }
}
</script>