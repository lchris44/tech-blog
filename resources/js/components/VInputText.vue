<template>
  <!-- Label for the input field, displayed only if provided -->
  <label v-if="label">
    {{ label }}
    <span v-if="required" class="text-red-500">*</span>
  </label>

  <!-- Input field with dynamic properties -->
  <InputText
    v-model="model"
    :class="inputClasses"
    :placeholder="placeholder"
    :disabled="disabled"
  />

  <!-- Help text displayed if no validation errors exist -->
  <small v-if="help && errors === undefined">{{ help }}</small>

  <!-- Validation error messages -->
  <v-errors v-if="errors !== undefined" :errors="errors"></v-errors>
</template>

<script setup>
import { computed } from "vue";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
  modelValue: String,
  mode: String,
  label: String,
  errors: String,
  disabled: Boolean,
  placeholder: String,
  class: {
    type: String,
    default: null,
  },
  help: {
    type: String,
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
});

// Computed property for handling v-model binding
const model = computed({
  get: () => props.modelValue,
  set: (val) => emit("update:modelValue", val),
});

// Computed property for dynamic input classes
const inputClasses = computed(() => [
  ...(props.class ? [props.class] : []),
  "w-full mb-3",
  { "p-invalid": props.errors !== undefined },
]);
</script>
