<template>
    <div class="form-group">
        <label :for="name">{{ label }}</label>
        <input @input="updateValue($event.target.value)"
               :value="value"
               :type="type"
               :id="name"
               :name="name"
               :disabled="disabled"
               :class="inputClasses">
        <div v-if="error"
             class="invalid-feedback"
             v-html="error[0]">
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      type: {
        type: String,
        default: 'text'
      },
      name: {
        type: String,
        required: true
      },
      label: {
        type: String,
        required: true
      },
      value: {
        default: null
      },
      disabled: {
        type: Boolean,
        default: false
      },
      error: {
        type: Array,
        default: () => []
      }
    },
    computed: {
      inputClasses () {
        return {
          'form-control': this.type !== 'file',
          'is-invalid': this.error.length
        }
      }
    },
    methods: {
      updateValue (value) {
        this.$emit('input', value)
      }
    }
  }
</script>
