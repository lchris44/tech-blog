<template>
  <ul class="layout-menu">
    <template v-for="(item, i) in menuModel" :key="item.label || i">
      <AppMenuItem v-if="!item.separator" :item="item" :index="i" />
      <li v-if="item.separator" class="menu-separator"></li>
    </template>
  </ul>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import AppMenuItem from "@/layout/Dashboard/AppMenuItem.vue";

// Menu model
const menuModel = [
  {
    label: "Home",
    items: [
      {
        label: "Dashboard",
        icon: "pi pi-fw pi-home",
        command: () => router.visit(route("dashboard")),
      },
    ],
  },
  {
    label: "Management",
    items: [
      {
        label: "Tags",
        icon: "pi pi-fw pi-tags",
        command: () => router.visit(route("tags.index")),
      },
      {
        label: "Posts",
        icon: "pi pi-fw pi-book",
        command: () => router.visit(route("posts.index")),
      },
    ],
  },
  {
    label: "System",
    items: [
      {
        label: "Users",
        icon: "pi pi-fw pi-users",
        command: () => router.visit(route("users.index")),
      },
    ],
  },
];
</script>
