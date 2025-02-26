<template>
  <div class="flex justify-between">
    <!-- Label for the tag selection -->
    <label for="label" v-if="showLabel"> Tags <span class="text-red">*</span> </label>

    <!-- Button to open the create new tag panel -->
    <Button
      class="p-button-sm p-button-primary width-auto"
      icon="pi pi-plus"
      label="Add New"
      text
      @click="showPanel = !showPanel"
    />
  </div>

  <!-- MultiSelect dropdown for selecting tags -->
  <MultiSelect
    v-model="model"
    :options="options"
    dataKey="id"
    optionLabel="name.en"
    :maxSelectedLabels="3"
    :class="inputClasses"
    placeholder="Select Tags"
    display="chip"
    filter
  />

  <!-- Display validation errors -->
  <v-errors v-if="errors" :errors="errors"></v-errors>

  <!-- Panel for creating a new tag -->
  <div v-if="showPanel" class="bg-light">
    <v-form-dialog title="Create tag" @close="showPanel = false">
      <template v-slot:buttons>
        <Button
          :loading="form.processing"
          class="p-button-sm"
          icon="pi pi-save"
          label="Save"
          type="submit"
          @click="saveNewTag"
        />
      </template>
      <template v-slot:form-fields>
        <TabView>
          <!-- English tab -->
          <TabPanel header="English">
            <div class="field">
              <v-input-text
                v-model="form.name.en"
                label="Name"
                placeholder="Name"
                :errors="form.errors['name.en']"
                required
              />
            </div>
          </TabPanel>
          <!-- Italian tab -->
          <TabPanel header="Italian">
            <div class="field">
              <v-input-text
                v-model="form.name.it"
                label="Name"
                placeholder="Name"
                :errors="form.errors['name.it']"
                required
              />
            </div>
          </TabPanel>
        </TabView>
      </template>
    </v-form-dialog>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
  modelValue: Object,
  options: Object,
  loading: {
    type: Boolean,
    default: false,
  },
  showLabel: {
    type: Boolean,
    default: true,
  },
  placeholder: {
    type: String,
  },
  errors: Object,
  required: {
    type: Boolean,
    default: false,
  },
});

// Computed property to handle model updates
const model = computed({
  get() {
    return props.modelValue;
  },
  set(val) {
    emit("update:modelValue", val);
  },
});

const showPanel = ref(false); // Controls visibility of the new tag form

// Form for creating a new tag
const form = useForm({
  name: {
    en: "",
    it: "",
  },
});

// Function to save a new tag
const saveNewTag = () => {
  form.post(route("tags.store"), {
    onSuccess: () => {
      showPanel.value = false;
      // Append the newly created tag to the model
      model.value = [
        {
          id: props.options.at(-1).id,
          name: form.name.en,
        },
      ];
      form.reset(); // Reset the form fields after submission
    },
  });
};

// Computed classes for input validation
const inputClasses = computed(() => {
  return ["w-full mb-3", { "p-invalid": props.errors !== undefined }];
});
</script>

<style lang="scss">
.panel-class {
  z-index: 10000 !important;
}
</style>
