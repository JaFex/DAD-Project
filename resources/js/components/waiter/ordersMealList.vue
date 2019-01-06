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
                        <th>ID</th>
                        <th>State</th>
                        <th>Item</th>
                        <th>Start</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders" :class="[order.state=='confirmed'? 'table-success' : 'table-warning']" :key="order.id">
                        <td>{{order.id}}</td>
                        <td>{{order.state}}</td>
                        <td><button class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter" @click="seeInfoIteam(order.item_id)">Show me item</button></td>
                        <td>{{order.start}}</td>
                        <td>
                            <button v-if="timeHideButton(order)" :id="order.id" type="button" class="btn btn-danger" @click.prevent="deleteInTime(order)">Delete (<strong :id="'time_'+order.id"></strong>)</button>
                            <!--<button v-if="order.state === 'in preparation'" type="button" class="btn btn-primary" @click.prevent="changeStateOrder(order, 'prepared')">I Finished Preparing</button>
                            <button v-else-if="order.state === 'confirmed'" type="button" class="btn btn-primary" @click.prevent="changeStateOrder(order, 'in preparation')">Start Preparation</button>-->
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
    props: ['method', 'meal', 'orders', 'links'],
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
        },
        loadOrders: function(url){
            axios.get('/api/'+url)
                .then(response => {
                    this.currentMeal = this.meal;
                    this.orders = response.data.data;
                    this.linksOrders = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log("loadOrders->"+error);
                });
        },
        loadOrdersSamePage: function(url, currentPage){
            axios.get('/api/'+url+'?page='+currentPage)
                .then(response => {
                    this.currentMeal = this.meal;
                    this.orders = response.data.data;
                    this.linksOrders = {
                        prev: response.data.links.prev,
                        next: response.data.links.next,
                        currentPage: currentPage,
                        lastPage: response.data.meta.last_page,
                        path: url+'?page='
                    }
                })
                .catch(function (error) {
                    console.log("loadOrders->"+error);
                });
        },
        timeHideButton(order) {
            var dateStartString = order.start+'';
            var date = new Date();
            date = new Date(date.getTime() - 5*1000);
            var str = dateStartString.split(" ");
            str = str[0]+"T"+str[1]+"Z";
            var dateStart = new Date(str);
            var timeDiff = Math.abs(date.getTime() - dateStart.getTime());
            if (date > dateStart) {
                if(order.state === "pending") {
                    this.needToBeConfirmed(order);
                }
                return false;
            } else {
                var secunds = Math.round(timeDiff/1000);
                var div = secunds>5?5:secunds;
                var time = (secunds>5?1000:(timeDiff/div))-250;//(timeDiff/div) - (menos o tempo de envio e reposta de delete)
                this.timeRunOut(time, div, order);
                return true;
            }
        },
        timeRunOut: function(time, timeToShow, order) {
            let soft = this;
            setTimeout(function () {
                //console.log("time out -"+timeToShow);
                var htmlTimeShow = document.getElementById('time_'+order.id+'');
                if(htmlTimeShow) {
                    htmlTimeShow.innerHTML = timeToShow;
                    if(timeToShow <= 0) {
                        document.getElementById(''+order.id+'').style.display='none';
                    } else {
                        timeToShow--;
                        soft.timeRunOut(time, timeToShow, order);
                    }
                }
            }, time);
        },
        deleteInTime: function(order) {
            var dateStartString = order.start+'';
            var date = new Date();
            date = new Date(date.getTime() - 5*1000);
            var str = dateStartString.split(" ");
            str = str[0]+"T"+str[1]+"Z";
            var dateStart = new Date(str);
            let soft = this;
            if (date <= dateStart) {
                axios.delete('/api/orders/'+order.id)
                    .then(response => {
                        order.state = 'deleted';
                        soft.$socket.emit('managerUpdateOrders', order);
                        order = response.data.data;
                        soft.messageTitle = "Order Deleted!";
                        soft.message = "Order "+order.id+" as been deleted on the meal "+order.meal_id+"!";
                        soft.showSuccess = true;
                        soft.selectedItemType = '';
                        soft.selectedItem = null;
                        soft.$emit('clickReloadMealAndOrder');
                    })
                    .catch(function (error) {
                        console.log("deleteInTime-"+error.response.data.test);
                        soft.messageTitleErro = "Fail to delete order!";
                        soft.messageErro = "Ops! Order ("+error.response.data.order_id+") not deleted because '"+error.response.data.message+"'";

                        soft.showErro = true;
                    });
            }
        },
        needToBeConfirmed: function(order) {
            console.log("Order that need to be confirmed long time ago");
            let soft = this;
            setTimeout(function () {
                var update = {};
                update["state"] = 'confirmed';
                axios.put('/api/orders/'+order.id, update)
                        .then(response => {
                            order = response.data.data;
                            soft.selectedItemType = '';
                            soft.selectedItem = null;
                            soft.$emit('clickUpdateKitchen');
                            soft.$emit('clickUpdateOrder', order);
                            soft.$socket.emit('managerUpdateOrders', order);
                        })
                        .catch(function (error) {
                            console.log("needToBeConfirmed->"+error);
                        });
            }, 5000);
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