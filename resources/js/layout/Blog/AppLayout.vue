<template>
  <div :class="themeClasses.root" class="min-h-screen">
    <AppTopbar />
    <div class="layout-blog-container">
      <div class="layout-main">
        <slot />
      </div>
      <AppFooter />
    </div>
  </div>
</template>

<script setup>
import AppTopbar from "@/layout/Blog/AppTopbar.vue";
import AppFooter from "@/layout/AppFooter.vue";
import { useLayout } from "@/layout/composables/layout";
import { computed } from "vue";

const { isDarkTheme } = useLayout();

// Theme classes computed property
const themeClasses = computed(() => ({
  root: {
    "dark bg-gray-900 text-white": isDarkTheme.value,
    "bg-gray-50 text-black": !isDarkTheme.value,
  },
}));
</script>
