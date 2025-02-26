<template>
  <v-data-table
    :items="payload"
    :columns="columns"
    :filters="filters"
    :global-filters="globalFilters"
    @update:items="(v) => updateItems(v)"
  >
    <!-- Header Buttons -->
    <template #header-buttons>
      <Button
        label="New Tag"
        icon="pi pi-plus"
        color="primary"
        class="mr-2"
        @click="visit('tags.create')"
      />
    </template>

    <!-- Custom Cell Templates -->
    <template #record.created_at="{ record }">
      {{ moment(record).format("DD-MM-YYYY") }}
    </template>

    <!-- Action Buttons -->
    <template #data-buttons="{ record }">
      <li class="p-menuitem">
        <div class="p-menuitem-content">
          <a
            @click="visit('tags.edit', { tag: record.id })"
            class="p-menuitem-link hover:opacity-75"
          >
            <span class="p-menuitem-icon pi pi-pencil"></span>
            <span class="p-menuitem-text ml-2">Edit</span>
          </a>
        </div>
      </li>
      <li class="p-menuitem">
        <div class="p-menuitem-content">
          <a @click="destroy(record.id)" class="p-menuitem-link hover:opacity-75">
            <span class="p-menuitem-icon pi pi-trash"></span>
            <span class="p-menuitem-text ml-2">Delete</span>
          </a>
        </div>
      </li>
    </template>
  </v-data-table>
</template>

<script setup>
import moment from "moment";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import { useConfirm } from "primevue/useconfirm";
import { FilterMatchMode, FilterOperator } from "@primevue/core/api";

// Props
defineProps({
  payload: {
    type: Object,
    required: false,
  },
});

// Confirm Dialog
const confirm = useConfirm();

// Global Filters
const globalFilters = ["id", "name.en"];

// Table Columns
const columns = [
  { field: "id", header: "ID", headerStyle: "width:1%", sortable: true },
  {
    field: "name.en",
    header: "Name",
    headerStyle: "width:15%",
    filter: {
      component: "InputText",
      properties: { type: "text", placeholder: "Search" },
    },
    sortable: false,
  },
  {
    field: "created_at",
    header: "Created",
    headerStyle: "width:8%",
    sortable: true,
    date: true,
  },
];

// Filters
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  id: {
    operator: FilterOperator.AND,
    constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }],
  },
  "name.en": {
    operator: FilterOperator.AND,
    constraints: [{ value: null, matchMode: FilterMatchMode.CONTAINS }],
  },
});

// Update Items
const updateItems = (params = {}) => {
  const attrs = {
    ...(params && { dt_params: params }),
    ...(params?.filters?.global && { searchable_columns: globalFilters }),
  };

  router.visit(
    route("tags.index", {
      ...attrs,
    }),
    {
      preserveState: true,
    }
  );
};

// Delete Tag
const destroy = (id) => {
  confirm.require({
    message: "Are you sure you want to delete this record?",
    header: "Delete Confirmation",
    icon: "pi pi-info-circle",
    accept: () => {
      router.delete(route("tags.destroy", id), {
        onSuccess: () => updateItems(),
      });
    },
  });
};
</script>
