<template>
  <div>
    <nav-bar></nav-bar>
    <br>
    <br>
    <br>
    <br>
    <br>
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
          <h5
            class="card-title float-left"
          >'Pending' invoices, 'Active' and 'Terminated' meals (and its orders)</h5>
        </div>
        <div class="card-body">
          <h3>Pending Invoices</h3>
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>State</th>
                <th>Meal ID</th>
                <th>Table Number</th>
                <th>Waiter Name</th>
                <th>Current Total Price</th>
                <th>Change State</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="invoice in invoices">
                <tr :key="invoice.id">
                  <td>{{invoice.id}}</td>
                  <td>{{invoice.state}}</td>
                  <td>{{invoice.meal_id}}</td>
                  <td>{{invoice.table_number}}</td>
                  <td>{{invoice.waiter_name}}</td>
                  <td>{{invoice.total_price}}</td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-primary"
                      @click.prevent="sendUpdateToNotPaidInvoice(invoice);"
                    >Not paid</button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>

          <h3>Active and Terminated Meals</h3>
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>State</th>
                <th>Table Number</th>
                <th>Waiter Name</th>
                <th>Current Total Price</th>
                <th>Action</th>
                <th>Change State</th>
              </tr>
            </thead>
            <tbody>
              <template v-for="meal in activemeals">
                <tr :key="meal.id">
                  <td>{{meal.id}}</td>
                  <td>{{meal.state}}</td>
                  <td>{{meal.table_number}}</td>
                  <td>{{meal.responsible_waiter}}</td>
                  <td>{{meal.total_price_preview}}</td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-primary"
                      @click.prevent="openOrders(meal);"
                    >See orders</button>
                  </td>
                </tr>
                <tr v-if="currentMeal && meal.id == currentMeal.id" :key="'meal_'+meal.id">
                  <td colspan="6">
                    <div class="card">
                      <div class="card-header">
                        <h5 class="card-title float-left">Order of meal {{ meal.id }}</h5>
                        <button
                          type="button"
                          class="btn btn-danger float-right"
                          @click.prevent="closeListOrders()"
                        >Close</button>
                      </div>
                      <div class="card-body">
                        <ordersMealList
                          @clickReloadMealAndOrder="reloadMealAndOrder"
                          @clickUpdateKitchen="updateKitchen"
                          @clickUpdateOrder="updateOrder"
                          :method="loadOrders"
                          :meal="currentMeal"
                          :orders="orders"
                          :links="linksOrders"
                        ></ordersMealList>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
              <template v-for="meal in terminatedmeals">
                <tr :key="meal.id">
                  <td>{{meal.id}}</td>
                  <td>{{meal.state}}</td>
                  <td>{{meal.table_number}}</td>
                  <td>{{meal.responsible_waiter}}</td>
                  <td>{{meal.total_price_preview}}</td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-primary"
                      @click.prevent="openOrders(meal);"
                    >See orders</button>
                  </td>
                  <td>
                    <button
                      type="button"
                      class="btn btn-primary"
                      @click.prevent="sendUpdateToNotPaidMeal(meal);"
                    >Not paid</button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
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
      activemeals: {},
      terminatedmeals: {},
      showSuccess: false,
      showErro: false,
      currentMeal: '',
      orders: {},
      linksOrders: {}
    };
  },
  methods: {
    sendUpdateToNotPaidMeal: function(meal) {
      let soft = this;
      var update = {};
      update["state"] = "notpaid";
      axios
        .put("/api/meals/" + meal.id + "/notpaid", update)
        .then(response => {
          meal = response.data.data;
          soft.$toasted.show("The meal (" + meal.id + ") is not paid", {
            theme: "bubble",
            position: "bottom-center",
            duration: 5000,
            className: ["success"]
          });
          this.loadActiveAndTerminatedMeals();
          this.loadPendingInvoices();
          soft.$socket.emit("kitchen");
          soft.$socket.emit("cashier");
        })
        .catch(function(error) {
          console.log("sendUpdateToNotPaidMeal->" + error);
          soft.$toasted.show(
            "ERRO: Couldn´t change state of (" + meal.id + ") to not paid",
            {
              theme: "bubble",
              position: "bottom-center",
              duration: 5000,
              className: ["error"]
            }
          );
        });
    },
    sendUpdateToNotPaidInvoice: function(invoice) {
      let soft = this;
      var update = {};
      update["state"] = "notpaid";
      axios
        .put("/api/invoices/" + invoice.id + "/notpaid", update)
        .then(response => {
          invoice = response.data.data;
          soft.$toasted.show("The invoice (" + invoice.id + ") is not paid", {
            theme: "bubble",
            position: "bottom-center",
            duration: 5000,
            className: ["success"]
          });
          this.loadPendingInvoices();
          /*soft.$socket.emit("kitchen");
          soft.$socket.emit("cashier");*/
        })
        .catch(function(error) {
          console.log("sendUpdateToNotPaidInvoice->" + error);
          soft.$toasted.show(
            "ERRO: Couldn´t change state of (" + invoice.id + ") to not paid",
            {
              theme: "bubble",
              position: "bottom-center",
              duration: 5000,
              className: ["error"]
            }
          );
        });
    },
    loadPendingInvoices: function() {
      axios
        .get("/api/invoices/all/pending")
        .then(response => {
          this.invoices = response.data.data;
          /*this.links = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }*/
        })
        .catch(function(error) {
          console.log("loadPendingInvoices-" + error);
        });
    },
    loadActiveAndTerminatedMeals: function() {
      axios
        .get("/api/meals/all/state/active")
        .then(response => {
          this.activemeals = response.data.data;
          console.log(this.activemeals);
        })
        .catch(function(error) {
          console.log("loadActiveAndTerminatedMeals-" + error);
        });

      axios
        .get("/api/meals/all/state/terminated")
        .then(response => {
          this.terminatedmeals = response.data.data;
          console.log(this.terminatedmeals);
        })
        .catch(function(error) {
          console.log("loadActiveAndTerminatedMeals-" + error);
        });
    },
    loadPendingInvoices: function() {
      axios
        .get("/api/invoices/all/pending")
        .then(response => {
          this.invoices = response.data.data;
          /*this.links = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }*/
        })
        .catch(function(error) {
          console.log("loadPendingInvoices-" + error);
        });
    },
    loadActiveAndTerminatedMeals: function() {
      axios
        .get("/api/meals/all/state/active")
        .then(response => {
          this.activemeals = response.data.data;
          console.log(this.activemeals);
        })
        .catch(function(error) {
          console.log("loadActiveAndTerminatedMeals-" + error);
        });

      axios
        .get("/api/meals/all/state/terminated")
        .then(response => {
          this.terminatedmeals = response.data.data;
          console.log(this.terminatedmeals);
        })
        .catch(function(error) {
          console.log("loadActiveAndTerminatedMeals-" + error);
        });
    },
    openOrders: function(meal) {
      this.currentMeal = meal;
      this.loadOrders("meals/" + meal.id + "/orders");
    },
    closeListOrders: function() {
      this.currentMeal = "";
    },
    loadOrders: function(url) {
      console.log("cheguei ");
      console.log(url);
      axios
        .get("/api/" + url)
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
    reloadMealAndOrder: function() {
      this.loadMeals("meals");
      if (
        this.currentMeal &&
        this.currentMealCreate &&
        this.currentMeal.id == this.currentMealCreate.id
      ) {
        this.loadOrders("meals/" + this.currentMeal.id + "/orders");
      }
    },
    updateKitchen: function() {
      this.$socket.emit("kitchen");
    },
    updateOrder: function(order) {
      if (this.currentMeal && order && this.currentMeal.id == order.meal_id) {
        this.loadOrders("meals/" + this.currentMeal.id + "/orders");
      }
    }
  },
  components: {
    "nav-bar": require("./dashboardnav.vue"),
    ordersMealList: require("./waiter/ordersMealList.vue")
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
    }
  },
  created() {
    this.loadPendingInvoices();
    this.loadActiveAndTerminatedMeals();
  }
};
</script>