<template>
  <agile
    :dots="false"
    :infinite="true"
    :navButtons="true"
    :autoplay="true"
    :autoplay-speed="3000"
    v-if="imageLoading"
  >
    <div v-for="image in images" :key="image.id" class="slide">
      <img :src="image.file_name" />
    </div>
    <div v-if="!images.length > 0" class="slide">
      <img src="images/default.jpg" alt />
    </div>
    <template slot="prevButton">
      <fa icon="chevron-left"></fa>
    </template>
    <template slot="nextButton">
      <fa icon="chevron-right"></fa>
    </template>
  </agile>
  <div v-else class="cover-loading bg-white">
    <div class="spinner">
      <div class="bounce1"></div>
      <div class="bounce2"></div>
      <div class="bounce3"></div>
    </div>
  </div>
</template>
<script>
import axios from "axios";

export default {
  name: "VImageSlider",

  props: ["spotId"],

  data() {
    return {
      data: {},
      images: {},
      imageLoading: false
    };
  },

  created() {
    this.fetchImages();
  },

  methods: {
    async fetchImages() {
      let vm = this;
      await axios
        .get("/api/spot/image/" + vm.spotId)
        .then(res => {
          this.imageLoading = true;
          console.log(res.data);
          this.images = res.data;
        })
        .catch(err => {
          console.log(err);
        });
    }
  }
};
</script>
