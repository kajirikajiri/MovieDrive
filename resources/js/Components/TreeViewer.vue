<style>
.tree-viewer {
  min-height: 60vh;
}
</style>

<template>
  <section class="tree-viewer">
    <ul>
      <Node :node="node" />
    </ul>
  </section>
</template>

<script>
import Node from "@/Components/TreeViewerNode.vue";

export default {
  components: {
    Node,
  },
  props: {
    node: null,
  },
};
</script>
