<template>
  <div :class="{ dark: isDarkTheme }" class="flex justify-center mt-8">
    <div :class="themeClasses.container" class="p-4 shadow-lg rounded w-full lg:w-150">
      <div class="text-center">
        <h2 :class="themeClasses.heading">
          {{ $t("login") }}
        </h2>
      </div>
      <form @submit.prevent="login">
        <div>
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

          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
              <Checkbox
                id="rememberme"
                :binary="true"
                v-model="form.remember"
                class="mr-2"
              />
              <label :class="themeClasses.label" for="rememberme">{{
                $t("auth.remember")
              }}</label>
            </div>
          </div>

          <Button
            :loading="form.processing"
            type="submit"
            :label="$t('auth.loginButton')"
            icon="pi pi-user"
            class="w-full"
          />
        </div>
      </form>

      <div class="text-center mt-4">
        <Button type="button" @click="goToRegister" class="w-full" text>
          {{ $t("auth.registerButton") }}
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, router } from "@inertiajs/vue3";
import { useLayout } from "@/layout/composables/layout";

const { isDarkTheme } = useLayout();

// Form handling
const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const login = () => {
  form.post("/login");
};

const goToRegister = () => {
  router.visit("/register");
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
  label: {
    "text-gray-300": isDarkTheme.value,
    "text-gray-700": !isDarkTheme.value,
  },
}));
</script>
