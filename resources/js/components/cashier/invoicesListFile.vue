<template>
    <div> 
        <nav-bar></nav-bar>
        <br>
        <br>
        <br>
        <br>
        <br>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title float-left">Invoices</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr class="table-active">
                                <th>ID</th>
                                <th>Meal ID</th>
                                <th>Waiter (ID)-Name</th>
                                <th>Table Number</th>
                                <th>State</th>
                                <th>NIF</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Total price</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="invoice in invoices">
                                <tr :key="invoice.id">
                                    <td>{{invoice.id}}</td>
                                    <td>{{invoice.meal_id}}</td>
                                    <td>{{'('+invoice.waiter_id+')-'+ invoice.waiter_name}}</td>
                                    <td>{{invoice.table_number}}</td>
                                    <td>{{invoice.state}}</td>
                                    <td>{{invoice.nif}}</td>
                                    <td>{{invoice.name}}</td>
                                    <td>{{invoice.date}}</td>
                                    <td>{{invoice.total_price}}</td>
                                    <td>
                                        <!--<button type="button" class="btn btn-success" @click.prevent="downloadPDF(invoice)">Download PDF</button>-->
                                        <a :href="url+'api/invoices/'+invoice.id+'/download'" type="application/pdf" class="btn btn-success">Download PDF</a>
                                    </td>
                                </tr>
                            </template >
                        </tbody>
                    </table>
                    <pagination :method="loadInvoices" :links="links"></pagination>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            invoices: {},
            links: {},
            url: window.location.protocol + "//" + window.location.host + "/" + window.location.pathname.split('/')[1],
        }
    },
    methods: {
        loadInvoices: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.invoices = response.data.data;
                    this.links = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log("loadInvoices-"+error);
                });
        },
        loadInvoicesSamePage: function(url, currentPage) {
            axios.get('/api/'+url+'?page='+currentPage)
                .then(response => {
                    this.invoices = response.data.data;
                    this.links = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: currentPage,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log("loadInvoicesSamePage-"+error);
                });
        },
        downloadPDF: function(invoice) {
            axios.get('/api/invoices/'+invoice.id+'/download')
                .then(response => {
                    console.log("downloadPDF-"+response);
                    console.log("downloadPDF-"+Object.values(response));
                })
                .catch(function (error) {
                    console.log("downloadPDF-"+error);
                });
        }
    },
    computed: {
        
    },
    components:{
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
    },
    created() {
        this.loadInvoices('invoices');
    },
    sockets:{
        update() {
            console.log('---SOCKETS TELL TO UPDATE---');
            this.loadInvoicesSamePage('invoices', this.links.currentPage);
        },
    }
}
</script>