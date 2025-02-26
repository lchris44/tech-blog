<template>
  <!-- Main container with grid layout -->
  <div class="p-grid">
    <div class="p-col-12 p-p-5">
      <div class="card p-shadow-1">
        <!-- Toolbar section with optional header buttons and search input -->
        <Toolbar v-if="showToolbar">
          <template #start>
            <slot name="header-buttons"></slot>
          </template>

          <template #end>
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText
                v-model="dataFilters['global'].value"
                @update:modelValue="onFilter()"
                placeholder="Keyword Search"
              />
            </IconField>
          </template>
        </Toolbar>

        <!-- Data table for displaying records -->
        <DataTable
          ref="dt"
          dataKey="id"
          class="p-datatable-sm"
          :sortField="'id'"
          :sortOrder="-1"
          v-model:selection="selected"
          v-model:filters="dataFilters"
          v-model:expandedRows="expandedRows"
          :rows="10"
          :lazy="true"
          :value="items.data"
          :paginator="paginator"
          :totalRecords="items.total"
          :rowsPerPageOptions="[10, 25, 50, 100]"
          :globalFilterFields="globalFilters"
          breakpoint="960px"
          filterDisplay="menu"
          responsiveLayout="stack"
          currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
          paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
          @page="onPage($event)"
          @sort="onSort($event)"
          @filter="onFilter($event)"
        >
          <!-- Display message when no data is found -->
          <template v-if="!loading" #empty>
            <strong class="p-p-3">
              <i class="pi pi-exclamation-circle p-mr-2" style="font-weight: 600"></i>
              No data found.
            </strong>
          </template>

          <!-- Expandable row option -->
          <Column v-if="expandable" :expander="true" headerStyle="width: 1%" />

          <!-- Selectable row option -->
          <Column v-if="selectable" selectionMode="multiple" headerStyle="width: 1%" />

          <!-- Dynamic columns based on provided configuration -->
          <Column
            v-for="col of columns"
            :field="col.field"
            :header="col.header"
            :key="col.field"
            :dataType="col.dataType"
            :headerStyle="col.headerStyle"
            :sortable="col.sortable"
          >
            <template v-if="!col.field.includes('.')" #body="{ data }">
              <slot :name="`record.${col.field}`" :record="data[col.field]" :data="data">
                <template v-if="Array.isArray(data[col.field]) && data[col.field].length">
                  {{ data[col.field] }}
                </template>
                <template v-else-if="!Array.isArray(data[col.field])">
                  {{ data[col.field] }}
                </template>
              </slot>
            </template>

            <!-- Filter component if column has a filter -->
            <template v-if="col.filter" #filter="{ filterModel }">
              <component
                :is="col.filter.component"
                v-bind="col.filter.properties"
                v-model="filterModel.value"
              />
            </template>
          </Column>

          <!-- Actions column for each row -->
          <Column v-if="showActionBtns" header="Actions" headerStyle="width:15%">
            <template #body="{ data }">
              <Button
                icon="pi pi-ellipsis-v"
                class="p-button-text p-button-plain p-button-rounded"
                @click="$refs[`panel-${data.id}`].toggle($event)"
              />
              <Popover :id="`panel-${data.id}`" :ref="`panel-${data.id}`" class="p-0">
                <div class="menu p-datatable">
                  <ul class="p-menu-list p-reset" role="menu">
                    <slot name="data-buttons" :record="data"></slot>
                  </ul>
                </div>
              </Popover>
            </template>
          </Column>

          <!-- Expansion slot for additional details -->
          <template #expansion="{ data }">
            <slot name="expand-body" :record="data"></slot>
          </template>
        </DataTable>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const emit = defineEmits(["update:items", "update:selected"]);

const loading = ref(false);

const props = defineProps({
  items: Object,
  columns: Object,
  globalFilters: Array,
  filters: Object,
  expandable: {
    type: Boolean,
    default: false,
  },
  selectable: {
    type: Boolean,
    default: false,
  },
  expandBody: String,
  showToolbar: {
    type: Boolean,
    default: true,
  },
  showActionBtns: {
    type: Boolean,
    default: true,
  },
  paginator: {
    type: Boolean,
    default: true,
  },
});

const params = ref({});
const selected = ref([]);
const expandedRows = ref([]);
const dataFilters = ref(props.filters);

watch(selected, () => {
  emit("update:selected", selected.value);
});

// Handles pagination events
const onPage = (event) => {
  emit("update:items", event);
};

// Handles sorting events
const onSort = (event) => {
  emit("update:items", {
    filters: event.filters,
    sortField: event.sortField,
    sortOrder: event.sortOrder,
  });
};

// Handles filtering logic
const onFilter = () => {
  params.value.filters = Object.entries(dataFilters.value)
    .filter(
      ([key, value]) =>
        (key == "global" && value.value) ||
        (value.constraints &&
          value.constraints.some((constraint) => constraint.value !== null))
    )
    .reduce((acc, [key, value]) => {
      acc[key] = value;
      return acc;
    }, {});

  emit("update:items", params.value);
};
</script>

<style scoped lang="scss">
::v-deep(.warning) {
  background-color: rgba(0, 0, 0, 0.15) !important;
}
::v-deep(.p-column-title) {
  font-weight: bold;
}
::v-deep(.p-menuitem-link) {
  cursor: pointer;
  display: flex;
  align-items: center;
  text-decoration: none;
  overflow: hidden;
  position: relative;
  color: unset;
  padding: 10px;
}
::v-deep(.p-menuitem) {
  :hover {
    background: opacity(0.2);
  }
}
</style>
