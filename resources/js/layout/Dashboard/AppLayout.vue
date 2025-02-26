<template>
  <div class="layout-wrapper" :class="containerClass">
    <AppTopbar />
    <AppSidebar />
    <div class="layout-main-container">
      <div class="layout-main">
        <slot />
      </div>
      <AppFooter />
    </div>
    <ConfirmDialog />
    <Modal />
    <div class="layout-mask animate-fadein"></div>
  </div>
</template>

<script setup>
import { computed, watch, ref, onUnmounted } from "vue";
import { Modal } from "/vendor/emargareten/inertia-modal";
import AppTopbar from "@/layout/Dashboard/AppTopbar.vue";
import AppFooter from "@/layout/AppFooter.vue";
import AppSidebar from "@/layout/Dashboard/AppSidebar.vue";
import ConfirmDialog from "primevue/confirmdialog";
import { useLayout } from "@/layout/composables/layout";
import { useToast } from "vue-toastification";
import { usePage as inertiaPage } from "@inertiajs/vue3";

const { layoutConfig, layoutState, isSidebarActive } = useLayout();
const page = computed(() => inertiaPage().props);
const toast = useToast();
const flushMessage = computed(() => page.value.flushMessage);
const outsideClickListener = ref(null);

// Watch for flush messages and show toast notifications
watch(flushMessage, ({ message = null, type = null }) => {
  if (type && !page.value.popstate.popstate) {
    toast(message, { type });
  }
});

// Watch for sidebar activity and bind/unbind outside click listener
watch(isSidebarActive, (newVal) => {
  if (newVal) {
    bindOutsideClickListener();
  } else {
    unbindOutsideClickListener();
  }
});

// Compute container classes based on layout configuration
const containerClass = computed(() => ({
  "layout-overlay": layoutConfig.menuMode === "overlay",
  "layout-static": layoutConfig.menuMode === "static",
  "layout-static-inactive":
    layoutState.staticMenuDesktopInactive && layoutConfig.menuMode === "static",
  "layout-overlay-active": layoutState.overlayMenuActive,
  "layout-mobile-active": layoutState.staticMenuMobileActive,
}));

// Bind outside click listener to close sidebar
function bindOutsideClickListener() {
  if (!outsideClickListener.value) {
    outsideClickListener.value = (event) => {
      if (isOutsideClicked(event)) {
        layoutState.overlayMenuActive = false;
        layoutState.staticMenuMobileActive = false;
        layoutState.menuHoverActive = false;
      }
    };
    document.addEventListener("click", outsideClickListener.value);
  }
}

// Unbind outside click listener
function unbindOutsideClickListener() {
  if (outsideClickListener.value) {
    document.removeEventListener("click", outsideClickListener.value);
    outsideClickListener.value = null;
  }
}

// Check if the click is outside the sidebar or topbar
function isOutsideClicked(event) {
  const sidebarEl = document.querySelector(".layout-sidebar");
  const topbarEl = document.querySelector(".layout-menu-button");

  return !(
    sidebarEl.isSameNode(event.target) ||
    sidebarEl.contains(event.target) ||
    topbarEl.isSameNode(event.target) ||
    topbarEl.contains(event.target)
  );
}

// Cleanup event listener on component unmount
onUnmounted(() => {
  unbindOutsideClickListener();
});
</script>
