<template>
  <div class="flex relative flex-grow">
    <div class="relative text-sm">
      <button id="langButton" class="flex items-center focus:outline-none ml-3">
        <span class="text-white">{{ locales[locale] }}</span>
      </button>
      <div
        id="langMenu"
        class="bg-white rounded shadow-md absolute top-0 left-0 min-w-full overflow-auto z-10 invisible"
        style="margin-top: 3em;"
      >
        <ul class="list-reset">
          <li
            v-for="(value, key) in locales"
            :key="key"
            class="px-4 py-2 block text-gray-900 hover:bg-gray-400 no-underline hover:no-underline cursor-pointer"
            href="#"
            @click.prevent="setLocale(key)"
          >{{ value }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import { loadMessages } from "~/plugins/i18n";

export default {
  computed: mapGetters({
    locale: "lang/locale",
    locales: "lang/locales"
  }),

  methods: {
    setLocale(locale) {
      if (this.$i18n.locale !== locale) {
        loadMessages(locale);

        this.$store.dispatch("lang/setLocale", { locale });
      }
    }
  }
};
</script>
