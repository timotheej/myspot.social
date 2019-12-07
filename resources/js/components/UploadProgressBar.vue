<template>
  <div class="progress">
    <span :style="{ width: getProgress, background: color }" role="progressbar"></span>
  </div>
</template>

<script>
export default {
  name: "VUploadProgressBar",

  props: {
    progress: {
      type: Number,
      default: 0
    },
    error: {
      type: Boolean,
      default: false
    }
  },

  data() {
    return {
      color: "#2BC210"
    };
  },

  computed: {
    getProgress() {
      return Math.round(this.progress) + "%";
    }
  },
  watch: {
    error: function() {
      if (this.error) {
        this.color = "#cc0000";
      }
    }
  }
};
</script>
