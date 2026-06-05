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
          <h1 class="auth-title">Selamat Datang</h1>
          <p class="auth-subtitle">Masuk ke akun Arunika Anda untuk menikmati pelayanan Omotenashi.</p>
        </div>

        <div class="divider-ink" style="margin: 1.25rem 0;"></div>

        <!-- Alert Error -->
        <div v-if="errorMessage" class="alert-error" style="display:flex; align-items:center; gap:0.4rem;">
          <SvgIcon name="warning" width="14" height="14" /> {{ errorMessage }}
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="auth-form">
          <div class="form-group">
            <label class="label-zen">ALAMAT EMAIL</label>
            <input 
              v-model="form.email" 
              type="email" 
              class="input-zen" 
              placeholder="budi@email.com atau admin@arunikaryoka.com" 
              required 
            />
          </div>

          <div class="form-group">
            <label class="label-zen">KATA SANDI</label>
            <input 
              v-model="form.password" 
              type="password" 
              class="input-zen" 
              placeholder="••••••••" 
              required 
            />
          </div>

          <div class="form-actions-row">
            <label class="remember-me">
              <input v-model="form.remember" type="checkbox" />
              <span>Ingat Saya</span>
            </label>
            <a href="#" class="forgot-link" @click.prevent="forgotPassword">Lupa Sandi?</a>
          </div>

          <button type="submit" class="btn-vermillion w-full" :disabled="loading">
            {{ loading ? 'Memproses...' : 'Masuk Ke Akun' }}
          </button>

          <div class="social-login-divider">
            <span class="sld-text">atau masuk dengan</span>
          </div>

          <a href="/auth/google/redirect" class="btn-google-login">
            <span class="google-icon-wrapper"><SvgIcon name="google" width="18" height="18" /></span>
            <span class="google-btn-text">Masuk dengan Google</span>
          </a>
        </form>

        <!-- Form Switcher -->
        <div class="auth-footer">
          Belum memiliki akun? 
          <Link href="/register" class="switch-link">Daftar Baru</Link>
        </div>
      </div>
    </div>

    <!-- Centered Fullscreen Loading Logo overlay -->
    <Transition name="fade">
      <div v-if="loginSuccess" class="fullscreen-loading">
        <div class="loading-content">
          <div class="loading-logo-kanji">阿</div>
          <div class="loading-logo-text">ARUNIKA RYOKA</div>
          <div class="spinner"></div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import SvgIcon from '@/Components/UI/SvgIcon.vue'
import { users } from '@/data/mockData'

const loading = ref(false)
const errorMessage = ref('')
const loginSuccess = ref(false)

const form = ref({
  email: '',
  password: '',
  remember: false
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

const handleLogin = () => {
  loading.value = true
  errorMessage.value = ''

  setTimeout(() => {
    // Search mock credentials
    const foundUser = users.find(u => u.email.toLowerCase() === form.value.email.toLowerCase())
    
    if (foundUser) {
      if (foundUser.status === 'inactive') {
        errorMessage.value = 'Akun Anda sedang dinonaktifkan (suspended). Hubungi admin.'
        loading.value = false
        return
      }

      // Save user login state in localstorage
      localStorage.setItem('miyabi_user', JSON.stringify({
        id: foundUser.id,
        name: foundUser.name,
        email: foundUser.email,
        role: foundUser.role
      }))

      // Save toast message in session storage
      sessionStorage.setItem('miyabi_flash', JSON.stringify({
        message: `Selamat datang kembali, ${foundUser.name}!`,
        type: 'success'
      }))

      // Show loading screen and delay redirect
      loginSuccess.value = true
      setTimeout(() => {
        if (foundUser.role === 'admin') {
          window.location.href = '/admin/dashboard'
        } else {
          window.location.href = '/'
        }
      }, 1500)
    } else {
      errorMessage.value = 'Email tidak terdaftar atau kata sandi salah.'
      loading.value = false
    }
  }, 1000)
}

const forgotPassword = () => {
  alert('Simulasi reset kata sandi dikirim ke email Anda.')
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

.back-home-wrapper {
  display: flex;
  justify-content: center;
  margin-bottom: 1.25rem;
  width: 100%;
}

.back-home-link {
  font-family: 'Noto Serif JP', serif;
  font-size: 0.8rem;
  color: var(--color-sumi-700);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.45rem 1.25rem;
  background: rgba(253, 250, 245, 0.85);
  border: 1px solid rgba(201, 168, 76, 0.25);
  border-radius: 999px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 10px rgba(26,26,46,0.03);
}

.back-home-link:hover {
  background: var(--color-beni-600);
  border-color: var(--color-beni-600);
  color: #fff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(155, 35, 53, 0.2);
}

.back-home-link:hover .arrow {
  transform: translateX(-3px);
}

.arrow {
  display: inline-block;
  transition: transform 0.2s ease;
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

.form-actions-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  font-family: 'Inter', sans-serif;
  font-size: 0.78rem;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: var(--color-sumi-700);
  cursor: pointer;
}
.remember-me input {
  cursor: pointer;
}

.forgot-link {
  color: var(--color-kin-700);
  text-decoration: none;
  font-weight: 500;
}
.forgot-link:hover {
  text-decoration: underline;
}

.w-full {
  width: 100%;
  justify-content: center;
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
  margin-left: 0.25rem;
}
.switch-link:hover {
  text-decoration: underline;
}

.reviewer-hint {
  margin-top: 2rem;
  padding: 0.85rem 1rem;
  background: rgba(201, 168, 76, 0.05);
  border: 1px dashed rgba(201, 168, 76, 0.3);
  border-radius: 4px;
  font-family: 'Inter', sans-serif;
  font-size: 0.72rem;
  color: var(--color-sumi-700);
}
.hint-title {
  font-weight: 600;
  color: var(--color-kin-700);
  margin-bottom: 0.3rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.hint-item {
  margin-bottom: 2px;
}
.hint-item:last-child {
  margin-bottom: 0;
}
.hint-sep {
  color: rgba(201, 168, 76, 0.4);
  margin: 0 0.25rem;
}

.social-login-divider {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 1.25rem 0;
  font-family: 'Inter', sans-serif;
}
.social-login-divider::before, .social-login-divider::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid rgba(201, 168, 76, 0.15);
}
.social-login-divider:not(:empty)::before {
  margin-right: .5em;
}
.social-login-divider:not(:empty)::after {
  margin-left: .5em;
}
.sld-text {
  font-size: 0.72rem;
  color: var(--color-sumi-600);
}

.btn-google-login {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 1rem;
  background: #fff;
  border: 1px solid rgba(201, 168, 76, 0.3);
  border-radius: 4px;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s ease;
}
.btn-google-login:hover {
  background: rgba(201, 168, 76, 0.05);
  border-color: var(--color-kin-500);
  box-shadow: 0 2px 8px rgba(201, 168, 76, 0.1);
}
.google-icon-wrapper {
  display: flex;
  align-items: center;
}
.google-btn-text {
  font-family: 'Inter', sans-serif;
  font-size: 0.88rem;
  font-weight: 500;
  color: var(--color-sumi-800);
}
.fullscreen-loading {
  position: fixed;
  inset: 0;
  background: rgba(253, 250, 245, 0.95);
  backdrop-filter: blur(10px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}
.loading-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}
.loading-logo-kanji {
  font-family: 'Noto Serif JP', serif;
  font-size: 4rem;
  font-weight: 700;
  color: var(--color-beni-600);
  line-height: 1;
}
.loading-logo-text {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.5rem;
  font-weight: 600;
  letter-spacing: 0.2em;
  color: var(--color-sumi-800);
}
.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid rgba(201, 168, 76, 0.3);
  border-top-color: var(--color-kin-500);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-top: 1rem;
}
@keyframes spin { 100% { transform: rotate(360deg); } }
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
