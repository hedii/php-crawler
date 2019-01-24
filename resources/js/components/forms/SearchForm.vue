<template>
    <div>
        <form-input v-model="search.url"
                    type="url"
                    name="url"
                    label="Url"
                    :error="errors.url">
        </form-input>
        <form-checkbox v-model="search.is_limited"
                       name="is_limited"
                       label="Limited?"
                       :error="errors.is_limited">
        </form-checkbox>
    </div>
</template>

<script>
  import FormInput from './FormInput'
  import FormCheckbox from './FormCheckbox'

  export default {
    components: {
      FormInput,
      FormCheckbox
    },
    props: {
      search: {
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
