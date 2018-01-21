<template>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div class="mt-sm-1">Search #{{ search.id }}</div>
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
            <h5 class="card-title">
                Status: <span v-html="statusBadge"></span>
            </h5>
            <h6 class="card-subtitle mb-2 text-muted">
                {{ search.url }}
            </h6>
            <div v-if="search">
                <hr>
                <h5 class="mb-3 card-title">
                    Emails Statistics
                </h5>
                <div class="mb-2">
                    <span class="badge badge-secondary">Emails found: {{ search.emails_count }}</span><br>
                </div>
                <a :href="downloadSearchEmailsLink"
                   class="btn btn-sm btn-success">
                    Download emails
                </a>
                <hr>
                <h5 class="mb-3 card-title">
                    Urls Statistics
                </h5>
                <progress-bar :value="percentCrawled">
                </progress-bar>
                <div class="mt-3 mb-2">
                    <span class="badge badge-dark">Total urls: {{ search.urls_count }}</span><br>
                    <span class="badge badge-primary">Url crawled: {{ search.crawled_urls_count }}</span><br>
                    <span class="badge badge-light">Url not crawled: {{ search.not_crawled_urls_count }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import api from '../../api'
  import ProgressBar from '../ProgressBar'

  export default {
    components: {
      ProgressBar
    },
    data () {
      return {
        search: {},
        interval: null
      }
    },
    computed: {
      statusBadge () {
        if (this.search === {}) {
          return `<span class="badge badge-dark">please wait</span>`
        } else if (this.search.status === 'CREATED') {
          return `<span class="badge badge-secondary">created</span>`
        } else if (this.search.status === 'RUNNING') {
          return `<span class="badge badge-info">running</span>`
        } else if (this.search.status === 'PAUSED') {
          return `<span class="badge badge-light">paused</span>`
        } else if (this.search.status === 'FAILED') {
          return `<span class="badge badge-danger">failed</span>`
        } else if (this.search.status === 'FINISHED') {
          return `<span class="badge badge-success">finished</span>`
        }
      },
      percentCrawled () {
        if (!this.search || this.search.urls_count === 0) {
          return 0
        } else {
          return (this.search.crawled_urls_count / this.search.urls_count) * 100
        }
      },
      downloadSearchEmailsLink () {
        return `/searches/${this.search.id}/emails`
      }
    },
    methods: {
      getSearchStatistics () {
        api.search_statistics.show(this.$route.params.searchId).then(response => {
          if (response.status === 200) {
            this.search = response.data.data
          }
        }).catch(error => {
          clearInterval(this.interval)
          console.log(error)
        })
      }
    },
    mounted () {
      this.getSearchStatistics()
      this.interval = setInterval(() => this.getSearchStatistics(), 2000)
    },
    destroyed () {
      clearInterval(this.interval)
    }
  }
</script>
