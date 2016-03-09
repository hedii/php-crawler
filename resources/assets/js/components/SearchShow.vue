<template>
    <div>Status: {{{ search.finished | searchStatus }}}</div>
    <div>Started: {{ search.created_at | timeAgo }}</div>
    <div>Url found: {{ search.related.urls.data.total | formatInteger }}</div>
    <div>Url crawled: {{ search.related.urls.data.crawled | formatInteger }}</div>
    <div>{{ search.resource_type | capitalize }}s found: {{ search.related.resources.data.total | formatInteger }}</div>
    <hr>
    <div>Url stats:</div>
    <br>
    <div id="crawl-progress" class="progress">
        <div id="crawl-progress-crawled" class="progress-bar progress-bar-success" v-bind:style="{width: search.related.urls.data.percent_crawled + '%'}">
            <span id="url-crawled-percent">{{ search.related.urls.data.percent_crawled }}%</span>
        </div>
        <div id="crawl-progress-not-crawled" class="progress-bar progress-bar-warning" v-bind:style="{width: search.related.urls.data.percent_not_crawled + '%'}">
            <span id="url-not-crawled-percent">{{ search.related.urls.data.percent_not_crawled }}%</span>
        </div>
    </div>
    <div>
        <span class="label label-success">&nbsp; &nbsp;</span> crawled
    </div>
    <div>
        <span class="label label-warning">&nbsp; &nbsp;</span> not crawled yet
    </div>
    <hr>
    <div>
        <a v-bind:href="downloadSearchResourceUrl(search.id)" class="btn btn-success"><i class="fa fa-btn fa-download"></i>  Download {{ search.resource_type }}s</a>
        <button v-on:click="stopSearch(search.id)" v-if="!search.finished" class="btn btn-danger"><i class="fa fa-btn fa-stop-circle"></i> Stop search</button>
        <small class="help-block">Once a search is stopped, it cannot be restarted.</small>
    </div>
    <hr>
    <p><a class="btn btn-primary" v-bind:href="UserSearchesPageUrl"><i class="fa fa-btn fa-arrow-left"></i> My searches</a></p>
</template>

<script>
    export default {
        data() {
            return {
                search: {
                    related: {
                        urls: {
                            data: {
                                total: 0,
                                crawled: 0
                            }
                        },
                        resources: {
                            data: {
                                total: 0,
                                crawled: 0
                            }
                        }
                    }
                },
                interval: null
            };
        },
        computed: {
            UserSearchesPageUrl: function() {
                return baseUrl + '/searches';
            }
        },
        methods: {
            getSearchEverySecond: function () {
                this.interval = setInterval(function () {
                    this.getSearch(searchId);
                }.bind(this), 1000);
            },
            getSearch: function (searchId) {
                this.$http.get(apiUrl + '/users/' + userId + '/searches/' + searchId).then(function (response) {
                    this.search = response.data;
                    if (this.search.finished) {
                        clearInterval(this.interval);
                    }
                }, function (response) {
                    clearInterval(this.interval);
                    console.log(response);
                });
            },
            downloadSearchResourceUrl: function (searchId) {
                return baseUrl + '/searches/' + searchId + '/resources';
            },
            stopSearch: function (searchId) {
                this.$http.patch(apiUrl + '/users/' + userId + '/searches/' + searchId, {finished: true}).then(function (response) {
                    //console.log(response);
                }, function (response) {
                    //console.log(response);
                });
            }
        },
        ready() {
            this.getSearch(searchId);
            this.getSearchEverySecond();
        }
    };
</script>

<style>

</style>