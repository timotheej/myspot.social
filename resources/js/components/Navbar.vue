<template>
  <div>
    <nav id="header" class="fixed w-full z-10 top-0 h-16 px-4">
      <div class="w-full flex items-center justify-between flex-wrap h-full mt-0 md:pb-0">
        <router-link to="/home" class="flex relative inline-block">
          <img class="logo" src="/images/logo_white_small.svg" />
        </router-link>

        <locale-dropdown />

        <div class="flex relative inline-block">
          <div class="relative text-sm">
            <button id="userButton" class="flex items-center focus:outline-none mr-3">
              <img class="w-8 h-8 rounded-full mr-4" :src="user.photo_url" alt="Avatar of User" />
              <span class="hidden md:inline-block text-white">{{ $t('hi') }}, {{ user.firstname }}</span>
              <svg
                class="pl-2 h-2"
                version="1.1"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 129 129"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                enable-background="new 0 0 129 129"
              >
                <g>
                  <path
                    d="m121.3,34.6c-1.6-1.6-4.2-1.6-5.8,0l-51,51.1-51.1-51.1c-1.6-1.6-4.2-1.6-5.8,0-1.6,1.6-1.6,4.2 0,5.8l53.9,53.9c0.8,0.8 1.8,1.2 2.9,1.2 1,0 2.1-0.4 2.9-1.2l53.9-53.9c1.7-1.6 1.7-4.2 0.1-5.8z"
                  />
                </g>
              </svg>
            </button>
            <div
              id="userMenu"
              class="bg-white rounded shadow-md mt-2 absolute mt-12 top-0 right-0 min-w-full overflow-auto z-10 invisible"
            >
              <ul class="list-reset">
                <li>
                  <router-link
                    :to="{ name: 'settings.profile' }"
                    href="#"
                    class="px-4 py-2 block text-gray-900 hover:bg-gray-400 no-underline hover:no-underline"
                  >{{ $t('settings') }}</router-link>
                </li>
                <li>
                  <hr class="border-t mx-2 border-gray-400" />
                </li>
                <li>
                  <a
                    href="#"
                    class="px-4 py-2 block text-gray-900 hover:bg-gray-400 no-underline hover:no-underline"
                    @click.prevent="logout"
                  >{{ $t('logout') }}</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import LocaleDropdown from "./LocaleDropdown";

export default {
  components: {
    LocaleDropdown
  },

  data: () => ({
    appName: window.config.appName
  }),

  computed: mapGetters({
    user: "auth/user"
  }),

  methods: {
    async logout() {
      // Log out the user.
      await this.$store.dispatch("auth/logout");

      // Redirect to welcome.
      this.$router.push({ name: "welcome" });
    }
  },

  mounted() {
    /*Toggle dropdown list*/
    /*https://gist.github.com/slavapas/593e8e50cf4cc16ac972afcbad4f70c8*/

    var userMenuDiv = document.getElementById("userMenu");
    var userMenu = document.getElementById("userButton");

    var langMenuDiv = document.getElementById("langMenu");
    var langMenu = document.getElementById("langButton");

    document.onclick = check;

    function check(e) {
      var target = (e && e.target) || (event && event.srcElement);

      //User Menu
      if (!checkParent(target, userMenuDiv)) {
        // click NOT on the menu
        if (checkParent(target, userMenu)) {
          // click on the link
          if (userMenuDiv.classList.contains("invisible")) {
            userMenuDiv.classList.remove("invisible");
          } else {
            userMenuDiv.classList.add("invisible");
          }
        } else {
          // click both outside link and outside menu, hide menu
          userMenuDiv.classList.add("invisible");
        }
      }

      //Lang Menu
      if (!checkParent(target, langMenuDiv)) {
        // click NOT on the menu
        if (checkParent(target, langMenu)) {
          // click on the link
          if (langMenuDiv.classList.contains("invisible")) {
            langMenuDiv.classList.remove("invisible");
          } else {
            langMenuDiv.classList.add("invisible");
          }
        } else {
          // click both outside link and outside menu, hide menu
          langMenuDiv.classList.add("invisible");
        }
      }

      function checkParent(t, elm) {
        while (t.parentNode) {
          if (t == elm) {
            return true;
          }
          t = t.parentNode;
        }
        return false;
      }
    }
  }
};
</script>
