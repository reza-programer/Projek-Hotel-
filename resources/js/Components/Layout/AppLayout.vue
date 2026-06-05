<template>
  <div class="app-layout">
    <!-- Floating sakura petals -->
    <div class="petal-container" aria-hidden="true">
      <div v-for="p in petals" :key="p.id" class="petal"
        :style="{ left: p.left, animationDuration: p.duration, animationDelay: p.delay, width: p.size, height: p.size, opacity: p.opacity }"/>
    </div>

    <Navbar />

    <main class="main-content">
      <slot />
    </main>

    <Footer />
    <Toast />
    <ChatWidget />
  </div>
</template>

<script setup>
import Navbar from './Navbar.vue'
import Footer from './Footer.vue'
import Toast from '../UI/Toast.vue'
import ChatWidget from '../UI/ChatWidget.vue'

const petals = Array.from({ length: 8 }, (_, i) => ({
  id: i,
  left: `${8 + i * 12}%`,
  duration: `${10 + Math.random() * 8}s`,
  delay: `${i * 1.5}s`,
  size: `${8 + Math.random() * 8}px`,
  opacity: (0.3 + Math.random() * 0.4).toFixed(2),
}))
</script>

<style scoped>
.app-layout { position:relative; min-height:100vh; display:flex; flex-direction:column; }
.petal-container { position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:1; overflow:hidden; }
.main-content { flex:1; }
</style>
