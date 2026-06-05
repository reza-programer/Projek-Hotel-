<template>
  <Transition name="toast-slide">
    <div v-if="show" class="miyabi-toast card-shoji" :class="type" @mouseenter="pauseTimer" @mouseleave="resumeTimer">
      <!-- Accent Line Left -->
      <div class="toast-accent-line"></div>
      
      <!-- Icon Wrapper -->
      <div class="toast-icon-area">
        <svg v-if="type === 'success'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <svg v-else-if="type === 'error'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="16" x2="12" y2="12"></line>
          <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
      </div>

      <!-- Text Content -->
      <div class="toast-text-area">
        <div class="toast-title">{{ titleText }}</div>
        <div class="toast-message">{{ message }}</div>
      </div>

      <!-- Close Button -->
      <button class="toast-close-btn" @click="close">✕</button>

      <!-- Progress bar tracker -->
      <div class="toast-progress-bar">
        <div class="toast-progress-fill" :style="{ width: `${progress}%` }"></div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const show = ref(false)
const message = ref('')
const type = ref('success') // 'success' | 'error' | 'info'
const progress = ref(100)

let duration = 4000 // 4 seconds
let startTime = 0
let remainingTime = duration
let timerId = null
let animationFrameId = null

const titleText = computed(() => {
  if (type.value === 'success') return 'Sukses'
  if (type.value === 'error') return 'Peringatan'
  return 'Informasi'
})

const startProgress = () => {
  startTime = Date.now()
  const updateProgress = () => {
    const elapsed = Date.now() - startTime
    progress.value = Math.max(0, ((remainingTime - elapsed) / duration) * 100)
    
    if (elapsed < remainingTime) {
      animationFrameId = requestAnimationFrame(updateProgress)
    } else {
      close()
    }
  }
  animationFrameId = requestAnimationFrame(updateProgress)
}

const pauseTimer = () => {
  cancelAnimationFrame(animationFrameId)
  const elapsed = Date.now() - startTime
  remainingTime = Math.max(0, remainingTime - elapsed)
}

const resumeTimer = () => {
  if (remainingTime > 0) {
    startProgress()
  }
}

const close = () => {
  show.value = false
  cancelAnimationFrame(animationFrameId)
}

const handleCustomToast = (e) => {
  if (e.detail) {
    message.value = e.detail.message
    type.value = e.detail.type || 'success'
    show.value = true
    remainingTime = duration
    cancelAnimationFrame(animationFrameId)
    startProgress()
  }
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    const storedFlash = sessionStorage.getItem('miyabi_flash')
    if (storedFlash) {
      try {
        const parsed = JSON.parse(storedFlash)
        message.value = parsed.message
        type.value = parsed.type || 'success'
        show.value = true
        sessionStorage.removeItem('miyabi_flash')
        startProgress()
      } catch (e) {
        console.error('Failed to parse flash message', e)
      }
    }

    window.addEventListener('miyabi-show-toast', handleCustomToast)
  }
})

onUnmounted(() => {
  cancelAnimationFrame(animationFrameId)
  if (typeof window !== 'undefined') {
    window.removeEventListener('miyabi-show-toast', handleCustomToast)
  }
})
</script>

<style scoped>
.miyabi-toast {
  position: fixed;
  top: 2rem;
  right: 2rem;
  z-index: 9999;
  width: 350px;
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  padding: 1.1rem 2.5rem 1.1rem 1.25rem;
  border-radius: 4px;
  background: rgba(253, 250, 245, 0.98);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(201, 168, 76, 0.25);
  box-shadow: 0 10px 30px rgba(26, 26, 46, 0.12);
  overflow: hidden;
  font-family: 'Inter', sans-serif;
  user-select: none;
}
.toast-accent-line {
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
}
.toast-icon-area {
  flex-shrink: 0;
  margin-top: 2px;
}
.toast-text-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.toast-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-sumi-800);
}
.toast-message {
  font-size: 0.8rem;
  color: var(--color-sumi-600);
  line-height: 1.45;
}
.toast-close-btn {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  background: transparent;
  border: none;
  font-size: 0.75rem;
  color: var(--color-sumi-500);
  cursor: pointer;
  padding: 0.25rem;
  transition: 0.2s;
}
.toast-close-btn:hover {
  color: var(--color-beni-600);
}

/* Accent types */
.miyabi-toast.success .toast-accent-line {
  background: var(--color-matcha-500);
}
.miyabi-toast.success .toast-icon-area {
  color: var(--color-matcha-500);
}

.miyabi-toast.error .toast-accent-line {
  background: var(--color-beni-500);
}
.miyabi-toast.error .toast-icon-area {
  color: var(--color-beni-500);
}

.miyabi-toast.info .toast-accent-line {
  background: var(--color-kin-500);
}
.miyabi-toast.info .toast-icon-area {
  color: var(--color-kin-500);
}

/* Progress bar tracking */
.toast-progress-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: rgba(201, 168, 76, 0.1);
}
.toast-progress-fill {
  height: 100%;
  transition: width 0.05s linear;
}
.success .toast-progress-fill {
  background: var(--color-matcha-500);
}
.error .toast-progress-fill {
  background: var(--color-beni-500);
}
.info .toast-progress-fill {
  background: var(--color-kin-500);
}

/* Slide transition */
.toast-slide-enter-active {
  transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s;
}
.toast-slide-leave-active {
  transition: transform 0.3s ease, opacity 0.25s;
}
.toast-slide-enter-from {
  transform: translate3d(120%, 0, 0);
  opacity: 0;
}
.toast-slide-leave-to {
  transform: translate3d(50px, -20px, 0) scale(0.9);
  opacity: 0;
}

@media(max-width: 480px) {
  .miyabi-toast {
    top: 1rem;
    right: 1rem;
    left: 1rem;
    width: auto;
  }
}
</style>
