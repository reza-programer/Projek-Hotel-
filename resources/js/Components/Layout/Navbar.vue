<template>
  <nav class="navbar-zen" :class="{ scrolled: isScrolled }">
    <div class="container-zen" style="display:flex; align-items:center; justify-content:space-between; height:72px;">

      <!-- Logo -->
      <Link href="/" class="navbar-logo">
        <span class="logo-kanji">阿</span>
        <span class="logo-text">DARMA MIZUKI</span>
        <span class="logo-sub">RYOKA</span>
      </Link>

      <!-- Desktop Nav -->
      <div class="nav-links" :class="{ 'nav-dark': !isScrolled }">
        <Link href="/" class="nav-link-zen" :class="{ active: $page.url === '/' }">Beranda</Link>
        <Link href="/tentang-kami" class="nav-link-zen" :class="{ active: $page.url.startsWith('/tentang') }">Tentang Kami</Link>
        <Link href="/kamar" class="nav-link-zen" :class="{ active: $page.url.startsWith('/kamar') }">Kamar</Link>
        <Link href="/booking/buat" class="nav-link-zen" :class="{ active: $page.url.startsWith('/booking') }">Booking</Link>
        <a href="#kontak" class="nav-link-zen">Kontak</a>
      </div>

      <!-- Right Actions -->
      <div class="nav-actions" :class="{ 'nav-dark': !isScrolled }">
        <div class="lang-toggle">
          <button :class="{ active: lang === 'ID' }" @click="lang = 'ID'">ID</button>
          <span>|</span>
          <button :class="{ active: lang === 'JP' }" @click="lang = 'JP'">JP</button>
        </div>
        <template v-if="currentUser">
          <div class="user-greeting-area">
            <span class="user-greeting">Selamat datang, <strong>{{ currentUser.name }}</strong></span>
            <Link v-if="currentUser.role === 'admin'" href="/admin/dashboard" class="btn-outline-gold btn-header-sm">Admin</Link>
            <div v-else style="position:relative;" @click.stop="accountDropdownOpen = !accountDropdownOpen">
              <button class="btn-outline-gold btn-header-sm" style="display:flex; align-items:center; gap:0.25rem;">
                Akun Saya
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
              </button>
              <Transition name="fade">
                <div v-if="accountDropdownOpen" class="account-dropdown">
                  <Link href="/akun/profil" class="dropdown-link" @click="accountDropdownOpen = false">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Profil Saya
                  </Link>
                  <Link href="/akun/reservasi" class="dropdown-link" @click="accountDropdownOpen = false">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    Riwayat Reservasi
                  </Link>
                </div>
              </Transition>
            </div>
            <button @click="handleLogout" class="btn-vermillion btn-header-sm">Keluar</button>
          </div>
        </template>
        <template v-else>
          <Link href="/login" class="btn-outline-gold" style="padding:0.5rem 1.2rem; font-size:0.8rem;">Masuk</Link>
          <Link href="/register" class="btn-vermillion" style="padding:0.5rem 1.2rem; font-size:0.8rem;">Daftar</Link>
        </template>
        <!-- Mobile Hamburger -->
        <button class="hamburger" @click="mobileOpen = !mobileOpen" aria-label="Menu">
          <span :class="{ open: mobileOpen }"></span>
          <span :class="{ open: mobileOpen }"></span>
          <span :class="{ open: mobileOpen }"></span>
        </button>
      </div>
    </div>

    <!-- Mobile Drawer -->
    <Transition name="drawer">
      <div v-if="mobileOpen" class="mobile-drawer">
        <Link href="/" class="mobile-link" @click="mobileOpen=false">Beranda</Link>
        <Link href="/tentang-kami" class="mobile-link" @click="mobileOpen=false">Tentang Kami</Link>
        <Link href="/kamar" class="mobile-link" @click="mobileOpen=false">Kamar & Suite</Link>
        <Link href="/booking/buat" class="mobile-link" @click="mobileOpen=false">Booking</Link>
        <a href="#kontak" class="mobile-link" @click="mobileOpen=false">Kontak</a>
        <div v-if="currentUser" style="display:flex; flex-direction:column; gap:0.5rem; padding: 1rem 1.5rem; border-top: 1px solid rgba(201,168,76,0.2); margin-top:0.5rem;">
          <span style="font-size:0.8rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; text-align:center;">Masuk sebagai: <strong>{{ currentUser.name }}</strong></span>
          <div style="display:flex; gap:0.5rem; width:100%;">
            <Link v-if="currentUser.role === 'admin'" href="/admin/dashboard" class="btn-outline-gold" style="flex:1; text-align:center; padding:0.6rem 1rem; font-size:0.8rem;" @click="mobileOpen=false">Admin</Link>
            <template v-else>
              <Link href="/akun/profil" class="btn-outline-gold" style="flex:1; text-align:center; padding:0.6rem 1rem; font-size:0.75rem;" @click="mobileOpen=false">Profil</Link>
              <Link href="/akun/reservasi" class="btn-outline-gold" style="flex:1; text-align:center; padding:0.6rem 1rem; font-size:0.75rem;" @click="mobileOpen=false">Reservasi</Link>
            </template>
            <button @click="handleLogout" class="btn-vermillion" style="flex:1; text-align:center; padding:0.6rem 1rem; font-size:0.8rem; border:none; cursor:pointer;">Keluar</button>
          </div>
        </div>
        <div v-else style="display:flex; gap:0.75rem; padding: 1rem 1.5rem; border-top: 1px solid rgba(201,168,76,0.2); margin-top:0.5rem;">
          <Link href="/login" class="btn-outline-gold" style="flex:1; text-align:center; padding:0.6rem 1rem; font-size:0.85rem;" @click="mobileOpen=false">Masuk</Link>
          <Link href="/register" class="btn-vermillion" style="flex:1; text-align:center; padding:0.6rem 1rem; font-size:0.85rem;" @click="mobileOpen=false">Daftar</Link>
        </div>
      </div>
    </Transition>

    <!-- Logout Loading Overlay -->
    <Transition name="fade">
      <div v-if="logoutSuccess" class="fullscreen-loading">
        <div class="loading-content">
          <div class="loading-logo-kanji">阿</div>
          <div class="loading-logo-text">DARMA MIZUKI</div>
          <div class="loading-line"></div>
          <p style="margin-top: 1rem; color: var(--color-sumi-600); font-family: 'Inter', sans-serif; font-size: 0.9rem;">Keluar dari sistem...</p>
        </div>
      </div>
    </Transition>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const isScrolled = ref(false)
const mobileOpen = ref(false)
const lang = ref('ID')
const localUser = ref(null)
const logoutSuccess = ref(false)
const accountDropdownOpen = ref(false)
const page = usePage()

const handleScroll = () => { isScrolled.value = window.scrollY > 40 }
const closeDropdowns = () => { accountDropdownOpen.value = false }

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  document.addEventListener('click', closeDropdowns)
  const storedUser = localStorage.getItem('miyabi_user')
  if (storedUser) {
    localUser.value = JSON.parse(storedUser)
  }
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('click', closeDropdowns)
})

const currentUser = computed(() => {
  if (page.props.auth && page.props.auth.user) {
    return page.props.auth.user
  }
  return localUser.value
})

const handleLogout = () => {
  mobileOpen.value = false
  logoutSuccess.value = true
  localStorage.removeItem('miyabi_user')
  localUser.value = null
  sessionStorage.setItem('miyabi_flash', JSON.stringify({
    message: 'Anda telah berhasil keluar dari akun.',
    type: 'error'
  }))
  
  setTimeout(() => {
    window.location.href = '/logout'
  }, 1500)
}
</script>

<style scoped>
.navbar-zen {
  position: fixed; top:0; left:0; right:0; z-index:100;
  transition: all 0.5s cubic-bezier(0.25,0.46,0.45,0.94);
}
.navbar-zen.scrolled {
  background: rgba(253,250,245,0.97);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(201,168,76,0.25);
  box-shadow: 0 2px 24px rgba(26,26,46,0.08);
}
.navbar-logo {
  display:flex; align-items:baseline; gap:0.35rem;
  text-decoration:none;
}
.logo-kanji {
  font-family: 'Noto Serif JP', serif;
  font-size:1.6rem; font-weight:700;
  color: var(--color-beni-600);
  line-height:1;
}
.logo-text {
  font-family: 'Cormorant Garamond', serif;
  font-size:1.25rem; font-weight:600;
  letter-spacing:0.2em;
  color: var(--color-sumi-800);
}
.logo-sub {
  font-family: 'Inter', sans-serif;
  font-size:0.6rem; font-weight:400;
  letter-spacing:0.3em;
  color: var(--color-kin-500);
  margin-left:0.1rem;
}
.navbar-zen:not(.scrolled) .logo-text,
.navbar-zen:not(.scrolled) .logo-sub { color: #fff; }
.navbar-zen:not(.scrolled) .logo-kanji { color: var(--color-sakura-300); }

.nav-links { display:flex; align-items:center; gap:2rem; }
.nav-dark .nav-link-zen { color: rgba(255,255,255,0.9); }
.nav-dark .nav-link-zen::after { background: var(--color-sakura-200); }

.nav-actions { display:flex; align-items:center; gap:0.75rem; }

.lang-toggle {
  display:flex; align-items:center; gap:0.3rem;
  font-family: 'Inter', sans-serif; font-size:0.75rem;
  letter-spacing:0.08em;
}
.lang-toggle button {
  background:none; border:none; cursor:pointer; padding:0.15rem 0.3rem;
  border-radius:3px; transition:0.2s;
  color: var(--color-sumi-600);
}
.lang-toggle button.active { color: var(--color-beni-600); font-weight:600; }
.nav-dark .lang-toggle button { color: rgba(255,255,255,0.7); }
.nav-dark .lang-toggle button.active { color: var(--color-sakura-200); }
.lang-toggle span { color: rgba(201,168,76,0.5); }

.hamburger {
  display:none; flex-direction:column; gap:5px;
  background:none; border:none; cursor:pointer; padding:4px;
}
.hamburger span {
  display:block; width:22px; height:2px;
  background: var(--color-sumi-800); border-radius:2px;
  transition: all 0.3s;
}
.navbar-zen:not(.scrolled) .hamburger span { background: #fff; }
.hamburger span.open:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.hamburger span.open:nth-child(2) { opacity:0; }
.hamburger span.open:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

.mobile-drawer {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  background: rgba(253,250,245,0.98);
  backdrop-filter: blur(16px);
  border-top: 1px solid rgba(201,168,76,0.2);
  padding: 0.5rem 0;
  box-shadow: 0 10px 30px rgba(26,26,46,0.1);
  transform-origin: top;
}
.mobile-link {
  display:block; padding: 0.85rem 1.5rem;
  font-family: 'Noto Serif JP', serif;
  font-size:0.9rem; letter-spacing:0.05em;
  color: var(--color-sumi-800); text-decoration:none;
  border-bottom: 1px solid rgba(201,168,76,0.1);
  transition:0.2s;
}
.mobile-link:hover { background: rgba(201,168,76,0.06); color: var(--color-beni-600); }

.drawer-enter-active, .drawer-leave-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.drawer-enter-from, .drawer-leave-to { opacity:0; transform: translateY(-10px) scaleY(0.95); }

.user-greeting-area {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-family: 'Inter', sans-serif;
}
.user-greeting {
  font-size: 0.8rem;
  color: var(--color-sumi-800);
}
.navbar-zen:not(.scrolled) .user-greeting {
  color: rgba(255, 255, 255, 0.9);
}
.btn-header-sm {
  padding: 0.45rem 0.95rem;
  font-size: 0.76rem !important;
  font-family: 'Inter', sans-serif;
  border-radius: var(--radius-zen);
}

.account-dropdown {
  position: absolute;
  top: calc(100% + 0.75rem);
  right: 0;
  background: rgba(253, 250, 245, 0.98);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(201,168,76,0.25);
  border-radius: 6px;
  min-width: 200px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 10px 30px rgba(26,26,46,0.15);
  overflow: hidden;
  padding: 0.5rem 0;
  z-index: 1000;
}
.dropdown-link {
  padding: 0.75rem 1.25rem;
  font-family: 'Inter', sans-serif;
  font-size: 0.82rem;
  color: var(--color-sumi-800);
  text-decoration: none;
  transition: all 0.25s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  display: flex;
  align-items: center;
  gap: 0.6rem;
  border-bottom: 1px dashed rgba(201,168,76,0.15);
}
.dropdown-link:last-child { border-bottom: none; }
.dropdown-link:hover {
  background: rgba(155, 35, 53, 0.04);
  color: var(--color-beni-600);
  padding-left: 1.5rem;
}

@media (max-width: 900px) {
  .nav-links { display:none; }
  .lang-toggle { display:none; }
  .nav-actions .btn-outline-gold, .nav-actions .btn-vermillion { display:none; }
  .nav-actions .user-greeting-area { display:none; }
  .hamburger { display:flex; }
}

.fullscreen-loading {
  position: fixed;
  inset: 0;
  background: rgba(253, 250, 245, 0.95);
  backdrop-filter: blur(10px);
  z-index: 99999;
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
.loading-line {
  width: 140px;
  height: 2px;
  background: rgba(201, 168, 76, 0.2);
  position: relative;
  overflow: hidden;
  margin-top: 1rem;
  border-radius: 2px;
}
.loading-line::after {
  content: '';
  position: absolute;
  top: 0; left: 0; bottom: 0;
  width: 40%;
  background: var(--color-kin-500);
  animation: sweep 1.5s ease-in-out infinite;
  border-radius: 2px;
}
@keyframes sweep {
  0% { transform: translateX(-100%); }
  50% { transform: translateX(150%); }
  100% { transform: translateX(-100%); }
}
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
