<template>
    <div class="progress">
        <div class="progress-bar"
             role="progressbar"
             :style="style"
             :aria-valuenow="roundedValue"
             aria-valuemin="0"
             aria-valuemax="100">
            {{ roundedValue }}%
        </div>
    </div>
</template>

<script>
  export default {
    props: {
      value: {
        type: Number,
        required: true
      }
    },
    computed: {
      roundedValue () {
        return Math.floor(this.value)
      },
      style () {
        return { width: this.roundedValue + '%', 'min-width': '20px' }
      }
    }
  }
</script>
