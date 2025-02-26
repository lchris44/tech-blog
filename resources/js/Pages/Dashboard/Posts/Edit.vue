<template>
  <v-form-dialog :title="title" width="1500px" @close="dismiss">
    <!-- Action Buttons -->
    <template #buttons>
      <Button
        :loading="form.processing"
        class="button-sm"
        icon="pi pi-save"
        label="Save"
        type="submit"
        @click="save"
      />
    </template>

    <!-- Form Fields -->
    <template #form-fields>
      <div class="grid grid-cols-12 gap-15">
        <!-- Translations Section -->
        <div class="col-span-12 lg:col-span-8">
          <div class="field">
            <Card>
              <template #title>Translations</template>
              <template #content>
                <TabView>
                  <!-- English Tab -->
                  <TabPanel header="English">
                    <div class="field">
                      <v-input-text
                        v-model="form.title.en"
                        label="Title"
                        placeholder="Title"
                        :errors="form.errors['title.en']"
                        required
                      />
                    </div>
                    <div class="field">
                      <label>Content</label>
                      <v-text-editor
                        v-model="form.content.en"
                        :errors="form.errors['content.en']"
                        required
                      />
                    </div>
                  </TabPanel>

                  <!-- Italian Tab -->
                  <TabPanel header="Italian">
                    <div class="field">
                      <v-input-text
                        v-model="form.title.it"
                        label="Title"
                        placeholder="Title"
                        :errors="form.errors['title.it']"
                        required
                      />
                    </div>
                    <div class="field">
                      <label>Content</label>
                      <v-text-editor
                        v-model="form.content.it"
                        :errors="form.errors['content.it']"
                      />
                    </div>
                  </TabPanel>
                </TabView>
              </template>
            </Card>
          </div>
        </div>

        <!-- Basic Information Section -->
        <div class="col-span-12 lg:col-span-4">
          <div class="field">
            <Card>
              <template #title>Basic Information</template>
              <template #content>
                <!-- Tags -->
                <div class="field">
                  <tag-select
                    v-model="form.tags"
                    :options="tags"
                    :errors="form.errors.tags"
                  />
                </div>

                <!-- Short Description -->
                <div class="field">
                  <TabView>
                    <!-- English Tab -->
                    <TabPanel header="English">
                      <v-input-text-area
                        v-model="form.short_description.en"
                        label="Short description"
                        placeholder="Short description"
                        :errors="form.errors['short_description.en']"
                        required
                      />
                    </TabPanel>

                    <!-- Italian Tab -->
                    <TabPanel header="Italian">
                      <v-input-text-area
                        v-model="form.short_description.it"
                        label="Short description"
                        placeholder="Short description"
                        :errors="form.errors['short_description.it']"
                        required
                      />
                    </TabPanel>
                  </TabView>
                </div>

                <!-- Cover Image -->
                <div class="field" v-if="props.post.id">
                  <label>Cover Image</label>
                  <div v-if="form.cover" class="flex">
                    <img
                      :src="form.cover"
                      class="mr-2 shadow-3"
                      rounded
                      size="xlarge"
                      width="200"
                    />
                    <div>
                      <Button
                        class="button-sm"
                        severity="danger"
                        icon="pi pi-trash"
                        label="Delete"
                        @click="deleteCover"
                      />
                    </div>
                  </div>

                  <FileUpload
                    v-else
                    mode="basic"
                    :auto="true"
                    :customUpload="true"
                    chooseLabel="Browse"
                    accept="image/*"
                    @uploader="uploader"
                  />
                </div>
              </template>
            </Card>
          </div>
        </div>
      </div>
    </template>
  </v-form-dialog>
</template>

<script setup>
import { computed } from "vue";
import { useModal } from "/vendor/emargareten/inertia-modal";
import { useForm } from "@inertiajs/vue3";
import TagSelect from "@/components/TagSelect.vue";

// Props
const props = defineProps({
  post: {
    type: Object,
  },
  tags: {
    type: Object,
  },
});

// Computed Properties
const isEdit = computed(() => form.id != null);
const title = computed(() => (isEdit.value ? "Edit Post" : "Create Post"));

// Modal Functions
const { close, redirect } = useModal();
const dismiss = () => {
  close();
  redirect();
};

// Form Handling
const form = useForm(props.post);

// Save Post
const save = () => {
  form.clearErrors();

  if (isEdit.value) {
    form.put(
      route("posts.update", {
        post: props.post.id,
      }),
      {
        onSuccess: () => dismiss(),
      }
    );
  } else {
    form.post(route("posts.store"), {
      onSuccess: () => dismiss(),
    });
  }
};

// Delete Cover Image
const deleteCover = () => {
  form.delete("/posts/" + props.post.id + "/remove-cover", {
    onSuccess: () => (form.cover = null),
  });
};

// Upload Cover Image
const uploader = (files) => {
  const xhr = new XMLHttpRequest();
  xhr.withCredentials = false;

  const url = "/posts/" + props.post.id + "/upload-cover";
  xhr.open("POST", url);

  const token = document.head.querySelector("[name=csrf-token]").content;
  xhr.setRequestHeader("X-CSRF-Token", token);

  xhr.onerror = () => {
    console.log("Image upload failed due to a XHR Transport error. Code: " + xhr.status);
  };

  xhr.onload = () => {
    if (xhr.status < 200 || xhr.status >= 300) {
      console.log("HTTP Error: " + xhr.status);
      return;
    }

    const json = JSON.parse(xhr.responseText);

    if (!json || typeof json.location !== "string") {
      console.log("Invalid JSON: " + xhr.responseText);
      return;
    }

    form.cover = json.location;
  };

  const formData = new FormData();
  formData.append("file", files.files[0], files.files[0].name);
  xhr.send(formData);
};
</script>
