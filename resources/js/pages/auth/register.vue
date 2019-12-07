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
        <alert-error :form="form" :message="$t('input_problem')"></alert-error>

        <div v-if="mustVerifyEmail" role="alert" class="alert alert-success alert-dismissible">
          <button type="button" aria-label="Close" class="close">
            <span aria-hidden="true">×</span>
          </button>
          <div>{{ $t('verify_email_address') }}</div>
        </div>

        <form @submit.prevent="register">
          <div v-if="step === 1">
            <h2 class="mb-10">{{ $t('step_1') }}</h2>
            <div class="w-full my-4">
              <label class="label-main">{{ $t('lastname') }}</label>
              <input
                class="input-main"
                :class="{ 'border-red-500': form.errors.has('lastname') }"
                type="text"
                :placeholder="$t('lastname')"
                v-model="form.lastname"
              />
              <has-error :form="form" field="lastname" />
            </div>
            <div class="w-full my-4">
              <label class="label-main">{{ $t('firstname') }}</label>
              <input
                class="input-main"
                :class="{ 'border-red-500': form.errors.has('firstname') }"
                type="text"
                :placeholder="$t('firstname')"
                v-model="form.firstname"
              />
              <has-error :form="form" field="firstname" />
            </div>

            <div class="w-full mt-6 flex justify-end items-center">
              <button
                @click.prevent="next()"
                :class="{ 'btn-disabled small' : disableButtonFirstStep, 'btn-secondary small' : !disableButtonFirstStep}"
                :disabled="disableButtonFirstStep"
              >
                Suivant
                <fa icon="arrow-right" />
              </button>
            </div>
          </div>

          <div v-if="step === 2">
            <h2 class="mb-10">{{ $t('step_2') }}</h2>
            <div class="w-full my-4">
              <label class="label-main">{{ $t('email') }}</label>
              <input
                class="input-main"
                :class="{ 'border-red-500': form.errors.has('email') }"
                type="text"
                :placeholder="$t('email')"
                v-model="form.email"
              />
              <has-error :form="form" field="email" />
            </div>
            <div class="w-full my-4">
              <label class="label-main">{{ $t('phone') }}</label>
              <input
                class="input-main"
                :class="{ 'border-red-500': form.errors.has('number') }"
                type="text"
                :placeholder="$t('phone')"
                v-model="form.number"
              />
              <has-error :form="form" field="number" />
            </div>

            <div class="w-full mt-6 flex justify-between items-center">
              <fa @click.prevent="prev()" icon="arrow-left" class="text-xl" />
              <button
                @click.prevent="next()"
                :class="{ 'btn-disabled small' : disableButtonSecondStep, 'btn-secondary small' : !disableButtonSecondStep}"
                :disabled="disableButtonSecondStep"
              >
                Suivant
                <fa icon="arrow-right" />
              </button>
            </div>
          </div>

          <div v-if="step === 3">
            <h2 class="mb-10">{{ $t('step_3') }}</h2>
            <div class="w-full my-4">
              <label class="label-main">{{ $t('password') }}</label>
              <input
                class="input-main"
                :class="{ 'border-red-500': form.errors.has('password') }"
                type="password"
                :placeholder="$t('password')"
                v-model="form.password"
              />
            </div>
            <has-error :form="form" field="password" />
            <div class="w-full my-4">
              <label class="label-main">{{ $t('confirm_password') }}</label>
              <input
                class="input-main"
                :class="{ 'border-red-500': form.errors.has('password_confirmation') }"
                type="password"
                :placeholder="$t('confirm_password')"
                v-model="form.password_confirmation"
              />
              <has-error :form="form" field="password_confirmation" />
            </div>

            <div class="w-full mt-6 flex justify-between items-center">
              <fa @click.prevent="prev()" icon="arrow-left" class="text-xl" />
              <button
                @click.prevent="next()"
                :class="{ 'btn-disabled small' : disableButtonThirdStep, 'btn-secondary small' : !disableButtonThirdStep}"
                :disabled="disableButtonThirdStep"
              >
                Suivant
                <fa icon="arrow-right" />
              </button>
            </div>
          </div>

          <div v-if="step === 4">
            <h2 class="mb-10">{{ $t('step_4') }}</h2>
            <!--             <label class="label-main">{{ $t('birthdate') }}</label>
            <div class="flex flex-wrap -mx-3 mb-2">
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <div class="relative">
                  <select
                    v-model="form.birthday.day"
                    class="select-main focus:outline-none focus:bg-white focus:border-gray-500"
                  >
                    <option disabled selected>Jour</option>
                    <option value="01">1</option>
                    <option value="02">2</option>
                    <option value="03">3</option>
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
              </div>
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <div class="relative">
                  <select
                    v-model="form.birthday.month"
                    class="select-main focus:outline-none focus:bg-white focus:border-gray-500"
                  >
                    <option disabled selected>Mois</option>
                    <option value="01">Janvier</option>
                    <option value="02">Février</option>
                    <option value="03">Mars</option>
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
              </div>
              <div class="w-full md:w-1/3 px-3 mb-6 md:mb-0">
                <div class="relative">
                  <select
                    v-model="form.birthday.year"
                    class="select-main focus:outline-none focus:bg-white focus:border-gray-500"
                  >
                    <option disabled selected>Année</option>
                    <option value="1997">1997</option>
                    <option value="1998">1998</option>
                    <option value="1999">1999</option>
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
              </div>
              <has-error :form="form" field="birthday" />
            </div>-->

            <p class="text-gray-600 text-xs italic my-8">
              En cliquant sur Terminé, vous acceptez nos
              <a href>Conditions générales</a>. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam sagittis pharetra orci, vitae luctus ipsum aliquet vitae.
            </p>

            <div class="w-full mt-6 flex justify-between items-center">
              <fa @click.prevent="prev()" icon="arrow-left" class="text-xl" />
              <v-button :loading="form.busy">{{ $t('register') }}</v-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import LoginWithGithub from "~/components/LoginWithGithub";
import {
  required,
  email,
  minLength,
  maxLength,
  sameAs,
  numeric
} from "vuelidate/lib/validators";

export default {
  middleware: "guest",

  components: {
    LoginWithGithub
  },

  metaInfo() {
    return { title: this.$t("register") };
  },

  data: () => ({
    step: 1,
    form: new Form({
      lastname: "",
      firstname: "",
      email: "",
      birthday: {
        year: "",
        month: "",
        day: ""
      },
      password: "",
      password_confirmation: ""
    }),
    mustVerifyEmail: false
  }),

  methods: {
    async register() {
      // Register the user.
      const { data } = await this.form.post("/api/register");

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true;
      } else {
        // Log in the user.
        const {
          data: { token }
        } = await this.form.post("/api/login");

        // Save the token.
        this.$store.dispatch("auth/saveToken", { token });

        // Update the user.
        await this.$store.dispatch("auth/updateUser", { user: data });

        // Redirect home.
        this.$router.push({ name: "home" });
      }
    },
    prev() {
      this.step--;
    },
    next() {
      this.step++;
    }
  }

  /*   computed: {
    disableButtonFirstStep: function() {
      if (
        this.$v.form.firstname.$invalid === true ||
        !this.$v.form.lastname.$invalid === false
      )
        return true;
    },
    disableButtonSecondStep: function() {
      if (
        this.$v.form.email.$invalid === true ||
        !this.$v.form.number.$invalid === false
      )
        return true;
    },
    disableButtonThirdStep: function() {
      if (
        this.$v.form.password.$invalid === true ||
        !this.$v.form.password_confirmation.$invalid === false
      )
        return true;
    }
  }, */
};
</script>
