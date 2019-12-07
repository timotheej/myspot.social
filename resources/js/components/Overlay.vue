<template>
  <transition name="slide-fade">
    <div v-if="showing" class="overlay">
      <button
        aria-label="close"
        class="absolute top-0 right-0 text-2xl my-2 mx-4 text-white z-10"
        @click.prevent="close"
      >
        <fa icon="times" />
      </button>
      <div v-if="loadingOverlay">
        <div class="overlay-top">
          <!-- Image slider -->
          <v-image-slider :spotId="spot.id"></v-image-slider>
          <!-- Image upload -->
          <v-image-upload :spotId="spot.id" :userId="user.id"></v-image-upload>
        </div>
        <div class="overlay-content">
          <!-- If editing as true and spot user id egal to user logged -->
          <div v-if="editing && user.id === spot.user.id">
            <!-- Input name -->
            <input
              class="appearance-none block w-full bg-gray-100 text-gray-500 rounded py-3 px-4 leading-tight mb-3 focus:bg-white focus:outline-none"
              type="text"
              :placeholder="$t('name')"
              v-model="spot.name"
            />
            <ul v-if="errors && errors.name" class="text-red-600 text-xs italic mb-4">
              <li v-for="(error, index) in errors.name" :key="index">{{ error }}</li>
            </ul>
            <!-- Select category -->
            <div class="relative">
              <select
                class="appearance-none block w-full bg-gray-100 text-gray-500 rounded py-3 px-4 leading-tight mb-3 focus:bg-white focus:outline-none"
                v-model="spot.categorie_id"
              >
                <option disabled selected="selected">{{ $t('category') }}</option>
                <option
                  v-for="categorie in categories"
                  :key="categorie.id"
                  :value="categorie.id"
                >{{ $t(categorie.name) }}</option>
              </select>
              <div
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700"
              >
                <svg
                  class="fill-current h-4 w-4"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                >
                  <path
                    d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                  />
                </svg>
              </div>
            </div>
            <!-- Textarea description -->
            <textarea
              class="appearance-none block w-full bg-gray-100 text-gray-500 rounded py-3 px-4 h-32 resize-none leading-tight mb-3 focus:bg-white focus:outline-none"
              type="text"
              :placeholder="$t('description')"
              v-model="spot.description"
            ></textarea>
            <ul v-if="errors && errors.description" class="text-red-600 text-xs italic mb-4">
              <li v-for="(error, index) in errors.description" :key="index">{{ error }}</li>
            </ul>
            <p
              class="text-gray-600 text-xs"
            >{{$t('remaining_characters') }} {{ remainingCharacters }}</p>
            <p
              v-if="errors && errors.status"
              class="text-red-600 text-xs italic"
            >{{ $t('unknown_error') }}</p>
            <!-- Buttons -->
            <div class="w-full my-4 flex justify-end items-center">
              <button
                @click="editing = false"
                class="bg-red-500 rounded-full h-8 w-8 mr-2 text-white flex justify-center items-center"
                v-show="!spinner && !back"
              >
                <fa icon="times"></fa>
              </button>
              <button
                @click="updateSpot"
                class="bg-green-500 rounded-full h-8 w-8 text-white flex justify-center items-center"
                :disabled="spinner"
              >
                <fa icon="check" v-if="!spinner"></fa>
                <fa icon="spinner" class="infinite-spin" v-if="spinner" />
              </button>
            </div>
          </div>

          <!-- Else editing as false -->
          <div v-else>
            <!-- Check if name and description are not empty -->
            <div v-if="spot.name != null && spot.description != null">
              <h3>{{ spot.name }}</h3>
              <div>
                <span class="text-gray-600 text-xs inline-block">{{ $t('in') }}</span>
                <span class="color-secondary inline-block">{{ $t(spot.categorie.name) }}</span>
              </div>
              <v-rating-spot :spotId="spot.id" :userId="user.id"></v-rating-spot>
              <div class="overlay-text">{{ spot.description }}</div>
              <div class="w-full my-4 flex justify-between items-center">
                <p
                  class="text-gray-600 text-xs italic"
                >{{ $t('add_by')}} {{ spot.user.firstname + '.' + spot.user.lastname.charAt(0)}}</p>
                <button
                  v-if="user.id === spot.user.id"
                  @click="editing = true"
                  class="bg-gray-600 rounded-full h-8 w-8 text-white flex justify-center items-center"
                >
                  <fa icon="pen"></fa>
                </button>
              </div>
            </div>

            <!-- If they are empty and the user is not an author, display a message -->
            <div v-else>
              <p class="text-center">{{ $t('empty_data') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div class="cover-loading" v-if="!loadingOverlay">
        <div class="spinner">
          <div class="bounce1"></div>
          <div class="bounce2"></div>
          <div class="bounce3"></div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { mapGetters } from "vuex";
import axios from "axios";

export default {
  name: "VOverlay",

  props: {
    showing: {
      required: true,
      type: Boolean
    },
    fetchSpot: { type: String }
  },

  data() {
    return {
      loadingOverlay: false,
      spot: {},
      editing: false,
      spinner: false,
      back: false,
      errors: {},
      maxCharacters: 300
    };
  },

  // get user

  computed: {
    ...mapGetters({
      user: "auth/user",
      categories: "spot/categories"
    }),

    // remaining characters for description textarea
    remainingCharacters: function() {
      let remainText = this.spot.description;
      if (remainText == null) {
        remainText = "";
      }
      return this.maxCharacters - remainText.length;
    }
  },

  watch: {
    // observe a change in the parent and get the key (spotId)
    fetchSpot: function(key) {
      this.resetData();
      this.getSpot(key).then(() => {
        // after getting the spot, allow the author to edit if name and description are null
        if (this.spot.name == null && this.spot.description == null) {
          if (this.user.id === this.spot.user.id) {
            this.back = true;
            this.editing = true;
          }
        }
      });
    },
    // observe a change in editing
    editing: function() {
      if (this.editing) {
        // if editing is true, fetch all categories
        this.fetchAllCategories();
        // if the name and description are complete add the back button
        if (this.spot.name != null && this.spot.description != null) {
          this.back = false;
        }
      }
    }
  },

  methods: {
    close() {
      this.$emit("close");
    },

    // get data by selected spot
    async getSpot(key) {
      let vm = this;
      vm.loadingOverlay = false;
      try {
        const response = await axios.get("/api/spot/" + key);
        vm.spot = await response.data[0];
        vm.loadingOverlay = true;
        return vm.spot;
      } catch (err) {
        console.log("fetch failed", err);
      }
    },

    // update a spot data
    async updateSpot() {
      let vm = this;
      vm.spinner = true;
      await axios
        .put("/api/spot/" + vm.spot.id, {
          name: vm.spot.name,
          description: vm.spot.description,
          categorie_id: vm.spot.categorie_id
        })
        .then(res => {
          vm.editing = false;
          vm.spinner = false;
          vm.errors = null;

          // reload spot to avoid errors
          this.reloadSpot();
        })
        .catch(err => {
          vm.spinner = false;
          if (err.response.status == 422) {
            vm.errors = err.response.data.errors;
          } else {
            vm.errors = err.response;
          }
        });
    },

    async fetchAllCategories() {
      await this.$store.dispatch("spot/fetchAllCategories");
    },

    // reload spot
    reloadSpot() {
      this.getSpot(this.spot.spot_id);
    },

    // reset data on spot change
    resetData() {
      this.loadingOverlay = false;
      this.editing = false;
      this.errors = null;
    }
  }
};
</script>
