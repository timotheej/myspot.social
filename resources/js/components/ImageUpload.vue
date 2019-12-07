<template>
  <div>
    <div class="absolute flex bottom-0 right-0 my-4 mx-4">
      <button v-show="showUploadPhoto" class="upload-photo" @click="showFilePicker">
        <fa icon="camera"></fa>
      </button>
      <button v-show="disabledDeletePhoto" @click="resetFiles" class="delete-photo">
        <fa icon="trash"></fa>
      </button>
      <button
        v-show="showSubmitPhoto"
        class="submit-photo"
        @click.prevent="onSubmit"
        :disabled="disabledSubmitPhoto"
      >
        <fa icon="upload"></fa>
      </button>
      <input type="file" ref="file" style="display:none;" @change="onChange" multiple />
    </div>
    <div
      v-if="error"
      class="absolute w-full flex justify-center text-sm text-white text-shadow"
      style="bottom: 1em;"
    >{{ $t('an_error') }}</div>
    <div
      v-if="success"
      class="absolute w-full flex justify-center text-sm text-white text-shadow"
      style="bottom: 1em;"
    >{{ $t('photo_added') }}</div>
    <v-upload-progress-bar :progress="progress" v-if="isUploading" :error="error"></v-upload-progress-bar>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "VImageUpload",

  props: ["spotId", "userId"],

  data: () => ({
    files: [],
    progress: 0,
    isUploading: false,
    showUploadPhoto: true,
    showSubmitPhoto: false,
    disabledSubmitPhoto: false,
    disabledDeletePhoto: false,
    error: false,
    success: false
  }),
  methods: {
    showFilePicker() {
      this.$refs.file.click();
    },
    onChange(event) {
      this.files = event.target.files;
      if (this.files != null) {
        this.showSubmitPhoto = true;
        this.showUploadPhoto = false;
        this.disabledDeletePhoto = true;
      }
    },

    onSubmit() {
      this.isUploading = true;
      this.disabledSubmitPhoto = true;
      this.disabledDeletePhoto = false;
      let formData = new FormData();

      for (let file of this.files) {
        formData.append("file_name[]", file, file.name);
      }

      formData.append("user_id", this.userId);
      formData.append("spot_id", this.spotId);

      axios
        .post("api/spot/image", formData, {
          onUploadProgress: e => {
            if (e.lengthComputable) {
              this.progress = (e.loaded / e.total) * 100;
            }
          }
        })
        .then(res => {
          this.success = true;
          setTimeout(() => {
            this.isUploading = false;
            this.disabledSubmitPhoto = false;
            this.showSubmitPhoto = false;
            this.showUploadPhoto = true;
            this.success = false;
          }, 2000);
        })
        .catch(err => {
          this.error = true;
          setTimeout(() => {
            this.isUploading = false;
            this.disabledSubmitPhoto = false;
            this.showSubmitPhoto = false;
            this.showUploadPhoto = true;
            this.error = false;
          }, 5000);
        });
    },
    getFilesName() {
      let filesName = [];
      if (this.files.length > 0) {
        for (let file of this.files) {
          filesName.push(file.name);
        }
      }

      return filesName.join(", ");
    },
    resetFiles() {
      this.files = [];
      this.showSubmitPhoto = false;
      this.showUploadPhoto = true;
      this.disabledDeletePhoto = false;
    }
  }
};
</script>
