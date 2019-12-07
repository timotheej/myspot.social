<template>
  <div id="settings">
    <div class="wrapper flex items-center justify-center flex-col h-full">
      <div
        class="shadow-2xl rounded bg-white h-auto xs:h-full mt-16 w-full sm:w-5/6 md:w-4/6 lg:w-3/6 xl:w-2/6"
      >
        <ul class="flex justify-around">
          <li v-for="tab in tabs" :key="tab.route" class="w-full h-16">
            <router-link
              :to="{ name: tab.route }"
              class="h-full w-full flex justify-center items-center rounded-t bg-gray-200 uppercase text-gray-700 text-sm font-semibold"
              active-class="active-class"
            >
              <fa :icon="tab.icon" fixed-width class="mr-2" />
              {{ tab.name }}
            </router-link>
          </li>
        </ul>

        <div class="p-6">
          <transition name="fade" mode="out-in">
            <router-view />
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  middleware: "auth",
  layout: "basic",

  computed: {
    tabs() {
      return [
        {
          icon: "user",
          name: this.$t("profile"),
          route: "settings.profile"
        },
        {
          icon: "lock",
          name: this.$t("password"),
          route: "settings.password"
        }
      ];
    }
  }
};
</script>
