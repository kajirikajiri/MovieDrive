
<template>
  <li
    class="tree-node p-2"
    @dragstart="onDragStart"
    @click="onClick"
    @dragover="onDragOver"
    @drop="onDrop"
    @click.right.prevent="onClickRight"
    draggable
    v-bind:link="node.link"
  >
    <span v-for="depth of node.depth" :key="depth" class="ml-3"></span>
    <span v-if="node.nodes.length > 0" class="m-1">
      <i class="fas fa-caret-down" v-if="isOpen"></i>
      <i class="fas fa-caret-right" v-else></i>
    </span>
    <span v-else class="ml-3"></span>
    <i class="fas fa-folder m-1"></i>
    <span class="node-text">{{ node.text }}</span>
    ({{ node.full_path }})
  </li>
  <div v-show="isOpen">
    <Node v-for="child in node.nodes" :key="child.text" :node="child" />
  </div>
</template>

<script>
import { Inertia } from "@inertiajs/inertia";
export default {
  name: "Node",
  props: {
    node: null,
  },
  data: function () {
    return {
      clicks: 0,
      dblClickDelay: 200,
      isOpen: false,
    };
  },
  updated: function () {
    console.debug(this.node);
    console.debug(this.isOpen);
  },
  methods: {
    onClickRight: function (e) {
      console.log(e);
    },
    onClick: function (e) {
      var elements = document.getElementsByClassName("tree-node selected");
      if (elements[0]) {
        elements[0].classList.remove("selected");
      }
      e.currentTarget.classList.add("selected");
      this.clicks++;
      if (this.clicks === 1) {
        var self = this;
        setTimeout(
          function (target) {
            switch (self.clicks) {
              case 1:
                self.onSingleClick(target.getAttribute("link"));
                break;
              default:
                self.onDoubleClick();
            }
            self.clicks = 0;
          },
          200,
          e.currentTarget
        );
      }
    },
    onSingleClick: function (link) {
      Inertia.visit(`/dashboard/${link}`, {
        method: "get",
        replace: true,
        preserveState: true,
        preserveScroll: true,
      });
    },
    onDoubleClick: function (e) {
      this.isOpen = !this.isOpen;
    },
    onDragStart: function (e) {
      console.log("dragstart");
      // e.dataTransfer.setData("node", this.node.id);
      // e.dataTransfer.dropEffect = "move";
      console.log(e);
    },
    onDragOver: function (e) {
      console.log("dragover");
      console.log(e);
    },
    onDrop: function (e) {
      console.log("drop");
      console.log(e);
    },
  },
};
</script>
