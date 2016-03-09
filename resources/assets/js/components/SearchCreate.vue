<template>
    <form @submit.prevent="createSearch">
        <label for="entrypoint">Entry point url</label>
        <div class="input-group">
            <input v-model="entrypoint" type="url" class="form-control" name="entrypoint" id="entrypoint" placeholder="http://...">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">Go!</button>
            </span>
        </div>
        <br>
        <div class="form-group">
            <label>Resource type to find</label>
            <br>
            <label class="radio-inline" for="email-radio">
                <input v-model="type" type="radio" name="type" id="email-radio" value="email" checked> Emails
            </label>
        </div>
    </form>
    <hr>
    <p>
        <a class="btn btn-primary" href="{{ searchesIndexUrl }}"><i class="fa fa-btn fa-arrow-left"></i> My searches</a>
    </p>
</template>

<script>
    export default {
        data: function () {
            return {
                entrypoint: '',
                type: ''
            };
        },
        computed: {
            searchesIndexUrl: function () {
                return baseUrl + '/searches';
            }
        },
        methods: {
            createSearch: function () {
                var request = {
                    entrypoint: this.entrypoint,
                    type: this.type
                };

                this.$http.post(baseUrl + '/searches', request).then(function () {
                    console.log(response);
                }, function (response) {
                    console.log(response);
                });

                window.location = baseUrl + '/searches';
            }
        }
    };
</script>

<style>

</style>