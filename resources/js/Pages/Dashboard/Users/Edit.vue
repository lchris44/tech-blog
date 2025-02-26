<template>
  <v-form-dialog :title="title" @close="dismiss">
    <!-- Action Buttons -->
    <template #buttons>
      <Button
        :loading="form.processing"
        class="p-button-sm"
        icon="pi pi-save"
        label="Save"
        type="submit"
        @click="save"
      />
    </template>

    <!-- Form Fields -->
    <template #form-fields>
      <div class="p-fluid formgrid grid">
        <!-- Name Field -->
        <div class="field col-12">
          <v-input-text
            v-model="form.name"
            label="Name"
            placeholder="Name"
            :errors="form.errors.name"
            required
          />
        </div>

        <!-- Surname Field -->
        <div class="field col-12">
          <v-input-text
            v-model="form.surname"
            label="Surname"
            placeholder="Surname"
            :errors="form.errors.surname"
            required
          />
        </div>

        <!-- Email Field -->
        <div class="field col-12">
          <v-input-text
            v-model="form.email"
            label="Email"
            placeholder="Email"
            :errors="form.errors.email"
            required
          />
        </div>

        <!-- Password Field -->
        <div class="field col-12">
          <v-password
            v-model="form.password"
            label="Password"
            placeholder="Password"
            :errors="form.errors.password"
            required
          />
          <Button icon="pi pi-sync" size="small" @click="generateRandomPassword" />
        </div>
      </div>
    </template>
  </v-form-dialog>
</template>

<script setup>
import { computed } from "vue";
import { useModal } from "/vendor/emargareten/inertia-modal";
import { useForm } from "@inertiajs/vue3";

// Props
const props = defineProps({
  user: {
    type: Object,
  },
});

// Computed Properties
const isEdit = computed(() => form.id != null);
const title = computed(() => (isEdit.value ? "Edit User" : "Create User"));

// Modal Functions
const { close, redirect } = useModal();
const dismiss = () => {
  close();
  redirect();
};

// Form Handling
const form = useForm(props.user);

// Generate Random Password
const generateRandomPassword = () => {
  const chars =
    "0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  const passwordLength = 8;
  let password = "";

  for (let i = 0; i <= passwordLength; i++) {
    const randomNumber = Math.floor(Math.random() * chars.length);
    password += chars.substring(randomNumber, randomNumber + 1);
  }

  form.password = password;
};

// Save User
const save = () => {
  form.clearErrors();

  if (isEdit.value) {
    form.put(route("users.update", { user: props.user.id }), {
      onSuccess: () => dismiss(),
    });
  } else {
    form.post(route("users.store"), {
      onSuccess: () => dismiss(),
    });
  }
};
</script>
