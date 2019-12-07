<template>
  <div id="homepage">
    <div class="wrapper">
      <div class="flex">
        <div
          class="flex w-full sm:w-4/4 md:w-2/4 lg:w-2/6 xl:w-1/4 flex-col items-center left-side"
        >
          <div class="flex h-auto w-full justify-center items-center">
            <div class="logo">
              <a href>
                <img src="/images/logo.svg" alt />
              </a>
            </div>
          </div>

          <div class="flex content-center w-full mt-6 lg:mt-8 xl:mt-16 items-center flex-col">
            <button class="btn-primary large" @click="toggleShow" v-if="!isLogin">{{ $t('login') }}</button>
            <router-link
              to="/register"
              tag="button"
              class="btn-secondary large"
              v-if="!isLogin"
            >{{ $t('register') }}</router-link>

            <div v-if="isLogin" class="w-full">
              <form class="w-full px-4" @submit.prevent="login" @keydown="form.onKeydown($event)">
                <fa icon="arrow-left" @click="toggleShow" class="block my-4" />
                <div class="w-full my-4">
                  <label
                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  >{{ $t('email') }}</label>
                  <input
                    class="input-main focus:bg-white focus:border-gray-200 focus:outline-none"
                    type="email"
                    :placeholder="$t('email')"
                    v-model="form.email"
                    :class="{ 'border-red-500': form.errors.has('email') }"
                  />
                  <has-error :form="form" field="email" />
                </div>
                <div class="w-full my-4">
                  <label
                    class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                  >{{ $t('password') }}</label>
                  <input
                    class="input-main focus:bg-white focus:border-gray-200 focus:outline-none"
                    type="password"
                    placeholder="*******"
                    v-model="form.password"
                    :class="{ 'border-red-500': form.errors.has('password') }"
                  />
                  <has-error :form="form" field="password" />
                </div>
                <router-link
                  :to="{ name: 'password.request' }"
                  class="text-sm"
                >{{ $t('forgot_password') }}</router-link>
                <div class="w-full my-4 flex justify-between items-center flex-wrap">
                  <checkbox v-model="remember" name="remember">{{ $t('remember_me') }}</checkbox>
                  <v-button :loading="form.busy">{{ $t('login') }}</v-button>
                </div>
              </form>
            </div>
          </div>

          <div class="flex justify-end flex-col h-full w-full mb-8 xl:mb-12">
            <h1 class="mb-4 xl:mb-6 mt-4 xl:text-4xl text-3xl">
              the social network for people
              <span>who move</span>
            </h1>
            <p
              class="xl:text-base text-sm"
            >Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam odio nunc, varius sed lectus nec, rhoncus auctor elit. Pellentesque pellentesque imperdiet mauris in aliquam. Ut eget felis eget est euismod commodo. Curabitur arcu nulla, ultricies porttitor mi et, semper cursus nulla.</p>
          </div>
        </div>
        <div class="w-0 sm:w-0/4 md:w-2/4 lg:w-4/6 xl:w-3/4 right-side"></div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";

export default {
  middleware: "guest",

  metaInfo() {
    return { title: this.$t("home") };
  },

  data: () => ({
    title: window.config.appName,
    isLogin: false,
    form: new Form({
      email: "",
      password: ""
    }),
    remember: false
  }),

  computed: mapGetters({
    authenticated: "auth/check"
  }),

  methods: {
    toggleShow() {
      this.isLogin = !this.isLogin;
    },
    async login() {
      // Submit the form.
      const { data } = await this.form.post("/api/login");

      // Save the token.
      this.$store.dispatch("auth/saveToken", {
        token: data.token,
        remember: this.remember
      });

      // Fetch the user.
      await this.$store.dispatch("auth/fetchUser");

      // Redirect home.
      this.$router.push({ name: "home" });
    }
  }
};
</script>
