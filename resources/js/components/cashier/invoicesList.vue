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
                                <th>Meal ID</th>
                                <th>Waiter (ID)-Name</th>
                                <th>Table Number</th>
                                <th>State</th>
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
                                    <td>{{invoice.date}}</td>
                                    <td>{{invoice.total_price}}</td>
                                    <td>
                                        <button v-if="invoice.total_price > 0" type="button" class="btn btn-primary" @click.prevent="openInvoicesItems(invoice);">See all invoices items</button>
                                        <button type="button" class="btn btn-success" @click.prevent="showPaidForm(invoice)">Paid and Fill Info</button>
                                    </td>
                                </tr>
                                <tr v-if='currentInvoice && invoice.id == currentInvoice.id' :key="'invoice_'+invoice.id">
                                    <td colspan="10">
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
        <formPaidFillInfo v-if="showForm" :messagemServer="messageToForm" :methodDone="paidTheMeal" :methodClose="hidePaidForm" :invoice="currentInvoiceToPaid" :invoiceItems="invoiceItems"></formPaidFillInfo>
    </div>
</template>
<script>
export default {
    data() {
        return {
            showForm: false,
            showSuccess: false,
            showErro: false,
            messageToForm: '',
            message: '',
            messageTitle: '',
            invoices: {},
            invoiceItems: {},
            links: {},
            linksInvoiceItems: {},
            currentInvoice: '',
            currentInvoiceToPaid: ''
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
            this.loadInvoices('invoices/all/pending');
            if(this.currentInvoice) {
                this.loadInvoiceItems('invoices/'+this.currentInvoice.id+'/items');
            }
        },
        showPaidForm: function(invoice) {
            this.showForm=true;
            this.currentInvoiceToPaid=invoice;
            this.messageToForm = '';
        },
        hidePaidForm: function() {
            this.showForm=false;
            this.currentInvoiceToPaid=null;
            this.messageToForm = '';
        },
        paidTheMeal: function(invoice_id, name, nif) {
            this.showSuccess = false;
            this.showErro = false;
            if(this.currentInvoiceToPaid.id != invoice_id) {
                this.showErro = true;
                this.message = 'The item to pay is different from the selected.';
                this.messageTitle = 'Ops!';
                this.hidePaidForm();
                return;
            }
            let soft = this;
            var array = {};
            array['name'] = name;
            array['nif'] = nif;
            array['state'] = 'paid';
            
            axios.put('/api/invoices/'+this.currentInvoiceToPaid.id, array)
                .then(response => {
                    soft.$socket.emit('managerMeal', soft.currentInvoiceToPaid.meal_id, soft.currentInvoiceToPaid.state);
                    soft.$socket.emit('cashierWichoutMe');
                    soft.currentInvoiceToPaid = response.data.data;
                    soft.showSuccess = true;
                    soft.message = 'The invoice is paid.';
                    soft.messageTitle = 'Paid Successful!';
                    soft.hidePaidForm();
                    soft.loadInvoicesSamePage('invoices/all/pending', this.links.currentPage);
                })
                .catch(function (error) {
                    console.log("needToBeConfirmed->"+error);
                    soft.messageToForm = 'Something went wrong with the server.';
                });
        }
    },
    computed: {
        
    },
    components:{
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
        'invoiceItemsList': require('./invoiceItemsList.vue'),
        'formPaidFillInfo': require('./formPaidFillInfo.vue')
    },
    created() {
        this.loadInvoices('invoices/all/pending');
    },
    sockets:{
        update() {
            console.log('---SOCKETS TELL TO UPDATE---');
            this.loadInvoicesSamePage('invoices/all/pending', this.links.currentPage);
        },
    }
}
</script>