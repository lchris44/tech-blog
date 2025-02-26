<template>
  <!-- Hero Section -->
  <div
    class="text-center py-20 bg-gradient-to-r to-gray-600"
    :style="{
      backgroundImage: `linear-gradient(to right, var(--primary-color), #4b5563)`,
    }"
  >
    <h1 class="text-4xl font-bold">{{ $t("welcome") }}</h1>
    <p class="mt-2 text-lg">{{ $t("stayUpdated") }}</p>
  </div>

  <!-- Main Content -->
  <div class="lg:w-8/12 md:w-6/12 mx-auto px-4 py-10">
    <!-- Tag Filter Section -->
    <div class="mb-6 flex flex-wrap gap-2">
      <Tag
        v-for="tag in tags"
        :key="tag.id"
        :value="tag.name[locale]"
        class="cursor-pointer"
        @click="goToPage(`/?tag=${tag.name.en}`)"
        rounded
      />
    </div>

    <!-- Blog Posts -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="post in posts.data"
        :key="post.id"
        :class="{
          'bg-gray-800 text-white': isDarkTheme,
          'bg-white text-black': !isDarkTheme,
        }"
        class="p-5 rounded-lg shadow"
      >
        <!-- Post Title -->
        <h2
          class="text-xl font-semibold"
          :class="{ 'text-blue-600': !isDarkTheme, 'text-white': isDarkTheme }"
        >
          {{ post.title[locale] }}
        </h2>

        <!-- Post Short Description -->
        <p class="mt-2">{{ post.short_description[locale] }}</p>

        <!-- Tags -->
        <div class="mt-3 flex flex-wrap gap-2">
          <Tag
            v-for="tag in post.tags"
            :key="tag.id"
            :value="tag.name[locale]"
            class="cursor-pointer"
            rounded
          />
        </div>

        <!-- Read More Button -->
        <Button
          :label="$t('readMore')"
          @click="goToPage(`/post/${post.id}`)"
          class="mt-2 w-full"
          text
        />
      </div>
    </div>

    <!-- Pagination -->
    <div class="col-12 pt-15">
      <ul class="flex justify-center space-x-2 mb-0">
        <!-- Previous Page Button -->
        <li :class="{ disabled: !posts.prev_page_url }">
          <Button
            :disabled="!posts.prev_page_url"
            icon="pi pi-chevron-left"
            @click="goToPage(posts.prev_page_url)"
            rounded
          />
        </li>

        <!-- Page Numbers -->
        <li
          v-for="page in pages"
          :key="page"
          :class="{ active: posts.current_page === page }"
        >
          <Button @click="goToPage(`/?page=${page}`)" :label="`${page}`" rounded />
        </li>

        <!-- Next Page Button -->
        <li :class="{ disabled: !posts.next_page_url }">
          <Button
            :disabled="!posts.next_page_url"
            icon="pi pi-chevron-right"
            @click="goToPage(posts.next_page_url)"
            rounded
          />
        </li>
      </ul>
    </div>
    <!-- Pagination End -->
  </div>
</template>

<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useLayout } from "@/layout/composables/layout";

// Layout and Localization
const { isDarkTheme } = useLayout();
const { locale } = useI18n();

// Props
const props = defineProps({
  tags: {
    type: Object,
    required: false,
  },
  posts: {
    type: Object,
    required: false,
  },
});

// Computed Properties
const pages = computed(() => {
  const pages = [];
  for (let i = 1; i <= props.posts.last_page; i++) {
    pages.push(i);
  }
  return pages;
});

// Navigation Method
const goToPage = (path) => {
  router.visit(path);
};
</script>
