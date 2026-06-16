<template>
  <div class="lazy-image-container" :style="{ width, height }">
    <Skeleton 
      v-if="!isLoaded" 
      variant="image" 
      :width="width" 
      :height="height"
    />
    <img
      v-show="isLoaded"
      :src="src"
      :alt="alt"
      :class="imageClass"
      @load="onLoad"
      @error="onError"
      loading="lazy"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Skeleton from './Skeleton.vue'

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: ''
  },
  width: {
    type: String,
    default: '100%'
  },
  height: {
    type: String,
    default: 'auto'
  },
  imageClass: {
    type: String,
    default: ''
  }
})

const isLoaded = ref(false)

const onLoad = () => {
  isLoaded.value = true
}

const onError = () => {
  isLoaded.value = false
}
</script>

<style scoped>
.lazy-image-container {
  position: relative;
  overflow: hidden;
}

.lazy-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: opacity 0.3s ease;
}
</style>
