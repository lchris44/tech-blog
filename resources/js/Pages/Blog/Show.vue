<template>
  <section :class="{ dark: isDarkTheme }" class="py-10">
    <div class="container mx-auto">
      <div class="flex flex-wrap -mx-4">
        <!-- Main Blog Post -->
        <div class="lg:w-8/12 md:w-6/12 w-full px-4">
          <!-- Blog Post Card -->
          <div
            :class="{
              'bg-gray-800 text-white': isDarkTheme,
              'bg-white text-gray-900': !isDarkTheme,
            }"
            class="rounded-lg shadow-lg overflow-hidden"
          >
            <!-- Cover Image -->
            <img
              v-if="post.cover"
              :src="post.cover"
              class="w-full rounded-t-lg"
              alt="Cover Image"
            />

            <!-- Post Content -->
            <div class="p-6">
              <h1>{{ post.title[locale] }}</h1>
              <div class="text-right">
                <small class="text-gray-500 block">
                  <i class="pi pi-user"></i>
                  {{ post.user.name + " " + post.user.surname }}
                </small>
                <small class="text-gray-500 block">
                  <i class="pi pi-calendar"></i> {{ formatDate(post.created_at) }}
                </small>
              </div>
              <p class="mt-3" v-html="post.content[locale]"></p>
            </div>
          </div>

          <!-- Latest News Section -->
          <div
            :class="{
              'bg-gray-800 text-white': isDarkTheme,
              'bg-white text-gray-900': !isDarkTheme,
            }"
            class="rounded-lg shadow-lg mt-4"
          >
            <div class="p-6">
              <h2 class="text-lg font-semibold mb-4">{{ $t("otherPosts") }}</h2>
              <div class="flex flex-wrap -mx-2">
                <!-- Related Posts -->
                <div
                  v-for="post in posts"
                  :key="post.id"
                  class="lg:w-1/2 md:w-1/2 w-full px-2 mt-4"
                >
                  <div
                    :class="{
                      'bg-gray-800 text-white': isDarkTheme,
                      'bg-white text-black': !isDarkTheme,
                    }"
                    class="p-5 rounded-lg shadow"
                  >
                    <h2
                      class="text-xl font-semibold"
                      :class="{
                        'text-blue-600': !isDarkTheme,
                        'text-white': isDarkTheme,
                      }"
                    >
                      {{ post.title[locale] }}
                    </h2>
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
                      label="Read More →"
                      @click="goToPage(`/post/${post.id}`)"
                      class="p-0 w-full"
                      text
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:w-4/12 md:w-6/12 w-full px-4 mt-4 md:mt-0">
          <div
            :class="{
              'bg-gray-800 text-white': isDarkTheme,
              'bg-white text-gray-900': !isDarkTheme,
            }"
            class="rounded-lg shadow-lg sticky top-0"
          >
            <div class="p-6">
              <h2 class="text-lg font-semibold mb-4">{{ $t("tags") }}</h2>
              <div class="flex flex-wrap">
                <!-- Tags -->
                <Tag
                  v-for="tag in tags"
                  :key="tag.id"
                  :value="tag.name[locale]"
                  class="cursor-pointer ml-2 mt-1"
                  @click="goToPage(`/?tag=${tag.name.en}`)"
                  rounded
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { defineProps } from "vue";
import { useI18n } from "vue-i18n";
import { router } from "@inertiajs/vue3";
import { useLayout } from "@/layout/composables/layout";

// Layout and Localization
const { isDarkTheme } = useLayout();
const { locale } = useI18n();

// Props
defineProps({
  post: {
    type: Object,
    required: false,
  },
  posts: {
    type: Object,
    required: false,
  },
  tags: {
    type: Object,
    required: false,
  },
});

// Function to format date
const formatDate = (isoString) => {
  const date = new Date(isoString);

  const day = date.getDate();
  const month = date.toLocaleString("default", { month: "long" });
  const year = date.getFullYear();

  const daySuffix = (day) => {
    if (day > 3 && day < 21) return "th"; // All days 4-20 end with 'th'
    switch (day % 10) {
      case 1:
        return "st";
      case 2:
        return "nd";
      case 3:
        return "rd";
      default:
        return "th";
    }
  };

  return `${day}${daySuffix(day)} ${month}, ${year}`;
};

// Navigation Method
const goToPage = (path) => {
  router.visit(path);
};
</script>
