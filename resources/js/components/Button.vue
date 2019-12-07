<template>
  <button
    :type="nativeType"
    :class="{
    [`btn-${type}`]: true,
    'large': large,
    'small': small,
  }"
  >
    <fa icon="spinner" class="infinite-spin" v-if="loading" />
    <slot />
  </button>
</template>

<script>
export default {
  name: "VButton",

  props: {
    type: {
      type: String,
      default: "secondary"
    },

    nativeType: {
      type: String,
      default: "submit"
    },

    loading: {
      type: Boolean,
      default: false
    },

    large: {
      type: Boolean,
      default: false
    },

    small: {
      type: Boolean,
      default: true
    }
  }
};
</script>