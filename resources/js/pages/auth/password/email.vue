<template>
  <div id="page">
    <div class="wrapper flex items-center flex-col">
      <div class="logo mt-12 mb-12">
        <router-link to="/">
          <img src="/images/logo_v_white.svg" />
        </router-link>
      </div>
      <div
        class="shadow-2xl rounded bg-white h-auto xs:h-full w-full sm:w-5/6 md:w-4/6 lg:w-3/6 xl:w-2/6 p-6"
      >
        <form @submit.prevent="send" @keydown="form.onKeydown($event)">
          <alert-success :form="form" :message="status" />

          <h2 class="mb-10">{{ $t('reset_password') }}</h2>

          <!-- Email -->

          <div class="w-full my-4">
            <label class="label-main">{{ $t('email') }}</label>
            <input
              class="input-main"
              :class="{ 'border-red-500': form.errors.has('email') }"
              type="email"
              :placeholder="$t('email')"
              v-model="form.email"
            />
            <has-error :form="form" field="email" />
          </div>

          <!-- Submit Button -->

          <div class="w-full mt-6 flex justify-end items-center">
            <v-button :loading="form.busy">{{ $t('send') }}</v-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";

export default {
  middleware: "guest",

  metaInfo() {
    return { title: this.$t("reset_password") };
  },

  data: () => ({
    status: "",
    form: new Form({
      email: ""
    })
  }),

  methods: {
    async send() {
      const { data } = await this.form.post("/api/password/email");

      this.status = data.status;

      this.form.reset();
    }
  }
};
</script>
