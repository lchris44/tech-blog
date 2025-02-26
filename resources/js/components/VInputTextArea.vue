<template>
  <label v-if="label">
    {{ label }}
    <span v-if="required" class="text-red-500">*</span>
  </label>

  <Textarea
    v-model="model"
    :class="inputClasses"
    :placeholder="placeholder"
    :disabled="disabled"
  />

  <small v-if="help && !errors">{{ help }}</small>
  <v-errors v-if="errors" :errors="errors" />
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
  help: {
    type: String,
    default: null,
  },
  required: {
    type: Boolean,
    default: false,
  },
});

// Computed property to manage the model value
const model = computed({
  get() {
    return props.modelValue;
  },
  set(val) {
    emit("update:modelValue", val);
  },
});

// Computed property for dynamic input classes
const inputClasses = computed(() => {
  return ["w-full mb-3", { "p-invalid": props.errors !== undefined }];
});
</script>
