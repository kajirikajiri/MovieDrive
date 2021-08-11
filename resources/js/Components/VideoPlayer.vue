<template>
  <div class="modal js-modal">
    <div style="width: 100%">
      <div
        class="modal__bg js-modal-close batsu mt-5 mr-5"
        style="margin-left: auto"
      >
        ×
      </div>
    </div>
    <div class="d-flex align-items-center" style="height: 100%">
      <video v-bind:src="source" controls />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    source: String,
  },
};
</script>
