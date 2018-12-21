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
                    <div v-if="showSuccess" class="alert alert-success alert-dismissible">
                        <a href="#" class="close" v-on:click='showSuccess = false'>&times;</a>
                        <strong>{{ messageTitle }}</strong> {{ message }}
                    </div>
                    <div v-if="showErro" class="alert alert-danger alert-dismissible">
                        <a href="#" class="close" v-on:click='showErro = false'>&times;</a>
                        <strong>{{ messageTitle }}</strong> {{ message }}
                    </div>
                    <table class="table">
                        <thead>
                            <tr class="table-active">
                                <th>ID</th>
                                <th>Meal id</th>
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
                                    <td>{{invoice.state}}</td>
                                    <td>{{invoice.nif}}</td>
                                    <td>{{invoice.name}}</td>
                                    <td>{{invoice.date}}</td>
                                    <td>{{invoice.total_price}}</td>
                                    <td>
                                        <button v-if="invoice.total_price > 0" type="button" class="btn btn-primary" @click.prevent="openInvoicesItems(invoice);">See all invoices items</button>
                                        <button type="button" class="btn btn-primary" @click.prevent="">Paid</button>
                                        <button type="button" class="btn btn-danger" @click.prevent="">Not Paid</button>
                                    </td>
                                </tr>
                                <tr v-if='currentInvoice && invoice.id == currentInvoice.id' :key="'invoice_'+invoice.id">
                                    <td colspan="6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title float-left">Invoice ({{ invoice.id }}) items</h5>
                                                <button type="button" class="btn btn-danger float-right" @click.prevent="closeListInvoicesItems()">Close</button>
                                            </div>
                                            <div class="card-body">
                                                <invoiceItemsList :method="loadInvoiceItems" :invoice="currentInvoice" :invoiceItems="invoiceItems" :links="linksInvoiceItems"></invoiceItemsList>
                                            </div>
                                        </div>
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
            showSuccess: false,
            showErro: false,
            message: '',
            messageTitle: '',
            invoices: {},
            invoiceItems: {},
            links: {},
            linksInvoiceItems: {},
            currentInvoice: '',
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
        loadInvoicesSamePage: function(url, currentPage){
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
        openInvoicesItems: function(invoice) {
            this.currentInvoice = invoice;
            this.loadInvoiceItems('invoices/'+invoice.id+'/items');
        },
        loadInvoiceItems: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.invoiceItems = response.data.data;
                    this.linksInvoiceItems = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log("loadInvoiceItems-"+error);
                });
        },
        closeListInvoicesItems: function(){
            this.currentInvoice = '';
        },
        reloadInvoiceAndInvoicesItems: function() {
            this.loadInvoices('invoices');
            if(this.currentInvoice) {
                this.loadInvoiceItems('invoices/'+this.currentInvoice.id+'/items');
            }
        }
    },
    computed: {
        
    },
    components:{
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
        'invoiceItemsList': require('./invoiceItemsList.vue')
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