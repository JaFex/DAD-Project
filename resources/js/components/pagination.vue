<template>
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item" :class="{'disabled' : links.prev == null}"><a href="#" class="page-link" @click.prevent="links.currentPage != 1 ? loadMethod(links.path+(1)) : '#'">First</a></li>
            <li class="page-item" :class="{'disabled' : links.prev == null}"><a href="#" class="page-link" @click.prevent="links.prev != null ? loadMethod(links.path+(links.currentPage-1)) : '#'">Previous</a></li>
            
            <template v-if="links.lastPage > 5">
                <li v-if="links.currentPage > 3" class="page-item disabled"><a href="#" class="page-link">...</a></li>
                <template v-if="links.currentPage >= 4">
                    <li v-if="links.currentPage-2 <= i && i <= links.currentPage+2" v-for="i in links.lastPage" class="page-item"  :class="{'active' : links.currentPage == i}" :key="i">
                        <a class="page-link" href="#" @click.prevent="loadMethod(links.path+i)">{{i}}</a>
                    </li>
                </template>
                <template v-else-if="links.currentPage < 4" v-for="i in links.lastPage">
                    <li v-if="i <= 5" class="page-item" :class="{'active' : links.currentPage == i}" :key="i">
                        <a class="page-link" href="#" @click.prevent="loadMethod(links.path+i)">{{i}}</a>
                    </li>
                </template>
                <li v-if="links.currentPage+2 < links.lastPage" class="page-item disabled"><a href="#" class="page-link">...</a></li>
            </template>
            <template v-else-if="links.lastPage <= 5" v-for="i in links.lastPage">
                <li v-if="i <= 5" class="page-item" :class="{'active' : links.currentPage == i}" :key="i">
                    <a class="page-link" href="#" @click.prevent="loadMethod(links.path+i)">{{i}}</a>
                </li>
            </template>
            
            
            
            <li class="page-item" :class="{'disabled' : links.next == null}"><a href="#" class="page-link" @click.prevent="links.next != null ? loadMethod(links.path+(links.currentPage+1)) : '#'">Next</a></li>
            <li class="page-item" :class="{'disabled' : links.next == null}"><a href="#" class="page-link" @click.prevent="links.currentPage != links.lastPage ? loadMethod(links.path+(links.lastPage)) : '#'">Last</a></li>
        </ul>
    </nav>
</template>
<script>
export default {
     props: ['method', 'links', 'path'],
     methods: {
         loadMethod: function(url){
            var res = url.split("?");
            if(res.length > 2){
                url = res[0]+"?"+res[res.length-1];
            }
            this.method(url);
         }
     }
}
</script>
