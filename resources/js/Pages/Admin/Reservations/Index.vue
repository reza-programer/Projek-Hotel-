<template>
  <div class="admin-reservations-page">
    <!-- Header -->
    <div>
      <h1 class="page-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="reservations" /> Manajemen Reservasi</h1>
      <p class="page-subtitle">Verifikasi, konfirmasi, dan kelola status kedatangan tamu hotel.</p>
    </div>

    <!-- Filters Card -->
    <div class="filters-card card-shoji">
      <div class="search-box">
        <span class="search-icon"><SvgIcon name="search" width="14" height="14" /></span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Cari ID, nama kamar, tamu..." 
          class="input-zen search-input"
        />
      </div>
      <div class="filter-options">
        <select v-model="filterStatus" class="input-zen select-filter">
          <option value="All">Semua Status</option>
          <option value="pending">Menunggu Konfirmasi</option>
          <option value="confirmed">Dikonfirmasi</option>
          <option value="completed">Selesai (Check-Out)</option>
          <option value="cancelled">Dibatalkan</option>
        </select>
        <select v-model="filterPayment" class="input-zen select-filter">
          <option value="All">Semua Pembayaran</option>
          <option value="paid">Lunas</option>
          <option value="pending">Belum Bayar</option>
          <option value="refunded">Dikembalikan</option>
        </select>
      </div>
    </div>

    <!-- Reservations Table -->
    <div class="table-card card-shoji">
      <div style="overflow-x: auto;">
        <table class="table-zen">
          <thead>
            <tr>
              <th>ID Reservasi</th>
              <th>Nama Tamu</th>
              <th>Kamar</th>
              <th>Check In</th>
              <th>Check Out</th>
              <th>Kamar / Tamu</th>
              <th>Total Biaya</th>
              <th>Status</th>
              <th>Pembayaran</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredReservations.length === 0">
              <td colspan="10" style="text-align: center; padding: 3rem 0; color: var(--color-sumi-600);">
                Tidak ada reservasi yang ditemukan.
              </td>
            </tr>
            <tr v-for="res in filteredReservations" :key="res.id">
              <td class="font-bold text-sumi">{{ res.id }}</td>
              <td>
                <!-- Standard username fallback since userId is linked to users array -->
                <div class="guest-name">{{ getGuestName(res.userId) }}</div>
                <div class="booking-date">Dipesan: {{ formatDateShort(res.bookingDate) }}</div>
              </td>
              <td>
                <div class="room-name">{{ res.roomName }}</div>
              </td>
              <td class="font-medium text-sumi">{{ formatDateShort(res.checkIn) }}</td>
              <td class="font-medium text-sumi">{{ formatDateShort(res.checkOut) }}</td>
              <td>{{ res.nights }} Mlm / {{ res.guests }} Org</td>
              <td class="font-bold text-beni">{{ formatCurrency(res.totalPrice) }}</td>
              <td>
                <span class="badge" :class="statusConfig[res.status]?.class">
                  <SvgIcon :name="statusConfig[res.status]?.icon" width="12" height="12" style="margin-right:2px;" /> {{ statusConfig[res.status]?.label }}
                </span>
              </td>
              <td>
                <span class="badge" :class="statusConfig[res.paymentStatus]?.class" style="font-size:0.68rem;">
                  {{ statusConfig[res.paymentStatus]?.label }}
                </span>
              </td>
              <td>
                <div class="action-buttons-cell">
                  <button @click="openDetailModal(res)" class="btn-action view-btn" title="Detail"><SvgIcon name="search" width="11" height="11" style="margin-right:2px;" /> Detail</button>
                  <button 
                    v-if="res.status === 'pending'" 
                    @click="updateStatus(res.id, 'confirmed')" 
                    class="btn-action approve-btn" 
                    title="Konfirmasi Reservasi"
                  >
                    <SvgIcon name="confirmed" width="11" height="11" style="margin-right:2px;" /> Konfirmasi
                  </button>
                  <button 
                    v-if="res.status === 'confirmed'" 
                    @click="updateStatus(res.id, 'completed')" 
                    class="btn-action complete-btn" 
                    title="Selesaikan Reservasi"
                  >
                    <SvgIcon name="door" width="11" height="11" style="margin-right:2px;" /> Check-out
                  </button>
                  <button 
                    v-if="res.status === 'pending' || res.status === 'confirmed'" 
                    @click="updateStatus(res.id, 'cancelled')" 
                    class="btn-action cancel-btn" 
                    title="Batalkan"
                  >
                    <SvgIcon name="cancelled" width="11" height="11" style="margin-right:2px;" /> Batal
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Detail Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="modal-overlay" @click.self="showModal=false">
        <div class="modal-content" style="max-width: 550px;">
          <div class="modal-header">
            <div>
              <div class="modal-title">Detail Reservasi Lengkap</div>
              <div class="modal-subtitle">{{ selectedRes?.id }}</div>
            </div>
            <button @click="showModal=false" class="modal-close">✕</button>
          </div>

          <div class="modal-body" v-if="selectedRes">
            <div class="detail-grid">
              <div class="detail-section">
                <h3 class="ds-title" style="display:flex; align-items:center; gap:0.35rem;"><SvgIcon name="users" /> Informasi Tamu</h3>
                <div class="df-row">
                  <span class="df-label">Nama Tamu</span>
                  <span class="df-val">{{ getGuestName(selectedRes.userId) }}</span>
                </div>
                <div class="df-row">
                  <span class="df-label">Email Tamu</span>
                  <span class="df-val">{{ getGuestEmail(selectedRes.userId) }}</span>
                </div>
              </div>

              <div class="detail-section">
                <h3 class="ds-title" style="display:flex; align-items:center; gap:0.35rem;"><SvgIcon name="rooms" /> Kamar & Tanggal</h3>
                <div class="df-row">
                  <span class="df-label">Kamar Yang Dipesan</span>
                  <span class="df-val">{{ selectedRes.roomName }}</span>
                </div>
                <div class="df-row">
                  <span class="df-label">Check-In</span>
                  <span class="df-val">{{ formatDateLong(selectedRes.checkIn) }}</span>
                </div>
                <div class="df-row">
                  <span class="df-label">Check-Out</span>
                  <span class="df-val">{{ formatDateLong(selectedRes.checkOut) }}</span>
                </div>
                <div class="df-row">
                  <span class="df-label">Durasi Menginap</span>
                  <span class="df-val">{{ selectedRes.nights }} Malam ({{ selectedRes.guests }} Tamu)</span>
                </div>
              </div>

              <div class="detail-section">
                <h3 class="ds-title" style="display:flex; align-items:center; gap:0.35rem;"><SvgIcon name="paid" /> Pembayaran & Status</h3>
                <div class="df-row">
                  <span class="df-label">Total Tagihan</span>
                  <span class="df-val font-bold text-beni">{{ formatCurrency(selectedRes.totalPrice) }}</span>
                </div>
                <div class="df-row flex-row-item">
                  <span class="df-label">Status Reservasi</span>
                  <span class="badge" :class="statusConfig[selectedRes.status]?.class">
                    <SvgIcon :name="statusConfig[selectedRes.status]?.icon" width="12" height="12" style="margin-right:2px;" />
                    {{ statusConfig[selectedRes.status]?.label }}
                  </span>
                </div>
                <div class="df-row flex-row-item">
                  <span class="df-label">Status Pembayaran</span>
                  <span class="badge" :class="statusConfig[selectedRes.paymentStatus]?.class">
                    <SvgIcon :name="statusConfig[selectedRes.paymentStatus]?.icon" width="12" height="12" style="margin-right:2px;" />
                    {{ statusConfig[selectedRes.paymentStatus]?.label }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button class="btn-outline-gold" @click="showModal=false">Tutup</button>
            <button 
              v-if="selectedRes?.status === 'pending'" 
              class="btn-vermillion" 
              @click="updateStatus(selectedRes.id, 'confirmed'); showModal=false;"
            >
              Konfirmasi Reservasi
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AdminLayout from '@/Components/Layout/AdminLayout.vue'
import SvgIcon from '@/Components/UI/SvgIcon.vue'
import { reservations as initialReservations, users, formatCurrency, statusConfig } from '@/data/mockData'

defineOptions({ layout: AdminLayout })

const reservations = ref([...initialReservations])
const searchQuery = ref('')
const filterStatus = ref('All')
const filterPayment = ref('All')

const showModal = ref(false)
const selectedRes = ref(null)

const filteredReservations = computed(() => {
  return reservations.value.filter(res => {
    const guestName = getGuestName(res.userId).toLowerCase()
    const matchesSearch = res.id.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          res.roomName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          guestName.includes(searchQuery.value.toLowerCase())
    
    const matchesStatus = filterStatus.value === 'All' || res.status === filterStatus.value
    const matchesPayment = filterPayment.value === 'All' || res.paymentStatus === filterPayment.value
    
    return matchesSearch && matchesStatus && matchesPayment
  })
})

const getGuestName = (userId) => {
  const user = users.find(u => u.id === userId)
  return user ? user.name : 'Tamu Premium'
}

const getGuestEmail = (userId) => {
  const user = users.find(u => u.id === userId)
  return user ? user.email : 'guest@email.com'
}

const formatDateShort = (dateStr) => {
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'short', year: 'numeric'
  }).format(new Date(dateStr))
}

const formatDateLong = (dateStr) => {
  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
  }).format(new Date(dateStr))
}

const openDetailModal = (res) => {
  selectedRes.value = res
  showModal.value = true
}

const updateStatus = (id, newStatus) => {
  const index = reservations.value.findIndex(r => r.id === id)
  if (index !== -1) {
    reservations.value[index].status = newStatus
    
    // Auto update payment if confirmed
    if (newStatus === 'confirmed' && reservations.value[index].paymentStatus === 'pending') {
      reservations.value[index].paymentStatus = 'paid'
    }

    // Informative message
    let statusText = newStatus === 'confirmed' ? 'Dikonfirmasi (Lunas)' : newStatus === 'completed' ? 'Selesai (Check-Out)' : 'Dibatalkan'
    alert(`Status reservasi #${id} berhasil diupdate ke ${statusText}.`)
  }
}
</script>

<style scoped>
.admin-reservations-page { display:flex; flex-direction:column; gap:1.5rem; }

.page-title { font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:600; color:var(--color-sumi-800); }
.page-subtitle { font-size:0.85rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; margin-top:2px; }

/* Filters */
.filters-card { padding:1.25rem 1.5rem; border-radius:4px; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
.search-box { position:relative; flex:1; min-width:280px; }
.search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); font-size:0.9rem; color:var(--color-sumi-600); }
.search-input { padding-left:2.5rem; }
.filter-options { display:flex; gap:0.75rem; }
.select-filter { width:170px; }

/* Table styling adjustments */
.table-card { padding:0.5rem; border-radius:4px; }
.guest-name { font-family:'Inter',sans-serif; font-weight:600; color:var(--color-sumi-800); font-size:0.9rem; }
.booking-date { font-size:0.68rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; }
.room-name { font-family:'Cormorant Garamond',serif; font-size:1.15rem; font-weight:600; color:var(--color-sumi-800); }

.action-buttons-cell { display:flex; gap:0.35rem; flex-wrap:wrap; }
.btn-action { padding:0.25rem 0.6rem; font-size:0.72rem; border-radius:3px; cursor:pointer; font-family:'Inter',sans-serif; transition:0.2s; border:1px solid transparent; background:transparent; }
.view-btn { color:var(--color-kin-700); border-color:rgba(201,168,76,0.3); }
.view-btn:hover { background:rgba(201,168,76,0.08); }
.approve-btn { color:var(--color-matcha-700); border-color:rgba(74,124,89,0.3); }
.approve-btn:hover { background:var(--color-matcha-500); color:#fff; }
.complete-btn { color:var(--color-ai-600); border-color:rgba(44,74,124,0.3); }
.complete-btn:hover { background:var(--color-ai-600); color:#fff; }
.cancel-btn { color:var(--color-beni-600); border-color:rgba(155,35,53,0.3); }
.cancel-btn:hover { background:var(--color-beni-600); color:#fff; }

/* Modals & details */
.modal-header { display:flex; justify-content:space-between; align-items:flex-start; padding:1.5rem; border-bottom:1px solid rgba(201,168,76,0.2); }
.modal-title { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--color-sumi-800); }
.modal-subtitle { font-family:'Noto Serif JP',serif; font-size:0.7rem; color:var(--color-kin-500); letter-spacing:0.1em; }
.modal-close { width:32px; height:32px; border:none; background:transparent; cursor:pointer; font-size:1rem; color:var(--color-sumi-600); border-radius:4px; transition:0.2s; }
.modal-close:hover { background:rgba(155,35,53,0.08); color:var(--color-beni-600); }
.modal-body { padding:1.5rem; }
.modal-footer { padding:1.25rem 1.5rem; border-top:1px solid rgba(201,168,76,0.2); display:flex; justify-content:flex-end; gap:0.75rem; }

.detail-grid { display:flex; flex-direction:column; gap:1.25rem; }
.detail-section { border-bottom:1px dashed rgba(201,168,76,0.15); padding-bottom:1rem; }
.detail-section:last-child { border-bottom:none; padding-bottom:0; }
.ds-title { font-family:'Noto Serif JP',serif; font-size:0.8rem; color:var(--color-kin-700); letter-spacing:0.06em; margin-bottom:0.6rem; text-transform:uppercase; font-weight:600; }

.df-row { display:flex; justify-content:space-between; align-items:center; font-family:'Inter',sans-serif; font-size:0.85rem; margin-bottom:0.4rem; }
.df-row:last-child { margin-bottom:0; }
.df-label { color:var(--color-sumi-600); }
.df-val { font-weight:500; color:var(--color-sumi-800); }
.flex-row-item { align-items:center; }

@media(max-width:768px) {
  .filters-card { flex-direction:column; align-items:stretch; }
  .filter-options { flex-direction:column; }
  .select-filter { width:100%; }
}
</style>
