<template>
  <div id="mygallery">
    <div v-for="file in files" :key="file">
      <img
        v-if="file.extension == 'png' || file.extension == 'jpg'"
        :src="file.link"
        style="padding: 1px"
      />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    files: null,
  },
  mounted: function () {
    $("#mygallery").justifiedGallery();
  },
  activated: function () {
    console.log(this.files);
  },
};
</script>
