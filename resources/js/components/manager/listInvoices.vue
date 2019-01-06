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
                    <h5 class="card-title float-left">Meals</h5>
                </div>
                <div class="card-body">
                    <form>
                        <h6 class="card-title float-left">Search:</h6>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Waiter: </span>
                            </div>
                            <select class="form-control" aria-describedby="basic-addon2" v-model="selectedWaiter">
                                <option :value="0">No Waiter</option>
                                <option v-for="waiter in waiters" :value="waiter.id" :key="waiter.id">{{"("+waiter.id+")-"+waiter.name}}</option>
                            </select>
                        </div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">State:  </span>
                            </div>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck1" v-model="selectedPending">
                                    <label class="form-check-label" for="gridCheck1">Pending</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck3" v-model="selectedPaid">
                                    <label class="form-check-label" for="gridCheck1">Paid</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck4" v-model="selectedNotPaid">
                                    <label class="form-check-label" for="gridCheck1">Not Paid</label>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">Start Date: </span>
                                <div class="input-group-text">
                                    <input type="checkbox" aria-label="Checkbox for following text input" v-model="selectedDateShow">
                                </div>
                            </div>
                            <input class="form-control" type="date" v-model="selectedDate" id="example-date-input" :disabled="selectedDateShow==false">
                        </div>
                        <button type="button" class="btn btn-primary float-right" @click.prevent="search()">Search</button>
                        <button type="button" class="btn btn-danger float-right" @click.prevent="clear()">Clear</button>
                    </form>
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
                                        <button v-if="invoice.state === 'paid'" type="button" class="btn btn-success" @click.prevent="downloadPDF(invoice)">Download PDF</button>
                                    </td>
                                </tr>
                                <tr v-if='currentInvoice && invoice.id == currentInvoice.id' :key="'invoice_'+invoice.id">
                                    <td colspan="8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title float-left">Invoice ({{ invoice.id }}) items</h5>
                                                <button type="button" class="btn btn-danger float-right" @click.prevent="closeListInvoicesItems()">Close</button>
                                            </div>
                                            <div class="card-body">
                                                <invoiceListItems :method="loadInvoiceItems" :invoice="currentInvoice" :invoiceItems="invoiceItems" :links="linksInvoiceItems"></invoiceListItems>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template >
                        </tbody>
                    </table>
                </div>
                <pagination :method="loadInvoices" :links="links"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            invoices: {},
            invoiceItems: {},
            links: {},
            linksInvoiceItems: {},
            currentInvoice: '',
            waiters: {},
            selectedWaiter: 0,
            selectedPending: true,
            selectedPaid: false,
            selectedNotPaid: false,
            selectedDateShow: false,
            selectedDate: '',
            currentMeal: '',
            url: window.location.protocol + "//" + window.location.host + "/" + window.location.pathname.split('/')[1],
        }
    },
    methods: {
        loadInvoices: function(url){
            if(this.selectedPending || this.selectedPaid || this.selectedNotPaid) {
                console.log("is in");
                var info = {};
                if(this.selectedPending) {
                    info["pending"] = this.selectedPending;
                }
                if(this.selectedPaid) {
                    info["paid"] = this.selectedPaid;
                }
                if(this.selectedNotPaid) {
                    info["not_paid"] = this.selectedNotPaid;
                }
                if(this.selectedWaiter != 0) {
                    info["waiter"] = this.selectedWaiter;
                }
                if(this.selectedDateShow) {
                    info["date"] = this.selectedDate;
                }

                axios.get('/api/'+url, {
                            params: info
                        }
                    )
                    .then(response => {
                        this.invoices = response.data.data;
                        if(this.invoices.length <= 0) {
                            this.$toasted.show('No invoices found!', {
                                theme: "bubble",
                                position: "bottom-center",
                                duration: 5000,
                                className: ['error']
                            });
                        }
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
            }
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
        loadWaiters: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.waiters = response.data.data;
                })
                .catch(function (error) {
                    console.log("loadWaiters-"+error);
                });
        },
        search: function(){
            this.currentMeal = '';
            this.loadInvoices('invoices/filter');
        },
        clear: function(){
            this.selectedWaiter = 0;
            this.selectedActive = true;
            this.selectedTerminated = true;
            this.selectedPaid = false;
            this.selectedNotPaid = false;
            this.selectedDateShow = false;
            var date = new Date();
            this.selectedDate = date.getFullYear()+'-'+((date.getMonth()+1)<10?'0':'')+''+(date.getMonth()+1)+'-'+(date.getDate()<10?'0':'')+date.getDate();

            this.currentMeal = '';
            this.loadInvoices('invoices/filter');
        },
        downloadPDF: function(invoice) {
            let soft = this;
            if(!invoice || invoice.state !== 'paid') {
                soft.$toasted.show('Invoice invalid', {
                            theme: "bubble",
                            position: "bottom-center",
                            duration: 5000,
                            className: ['error']
                        });
            }
            axios({
                url: 'api/invoices/'+invoice.id+'/download',
                method: 'GET',
                responseType: 'blob', // important
            }).then((response) => {
                const urlLocal = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = urlLocal;
                link.setAttribute('download', 'invoice_'+invoice.id+'.pdf');
                document.body.appendChild(link);
                link.click();
            }).catch(function (error) {
                    console.log("downloadPDF-"+error.response.data.data);
                    soft.$toasted.show(error.response.data.data, {
                            theme: "bubble",
                            position: "bottom-center",
                            duration: 5000,
                            className: ['error']
                        });
                });
        }
    },
    computed: {
        
    },
    components:{
        'nav-bar': require('../dashboardnav.vue'),
        'pagination': require('../pagination.vue'),
        'invoiceListItems': require('./invoiceListItems.vue')
    },
    created() {
        this.loadInvoices('invoices/filter');
        this.loadWaiters('users/search/waiter');
        var date = new Date();
        this.selectedDate = date.getFullYear()+'-'+((date.getMonth()+1)<10?'0':'')+''+(date.getMonth()+1)+'-'+(date.getDate()<10?'0':'')+date.getDate();
    },
    sockets:{

    }
}
</script>