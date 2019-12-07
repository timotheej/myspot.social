<template>
  <div>
    <div v-if="toRate">
      <star-rating
        :increment="0.5"
        :star-size="20"
        :show-rating="false"
        @rating-selected="setRating"
        :rating="avgRating"
        :inline="true"
      ></star-rating>
      <div
        class="inline-block float-right text-gray-600 text-xs"
      >{{ totalRating }} {{ $t('ratings') }}</div>
    </div>
    <div v-if="!toRate">
      <star-rating
        :increment="0.5"
        :star-size="20"
        :read-only="true"
        :show-rating="false"
        :rating="avgRating"
        :inline="true"
      ></star-rating>
      <div
        class="inline-block float-right text-gray-600 text-xs"
      >{{ totalRating }} {{ $t('ratings') }}</div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import StarRating from "vue-star-rating";

export default {
  name: "VRatingSpot",

  components: {
    StarRating
  },

  props: ["spotId", "userId"],

  data() {
    return {
      rating: 0,
      allRating: [],
      userInRating: [],
      toRate: false
    };
  },

  mounted() {
    this.getAllRating();
  },

  computed: {
    // average rating
    avgRating: function() {
      let vm = this;
      let countRating = vm.allRating.length;
      let sumRating = vm.allRating.reduce((a, b) => a + b, 0);
      let avgRating = sumRating / countRating;
      return vm.round(avgRating, 0.5);
    },
    // total rating
    totalRating: function() {
      return this.allRating.length;
    }
  },

  methods: {
    async setRating(rating) {
      let vm = this;
      vm.rating = rating;
      await axios
        .post("/api/rating", {
          user_id: vm.userId,
          spot_id: vm.spotId,
          rating: vm.rating
        })
        .then(res => {
          vm.toRate = false;
        })
        .catch(err => {
          console.log(err);
        });
    },

    async getAllRating() {
      let vm = this;
      await axios
        .get("/api/rating/" + vm.spotId)
        .then(res => {
          res.data.forEach(elmt => {
            // push all rates in array
            vm.allRating.push(elmt.rating);
            // push all users rates in array
            vm.userInRating.push(elmt.user_id);
          });
        })
        .then(() => {
          // check if user log include in rating
          if (!vm.userInRating.includes(vm.userId)) {
            vm.toRate = true;
          }
        })
        .catch(err => {
          console.log(err);
        });
    },

    // fonction for round to 0.5 ex: 3.66 = 3.5
    round(value, step) {
      step || (step = 1.0);
      var inv = 1.0 / step;
      return Math.round(value * inv) / inv;
    }
  }
};
</script>
