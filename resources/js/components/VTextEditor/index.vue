<template>
  <label v-if="label">
    {{ label }}
    <span v-if="required" class="text-danger">*</span>
  </label>

  <Editor
    :api-key="tinyMceApiKey"
    v-model="model"
    :init="{
      statusbar: false,
      height: 600,
      skin: isDarkTheme ? 'oxide-dark' : 'oxide',
      content_css: isDarkTheme ? 'dark' : 'default',
      plugins: 'lists link image table code help wordcount',
    }"
  />

  <v-errors v-if="errors != undefined" :errors="errors"></v-errors>
</template>
<script setup>
import { computed } from "vue";
import Editor from "@tinymce/tinymce-vue";
import { useLayout } from "@/layout/composables/layout";
const { isDarkTheme } = useLayout();

const props = defineProps({
  modelValue: String,
  label: String,
  required: {
    type: Boolean,
    default: false,
  },
  errors: Object,
});

// TinyMCE API Key (loaded securely)
const tinyMceApiKey = import.meta.env.VITE_TINYMCE_API_KEY;

const emit = defineEmits(["update:modelValue"]);

const model = computed({
  get() {
    return props.modelValue;
  },
  set(val) {
    emit("update:modelValue", val);
  },
});
</script>
