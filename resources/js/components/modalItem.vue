<template>
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">{{object.name}}</h5>
                    <button type="button" class="close" data-dismiss="modal" data-target="#exampleModalCenter" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img class="img-fluid" v-bind:src="object.photoUrl" style="max-heigth:50px;">
                    <h6>Price: {{object.price}}€</h6>
                    {{ object.longDescription }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['object']
}
</script>

