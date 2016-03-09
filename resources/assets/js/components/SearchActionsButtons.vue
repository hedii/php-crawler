<template>
    <a v-bind:href="createSearchUrl" class="btn btn-primary"><i class="fa fa-btn fa-plus"></i> Start a new search</a>
    <a v-if="countSearches" v-bind:href="downloadResourcesUrl" class="btn btn-success"><i class="fa fa-btn fa-btn fa-download"></i> Download all resources</a>
    <a v-if="countSearches" v-on:click.stop="deleteAllSearches" class="btn btn-danger"><i class="fa fa-btn fa-btn fa-trash"></i> Delete all searches</a>
</template>

<script>
    export default {
        computed: {
            createSearchUrl: function () {
                return baseUrl + '/searches/create';
            },
            downloadResourcesUrl: function () {
                return baseUrl + '/resources';
            },
            countSearches: function () {
                if (this.$parent.searches !== undefined) {
                    return this.$parent.searches.length;
                }

                return 0;
            }
        },
        methods: {
            deleteAllSearches: function () {
                if (confirm('Are you shure you want to delete all your searches?')) {
                    this.$http.post(baseUrl + '/searches', {_method: 'DELETE'}).then(function () {
                        this.$parent.getUserSearches();
                    }, function (response) {
                        console.log(response);
                    });

                    return true;
                }

                return false;
            }
        }
    };
</script>

<style>

</style>