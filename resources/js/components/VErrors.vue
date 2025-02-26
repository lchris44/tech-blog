<template>
  <div>
    <small class="text-red-500" v-for="error in errors" :key="error">
      {{ error }}
    </small>
  </div>
</template>

<script setup>
const props = defineProps({
  errors: String,
});
</script>
