<template>
  <div class="admin-shell">
    <!-- Sidebar -->
    <aside class="admin-sidebar" :class="{ collapsed: sidebarCollapsed, 'mobile-open': mobileOpen }">
      <!-- Logo -->
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <span class="s-kanji">阿</span>
          <div v-if="!sidebarCollapsed">
            <div class="s-name">DARMA MIZUKI</div>
            <div class="s-sub">ADMIN PANEL</div>
          </div>
        </div>
      </div>

      <!-- Nav -->
      <nav class="sidebar-nav">
        <div class="nav-group">
          <div v-if="!sidebarCollapsed" class="nav-group-label">OVERVIEW</div>
          <a href="/admin/dashboard" class="sidebar-link" :class="{ active: isActive('/admin/dashboard') }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span v-if="!sidebarCollapsed">Dashboard</span>
          </a>
        </div>

        <div class="nav-group">
          <div v-if="!sidebarCollapsed" class="nav-group-label">KONTEN</div>
          <a href="/admin/kamar" class="sidebar-link" :class="{ active: isActive('/admin/kamar') }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span v-if="!sidebarCollapsed">Manajemen Kamar</span>
          </a>
          <a href="/admin/fasilitas" class="sidebar-link" :class="{ active: isActive('/admin/fasilitas') }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
            <span v-if="!sidebarCollapsed">Manajemen Fasilitas</span>
          </a>
        </div>

        <div class="nav-group">
          <div v-if="!sidebarCollapsed" class="nav-group-label">OPERASIONAL</div>
          <a href="/admin/reservasi" class="sidebar-link" :class="{ active: isActive('/admin/reservasi') }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span v-if="!sidebarCollapsed">Manajemen Reservasi</span>
          </a>
          <a href="/admin/pembayaran" class="sidebar-link" :class="{ active: isActive('/admin/pembayaran') }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span v-if="!sidebarCollapsed">Manajemen Pembayaran</span>
          </a>
          <a href="/admin/chat" class="sidebar-link" :class="{ active: isActive('/admin/chat') }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span v-if="!sidebarCollapsed">Layanan Chat</span>
          </a>
        </div>

        <div class="nav-group">
          <div v-if="!sidebarCollapsed" class="nav-group-label">ANALITIK</div>
          <a href="/admin/laporan/pendapatan" class="sidebar-link" :class="{ active: isActive('/admin/laporan') }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <span v-if="!sidebarCollapsed">Laporan Pendapatan</span>
          </a>
        </div>

        <div class="nav-group">
          <div v-if="!sidebarCollapsed" class="nav-group-label">PENGGUNA</div>
          <a href="/admin/pengguna" class="sidebar-link" :class="{ active: isActive('/admin/pengguna') }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            <span v-if="!sidebarCollapsed">Manajemen Pengguna</span>
          </a>
        </div>
      </nav>

      <!-- Profile bottom -->
      <div class="sidebar-footer">
        <div class="admin-profile">
          <div class="admin-avatar">A</div>
          <div v-if="!sidebarCollapsed">
            <div class="admin-name">Ahmad Admin</div>
            <div class="admin-role">Super Admin</div>
          </div>
        </div>
        <button @click="handleLogout" class="sidebar-link logout-link" v-if="!sidebarCollapsed" style="background: none; border: none; cursor: pointer; text-align: left; width: 100%;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          <span>Keluar</span>
        </button>
      </div>
    </aside>

    <!-- Mobile overlay -->
    <div v-if="mobileOpen" class="mobile-overlay" @click="mobileOpen = false"/>

    <!-- Main Area -->
    <div class="admin-main" :class="{ expanded: sidebarCollapsed }">
      <!-- Top Header -->
      <header class="admin-header">
        <div class="header-left">
          <button class="collapse-btn" @click="toggleSidebar" aria-label="Toggle sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          </button>
          <div class="header-breadcrumb">
            <span class="breadcrumb-hotel">阿 Darma Mizuki</span>
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-page">{{ title }}</span>
          </div>
        </div>
        <div class="header-right" style="position: relative;">
          <!-- Notification Bell -->
          <button class="notif-btn" @click.stop="toggleNotifs" aria-label="Notifikasi">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
            <span v-if="unreadCount > 0" class="notif-badge">{{ unreadCount }}</span>
          </button>

          <!-- Notifications Dropdown -->
          <div v-if="showNotifs" class="notifs-dropdown card-shoji" @click.stop>
            <div class="notifs-header">
              <h4>Notifikasi Baru</h4>
              <button v-if="unreadCount > 0" @click="markAllAsRead" class="mark-read-btn">Tandai Dibaca</button>
            </div>
            <div class="notifs-body">
              <div v-if="notifications.length === 0" class="notifs-empty">
                Tidak ada notifikasi.
              </div>
              <div v-else class="notifs-list">
                <div v-for="n in notifications" :key="n.id" class="notif-item" :class="{ unread: !n.read }">
                  <div class="notif-item-icon">🛎️</div>
                  <div class="notif-item-content">
                    <p class="notif-item-text">{{ n.text }}</p>
                    <span class="notif-item-time">{{ formatTime(n.timestamp) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <a href="/" class="header-back-hotel">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            <span>Lihat Hotel</span>
          </a>
        </div>
      </header>

      <!-- Page Content -->
      <div class="admin-content">
        <slot />
      </div>
    </div>
    <Toast />

    <!-- Logout Loading Overlay -->
    <Transition name="fade">
      <div v-if="logoutSuccess" class="fullscreen-loading">
        <div class="loading-content">
          <div class="loading-logo-kanji">阿</div>
          <div class="loading-logo-text">DARMA MIZUKI</div>
          <div class="loading-line"></div>
          <p style="margin-top: 1rem; color: var(--color-sumi-600); font-family: 'Inter', sans-serif; font-size: 0.9rem;">Keluar dari sistem admin...</p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Toast from '../UI/Toast.vue'

const props = defineProps({ title: { type: String, default: 'Dashboard' } })
const sidebarCollapsed = ref(false)
const mobileOpen = ref(false)
const logoutSuccess = ref(false)

const toggleSidebar = () => {
  if (window.innerWidth < 900) mobileOpen.value = !mobileOpen.value
  else sidebarCollapsed.value = !sidebarCollapsed.value
}

const isActive = (path) => window.location.pathname.startsWith(path)

const handleLogout = () => {
  logoutSuccess.value = true
  localStorage.removeItem('miyabi_user')
  sessionStorage.setItem('miyabi_flash', JSON.stringify({
    message: 'Anda telah berhasil keluar dari akun.',
    type: 'error'
  }))
  
  setTimeout(() => {
    window.location.href = '/logout'
  }, 1500)
}

// Notifications state
const showNotifs = ref(false)
const notifications = ref([])

const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.read).length
})

const loadNotifs = () => {
  if (typeof window !== 'undefined') {
    const raw = localStorage.getItem('miyabi_admin_notifications')
    notifications.value = raw ? JSON.parse(raw) : []
  }
}

const toggleNotifs = () => {
  showNotifs.value = !showNotifs.value
  if (showNotifs.value) {
    loadNotifs()
  }
}

const markAllAsRead = () => {
  notifications.value = notifications.value.map(n => ({ ...n, read: true }))
  localStorage.setItem('miyabi_admin_notifications', JSON.stringify(notifications.value))
}

const formatTime = (isoString) => {
  try {
    const date = new Date(isoString)
    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  } catch (e) {
    return ''
  }
}

const closeDropdown = () => {
  showNotifs.value = false
}

// Listen to localStorage changes
const handleStorageChange = (e) => {
  if (e.key === 'miyabi_admin_notifications') {
    const prevCount = unreadCount.value
    loadNotifs()
    
    // Check if new unread notification arrived
    const newUnread = notifications.value.filter(n => !n.read)
    if (newUnread.length > 0 && unreadCount.value > prevCount) {
      window.dispatchEvent(new CustomEvent('miyabi-show-toast', {
        detail: {
          message: newUnread[0].text,
          type: 'info'
        }
      }))
    }
  }
}

onMounted(() => {
  loadNotifs()
  window.addEventListener('storage', handleStorageChange)
  document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
  window.removeEventListener('storage', handleStorageChange)
  document.removeEventListener('click', closeDropdown)
})
</script>

<style scoped>
.admin-shell { display:flex; min-height:100vh; background: var(--color-washi-300); }

/* Sidebar */
.admin-sidebar {
  width:250px; flex-shrink:0;
  background: var(--color-sumi-800);
  border-right: 1px solid rgba(201,168,76,0.12);
  display:flex; flex-direction:column;
  height:100vh; position:sticky; top:0;
  transition: width 0.3s ease;
  overflow-y:auto; overflow-x:hidden;
  z-index:50;
}
.admin-sidebar.collapsed { width:64px; }
.admin-sidebar::-webkit-scrollbar { width:3px; }
.admin-sidebar::-webkit-scrollbar-thumb { background:rgba(201,168,76,0.3); border-radius:2px; }

.sidebar-header { padding:1.25rem 1rem; border-bottom:1px solid rgba(201,168,76,0.12); }
.sidebar-logo { display:flex; align-items:center; gap:0.75rem; }
.s-kanji { font-family:'Noto Serif JP',serif; font-size:1.8rem; font-weight:700; color:var(--color-beni-500); flex-shrink:0; }
.s-name { font-family:'Cormorant Garamond',serif; font-size:1rem; font-weight:600; letter-spacing:0.18em; color:#fff; }
.s-sub { font-size:0.55rem; letter-spacing:0.22em; color:var(--color-kin-500); font-family:'Inter',sans-serif; }

.sidebar-nav { flex:1; padding:0.75rem 0; }
.nav-group { margin-bottom:0.25rem; }
.nav-group-label { padding:0.6rem 1.25rem 0.3rem; font-size:0.6rem; letter-spacing:0.18em; color:rgba(201,168,76,0.45); font-family:'Inter',sans-serif; font-weight:600; }

.sidebar-link {
  display:flex; align-items:center; gap:0.75rem;
  padding:0.7rem 1.25rem; color:rgba(247,243,237,0.65);
  text-decoration:none; font-size:0.82rem; font-family:'Inter',sans-serif;
  border-left:3px solid transparent;
  transition:all 0.2s; white-space:nowrap;
}
.sidebar-link svg { flex-shrink:0; }
.sidebar-link:hover { background:rgba(201,168,76,0.08); color:var(--color-kin-300); border-left-color:rgba(201,168,76,0.4); }
.sidebar-link.active { background:rgba(155,35,53,0.18); color:var(--color-sakura-200); border-left-color:var(--color-beni-500); }

.sidebar-footer { padding:1rem; border-top:1px solid rgba(201,168,76,0.12); }
.admin-profile { display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem; }
.admin-avatar { width:34px; height:34px; border-radius:50%; background:var(--color-beni-600); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:0.85rem; flex-shrink:0; }
.admin-name { font-size:0.82rem; color:#fff; font-family:'Inter',sans-serif; font-weight:500; }
.admin-role { font-size:0.68rem; color:var(--color-kin-500); letter-spacing:0.05em; }
.logout-link { color:rgba(247,243,237,0.5); font-size:0.8rem; border-left-color:transparent; }
.logout-link:hover { color:var(--color-beni-400); }

/* Main */
.admin-main { flex:1; display:flex; flex-direction:column; min-width:0; transition:0.3s; }

.admin-header {
  height:60px; background:rgba(253,250,245,0.97);
  border-bottom:1px solid rgba(201,168,76,0.2);
  display:flex; align-items:center; justify-content:space-between;
  padding:0 1.5rem; position:sticky; top:0; z-index:40;
  backdrop-filter:blur(8px);
  box-shadow: 0 1px 12px rgba(26,26,46,0.06);
}
.header-left { display:flex; align-items:center; gap:1rem; }
.collapse-btn {
  width:36px; height:36px; border:none; background:transparent;
  cursor:pointer; border-radius:6px; display:flex; align-items:center; justify-content:center;
  color:var(--color-sumi-700); transition:0.2s;
}
.collapse-btn:hover { background:rgba(155,35,53,0.08); color:var(--color-beni-600); }

.header-breadcrumb { display:flex; align-items:center; gap:0.5rem; font-family:'Inter',sans-serif; font-size:0.85rem; }
.breadcrumb-hotel { font-family:'Noto Serif JP',serif; color:var(--color-beni-600); font-weight:600; }
.breadcrumb-sep { color:var(--color-kin-500); }
.breadcrumb-page { color:var(--color-sumi-700); font-weight:500; }

.header-right { display:flex; align-items:center; gap:0.75rem; }
.notif-btn { position:relative; width:38px; height:38px; border:none; background:transparent; cursor:pointer; border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--color-sumi-700); transition:0.2s; }
.notif-btn:hover { background:rgba(155,35,53,0.08); }
.notif-badge { position:absolute; top:2px; right:2px; background:var(--color-beni-600); color:#fff; font-size:0.62rem; font-weight:700; width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center; border:1px solid #fff; }
.notifs-dropdown { position:absolute; top:50px; right:0; width:320px; max-height:360px; background:#fff; border:1px solid rgba(201,168,76,0.3); border-radius:4px; box-shadow:0 8px 30px rgba(26,26,46,0.15); z-index:100; display:flex; flex-direction:column; overflow:hidden; }
.notifs-header { padding:0.75rem 1rem; background:var(--color-washi-300); border-bottom:1px solid rgba(201,168,76,0.2); display:flex; justify-content:space-between; align-items:center; }
.notifs-header h4 { font-family:var(--font-display); font-size:0.9rem; font-weight:700; color:var(--color-sumi-800); }
.mark-read-btn { background:transparent; border:none; color:var(--color-beni-600); font-size:0.7rem; font-weight:600; cursor:pointer; }
.mark-read-btn:hover { text-decoration:underline; }
.notifs-body { overflow-y:auto; flex:1; }
.notifs-body::-webkit-scrollbar { width:3px; }
.notifs-body::-webkit-scrollbar-thumb { background:rgba(201,168,76,0.2); }
.notifs-empty { padding:2rem; text-align:center; color:var(--color-sumi-500); font-size:0.8rem; }
.notifs-list { display:flex; flex-direction:column; }
.notif-item { display:flex; gap:0.75rem; padding:0.75rem 1rem; border-bottom:1px solid rgba(201,168,76,0.1); transition:background 0.2s; }
.notif-item:hover { background:rgba(201,168,76,0.04); }
.notif-item.unread { background:rgba(155,35,53,0.03); }
.notif-item-icon { font-size:1.1rem; flex-shrink:0; margin-top:2px; }
.notif-item-content { flex:1; min-width:0; }
.notif-item-text { font-size:0.78rem; color:var(--color-sumi-800); line-height:1.4; text-align:left; }
.notif-item.unread .notif-item-text { font-weight:600; }
.notif-item-time { font-size:0.65rem; color:var(--color-sumi-500); display:block; margin-top:3px; text-align:left; }
.header-back-hotel { display:flex; align-items:center; gap:0.4rem; padding:0.5rem 1rem; border-radius:6px; background:var(--color-beni-600); color:#fff; font-size:0.78rem; text-decoration:none; font-family:'Inter',sans-serif; font-weight:500; transition:0.2s; border:none; box-shadow:0 2px 4px rgba(155,35,53,0.15); }
.header-back-hotel:hover { background:var(--color-beni-700); box-shadow:0 3px 6px rgba(155,35,53,0.25); transform:translateY(-1px); }

.admin-content { padding:1.75rem; flex:1; }

/* Mobile */
.mobile-overlay { position:fixed; inset:0; background:rgba(26,26,46,0.5); z-index:49; }
@media(max-width:900px) {
  .admin-sidebar { position:fixed; left:-250px; height:100vh; }
  .admin-sidebar.mobile-open { left:0; }
  .admin-main { margin-left:0 !important; }
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
