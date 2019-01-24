<template>
    <div>
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div class="mt-sm-1">All your searches</div>
                    <div class="d-none d-sm-block">
                        <router-link :to="{ name: 'searches.create' }"
                                     class="btn btn-sm btn-primary">
                            Create a new search
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-block d-sm-none">
                    <p>
                        <router-link :to="{ name: 'searches.create' }"
                                     class="btn btn-sm btn-primary">
                            Create a new search
                        </router-link>
                    </p>
                    <hr>
                </div>
                <div v-if="searches.length">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Url</th>
                                <th scope="col">Limited</th>
                                <th scope="col">Status</th>
                                <th scope="col">Creation date</th>
                                <th scope="col">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="search in searches"
                                :key="search.id">
                                <th scope="row">{{ search.id }}</th>
                                <td>
                                    <a :href="search.url"
                                       target="_blank"
                                       rel="noreferrer noopener">
                                        {{ search.url | truncate(20) }}
                                    </a>
                                </td>
                                <td>
                                    <span v-html="limitedBadge(search)"></span>
                                </td>
                                <td>
                                    <span v-html="statusBadge(search)"></span>
                                </td>
                                <td>{{ search.created_at }}</td>
                                <td>
                                    <router-link :to="searchLink(search)">
                                        Show
                                    </router-link>
                                    |
                                    <a @click.prevent="confirmSearchDestroy(search)"
                                       href="#">
                                        Delete
                                    </a>
                                    <confirm-delete-modal @confirmed="destroySearch(search)">
                                    </confirm-delete-modal>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <pagination :meta="meta"
                                :links="links"
                                @pageChanged="getSearches">
                    </pagination>
                </div>
                <p v-if="noSearch">
                    No search yet...
                </p>
            </div>
        </div>
    </div>
</template>

<script>
  import $ from 'jquery'
  import api from '../../api'
  import Pagination from '../Pagination'
  import ConfirmDeleteModal from '../ConfirmDeleteModal'

  export default {
    components: {
      Pagination,
      ConfirmDeleteModal
    },
    data () {
      return {
        searches: [],
        links: {},
        meta: {}
      }
    },
    computed: {
      noSearch () {
        return !this.searches.length && this.links && this.meta
      }
    },
    methods: {
      getSearches (page = 1) {
        api.user_searches.index(this.$root.user, page).then(response => {
          if (response.status === 200) {
            this.searches = response.data.data
            this.links = response.data.links
            this.meta = response.data.meta
          }
        }).catch(error => console.log(error))
      },
      destroySearch (search) {
        api.searches.destroy(search.id).then(response => {
          this.getSearches()
        }).catch(error => console.log(error))
      },
      searchLink (search) {
        return {
          name: 'searches.show',
          params: {
            searchId: search.id
          }
        }
      },
      limitedBadge (search) {
        if (search.is_limited) {
          return `<span class="badge badge-primary">yes</span>`
        } else {
          return `<span class="badge badge-warning">no</span>`
        }
      },
      statusBadge (search) {
        if (search.status === 'CREATED') {
          return `<span class="badge badge-secondary">created</span>`
        } else if (search.status === 'RUNNING') {
          return `<span class="badge badge-info">running</span>`
        } else if (search.status === 'PAUSED') {
          return `<span class="badge badge-light">paused</span>`
        } else if (search.status === 'FAILED') {
          return `<span class="badge badge-danger">failed</span>`
        } else if (search.status === 'FINISHED') {
          return `<span class="badge badge-success">finished</span>`
        }
      },
      confirmSearchDestroy (search) {
        $('#confirmDeleteModal').modal()
      }
    },
    mounted () {
      this.getSearches()
    }
  }
</script>
