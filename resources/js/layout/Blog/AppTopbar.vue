<template>
  <nav :class="themeClasses.nav" class="shadow py-4">
    <div class="lg:w-8/12 md:w-6/12 mx-auto flex justify-between items-center">
      <Link href="/">
        <h2 :class="themeClasses.logo">Tech Blog</h2>
      </Link>
      <div class="flex items-center gap-4">
        <!-- Login and Register Links -->
        <Button :label="$t('login')" @click="goToLogin" text />
        <Button :label="$t('register')" @click="goToRegister" />

        <!-- Language Dropdown -->
        <div class="relative">
          <!-- Button -->
          <button @click="toggleLanguageDropdown" :class="themeClasses.languageButton">
            <img :src="getFlagUrl(locale)" class="w-5 h-5 mr-2" />
            {{ locale.toUpperCase() }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              ></path>
            </svg>
          </button>

          <!-- Dropdown -->
          <div v-if="isLanguageDropdownOpen" :class="themeClasses.languageDropdown">
            <button
              v-for="lang in availableLocales"
              :key="lang"
              @click="changeLanguage(lang)"
              :class="themeClasses.languageItem"
            >
              <img :src="getFlagUrl(lang)" class="w-5 h-5" />
              {{ lang.toUpperCase() }}
            </button>
          </div>
        </div>

        <!-- Settings Toggle -->
        <button
          type="button"
          class="transition-transform duration-200 hover:scale-150"
          @click="showAppConfiguration = !showAppConfiguration"
        >
          <i class="pi pi-palette"></i>
        </button>
      </div>
    </div>
  </nav>
  <Drawer v-model:visible="showAppConfiguration" position="right">
    <AppConfigurator />
  </Drawer>
</template>

<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import AppConfigurator from "@/layout/Dashboard/AppConfigurator.vue";
import { useLayout } from "@/layout/composables/layout";
import { computed } from "vue";

const showAppConfiguration = ref(false);

const { isDarkTheme } = useLayout();

// i18n Locale
const { locale, availableLocales } = useI18n();

// Language Dropdown State
const isLanguageDropdownOpen = ref(false);

const toggleLanguageDropdown = () => {
  isLanguageDropdownOpen.value = !isLanguageDropdownOpen.value;
};

const changeLanguage = (lang) => {
  locale.value = lang;
  localStorage.setItem("locale", lang);
  isLanguageDropdownOpen.value = false;
  router.post("/change-language", { lang });
};

// Function to get flag URL based on locale
const getFlagUrl = (lang) => {
  const countryCodes = {
    en: "GB", // English -> Great Britain
    it: "IT", // Italian -> Italy
  };
  const countryCode = countryCodes[lang] || "GB"; // Default to GB if no mapping
  return `https://flagsapi.com/${countryCode}/flat/16.png`;
};

const goToRegister = () => {
  router.visit("/register");
};

const goToLogin = () => {
  router.visit("/login");
};

// Theme classes computed property
const themeClasses = computed(() => ({
  nav: {
    "bg-gray-800 text-white": isDarkTheme.value,
    "bg-white text-black": !isDarkTheme.value,
  },
  logo: {
    "text-blue-600": !isDarkTheme.value,
    "text-white": isDarkTheme.value,
  },
  languageButton: {
    "inline-flex items-center justify-center w-full rounded-md border shadow-sm px-4 py-2 border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors": !isDarkTheme.value,
    "inline-flex items-center justify-center w-full rounded-md border shadow-sm px-4 py-2 border-gray-600 bg-gray-800 text-gray-200 hover:bg-gray-700 transition-colors":
      isDarkTheme.value,
  },
  languageDropdown: {
    "absolute right-0 mt-2 w-40 rounded-lg shadow-lg border bg-white text-gray-700 border-gray-300 z-100": !isDarkTheme.value,
    "absolute right-0 mt-2 w-40 rounded-lg shadow-lg border bg-gray-800 text-gray-200 border-gray-600 z-100":
      isDarkTheme.value,
  },
  languageItem: {
    "flex items-center gap-2 w-full px-4 py-2 text-sm rounded-md hover:bg-gray-100": !isDarkTheme.value,
    "flex items-center gap-2 w-full px-4 py-2 text-sm rounded-md hover:bg-gray-700":
      isDarkTheme.value,
  },
}));
</script>
