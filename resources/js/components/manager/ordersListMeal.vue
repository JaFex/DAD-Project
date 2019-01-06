<template>
    <div>
        <div class="container">
            <table class="table">
                <thead>
                    <tr class="table-active">
                        <th>ID</th>
                        <th>State</th>
                        <th>Cook</th>
                        <th>Item</th>
                        <th>Start</th>
                        <th>End</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders" :key="order.id">
                        <td>{{order.id}}</td>
                        <td>{{order.state}}</td>
                        <td>{{order.responsible_cook_id?'('+order.responsible_cook_id+')-'+ order.responsible_cook_name:''}}</td>
                        <td><button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter" @click="seeInfoIteam(order.item_id)">Show me item</button></td>
                        <td>{{order.start}}</td>
                        <td>{{order.end}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <pagination :method="method" :links="links"></pagination>
        <modalItem :object="currentIteam"></modalItem>
    </div>
</template>
<script>
export default {
    props: ['method', 'meal', 'orders', 'links'],
    data() {
        return {
            currentIteam: '',
            orderLocal: '',
        }
    },
    methods: {
        seeInfoIteam: function(item_id){
            axios.get('/api/items/'+item_id)
                .then(response => {
                    this.currentIteam = response.data.data;
                })
                .catch(function (error) {
                    console.log("seeInfoIteam->"+error);
                });
        }
    },
    computed: {
    },
    components:{
        'pagination': require('../pagination.vue'),
        'modalItem': require('../modalItem.vue')
    },
    created() {
    },
}
</script>