<template>
  <Dialog
    :visible="visible"
    :header="title"
    :breakpoints="{ '960px': '75vw', '640px': '100vw' }"
    :style="{ width }"
    :modal="true"
    :maximizable="true"
    class="p-fluid p-form-dialog"
    @update:visible="dismiss"
  >
    <!-- Slot for form fields -->
    <div>
      <slot name="form-fields"></slot>
    </div>

    <!-- Slot for buttons in the footer -->
    <template #footer>
      <slot name="buttons"></slot>
    </template>
  </Dialog>
</template>

<script setup>
const props = defineProps({
  title: String,
  visible: {
    type: Boolean,
    default: true,
  },
  width: {
    type: String,
    default: "500px",
  },
});

const emit = defineEmits(["close"]);

// Function to emit close event when dialog is dismissed
const dismiss = () => {
  emit("close");
};
</script>
