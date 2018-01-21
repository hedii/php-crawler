<template>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="mt-sm-1">New search</div>
                <div class="d-none d-sm-block">
                    <router-link :to="{ name: 'searches.index' }"
                                 class="btn btn-sm btn-outline-primary">
                        Back to searches
                    </router-link>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="d-block d-sm-none">
                <p>
                    <router-link :to="{ name: 'searches.index' }"
                                 class="btn btn-sm btn-outline-primary">
                        Back to searches
                    </router-link>
                </p>
                <hr>
            </div>
            <div>
                <search-form :search="search"
                             :errors="errors">
                </search-form>
                <div class="form-group">
                    <button @click="createSearch"
                            :disabled="loading"
                            class="btn btn-primary">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import SearchForm from '../forms/SearchForm'
  import api from '../../api'

  export default {
    components: {
      SearchForm
    },
    data () {
      return {
        search: {
          is_limited: true
        },
        errors: {},
        loading: false
      }
    },
    methods: {
      createSearch () {
        if (this.loading) return
        this.loading = true
        api.user_searches.store(this.$root.user, this.search).then(response => {
          this.loading = false
          if (response.status === 201 || response.status === 200) {
            this.$router.push({
              name: 'searches.show',
              params: { searchId: response.data.data.id }
            })
          }
        }).catch(error => {
          this.loading = false
          if (error.response && 'errors' in error.response.data) {
            this.errors = error.response.data.errors
          }
        })
      }
    }
  }
</script>
