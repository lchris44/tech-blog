<template>
  <div :class="{ dark: isDarkTheme }" class="flex justify-center mt-8">
    <div :class="themeClasses.container" class="p-4 shadow-lg rounded w-full lg:w-150">
      <div class="text-center">
        <h2 :class="themeClasses.heading">
          {{ $t("register") }}
        </h2>
      </div>
      <form @submit.prevent="register">
        <div>
          <v-input-text
            v-model="form.name"
            :label="$t('auth.name')"
            :placeholder="$t('auth.name')"
            :errors="form.errors.name"
            required
            :class="themeClasses.input"
          />

          <v-input-text
            v-model="form.surname"
            :label="$t('auth.surname')"
            :placeholder="$t('auth.surname')"
            :errors="form.errors.surname"
            required
            :class="themeClasses.input"
          />

          <v-input-text
            v-model="form.email"
            :label="$t('auth.email')"
            :placeholder="$t('auth.email')"
            :errors="form.errors.email"
            required
            :class="themeClasses.input"
          />

          <v-password
            v-model="form.password"
            :label="$t('auth.password')"
            :placeholder="$t('auth.password')"
            :errors="form.errors.password"
            required
            :class="themeClasses.input"
          />

          <v-password
            v-model="form.password_confirmation"
            :label="$t('auth.confirmPassword')"
            :placeholder="$t('auth.confirmPassword')"
            :errors="form.errors.password_confirmation"
            required
            :class="themeClasses.input"
          />

          <Button
            :loading="form.processing"
            type="submit"
            :label="$t('auth.registerButton')"
            icon="pi pi-user"
            class="w-full"
          />
        </div>
      </form>
      <div class="text-center mt-4">
        <Button @click="goToLogin" class="w-full" text>
          {{ $t("auth.alreadyHaveAccount") }}
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, router } from "@inertiajs/vue3";
import { useLayout } from "@/layout/composables/layout";
import { computed } from "vue";

const { isDarkTheme } = useLayout();

// Form handling
const form = useForm({
  name: "",
  surname: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const register = () => {
  form.post("/register");
};

const goToLogin = () => {
  router.visit("/login");
};

// Theme classes computed property
const themeClasses = computed(() => ({
  container: {
    "bg-gray-800 text-white": isDarkTheme.value,
    "bg-white text-gray-900": !isDarkTheme.value,
  },
  heading: {
    "text-white": isDarkTheme.value,
    "text-gray-900": !isDarkTheme.value,
  },
  input: {
    "bg-gray-700 text-white border-gray-600": isDarkTheme.value,
    "bg-gray-100 text-gray-900 border-gray-300": !isDarkTheme.value,
  },
}));
</script>
