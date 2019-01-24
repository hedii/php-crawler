<template>
    <div class="card">
        <div class="card-header">
            My account
        </div>
        <div class="card-body">
            <div v-if="showSuccess"
                 class="alert alert-success"
                 role="alert">
                Your account has been updated!
            </div>
            <div v-if="user">
                <user-form :user="user"
                           :errors="errors">
                </user-form>
                <div class="form-group">
                    <button @click="updateUser"
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
  import UserForm from './../forms/UserForm'
  import api from './../../api'

  export default {
    components: {
      UserForm
    },
    data () {
      return {
        user: null,
        errors: {},
        loading: false,
        showSuccess: false
      }
    },
    methods: {
      updateUser () {
        if (this.loading) return
        this.loading = true
        this.showSuccess = false
        api.users.update(this.user).then(response => {
          this.loading = false
          if (response.status === 200) {
            this.showSuccess = true
            this.user = response.data.data
            this.$root.user = this.user
          }
        }).catch(error => {
          this.loading = false
          if (error.response && 'errors' in error.response.data) {
            this.errors = error.response.data.errors
          }
        })
      }
    },
    mounted () {
      this.user = this.$root.user
    }
  }
</script>
