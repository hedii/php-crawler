<template>
    <nav v-if="showPagination"
         aria-label="Page navigation">
        <ul class="pagination">
            <li v-if="showPrev">
                <a @click.prevent="changePage(meta.current_page - 1)"
                   href="#"
                   aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li v-else class="disabled">
                <span aria-hidden="true">&laquo;</span>
            </li>
            <li v-for="n in meta.last_page"
                :class="{ 'active': n === meta.current_page }">
                <span v-if="n === meta.current_page">
                    {{ n }} <span class="sr-only">(current)</span>
                </span>
                <a v-else
                   @click.prevent="changePage(n)"
                   href="#">
                    {{ n }}
                </a>
            </li>
            <li v-if="showNext">
                <a @click.prevent="changePage(meta.current_page + 1)"
                   href="#"
                   aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <li v-else
                class="disabled">
                <span aria-hidden="true">&raquo;</span>
            </li>
        </ul>
    </nav>
</template>

<script>
  export default {
    props: {
      meta: {
        type: Object,
        required: true
      },
      links: {
        type: Object,
        required: true
      }
    },
    computed: {
      showPagination () {
        return this.meta.last_page > 1
      },
      showPrev () {
        return this.links.prev !== null
      },
      showNext () {
        return this.links.next !== null
      }
    },
    methods: {
      changePage (page) {
        this.$emit('pageChanged', page)
      }
    }
  }
</script>
