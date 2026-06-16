<template>
  <div class="auth-page pattern-washi">
    <!-- Floating sakura petals -->
    <div class="petal-container" aria-hidden="true">
      <div v-for="p in petals" :key="p.id" class="petal"
        :style="{ left: p.left, animationDuration: p.duration, animationDelay: p.delay, width: p.size, height: p.size, opacity: p.opacity }"/>
    </div>

    <div class="auth-container">
      <div class="auth-card card-shoji">
        <!-- Logo Header -->
        <div class="auth-header">
          <span class="auth-kanji">阿</span>
          <h1 class="auth-title">Reset Sandi</h1>
          <p class="auth-subtitle">Buat kata sandi baru untuk akun Anda.</p>
        </div>

        <div class="divider-ink" style="margin: 1.25rem 0;"></div>

        <!-- Alert Success/Error -->
        <div v-if="successMessage" class="alert-success" style="display:flex; align-items:center; gap:0.4rem;">
          <SvgIcon name="check" width="14" height="14" /> {{ successMessage }}
        </div>

        <div v-if="errorMessage" class="alert-error" style="display:flex; align-items:center; gap:0.4rem;">
          <SvgIcon name="warning" width="14" height="14" /> {{ errorMessage }}
        </div>

        <!-- Form -->
        <form v-if="!successMessage" @submit.prevent="handleSubmit" class="auth-form">
          <div class="form-group">
            <label class="label-zen">EMAIL</label>
            <input 
              v-model="form.email" 
              type="email" 
              class="input-zen" 
              placeholder="budi@email.com" 
              required 
            />
          </div>

          <div class="form-group">
            <label class="label-zen">KATA SANDI BARU</label>
            <input 
              v-model="form.password" 
              type="password" 
              class="input-zen" 
              placeholder="••••••••" 
              required 
            />
          </div>

          <div class="form-group">
            <label class="label-zen">KONFIRMASI KATA SANDI</label>
            <input 
              v-model="form.password_confirmation" 
              type="password" 
              class="input-zen" 
              placeholder="••••••••" 
              required 
            />
          </div>

          <button type="submit" class="btn-vermillion w-full" :disabled="loading">
            {{ loading ? 'Memproses...' : 'Reset Sandi' }}
          </button>
        </form>

        <!-- Back to Login -->
        <div class="auth-footer">
          <Link href="/login" class="switch-link">← Kembali ke Login</Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import SvgIcon from '@/Components/UI/SvgIcon.vue'

const props = defineProps({
  email: String,
  token: String,
})

const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: ''
})

// Floating petals background
const petals = Array.from({ length: 10 }, (_, i) => ({
  id: i,
  left: `${5 + i * 10}%`,
  duration: `${8 + Math.random() * 8}s`,
  delay: `${i * 1.2}s`,
  size: `${6 + Math.random() * 8}px`,
  opacity: (0.3 + Math.random() * 0.4).toFixed(2),
}))

const handleSubmit = () => {
  loading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  form.post('/reset-password', {
    onSuccess: () => {
      successMessage.value = 'Kata sandi berhasil direset. Anda akan dialihkan ke halaman login.'
      loading.value = false
      setTimeout(() => {
        window.location.href = '/login'
      }, 2000)
    },
    onError: (errors) => {
      errorMessage.value = errors.email || errors.password || 'Terjadi kesalahan. Silakan coba lagi.'
      loading.value = false
    }
  })
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  position: relative;
  overflow: hidden;
  padding: 2rem 1rem;
}

.petal-container {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  pointer-events: none; z-index: 1; overflow: hidden;
}

.auth-container {
  width: 100%;
  max-width: 440px;
  position: relative;
  z-index: 5;
}

.auth-card {
  padding: 2.5rem;
  border-radius: 4px;
}

.auth-header {
  text-align: center;
}
.auth-kanji {
  font-family: 'Noto Serif JP', serif;
  font-size: 2.6rem;
  font-weight: 700;
  color: var(--color-beni-600);
  line-height: 1;
}
.auth-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.8rem;
  font-weight: 600;
  color: var(--color-sumi-800);
  margin-top: 0.5rem;
}
.auth-subtitle {
  font-size: 0.8rem;
  color: var(--color-sumi-600);
  font-family: 'Inter', sans-serif;
  margin-top: 0.25rem;
  line-height: 1.5;
}

.auth-form {
  margin-top: 1.5rem;
}
.form-group {
  margin-bottom: 1.25rem;
  display: flex;
  flex-direction: column;
}

.w-full {
  width: 100%;
  justify-content: center;
}

.alert-success {
  background: #E8F5E9;
  border: 1px solid rgba(76, 175, 80, 0.2);
  color: #2E7D32;
  padding: 0.75rem 1rem;
  font-family: 'Inter', sans-serif;
  font-size: 0.78rem;
  border-radius: 2px;
  margin-bottom: 1rem;
}

.alert-error {
  background: #FFEBEB;
  border: 1px solid rgba(155, 35, 53, 0.2);
  color: var(--color-beni-700);
  padding: 0.75rem 1rem;
  font-family: 'Inter', sans-serif;
  font-size: 0.78rem;
  border-radius: 2px;
  margin-bottom: 1rem;
}

.auth-footer {
  text-align: center;
  margin-top: 1.5rem;
  font-family: 'Inter', sans-serif;
  font-size: 0.82rem;
  color: var(--color-sumi-600);
}
.switch-link {
  color: var(--color-beni-600);
  text-decoration: none;
  font-weight: 600;
}
.switch-link:hover {
  text-decoration: underline;
}
</style>
