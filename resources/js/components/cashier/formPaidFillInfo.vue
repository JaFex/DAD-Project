<template>
    <div class="modal-backdrop">
    <div class="modal fade show" id="formPaidFillInformacion" tabindex="-1" role="dialog" aria-labelledby="formPaidFillInformacionLabel" style="display: block; padding-right: 17px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="formPaidFillInformacionLabel">Paid Meal {{invoice.meal_id}}</h3>
                <button type="button" class="close" @click.prevent="methodClose">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div v-if="messagemServer" class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" v-on:click='messagemServer = ""'>&times;</a>
                    <strong>Ops!</strong> {{ messagemServer }}
                </div>
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Customer Name:</label>
                        <input type="text" class="form-control " v-model="name" :class="valid(validateName)"  @keyup="validate('name', name)" id="text-name" required>
                        <div v-if="validateName==-1" class="help-block with-errors .text-danger">Only letters and spaces!</div>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Customer NIF:</label>
                        <input type="text" class="form-control " v-model="nif" :class="valid(validateNIF)" @keyup="validate('nif', nif)" id="message-text" required>
                        <div v-if="validateNIF==-1" class="help-block with-errors .text-danger">Only number and need to be 9 numbers!</div>
                    </div>
                </form>
            </div>
            <div class="modal-body">
                <h6 class="modal-title" id="formPaidFillInformacionLabel">Calculater</h6>
                <form>
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Money to Pay:</label>
                        <input type="number" class="form-control disable" id="text-name" :value="invoice.total_price" disabled>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Money Received:</label>
                        <input type="number" class="form-control " id="message-text" v-model="moneyReceived" :class="validMoney()">
                        <div v-if="validMoney()==='is-invalid'" class="help-block with-errors .text-danger">You don t have money enough!</div>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Change to give:</label>
                        <input type="number" class="form-control" id="message-text" :value="calculateChange" disabled>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click.prevent="methodClose">Cancel</button>
                <button type="button" class="btn btn-primary" @click.prevent="validateForm">Paid</button>
            </div>
            </div>
        </div>
    </div>
    </div>
</template>
<style>
.modal-backdrop {
    background-color: #000000ab;
}
</style>

<script>
export default {
    props: ['methodDone', 'messagemServer', 'methodClose', 'invoice'],
    data() {
        return {
            moneyReceived: 0,
            validateName: 0,
            name: "",
            validateNIF: 0,
            nif: "",
        }
    },
    methods: {
        validateForm: function() {
            if(!/^[a-zA-Z ]+$/.test(this.name)) {
                this.validateName=-1;
                return ;
            }
            this.validateName=1;

            var nifTest = parseInt(this.nif)
            if(!(nifTest > 99999999 && nifTest < 1000000000)) {
                this.validateNIF=-1;
                return ;
            }
            this.validateNIF=1;

            this.methodDone(this.invoice.id, this.name, this.nif);
        },
        validate: function(type, value) {
            if(value) {
                if(type === 'name') {
                    if(!/^[a-zA-Z ]+$/.test(value)) {
                        this.validateName=-1;
                        return ;
                    }
                    this.validateName=1;
                    return ;
                } else if(type === 'nif') {
                    var nif = parseInt(value)
                    if(!(nif > 99999999 && nif < 1000000000)) {
                        this.validateNIF=-1;
                        return ;
                    }
                    this.validateNIF=1;
                    return ;
                }
            }
        },
        valid: function(validate) {
            if(validate==0) {
                return "";
            }
            return validate==1?'is-valid':'is-invalid';
        },
        validMoney: function() {
            if(!this.invoice || !this.invoice.total_price || !this.moneyReceived) {
                return "";
            }
            var moneyToPay = parseFloat(this.invoice.total_price);
            var moneyReceivedFloat = parseFloat(this.moneyReceived);
            return moneyReceivedFloat>=moneyToPay?'is-valid':'is-invalid';
        }
    },
    computed: {
        calculateChange: function() {
            if(!this.invoice || !this.invoice.total_price || !this.moneyReceived) {
                return 0;
            }
            var moneyToPay = parseFloat(this.invoice.total_price);
            var moneyReceivedFloat = parseFloat(this.moneyReceived);
            return moneyReceivedFloat>=moneyToPay?(moneyReceivedFloat-moneyToPay).toFixed(2):0;
        }
    },
    components:{
    },
    created() {
    },
}
</script>