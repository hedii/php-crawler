<template>
    <div class="modal fade"
         id="confirmDeleteModal"
         tabindex="-1"
         role="dialog"
         aria-labelledby="confirmDeleteModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        Are you sure?
                    </h5>
                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    This action est irreversible.
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-outline-primary"
                            data-dismiss="modal">
                        No, cancel
                    </button>
                    <button @click="confirm"
                            type="button"
                            class="btn btn-danger">
                        Yes, delete it
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
  import $ from 'jquery'

  export default {
    methods: {
      confirm () {
        $('#confirmDeleteModal').modal('hide')
        this.$emit('confirmed', true)
      }
    }
  }
</script>
