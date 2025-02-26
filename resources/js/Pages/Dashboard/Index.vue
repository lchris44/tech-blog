<template>
  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <!-- Posts Card -->
    <div class="col-span-1">
      <div class="card shadow mb-0">
        <div class="flex justify-between mb-4">
          <div>
            <span class="block text-muted-color font-semibold mb-4">Posts</span>
            <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">
              {{ posts_count }}
            </div>
          </div>
          <div
            class="flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded w-10 h-10"
          >
            <i class="pi pi-book text-blue-500 !text-xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Tags Card -->
    <div class="col-span-1">
      <div class="card shadow mb-0">
        <div class="flex justify-between mb-4">
          <div>
            <span class="block text-muted-color font-semibold mb-4">Tags</span>
            <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">
              {{ tags_count }}
            </div>
          </div>
          <div
            class="flex items-center justify-center bg-orange-100 dark:bg-orange-400/10 rounded w-10 h-10"
          >
            <i class="pi pi-tag text-orange-500 !text-xl"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  posts_count: {
    type: Number,
    required: true,
  },
  tags_count: {
    type: Number,
    required: true,
  },
});
</script>
