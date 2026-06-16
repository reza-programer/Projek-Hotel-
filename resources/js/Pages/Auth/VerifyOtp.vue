<template>
  <div class="auth-page pattern-washi">
    <div class="auth-container">
      <div class="auth-card card-shoji" style="text-align: center;">
        <div class="auth-header">
          <span class="auth-kanji">証</span>
          <h1 class="auth-title">Verifikasi Email</h1>
          <p class="auth-subtitle">
            Kami telah mengirimkan 6-digit kode OTP ke email Anda.<br>
            Silakan masukkan kode tersebut di bawah ini.
          </p>
        </div>

        <div class="divider-ink" style="margin: 1.5rem 0;"></div>

        <div v-if="errorMessage" class="alert-error" style="display:flex; justify-content:center; align-items:center; gap:0.4rem;">
          <SvgIcon name="warning" width="14" height="14" /> {{ errorMessage }}
        </div>

        <form @submit.prevent="verifyOtp" class="auth-form">
          <div class="form-group" style="align-items: center;">
            <label class="label-zen">KODE OTP</label>
            <div class="otp-inputs">
              <input
                v-for="(digit, index) in otp"
                :key="index"
                ref="otpRefs"
                v-model="otp[index]"
                type="text"
                maxlength="1"
                class="otp-box"
                @input="handleInput(index, $event)"
                @keydown="handleKeydown(index, $event)"
              />
            </div>
          </div>

          <button type="submit" class="btn-vermillion w-full" :disabled="loading" style="margin-top: 1.5rem;">
            {{ loading ? 'Memverifikasi...' : 'Verifikasi & Lanjutkan' }}
          </button>
        </form>

        <div class="auth-footer" style="margin-top: 2rem;">
          Tidak menerima email?
          <button @click="resendOtp" class="btn-text-gold" :disabled="resendLoading">
            {{ resendLoading ? 'Mengirim ulang...' : 'Kirim Ulang Kode' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import SvgIcon from '@/Components/UI/SvgIcon.vue'

const loading = ref(false)
const resendLoading = ref(false)
const errorMessage = ref('')

const otp = ref(['', '', '', '', '', ''])
const otpRefs = ref([])

// Check if email is in session storage, otherwise redirect back to register
const email = ref('')

onMounted(() => {
  email.value = sessionStorage.getItem('miyabi_pending_email')
  if (!email.value) {
    router.visit('/register')
  }
})

const handleInput = (index, event) => {
  const val = event.target.value
  if (val.match(/[^0-9]/)) {
    otp.value[index] = ''
    return
  }
  if (val && index < 5) {
    otpRefs.value[index + 1].focus()
  }
}

const handleKeydown = (index, event) => {
  if (event.key === 'Backspace' && !otp.value[index] && index > 0) {
    otpRefs.value[index - 1].focus()
  }
}

const verifyOtp = async () => {
  errorMessage.value = ''
  const code = otp.value.join('')
  
  if (code.length < 6) {
    errorMessage.value = 'Mohon masukkan 6 digit kode OTP.'
    return
  }

  loading.value = true

  try {
    await axios.post('/verify-otp', {
      email: email.value,
      otp: code
    })
    
    // Clear session storage
    sessionStorage.removeItem('miyabi_pending_email')
    
    // Set success toast
    sessionStorage.setItem('miyabi_flash', JSON.stringify({
      message: 'Registrasi berhasil! Selamat bergabung di Darma Mizuki Club.',
      type: 'success'
    }))
    
    // Redirect to home
    window.location.href = '/'
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.'
    loading.value = false
  }
}

const resendOtp = async () => {
  resendLoading.value = true
  errorMessage.value = ''
  
  try {
    // We assume the backend register route can just resend if we pass the original data 
    // Since we don't have the password anymore, we might just show an error asking them to register again
    // For a simple fix without building a dedicated resend endpoint:
    alert('Sistem sedang dalam pengembangan untuk fitur kirim ulang. Silakan kembali ke halaman Registrasi untuk mendaftar ulang jika kode belum masuk.')
  } finally {
    resendLoading.value = false
  }
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 2rem 1rem;
}
.auth-container {
  width: 100%;
  max-width: 480px;
}
.auth-card {
  padding: 2.5rem;
  border-radius: 4px;
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
  font-size: 0.85rem;
  color: var(--color-sumi-600);
  font-family: 'Inter', sans-serif;
  margin-top: 0.5rem;
  line-height: 1.5;
}
.otp-inputs {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-top: 0.5rem;
}
.otp-box {
  width: 45px;
  height: 55px;
  text-align: center;
  font-size: 1.5rem;
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  border: 1px solid rgba(201, 168, 76, 0.4);
  border-radius: 4px;
  background: rgba(253, 250, 245, 0.8);
  color: var(--color-sumi-800);
  transition: all 0.2s ease;
}
.otp-box:focus {
  outline: none;
  border-color: var(--color-kin-500);
  box-shadow: 0 0 0 2px rgba(201, 168, 76, 0.2);
  background: #fff;
}
.w-full { width: 100%; justify-content: center; }
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
  font-family: 'Inter', sans-serif;
  font-size: 0.82rem;
  color: var(--color-sumi-600);
}
.btn-text-gold {
  background: none;
  border: none;
  color: var(--color-kin-500);
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  margin-left: 0.25rem;
}
.btn-text-gold:hover {
  text-decoration: underline;
  color: var(--color-kin-600);
}
</style>
