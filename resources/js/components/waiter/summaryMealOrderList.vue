<template>
    <div>
        <div class="container">
            <table class="table">
                <thead>
                    <tr class="table-active">
                        <th>ID order</th>
                        <th>ID item</th>
                        <th>Item Name</th>
                        <th>Item Type</th>
                        <th>Item Price</th>
                        <th>Item All Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="summary in summarys" :key="summary.order_id">
                        <td>{{summary.order_id}}</td>
                        <td>{{summary.item_id}}</td>
                        <td>{{summary.item_name}}</td>
                        <td>{{summary.item_type}}</td>
                        <td>{{summary.item_price}}</td>
                        <td><button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter" @click="seeInfoIteam(summary.item_id)">Show me item</button></td>
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
    props: ['method', 'summarys', 'links'],
    data() {
        return {
            currentIteam: ''
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