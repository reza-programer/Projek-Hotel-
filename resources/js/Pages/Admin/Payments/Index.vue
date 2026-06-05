<template>
  <div class="admin-payments-page">
    <!-- Header -->
    <div>
      <h1 class="page-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="payments" /> Verifikasi Pembayaran</h1>
      <p class="page-subtitle">Verifikasi bukti transfer bank dan persetujuan transaksi pembayaran reservasi.</p>
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
          <option value="All">Semua Transaksi</option>
          <option value="pending">Menunggu Verifikasi</option>
          <option value="paid">Terverifikasi (Lunas)</option>
          <option value="refunded">Refund / Dibatalkan</option>
        </select>
      </div>
    </div>

    <!-- Payments Table -->
    <div class="table-card card-shoji">
      <div style="overflow-x: auto;">
        <table class="table-zen">
          <thead>
            <tr>
              <th>ID Reservasi</th>
              <th>Nama Tamu</th>
              <th>Kamar</th>
              <th>Total Bayar</th>
              <th>Metode Pembayaran</th>
              <th>Bukti Transfer</th>
              <th>Status Verifikasi</th>
              <th>Aksi Verifikasi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredPayments.length === 0">
              <td colspan="8" style="text-align: center; padding: 3rem 0; color: var(--color-sumi-600);">
                Tidak ada data pembayaran yang ditemukan.
              </td>
            </tr>
            <tr v-for="res in filteredPayments" :key="res.id">
              <td class="font-bold text-sumi">{{ res.id }}</td>
              <td>
                <div class="guest-name">{{ getGuestName(res.userId) }}</div>
                <div class="booking-date">Dipesan: {{ formatDateShort(res.bookingDate) }}</div>
              </td>
              <td>
                <div class="room-name">{{ res.roomName }}</div>
              </td>
              <td class="font-bold text-beni">{{ formatCurrency(res.totalPrice) }}</td>
              <td>
                <span class="pay-method-badge"><SvgIcon name="business" width="11" height="11" style="margin-right:2px;" /> Bank Transfer (BCA)</span>
              </td>
              <td>
                <!-- Bukti transfer button -->
                <button 
                  class="btn-proof" 
                  @click="openProofModal(res)"
                  :class="{ 'has-proof': res.paymentStatus === 'paid' || res.status === 'confirmed' || res.id === 'MYB-2025-001' }"
                >
                  <SvgIcon name="search" width="11" height="11" style="margin-right:2px;" /> Lihat Bukti
                </button>
              </td>
              <td>
                <span class="badge" :class="statusConfig[res.paymentStatus]?.class">
                  <SvgIcon :name="statusConfig[res.paymentStatus]?.icon" width="12" height="12" style="margin-right:2px;" /> {{ statusConfig[res.paymentStatus]?.label }}
                </span>
              </td>
              <td>
                <div class="action-buttons-cell">
                  <button 
                    v-if="res.paymentStatus === 'pending'" 
                    @click="verifyPayment(res.id, 'paid')" 
                    class="btn-action approve-btn" 
                    title="Terma Pembayaran"
                  >
                    <SvgIcon name="confirmed" width="11" height="11" style="margin-right:2px;" /> Verifikasi
                  </button>
                  <button 
                    v-if="res.paymentStatus === 'pending'" 
                    @click="verifyPayment(res.id, 'rejected')" 
                    class="btn-action cancel-btn" 
                    title="Tolak Pembayaran"
                  >
                    <SvgIcon name="cancelled" width="11" height="11" style="margin-right:2px;" /> Tolak
                  </button>
                  <span v-else style="font-size:0.75rem; color:var(--color-sumi-600); font-style:italic;">
                    Sudah Diproses
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Proof of Payment Lightbox Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="modal-overlay" @click.self="showModal=false">
        <div class="modal-content" style="max-width: 450px;">
          <div class="modal-header">
            <div>
              <div class="modal-title">Verifikasi Bukti Transfer</div>
              <div class="modal-subtitle">{{ selectedPayment?.id }}</div>
            </div>
            <button @click="showModal=false" class="modal-close">✕</button>
          </div>

          <div class="modal-body" v-if="selectedPayment" style="text-align: center;">
            <!-- Bank slip visual representation -->
            <div class="bank-slip">
              <div class="slip-header">
                <span class="slip-bank">BANK CENTRAL ASIA</span>
                <span class="slip-success">TRANSAKSI BERHASIL</span>
              </div>
              <div class="slip-divider"></div>
              <div class="slip-row">
                <span class="sr-label">TANGGAL</span>
                <span class="sr-val">{{ formatDateShort(selectedPayment.bookingDate) }}</span>
              </div>
              <div class="slip-row">
                <span class="sr-label">KEPADA REKENING</span>
                <span class="sr-val">123-4567-890 (ARUNIKA RYOKA PT)</span>
              </div>
              <div class="slip-row">
                <span class="sr-label">NAMA PENGIRIM</span>
                <span class="sr-val">{{ getGuestName(selectedPayment.userId).toUpperCase() }}</span>
              </div>
              <div class="slip-row">
                <span class="sr-label">JUMLAH TRANSFER</span>
                <span class="sr-val slip-amount">{{ formatCurrency(selectedPayment.totalPrice) }}</span>
              </div>
              <div class="slip-footer">
                <span>RESI DIGITAL VALID - TERIMA KASIH</span>
              </div>
            </div>
            <p style="font-size: 0.78rem; color: var(--color-sumi-600); margin-top: 1rem; font-family: 'Inter',sans-serif;">
              Silakan periksa apakah jumlah transfer sesuai dengan total tagihan.
            </p>
          </div>

          <div class="modal-footer">
            <button class="btn-outline-gold" @click="showModal=false">Tutup</button>
            <button 
              v-if="selectedPayment?.paymentStatus === 'pending'" 
              class="btn-vermillion" 
              @click="verifyPayment(selectedPayment.id, 'paid'); showModal=false;"
            >
              Terima & Verifikasi
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

const showModal = ref(false)
const selectedPayment = ref(null)

const filteredPayments = computed(() => {
  return reservations.value.filter(res => {
    const guestName = getGuestName(res.userId).toLowerCase()
    const matchesSearch = res.id.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          res.roomName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                          guestName.includes(searchQuery.value.toLowerCase())
    
    const matchesStatus = filterStatus.value === 'All' || res.paymentStatus === filterStatus.value
    
    return matchesSearch && matchesStatus
  })
})

const getGuestName = (userId) => {
  const user = users.find(u => u.id === userId)
  return user ? user.name : 'Tamu Premium'
}

const formatDateShort = (dateStr) => {
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'short', year: 'numeric'
  }).format(new Date(dateStr))
}

const openProofModal = (res) => {
  selectedPayment.value = res
  showModal.value = true
}

const verifyPayment = (id, newPaymentStatus) => {
  const index = reservations.value.findIndex(r => r.id === id)
  if (index !== -1) {
    reservations.value[index].paymentStatus = newPaymentStatus
    
    // Auto confirm reservation if paid is verified
    if (newPaymentStatus === 'paid') {
      reservations.value[index].status = 'confirmed'
      window.dispatchEvent(new CustomEvent('miyabi-show-toast', { 
        detail: { message: `Pembayaran reservasi #${id} berhasil diverifikasi. Reservasi otomatis berstatus DIKONFIRMASI.`, type: 'success' } 
      }))
    } else {
      window.dispatchEvent(new CustomEvent('miyabi-show-toast', { 
        detail: { message: `Pembayaran reservasi #${id} DITOLAK.`, type: 'error' } 
      }))
    }
  }
}
</script>

<style scoped>
.admin-payments-page { display:flex; flex-direction:column; gap:1.5rem; }

.page-title { font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:600; color:var(--color-sumi-800); }
.page-subtitle { font-size:0.85rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; margin-top:2px; }

/* Filters */
.filters-card { padding:1.25rem 1.5rem; border-radius:4px; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
.search-box { position:relative; flex:1; min-width:280px; }
.search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); font-size:0.9rem; color:var(--color-sumi-600); }
.search-input { padding-left:2.5rem; }
.filter-options { display:flex; gap:0.75rem; }
.select-filter { width:190px; }

/* Table elements */
.table-card { padding:0.5rem; border-radius:4px; }
.guest-name { font-family:'Inter',sans-serif; font-weight:600; color:var(--color-sumi-800); font-size:0.9rem; }
.booking-date { font-size:0.68rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; }
.room-name { font-family:'Cormorant Garamond',serif; font-size:1.15rem; font-weight:600; color:var(--color-sumi-800); }

.pay-method-badge { font-size:0.72rem; color:var(--color-ai-800); background:rgba(44,74,124,0.08); padding:0.25rem 0.5rem; border-radius:3px; font-family:'Inter',sans-serif; font-weight:500; }

.btn-proof { padding:0.35rem 0.65rem; font-size:0.72rem; font-family:'Inter',sans-serif; font-weight:500; border-radius:3px; cursor:pointer; border:1px solid rgba(201,168,76,0.3); background:transparent; color:var(--color-kin-700); transition:0.2s; }
.btn-proof:hover { background:rgba(201,168,76,0.08); }
.btn-proof.has-proof { background:rgba(201,168,76,0.06); font-weight:600; }

.action-buttons-cell { display:flex; gap:0.35rem; }
.btn-action { padding:0.25rem 0.6rem; font-size:0.72rem; border-radius:3px; cursor:pointer; font-family:'Inter',sans-serif; transition:0.2s; border:1px solid transparent; background:transparent; }
.approve-btn { color:var(--color-matcha-700); border-color:rgba(74,124,89,0.3); }
.approve-btn:hover { background:var(--color-matcha-500); color:#fff; }
.cancel-btn { color:var(--color-beni-600); border-color:rgba(155,35,53,0.3); }
.cancel-btn:hover { background:var(--color-beni-600); color:#fff; }

/* Bank Slip visual simulation */
.bank-slip {
  background:#fff; border:1px solid rgba(26,26,46,0.12); padding:1.5rem; border-radius:8px;
  box-shadow:0 8px 30px rgba(0,0,0,0.05); text-align:left; font-family:'Courier New',Courier,monospace;
  color:#1A1A2E; margin:1rem 0;
}
.slip-header { display:flex; flex-direction:column; align-items:center; text-align:center; font-weight:bold; font-size:0.85rem; margin-bottom:1rem; }
.slip-bank { color:#1A2E52; }
.slip-success { color:var(--color-matcha-600); font-size:0.72rem; margin-top:2px; }
.slip-divider { border-bottom:1px dashed #A0B8D8; margin-bottom:1rem; }
.slip-row { display:flex; justify-content:space-between; font-size:0.75rem; margin-bottom:0.6rem; }
.sr-label { color:#8B8B8B; font-weight:bold; }
.sr-val { font-weight:bold; text-align:right; }
.slip-amount { font-size:0.9rem; color:var(--color-beni-600); }
.slip-footer { border-top:1px dashed #A0B8D8; text-align:center; font-size:0.65rem; color:#8B8B8B; padding-top:0.75rem; margin-top:1rem; font-weight:bold; }

/* Modals */
.modal-header { display:flex; justify-content:space-between; align-items:flex-start; padding:1.5rem; border-bottom:1px solid rgba(201,168,76,0.2); }
.modal-title { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--color-sumi-800); }
.modal-subtitle { font-family:'Noto Serif JP',serif; font-size:0.7rem; color:var(--color-kin-500); letter-spacing:0.1em; }
.modal-close { width:32px; height:32px; border:none; background:transparent; cursor:pointer; font-size:1rem; color:var(--color-sumi-600); border-radius:4px; transition:0.2s; }
.modal-close:hover { background:rgba(155,35,53,0.08); color:var(--color-beni-600); }
.modal-body { padding:1.5rem; }
.modal-footer { padding:1.25rem 1.5rem; border-top:1px solid rgba(201,168,76,0.2); display:flex; justify-content:flex-end; gap:0.75rem; }

@media(max-width:768px) {
  .filters-card { flex-direction:column; align-items:stretch; }
  .filter-options { flex-direction:column; }
  .select-filter { width:100%; }
}
</style>
