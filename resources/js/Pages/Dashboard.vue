<template>
  <app-layout title="Dashboard">
    <div class="container">
      <div class="row pt-5">
        <div class="border-right col-md-4 col-lg-2 p-4">
          <div class="mt-3 mb-3 ml-4 mr-4">
            <div class="progress">
              <div
                class="progress-bar"
                role="progressbar"
                v-bind:class="progressClasses"
                v-bind:style="{ width: progressWidth }"
                v-bind:aria-valuenow="progressValue"
                aria-valuemin="0"
                aria-valuemax="100"
              ></div>
            </div>
            <div style="font-size: 16px" class="text-center">
              <span>
                {{ progress.volumeSize }}GB中
                {{ progress.usedVolumeSize }}GB使用中
              </span>
            </div>
            <div class="pt-3 pb-3 text-center">
              <button class="btn btn-primary">保存容量をアップグレード</button>
            </div>
          </div>
          <TreeViewer :node="folderTree" />
        </div>
        <div class="col-md-8 col-lg-10 p-4">
          <div class="container">
            <div class="row">
              <div class="col">
                <div class="row">
                  <div class="col-4">
                    <input type="text" class="form-control" />
                  </div>
                  <div
                    class="col-3 align-middle"
                    style="display: flex; align-items: center"
                  >
                    <i class="fas fa-search h4 m-1"></i>
                    <i class="fas fa-filter h4 m-1"></i>
                  </div>
                  <div
                    class="col-5 text-right"
                    style="display: flex; align-items: center"
                  >
                    <div class="dropdown">
                      <button
                        class="btn btn-outline-dark dropdown-toggle"
                        id="dropdownMenu"
                        data-toggle="dropdown"
                      >
                        <i class="fas fa-plus"></i>アップロード
                      </button>
                      <div class="dropdown-menu">
                        <button class="dropdown-item" @click="selectUploadFile">
                          <i class="fas fa-file-upload"></i>
                          ファイルをアップロード
                        </button>
                        <button
                          class="dropdown-item"
                          @click="selectUploadFolder"
                        >
                          <i class="fas fa-upload"></i>
                          フォルダをアップロード
                        </button>
                      </div>
                      <input
                        type="file"
                        id="file-selecter"
                        @change="uploadFiles"
                        required
                        multiple
                        hidden
                      />
                      <input
                        type="file"
                        id="folder-selecter"
                        webkitdirectory
                        @change="uploadFiles"
                        required
                        hidden
                      />
                    </div>
                    <i class="fas fa-trash h4 m-1"></i>
                    <i class="fas fa-cog h4 m-1"></i>
                  </div>
                </div>
              </div>
            </div>
            <hr />
            <ul class="nav nav-tabs">
              <li class="nav-item">
                <div
                  href=""
                  class="nav-link active"
                  @click="galleryType = 'movie-gallery'"
                >
                  <i class="fas fa-video"></i>
                  Movie
                </div>
              </li>
              <li class="nav-item">
                <div
                  href=""
                  class="nav-link"
                  @click="galleryType = 'picture-gallery'"
                >
                  <i class="far fa-image"></i>
                  Picture
                </div>
              </li>
              <li class="nav-item">
                <a href="" class="nav-link">
                  <i class="far fa-file"></i>
                  Other
                </a>
              </li>
            </ul>
            <component :is="galleryType" :files="files" />
          </div>
        </div>
      </div>
    </div>
    <VideoPlayer source="" />
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import VideoPlayer from "@/Components/VideoPlayer.vue";
import TreeViewer from "@/Components/TreeViewer.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import PictureGallery from "@/Components/PictureGallery.vue";
import MovieGallery from "@/Components/MovieGallery.vue";

export default {
  components: {
    AppLayout,
    VideoPlayer,
    TreeViewer,
    PictureGallery,
    MovieGallery,
  },
  props: {
    progress: {
      usedVolumeSize: Number,
      volumeSize: Number,
    },
    folderTree: null,
    files: null,
    postUri: String,
  },
  data: function () {
    return {
      galleryType: "movie-gallery",
    };
  },
  setup() {
    const form = useForm({
      files: null,
      postUri: String,
    });
    return { form };
  },
  computed: {
    progressValue: function () {
      return (this.progress.usedVolumeSize / this.progress.volumeSize) * 100;
    },
    progressWidth: function () {
      return this.progressValue + "%";
    },
    progressClasses: function () {
      const p = this.progressValue;
      if (p >= 95) {
        return "bg-danger";
      } else if (p >= 70) {
        return "bg-warning";
      } else {
        return "bg-success";
      }
    },
  },
  mounted: function () {
    this.printDebugLog();
    new Swiper(".swiper-container", {
      watchSlidesProgress: true,
      watchSlidesVisibility: true,
      slidesPerView: 3,
      scrollbar: {
        el: ".swiper-scrollbar",
        draggable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });

    // modal window
    $(function () {
      $(".js-modal-open").on("click", function () {
        $(".js-modal").fadeIn();
        return false;
      });
      $(".js-modal-close").on("click", function () {
        $(".js-modal").fadeOut();
        return false;
      });
    });
  },
  updated: function () {
    this.printDebugLog();
  },
  methods: {
    selectUploadFile: function (e) {
      document.getElementById("file-selecter").click();
    },
    selectUploadFolder: function (e) {
      document.getElementById("folder-selecter").click();
    },
    uploadFiles: function (e) {
      if (e.target.files.length > 0) {
        this.form
          .transform((data) => ({
            ...data,
            postUri: this.postUri,
            files: this.convertFiles(e.target.files),
          }))
          .post(
            route("dashboard.upload_files", {
              replace: true,
              preserveState: true,
              preserveScroll: true,
            })
          );
      }
      e.target.value = "";
    },
    convertFiles: function (args) {
      let files = [];
      for (let i = 0; i < args.length; i++) {
        let file = {
          file: args[i],
          relativePath: args[i].webkitRelativePath,
          name: args[i].name,
          bytes: args[i].size,
          type: args[i].type,
        };
        files.push(file);
      }
      return files;
    },
    printDebugLog: function () {
      console.debug(`folderTree root: ${this.folderTree.text}`);
      console.debug(`files: ${this.files.length}件`);
      console.debug(`postUri: ${this.postUri}`);
      console.debug(`galleryType: ${this.galleryType}`);
    },
  },
};
</script>
