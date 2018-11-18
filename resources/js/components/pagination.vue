<template>
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item" :class="{'disabled' : links.prev == null}"><a href="#" class="page-link" @click.prevent="links.prev != null ? method(links.path+(links.currentPage-1)) : '#'">Previous</a></li>
            <li class="page-item" v-for="i in links.lastPage" :class="{'active' : links.currentPage == i}">
                <a class="page-link" href="#" @click.prevent="method(links.path+i)">{{i}}</a>
            </li>
            <li class="page-item" :class="{'disabled' : links.next == null}"><a href="#" class="page-link" @click.prevent="links.next != null ? method(links.path+(links.currentPage+1)) : '#'">Next</a></li>
        </ul>
    </nav>
</template>
<script>
export default {
     props: ['method', 'links', 'path']
}
</script>
