<template>
  <div class="container">
    <form>
      <div v-if="showSuccess" class="alert alert-success alert-dismissible">
        <a href="#" class="close" v-on:click="showSuccess = false">&times;</a>
        <strong>{{ messageTitle }}</strong>
        {{ message }}
      </div>
      <div v-if="showErro" class="alert alert-danger alert-dismissible">
        <a href="#" class="close" v-on:click="showErro = false">&times;</a>
        <strong>{{ messageTitle }}</strong>
        {{ message }}
      </div>
    </form>
    <div class="card">
      <div class="card-header">
        <h5 class="card-title float-left">Pending Invoices</h5>
      </div>
      <div class="card-body">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>State</th>
              <th>Meal ID</th>
              <th>Table Number</th>
              <th>Waiter Name</th>
              <th>Current Total Price</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="invoice in invoices">
              <tr :key="invoice.id">
                <td>{{invoice.id}}</td>
                <td>{{invoice.state}}</td>
                <td>{{invoice.meal_id}}</td>
                <td>{{invoice.table_number}}</td>
                <td>{{'('+invoice.waiter_id+')-'+ invoice.waiter_name}}</td>
                <td>{{invoice.total_price}}</td>
                <td>
                  <button v-if="invoice.total_price > 0" type="button" class="btn btn-primary" @click.prevent="openInvoicesItems(invoice);">See all invoices items</button>
                  <button type="button" class="btn btn-danger" @click.prevent="sendUpdateToNotPaid(invoice, 'invoice');">Not paid</button>
                </td>
              </tr>
              <tr v-if="currentInvoice && invoice.id == currentInvoice.id" :key="'invoice_'+invoice.id">
                  <td colspan="7">
                    <div class="card">
                      <div class="card-header">
                        <h5 class="card-title float-left">Invoice ({{ invoice.id }}) items</h5>
                        <button type="button" class="btn btn-danger float-right" @click.prevent="closeListOrders()">Close</button>
                      </div>
                      <div class="card-body">
                        <invoiceListItems :method="loadInvoiceItems" :invoice="currentInvoice" :invoiceItems="invoiceItems" :links="linksInvoiceItems"></invoiceListItems>
                      </div>
                    </div>
                  </td>
                </tr>
            </template>
          </tbody>
        </table>
        <pagination :method="loadPendingInvoices" :links="linksInvoices"></pagination>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h5 class="card-title float-left">Active and Terminated Meals</h5>
      </div>
      <div class="card-body">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>State</th>
                <th>Table Number</th>
                <th>Waiter Name</th>
                <th>Current Total Price</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="meal in meals">
                <tr :key="meal.id">
                  <td>{{meal.id}}</td>
                  <td>{{meal.state}}</td>
                  <td>{{meal.table_number}}</td>
                  <td>{{'('+meal.responsible_waiter_id+')-'+ meal.responsible_waiter}}</td>
                  <td>{{meal.total_price_preview}}</td>
                  <td>
                    <button v-if="meal.total_price_preview > 0" type="button" class="btn btn-primary" @click.prevent="openOrders(meal);">See orders</button>
                    <button v-if="meal.state === 'terminated'" type="button" class="btn btn-danger" @click.prevent="sendUpdateToNotPaid(meal, 'meal');">Not paid</button>
                  </td>
                </tr>
                <tr v-if="currentMeal && meal.id == currentMeal.id" :key="'meal_'+meal.id">
                  <td colspan="6">
                    <div class="card">
                      <div class="card-header">
                        <h5 class="card-title float-left">Order of meal {{ meal.id }}</h5>
                        <button type="button" class="btn btn-danger float-right" @click.prevent="closeListOrders()">Close</button>
                      </div>
                      <div class="card-body">
                        <ordersListMeal :method="loadOrders" :meal="currentMeal" :orders="orders" :links="linksOrders"></ordersListMeal>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
          <pagination :method="loadActiveAndTerminatedMeals" :links="linksMeals"></pagination>
      </div>
    </div>
  </div>
</template>
<script>
import moment from "moment";
export default {
  data() {
    return {
      invoices: {},
      linksInvoices: {},
      meals: {},
      linksMeals: {},
      showSuccess: false,
      showErro: false,
      message: '',
      currentMeal: '',
      currentInvoice: '',
      orders: {},
      linksOrders: {},
      invoiceItems: {},
      linksInvoiceItems: {},
    };
  },
  methods: {
    sendUpdateToNotPaid: function(object, type) {
      let soft = this;
      let state = object.state;
      var update = {};
      if(type === 'meal' && object === 'active') {
        this.showErro = true;
        this.message = 'You cant not change meal on state active to not paid!';
      }
      update["state"] = "not paid";
      axios.put("/api/"+type+"s/" + object.id + "/notpaid", update)
        .then(response => {
          object = response.data;
          soft.$toasted.show("The "+type+" (" + object.id + ") is declare was 'not paid'", {
            theme: "bubble",
            position: "bottom-center",
            duration: 5000,
            className: ["success"]
          });
          soft.loadActiveAndTerminatedMealsSamePage('meals/filter');
          soft.loadPendingInvoicesSamePage('invoices/all/pending');
          soft.$socket.emit("cashier");
        })
        .catch(function(error) {
          console.log("sendUpdateToNotPaid->" + error);
          soft.$toasted.show("ERRO: Couldn´t change state of "+type+"(" + object.id + ") to not paid", {
              theme: "bubble",
              position: "bottom-center",
              duration: 5000,
              className: ["error"]
            }
          );
        });
    },
    loadPendingInvoices: function(url) {
      axios.get('/api/'+url)
        .then(response => {
            this.invoices = response.data.data;
            this.linksInvoices = {
                prev: response.data.links.prev,
                next: response.data.links.next,
                currentPage: response.data.meta.current_page,
                lastPage: response.data.meta.last_page,
                path: url+'?page='
            }
        })
        .catch(function (error) {
            console.log("loadPendingInvoices-"+error);
        });
        
    },
    loadActiveAndTerminatedMeals: function(url) {
      var info = {};
        info["active"] = true;
        info["terminated"] = true;
        axios.get('/api/'+url, { params: info }).then(response => {
                this.meals = response.data.data;
                this.linksMeals = {
                    prev: response.data.links.prev,
                    next: response.data.links.next,
                    currentPage: response.data.meta.current_page,
                    lastPage: response.data.meta.last_page,
                    path: url+'?page='
                }
            })
            .catch(function (error) {
                console.log("loadActiveAndTerminatedMeals-"+error);
            });
    },
    openOrders: function(meal) {
      this.currentMeal = meal;
      this.loadOrders("meals/" + meal.id + "/orders/all");
    },
    closeListOrders: function() {
      this.currentMeal = "";
    },
    loadOrders: function(url) {
      axios.get("/api/" + url)
        .then(response => {
          this.orders = response.data.data;
          this.linksOrders = {
            prev: response.data.links.prev,
            next: response.data.links.next,
            currentPage: response.data.meta.current_page,
            lastPage: response.data.meta.last_page,
            path: url + "?page="
          };
        })
        .catch(function(error) {
          console.log("loadOrders-" + error);
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
    loadPendingInvoicesSamePage: function(url) {
      axios.get('/api/'+url+'?page='+this.linksInvoices.current_page)
        .then(response => {
            this.invoices = response.data.data;
            this.linksInvoices = {
                prev: response.data.links.prev,
                next: response.data.links.next,
                currentPage: response.data.meta.current_page,
                lastPage: response.data.meta.last_page,
                path: url+'?page='
            }
        })
        .catch(function (error) {
            console.log("loadPendingInvoices-"+error);
        });
        
    },
    loadActiveAndTerminatedMealsSamePage: function(url) {
      var info = {};
      info["page"] = this.linksMeals.current_page;
      info["active"] = true;
      info["terminated"] = true;
      axios.get('/api/'+url, { params: info }).then(response => {
              this.meals = response.data.data;
              this.linksMeals = {
                  prev: response.data.links.prev,
                  next: response.data.links.next,
                  currentPage: response.data.meta.current_page,
                  lastPage: response.data.meta.last_page,
                  path: url+'?page='
              }
          })
          .catch(function (error) {
              console.log("loadActiveAndTerminatedMeals-"+error);
          });
    }
  },
  components: {
    "nav-bar": require("../dashboardnav.vue"),
    'pagination': require('../pagination.vue'),
    'invoiceListItems': require('./invoiceListItems.vue'),
    'ordersListMeal': require('./ordersListMeal.vue')
  },
  computed: {

  },
  created() {
    this.loadPendingInvoices('invoices/all/pending');
    this.loadActiveAndTerminatedMeals('meals/filter');
  },
    sockets:{
        updateInvoiceAndMeal() {
            console.log('---SOCKETS TELL TO UPDATE INVOICES---');
            this.loadActiveAndTerminatedMealsSamePage('meals/filter');
            this.loadPendingInvoicesSamePage('invoices/all/pending');
            this.currentMeal = "";
            this.currentInvoice = "";
        },
        updateMeal() {
            console.log('---SOCKETS TELL TO UPDATE MEALS---');
            this.loadActiveAndTerminatedMealsSamePage('meals/filter');
            this.currentMeal = "";
        },
        updateOrders(order) {
            console.log('---SOCKETS TELL TO UPDATE ORDERS---');
            console.dir(order);
            if(order.state === 'pending' || order.state === 'deleted'){
              this.loadActiveAndTerminatedMealsSamePage('meals/filter');
            }
            if(this.currentMeal && order.meal_id && this.currentMeal.id == order.meal_id) {
              this.loadOrders("meals/" + this.currentMeal.id + "/orders/all");
            }
        }
    }
};
</script>