<template>
  <v-form-dialog :title="title" @close="dismiss">
    <!-- Action Buttons -->
    <template #buttons>
      <Button
        :loading="form.processing"
        class="p-button-sm"
        icon="pi pi-save"
        label="Save"
        type="submit"
        @click="save"
      />
    </template>

    <!-- Form Fields -->
    <template #form-fields>
      <div class="p-fluid formgrid grid">
        <div class="field col-12">
          <TabView>
            <!-- English Tab -->
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

            <!-- Italian Tab -->
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
        </div>
      </div>
    </template>
  </v-form-dialog>
</template>

<script setup>
import { computed } from "vue";
import { useModal } from "/vendor/emargareten/inertia-modal";
import { useForm } from "@inertiajs/vue3";

// Props
const props = defineProps({
  tag: {
    type: Object,
  },
});

// Computed Properties
const isEdit = computed(() => form.id != null);
const title = computed(() => (isEdit.value ? "Edit Tag" : "Create Tag"));

// Modal Functions
const { close, redirect } = useModal();
const dismiss = () => {
  close();
  redirect();
};

// Form Handling
const form = useForm(props.tag);

// Save Tag
const save = () => {
  form.clearErrors();

  if (isEdit.value) {
    form.put(
      route("tags.update", {
        tag: props.tag.id,
      }),
      {
        onSuccess: () => dismiss(),
      }
    );
  } else {
    form.post(route("tags.store"), {
      onSuccess: () => dismiss(),
    });
  }
};
</script>
