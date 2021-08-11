<style>
img {
  transition-duration: 0.3s;
}

img:hover {
  transform: scale(1.1);
}

.logo {
  font-size: 24px;
}

.profile-icon {
  height: 32px;
  width: 32px;
}

.icon {
  height: 24px;
  width: 24px;
}

.icon-round {
  border-radius: 20px;
  border-color: black;
  border: solid;
  border-width: 1px;
}

.progress {
  height: 10px;
  border-radius: 10px;
}

.progress-bar {
  height: 10px;
  background-color: gainsboro;
  border-radius: 10px;
}

.swiper-custom-parent {
  width: 100%;
  position: relative;
}

.swiper-container {
  width: 85%;
  height: 220px;
  padding: 10px;
}

.swiper-button-next,
.swiper-button-prev {
  backdrop-filter: blur(3px);
  margin: 100;
}

.modal {
  background-color: black;
}

video {
  height: 50%;
  width: 50%;
  margin-left: auto;
  margin-right: auto;
  display: block;
}

.batsu {
  font-size: 150%;
  font-weight: bold;
  border: 1px solid #999;
  color: #999;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 100%;
  width: 1.3em;
  line-height: 1.3em;
  cursor: pointer;
  transition: 0.2s;
}

.batsu:hover {
  background: #333;
  border-color: #333;
  color: #fff;
}
</style>

<template>
  <app-layout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

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
            <div style="font-size: 16px">
              <span>保存容量</span>
              <span>　</span>
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
                        name="files"
                        @change="uploadFiles"
                        required
                        multiple
                        hidden
                      />
                      <input
                        type="file"
                        id="folder-selecter"
                        name="folder-files"
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
            <div>
              <div v-for="file in files" :key="file.name">
                <p>{{ file.name }}</p>
                <div class="swiper-custom-parent">
                  <div class="swiper-container mySwiper">
                    <div class="swiper-wrapper">
                      <img src="" class="swiper-slide js-modal-open" />
                      <img src="" class="swiper-slide js-modal-open" />
                      <img src="" class="swiper-slide js-modal-open" />
                      <img src="" class="swiper-slide js-modal-open" />
                    </div>
                    <!-- 前ページボタン -->
                    <div class="swiper-button-prev"></div>
                    <!-- 次ページボタン -->
                    <div class="swiper-button-next"></div>
                    <!-- スクロールバー -->
                    <div class="swiper-scrollbar"></div>
                  </div>
                </div>
                <hr />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <VideoPlayer :source="selectedVideoSource" />
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import VideoPlayer from "@/Components/VideoPlayer.vue";
import TreeViewer from "@/Components/TreeViewer.vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default {
  components: {
    AppLayout,
    VideoPlayer,
    TreeViewer,
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
      selectedVideoSource: "C:Usersm-tanakaworkmoviemov_hts-samp001.mp4",
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
  },
};
</script>
