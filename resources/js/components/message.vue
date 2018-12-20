<template>
    <div>
        <nav-bar></nav-bar>
        <div class="container-fluid" style="margin-top: 50px;">
            <div class="row">
                <div class="col">
                    <b-jumbotron header="Message" lead="Send a message to managers">
                        <hr class="my-4">
                        <div class="row justify-content-center">
                            <div class="col-6">
                                <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="showMessage">
                                    Please fill the field message.
                                    <button type="button" class="close" @click.prevent="dismissAlert">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="form-group">
                                    <label for="messageInput">Message:</label>
                                    <textarea class="form-control" id="messageInput" rows="3" v-model="message"></textarea>
                                    <div class="invalid" v-show="!$v.message.required">
                                        Field is required.
                                    </div>
                                </div>
                                <button class="btn btn-success float-right" @click.prevent="sendMessage">Submit</button>
                            </div>
                        </div>
                    </b-jumbotron>
                </div>
            </div>
        </div> 
    </div>
</template>
<script>
import { required } from 'vuelidate/lib/validators'
export default {
    data() {
        return {
            message: '',
            showMessage: false,
        }
    },
    components: {
        'nav-bar': require('./dashboardnav.vue')
    },
    methods:{
        sendMessage() {
            if(this.$v.$invalid){
                this.showMessage = true;
            } else {
                console.log(this.message);
                this.$socket.emit('msg_to_managers_from_client', this.message, this.$store.state.user);
                this.$toasted.show('Message sent.', {
                    theme: "bubble",
                    position: "bottom-center",
                    duration: 5000,
                    className: ['success']
                });
                this.message = null;
            }
        },
        dismissAlert(){
            this.showMessage = false;
        }
    },
    validations: {
        message: {
            required
        }
    }
}
</script>
<style>
    .invalid{
        font-size: 80%;
        margin-top: .25rem;
        color: #dc3545;
    }
    .success{
        background-color: #28a745;
        border-color: #28a745;
    }
</style>

