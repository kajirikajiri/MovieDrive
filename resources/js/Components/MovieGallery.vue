<template>
  <div>
    <div v-for="file in files" :key="file.link">
      <div v-if="file.extension == 'mp4'">
        <!-- プレビュー -->
        <video :src="file.link" @click="contentSelect"></video>
        <!-- サムネイル -->
        <div v-if="file.hasThumbnail">
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
        </div>
        <div v-else>
          <p>サムネイル作成中</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    files: null,
  },
  methods: {
    contentSelect: function () {},
  },
};
</script>
