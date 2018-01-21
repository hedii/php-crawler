<template>
    <div>
        <form-input v-model="user.name"
                    type="text"
                    name="name"
                    label="Name"
                    :error="errors.name">
        </form-input>
        <form-input v-model="user.email"
                    type="email"
                    name="email"
                    label="Email"
                    :error="errors.email">
        </form-input>
        <form-input v-model="user.password"
                    type="password"
                    name="password"
                    label="Password"
                    :error="errors.password">
        </form-input>
        <form-input v-model="user.password_confirmation"
                    type="password"
                    name="password_confirmation"
                    label="Confirm Password">
        </form-input>
    </div>
</template>

<script>
  import FormInput from './FormInput'

  export default {
    components: {
      FormInput
    },
    props: {
      user: {
        type: Object,
        required: true
      },
      errors: {
        type: Object,
        required: true
      }
    }
  }
</script>
