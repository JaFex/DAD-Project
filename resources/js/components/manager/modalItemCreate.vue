<template>
    <div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit item</h5>
                    <button type="button" class="close" data-dismiss="modal" data-target="#exampleModalCenter" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="showMessage">
                        {{message}}
                        <button type="button" class="close" @click.prevent="dismissAlert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="form-group">
                        <label for="inputName">Name</label>
                        <input id="inputName" type="text" class="form-control" v-model="name">
                    </div>
                    <div class="form-group">
                        <label for="inputType">Type</label>
                        <select class="form-control" id="inputType" v-model="type">
                            <option disabled selected value="">Please choose one type</option>
                            <option value="drink">Drink</option>
                            <option value="dish">Dish</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="inputPrice">Price</label>
                        <input type="text" class="form-control" id="inputPrice" v-model="price">
                    </div>
                    <div class="form-group">
                        <label for="inputDescription">Description</label>
                        <textarea class="form-control" id="inputDescription" rows="3" v-model="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="inputPhoto">Photo</label>
                        <input type="file" class="form-control-file" id="inputPhoto" ref="file">
                    </div>
                    <button type="submit" class="btn btn-primary" @click.prevent="createItem">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            name:'',
            type: '',
            price: '',
            description: '',
            message: '',
            showMessage: false,
        }
    },
    methods: {
        createItem(){
            let formData = new FormData();
            formData.append('file', this.$refs.file.files[0]);
            formData.append('name', this.name);
            formData.append('type', this.type);
            formData.append('price', this.price);
            formData.append('description', this.description);
            axios.post('/api/items', formData)
                .then(response => {
                    this.$toasted.show('Item created successfully.', {
                        theme: "bubble",
                        position: "bottom-center",
                        duration: 5000,
                        className: ['success']
                    });
                    this.$socket.emit('msg_update_items_from_client');
                }).catch(error => {
                    console.log(error);
                    this.message = '';
                    this.message += error.response.data.errors.description !== undefined ? error.response.data.errors.description[0] + ' ' : '';
                    this.message += error.response.data.errors.name !== undefined ? error.response.data.errors.name[0] + ' ' : '';
                    this.message += error.response.data.errors.price !== undefined ? error.response.data.errors.price[0] + ' ': '';
                    this.message += error.response.data.errors.type !== undefined ? error.response.data.errors.type[0] + ' ': '';
                    this.message += error.response.data.errors.file !== undefined ? error.response.data.errors.file[0] : '';
                    this.showMessage = true;
                });
        },
        dismissAlert(){
            this.showMessage = false;
        }
    }
}
</script>
<style>
    textarea {
        resize: none;
    }

    .success{
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
</style>
