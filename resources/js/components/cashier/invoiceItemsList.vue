<template>
    <div>
        <div class="container">
            <div v-if="showSuccess" class="alert alert-success alert-dismissible">
                <a href="#" class="close" v-on:click='showSuccess = false'>&times;</a>
                <strong>{{ messageTitle }}</strong> {{ message }}
            </div>
            <div v-if="showErro" class="alert alert-danger alert-dismissible">
                <a href="#" class="close" v-on:click='showErro = false'>&times;</a>
                <strong>{{ messageTitleErro }}</strong> {{ messageErro }}
            </div>
            <table class="table">
                <thead>
                    <tr class="table-active">
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Sub Total Price</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="invoiceItem in invoiceItems" :key="invoiceItem.id+'-'+invoiceItem.item_id">
                        <td>{{invoiceItem.item_name}}</td>
                        <td>{{invoiceItem.quantity}}</td>
                        <td>{{invoiceItem.unit_price}}</td>
                        <td>{{invoiceItem.sub_total_price}}</td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter" @click="seeInfoIteam(invoiceItem.item_id)">Show Details the item</button>
                        </td>
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
    props: ['method', 'invoice', 'invoiceItems', 'links'],
    data() {
        return {
            currentOrder: '',
            currentIteam: '',
            showSuccess: false,
            showErro: false,
            messageTitle: '',
            message: '',
            messageTitleErro: '',
            messageErro: ''
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