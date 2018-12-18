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
            <b-form-select v-model="selectedMeal" class="mb-3" id="tableFormSelect">
                <option :value="null" disabled>-- Please select an option --</option>
                <option v-for="myMeal in myMeals" :value="myMeal" :key="myMeal.id">{{ myMeal.table_number }}</option>
            </b-form-select>
        </div>
        <div class="form-group">
            <label for="itemType">Type:</label>
            <b-form-select v-model="selectedItemType" @change="onChangeItemType()" class="mb-3"  id="itemType">
                <option :value="''" disabled selected="selected">-- Please select an option --</option>
                <option value="dish" key="dish">dish</option>
                <option value="drink" key="drink">drink</option>
            </b-form-select>
        </div>
        <div class="form-group">
            <label for="itemName">Type:</label>
            <b-form-select v-model="selectedItem" class="mb-3"  id="itemName">
                <option :value="null" disabled selected>-- Please select an option --</option>
                <option v-for="item in listItems" :value="item" :key="item.id">{{item.name}}</option>
            </b-form-select>
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
    data() {
        return {
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
            if(this.selectedItemType && this.selectedMeal && this.selectedItem) {
                showSuccess = false;
                showErro = false;
                let order = {};
                var now = new Date;
                order['item_id']=this.selectedItem.id; 
                order['meal_id']=this.selectedMeal.id; 
                order['start']=now.getUTCFullYear()+"/"+now.getUTCMonth()+"/"+now.getUTCDate()+" "+now.getUTCHours()+":"+now.getUTCMinutes()+":"+now.getUTCSeconds();
                let soft = this;
                axios.post('/api/orders', order)
                    .then(response => {
                        soft.listItems = response.data.data;
                        soft.messageTitle = "Success";
                        soft.message = "Order as been created to the meal "+order['meal_id']+"!";
                        soft.showSuccess = true;
                    })
                    .catch(function (error) {
                        console.log(error);
                        soft.messageTitle = "Fail";
                        soft.message = "Ops!";

                        soft.showErro = true;
                    });
            }
        },
    },
    computed: {
    },
    components:{
    },
    created() {
        this.selectedMeal = this.meal;
    }
}
</script>