<template>
    <div class="form-group">
        <div class="form-check">
            <input @change="updateValue"
                   :value="value"
                   :name="name"
                   :id="name"
                   :class="inputClasses"
                   type="checkbox"
                   :disabled="disabled"
                   :checked="value">
            <label :for="name">
                {{ label }}
            </label>
        </div>
        <div v-if="error"
             class="invalid-feedback"
             v-html="error[0]">
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      value: {
        type: Boolean
      },
      name: {
        type: String,
        required: true
      },
      label: {
        type: String,
        required: true
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
          'form-check-input': true,
          'is-invalid': this.error.length
        }
      }
    },
    methods: {
      updateValue (event) {
        this.value = !!this.value
        this.$emit('input', event.target.checked)
      }
    }
  }
</script>
