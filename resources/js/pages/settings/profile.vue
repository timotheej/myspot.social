<template>
  <div>
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('info_updated')" />

      <!-- Lastname -->
      <div class="w-full my-4">
        <label class="label-main">{{ $t('lastname') }}</label>
        <input
          class="input-main"
          :class="{ 'border-red-500': form.errors.has('lastname') }"
          type="text"
          v-model="form.lastname"
        />
        <has-error :form="form" field="lastname" />
      </div>

      <!-- Firstname -->
      <div class="w-full my-4">
        <label class="label-main">{{ $t('firstname') }}</label>
        <input
          class="input-main"
          :class="{ 'border-red-500': form.errors.has('firstname') }"
          type="text"
          v-model="form.firstname"
        />
        <has-error :form="form" field="firstname" />
      </div>

      <!-- Email -->
      <div class="w-full my-4">
        <label class="label-main">{{ $t('email') }}</label>
        <input
          class="input-main"
          :class="{ 'border-red-500': form.errors.has('email') }"
          type="email"
          v-model="form.email"
        />
        <has-error :form="form" field="email" />
      </div>

      <!-- Submit Button -->
      <div class="w-full mt-6 flex justify-end items-center">
        <v-button :loading="form.busy" type="secondary">{{ $t('update') }}</v-button>
      </div>
    </form>
  </div>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";

export default {
  scrollToTop: false,

  metaInfo() {
    return { title: this.$t("settings") };
  },

  data: () => ({
    form: new Form({
      lastname: "",
      firstname: "",
      email: ""
    })
  }),

  computed: mapGetters({
    user: "auth/user"
  }),

  created() {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key];
    });
  },

  methods: {
    async update() {
      const { data } = await this.form.patch("/api/settings/profile");

      this.$store.dispatch("auth/updateUser", { user: data });
    }
  }
};
</script>
