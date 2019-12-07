<template>
  <div>
    <navbar />
    <child />
  </div>
</template>

<script>
import Navbar from "~/components/Navbar";

export default {
  name: "BasicLayout",

  components: {
    Navbar
  }
};
</script>
