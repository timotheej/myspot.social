<template>
  <div>
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('password_updated')" />

      <!-- Password -->
      <div class="w-full my-4">
        <label class="label-main">{{ $t('new_password') }}</label>
        <input
          class="input-main"
          :class="{ 'border-red-500': form.errors.has('password') }"
          type="password"
          v-model="form.password"
        />
        <has-error :form="form" field="password" />
      </div>

      <!-- Password Confirmation -->

      <div class="w-full my-4">
        <label class="label-main">{{ $t('confirm_password') }}</label>
        <input
          class="input-main"
          :class="{ 'border-red-500': form.errors.has('password_confirmation') }"
          type="password"
          v-model="form.password_confirmation"
        />
        <has-error :form="form" field="password_confirmation" />
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

export default {
  scrollToTop: false,

  metaInfo() {
    return { title: this.$t("settings") };
  },

  data: () => ({
    form: new Form({
      password: "",
      password_confirmation: ""
    })
  }),

  methods: {
    async update() {
      await this.form.patch("/api/settings/password");

      this.form.reset();
    }
  }
};
</script>
