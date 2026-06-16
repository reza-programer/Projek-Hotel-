<template>
  <div class="skeleton" :class="variant" :style="{ width, height }">
    <div class="shimmer"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'text', 'circle', 'card', 'image', 'button'].includes(value)
  },
  width: {
    type: String,
    default: '100%'
  },
  height: {
    type: String,
    default: '20px'
  }
})
</script>

<style scoped>
.skeleton {
  position: relative;
  overflow: hidden;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  border-radius: 4px;
}

.skeleton.text {
  height: 16px;
  border-radius: 2px;
}

.skeleton.circle {
  border-radius: 50%;
}

.skeleton.card {
  border-radius: 8px;
  height: 200px;
}

.skeleton.image {
  border-radius: 8px;
  height: 200px;
}

.skeleton.button {
  height: 40px;
  border-radius: 4px;
  width: 120px;
}

.shimmer {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent 0%,
    rgba(255, 255, 255, 0.4) 50%,
    transparent 100%
  );
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% {
    transform: translateX(-100%);
  }
  100% {
    transform: translateX(100%);
  }
}
</style>
