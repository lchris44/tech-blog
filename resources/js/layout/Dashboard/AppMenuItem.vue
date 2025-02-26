<template>
  <li :class="menuItemClass">
    <!-- Root menu item label -->
    <div v-if="root && item.visible !== false" class="layout-menuitem-root-text">
      {{ item.label }}
    </div>

    <!-- Link with items or external URL -->
    <a
      v-if="(!item.to || item.items) && item.visible !== false"
      :href="item.url"
      @click="itemClick($event, item)"
      :class="item.class"
      :target="item.target"
      tabindex="0"
    >
      <i :class="item.icon" class="layout-menuitem-icon"></i>
      <span class="layout-menuitem-text">{{ item.label }}</span>
      <i v-if="item.items" class="pi pi-fw pi-angle-down layout-submenu-toggler"></i>
    </a>

    <!-- Link with internal route -->
    <a
      v-if="item.to && !item.items && item.visible !== false"
      @click="itemClick($event, item)"
      tabindex="0"
    >
      <i :class="item.icon" class="layout-menuitem-icon"></i>
      <span class="layout-menuitem-text">{{ item.label }}</span>
      <i v-if="item.items" class="pi pi-fw pi-angle-down layout-submenu-toggler"></i>
    </a>

    <!-- Submenu items -->
    <Transition v-if="item.items && item.visible !== false" name="layout-submenu">
      <ul v-show="root ? true : isActiveMenu" class="layout-submenu">
        <AppMenuItem
          v-for="(child, i) in item.items"
          :key="child.label || i"
          :item="child"
          :index="i"
          :parent-item-key="itemKey"
          :root="false"
        />
      </ul>
    </Transition>
  </li>
</template>

<script setup>
import { useLayout } from "@/layout/composables/layout";
import { onBeforeMount, ref, watch } from "vue";

const { layoutState, setActiveMenuItem, toggleMenu } = useLayout();

const props = defineProps({
  item: {
    type: Object,
    default: () => ({}),
  },
  index: {
    type: Number,
    default: 0,
  },
  root: {
    type: Boolean,
    default: true,
  },
  parentItemKey: {
    type: String,
    default: null,
  },
});

const isActiveMenu = ref(false);
const itemKey = ref(null);

onBeforeMount(() => {
  itemKey.value = props.parentItemKey
    ? props.parentItemKey + "-" + props.index
    : String(props.index);

  const activeItem = layoutState.activeMenuItem;

  isActiveMenu.value =
    activeItem === itemKey.value || activeItem
      ? activeItem.startsWith(itemKey.value + "-")
      : false;
});

watch(
  () => layoutState.activeMenuItem,
  (newVal) => {
    isActiveMenu.value =
      newVal === itemKey.value || newVal.startsWith(itemKey.value + "-");
  }
);

function itemClick(event, item) {
  if (item.disabled) {
    event.preventDefault();
    return;
  }

  if (
    (item.to || item.url) &&
    (layoutState.staticMenuMobileActive || layoutState.overlayMenuActive)
  ) {
    toggleMenu();
  }

  if (item.command) {
    item.command({ originalEvent: event, item: item });
  }

  const foundItemKey = item.items
    ? isActiveMenu.value
      ? props.parentItemKey
      : itemKey
    : itemKey.value;

  setActiveMenuItem(foundItemKey);
}

// Compute menu item class
const menuItemClass = computed(() => ({
  "layout-root-menuitem": props.root,
  "active-menuitem": isActiveMenu.value,
}));
</script>
