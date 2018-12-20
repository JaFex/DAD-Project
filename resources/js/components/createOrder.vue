<template>
    <form>
        <div v-if="showSuccess" class="alert alert-success alert-dismissible">
            <a href="#" class="close" v-on:click='showSuccess = false'>&times;</a>
            <strong>{{ messageTitle }}</strong> {{ message }}
        </div>
        <div v-if="showErro" class="alert alert-danger alert-dismissible">
            <a href="#" class="close" v-on:click='showErro = false'>&times;</a>
            <strong>{{ messageTitle }}</strong> {{ message }}
        </div>
        <div class="form-group">
            <label for="tableFormSelect">Table number: </label>
            <select v-model="selectedMeal" class="mb-3 form-control" id="tableFormSelect">
                <option :value="null" disabled selected>-- Please select an option --</option>
                <option v-for="myMeal in meals" :value="myMeal" :key="myMeal.id">{{ myMeal.table_number }}</option>
            </select>
        </div>
        <div class="form-group">
            <label for="itemType">Type:</label>
            <select v-model="selectedItemType" @change="onChangeItemType" class="mb-3 form-control"  id="itemType">
                <option :value="''" disabled selected>-- Please select an option --</option>
                <option value="dish">dish</option>
                <option value="drink">drink</option>
            </select>
        </div>
        <div class="form-group">
            <label for="itemName">Type:</label>
            <select v-model="selectedItem" class="mb-3 form-control"  id="itemName">
                <option :value="null" disabled selected>-- Please select an option --</option>
                <option v-for="item in listItems" :value="item" :key="item.id">{{ item.name }}</option>
            </select>
        </div>
        <div v-if="selectedItem" class="form-group">
            <img class="img-thumbnai" v-bind:src="selectedItem.photoUrl" style="max-width: 30%;">
            <h6>Price: {{selectedItem.price}}€</h6>
            {{ selectedItem.longDescription }}
        </div>
        <button type="button" class="btn btn-primary" @click.prevent="createOrder">Add order</button>
    </form>
</template>
<script>
export default {
    props: ['myMeals', 'meal'],
    watch: { 
        myMeals: function(newVal, oldVal) { // watch it
          if(newVal.length != oldVal.length) { //para não causar problemas no select caso a meal o preço previsto mude ele deteta que houve mudança mas eu não me interessa isso só interessa se almentou a lista pois o id e numero da tabela não podem ser alterados e eu uso só esses
            this.meals = newVal;
          }
        },
        meal: function(newVal, oldVal) { // watch it
          this.selectedMeal = newVal;
        }
      },
    data() {
        return {
            meals: {},
            selectedMeal: null,
            selectedItemType: '',
            selectedItem: null,
            listItems: {},
            showSuccess: false,
            showErro: false,
            messageTitle: '',
            message: ''
        }
    },
    methods: {
        onChangeItemType: function(){
            console.log(this.selectedItemType);
            if(this.selectedItemType) {
                this.selectedItem = null;
                let soft = this;
                axios.get('/api/items/type/'+this.selectedItemType)
                    .then(response => {
                        soft.listItems = response.data.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        },
        createOrder: function() {
            this.showSuccess = false;
            this.showErro = false;
            this.messageTitle = '';
            this.message = '';
            
            if(this.selectedItemType && this.selectedMeal && this.selectedItem) {
                if(this.selectedMeal.state !== "active") {
                    this.showErro = true;
                    this.messageTitle = 'Meal invalid';
                    this.message = 'Meal is not active';
                }
                let order = {};
                order['item_id']=this.selectedItem.id; 
                order['meal_id']=this.selectedMeal.id; 
                let soft = this;
                axios.post('/api/orders', order)
                    .then(response => {
                        order = response.data.data;
                        soft.messageTitle = "Success";
                        soft.message = "Order as been created to the meal "+order.meal_id+" on table "+soft.selectedMeal.table_number+"!";
                        soft.showSuccess = true;
                        soft.selectedItemType = '';
                        soft.selectedItem = null;
                        soft.$emit('clickReloadMealAndOrder');
                    })
                    .catch(function (error) {
                        console.log(error);
                        soft.messageTitle = "Fail";
                        soft.message = "Ops!";

                        soft.showErro = true;
                    });
            } else {
                this.showErro = true;
                this.messageTitle = 'Invalid!';
                this.message = 'You need to select all options';
            }
        },
    },
    computed: {
    },
    components:{
    },
    created() {
        this.meals = this.myMeals;
        this.selectedMeal = this.meal;
    }
}
</script>