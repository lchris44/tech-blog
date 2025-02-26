<template>
  <div class="layout-topbar">
    <div class="layout-topbar-logo-container">
      <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
        <i class="pi pi-bars"></i>
      </button>
      <Link href="/dashboard" class="layout-topbar-logo">
        <span class="font-semibold">Tech Blog</span>
      </Link>
    </div>

    <div class="layout-topbar-actions">
      <div class="layout-config-menu">
        <div class="relative">
          <button
            @click="showAppConfiguration = !showAppConfiguration"
            class="layout-topbar-action"
          >
            <i class="pi pi-palette"></i>
            <span>Preferences</span>
          </button>

          <button @click="logout" class="layout-topbar-action">
            <i class="pi pi-sign-out"></i>
            <span>Logout</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <Drawer v-model:visible="showAppConfiguration" position="right">
    <AppConfigurator />
  </Drawer>
</template>

<script setup>
import { Link, router } from "@inertiajs/vue3";
import { useLayout } from "@/layout/composables/layout";
import AppConfigurator from "@/layout/Dashboard/AppConfigurator.vue";
import { useConfirm } from "primevue/useconfirm";

const { toggleMenu } = useLayout();
const showAppConfiguration = ref(false);
const confirm = useConfirm();

const logout = () => {
  confirm.require({
    message: "Are you sure you want to log out?",
    header: "Logout Confirmation",
    icon: "pi pi-info-circle",
    acceptClass: "p-button-primary p-button-sm",
    acceptIcon: "pi pi-power-off",
    rejectClass: "p-button-secondary p-button-sm",
    rejectIcon: "pi pi-times",
    accept: () => {
      router.post("logout");
    },
  });
};
</script>
