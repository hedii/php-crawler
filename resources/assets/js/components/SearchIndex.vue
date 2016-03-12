<template>
    <search-actions-buttons></search-actions-buttons>
    <hr>
    <table class="table table-hover table-condensed">
        <thead>
            <tr>
                <th class="hidden-xs">Type</th>
                <th>Entry point</th>
                <th class="hidden-xs">Started</th>
                <th>Status</th>
                <th class="hidden-xs">Urls</th>
                <th class="hidden-xs">Found</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="search in searches | orderBy 'created_at' -1">
                <td class="hidden-xs">{{ search.resource_type | capitalize }}</td>
                <td>{{ search.entrypoint_url }}</td>
                <td class="hidden-xs">{{ search.created_at | timeAgo }}</td>
                <td>{{{ search.finished | searchStatus }}}</td>
                <td class="hidden-xs">{{ search.related.urls.data.total | formatInteger }}</td>
                <td class="hidden-xs">{{ search.related.resources.data.total | formatInteger }}</td>
                <td>
                    <a v-bind:href="viewSearchUrl(search.id)" class="btn btn-action btn-xs btn-primary" title="View search"><i class="fa fa-arrow-right"></i></a>
                    <a v-bind:href="downloadSearchResourcesUrl(search.id)" class="btn btn-action btn-xs btn-success" title="Download {{ search.resource_type }}s"><i class="fa fa-download"></i></a>
                    <a v-on:click.stop="deleteSearch(search.id)" class="btn btn-action btn-xs btn-danger" title="Delete search"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import SearchActionsButtons from './SearchActionsButtons.vue';

    export default {
        data() {
            return {
                searches: []
            };
        },
        methods: {
            getUserSearches: function () {
                this.$http.get(apiUrl + '/users/' + userId + '/searches').then(function (response) {
                    this.$root.searches = response.data.searches;
                    this.searches = response.data.searches;
                }, function (response) {
                    console.log(response);
                });
            },
            getUserSearchesEveryFiveSeconds: function () {
                this.interval = setInterval(function () {
                    this.getUserSearches();
                    if (this.searches.length > 0) {
                        var searchesActive = false;
                        for (var i = 0; i < this.searches.length; i++) {
                            if (this.searches[i]['finished'] === false) {
                                searchesActive = true;
                            }
                        }
                        if (searchesActive === false) {
                            clearInterval(this.interval);
                        }
                    } else {
                        clearInterval(this.interval);
                    }
                }.bind(this), 5000);
            },
            deleteSearch: function (searchId) {
                if (confirm('Are you shure you want to delete this search?')) {
                    this.$http.delete(apiUrl + '/users/' + userId + '/searches/' + searchId).then(function () {
                        this.getUserSearches();
                    }, function (response) {
                        console.log(response);
                    });

                    return true;
                }

                return false;
            },
            viewSearchUrl: function (searchId) {
                return baseUrl + '/searches/' + searchId;
            },
            downloadSearchResourcesUrl: function (searchId) {
                return baseUrl + '/searches/' + searchId + '/resources';
            }
        },
        ready() {
            this.getUserSearches();
            this.getUserSearchesEveryFiveSeconds();
        },
        components: {
            SearchActionsButtons
        }
    };
</script>

<style>
    @media screen and (max-width: 991px) {
        .btn-action {
            margin-bottom: 3px;
        }
    }
</style>