<template>
  <div class="reservations-page pattern-washi" style="min-height:100vh; padding:6rem 0 4rem;">
    <div class="container-zen">
      <div style="margin-bottom:2rem;">
        <p class="section-kicker">予約履歴</p>
        <h1 style="font-family:'Cormorant Garamond',serif; font-size:2.4rem; font-weight:600; color:var(--color-sumi-800);">Riwayat Reservasi Saya</h1>
      </div>

      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <button v-for="tab in tabs" :key="tab.key" class="tab-btn" :class="{ active: activeTab===tab.key }" @click="activeTab=tab.key">
          {{ tab.label }}
          <span class="tab-count">{{ countByStatus(tab.key) }}</span>
        </button>
      </div>

      <!-- Reservations Timeline -->
      <div class="reservations-list">
        <div v-if="filteredReservations.length === 0" class="empty-state">
          <div class="empty-kanji">無</div>
          <p style="font-family:'Cormorant Garamond',serif; font-size:1.2rem; color:var(--color-sumi-600);">Tidak ada reservasi untuk kategori ini</p>
        </div>

        <div v-for="res in filteredReservations" :key="res.id" class="reservation-item">
          <!-- Date block -->
          <div class="res-date-block" :class="`date-${res.status}`">
            <div class="res-month">{{ formatMonth(res.checkIn) }}</div>
            <div class="res-day">{{ formatDay(res.checkIn) }}</div>
            <div class="res-year">{{ formatYear(res.checkIn) }}</div>
          </div>

          <!-- Card -->
          <div class="res-card card-shoji">
            <div class="res-card-header">
              <div>
                <div class="res-booking-id">{{ res.id }}</div>
                <div class="res-room-name">{{ res.roomName }}</div>
              </div>
              <div class="res-badges">
                <span class="badge" :class="statusConfig[res.status]?.class">
                  <SvgIcon :name="statusConfig[res.status]?.icon" width="12" height="12" style="margin-right:2px;" /> {{ statusConfig[res.status]?.label }}
                </span>
                <span class="badge" :class="statusConfig[res.paymentStatus]?.class" style="font-size:0.68rem;">
                  {{ statusConfig[res.paymentStatus]?.label }}
                </span>
              </div>
            </div>

            <div class="res-details-row">
              <div class="res-detail">
                <span class="rd-label">Check-In</span>
                <span class="rd-val">{{ formatDate(res.checkIn) }}</span>
              </div>
              <div class="res-detail-sep">→</div>
              <div class="res-detail">
                <span class="rd-label">Check-Out</span>
                <span class="rd-val">{{ formatDate(res.checkOut) }}</span>
              </div>
              <div class="res-detail">
                <span class="rd-label">Malam</span>
                <span class="rd-val">{{ res.nights }} Malam</span>
              </div>
              <div class="res-detail">
                <span class="rd-label">Tamu</span>
                <span class="rd-val">{{ res.guests }} Orang</span>
              </div>
              <div class="res-detail res-total">
                <span class="rd-label">Total</span>
                <span class="rd-val total-val">{{ formatCurrency(res.totalPrice) }}</span>
              </div>
            </div>

            <div class="res-card-footer">
              <div style="font-size:0.72rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif;">
                Dipesan: {{ formatDate(res.bookingDate) }}
              </div>
              <div class="res-actions">
                <button class="res-btn btn-detail" @click="openDetail(res)">Lihat Detail</button>
                <button v-if="res.paymentStatus==='paid' || res.status==='confirmed' || res.status==='completed'" class="res-btn btn-invoice" @click="openInvoice(res)">Cetak Nota</button>
                <button v-if="res.status==='pending'" class="res-btn btn-cancel" @click="openCancel(res)">Batalkan</button>
                <Link v-if="res.status==='completed'" href="/akun/ulasan" class="res-btn btn-review">Beri Ulasan</Link>
                <Link v-if="res.status==='pending' && res.paymentStatus==='pending'" href="/booking/pembayaran" class="res-btn btn-pay">Bayar Sekarang</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <Transition name="modal">
      <div v-if="showDetail" class="modal-overlay" @click.self="showDetail=false">
        <div class="modal-content">
          <div class="modal-header">
            <div>
              <div class="modal-title">Detail Reservasi</div>
              <div class="modal-subtitle">{{ selectedRes?.id }}</div>
            </div>
            <button @click="showDetail=false" class="modal-close">✕</button>
          </div>
          <div class="modal-body" v-if="selectedRes">
            <div class="detail-grid">
              <div class="detail-field"><span class="df-label">Kamar</span><span class="df-val">{{ selectedRes.roomName }}</span></div>
              <div class="detail-field"><span class="df-label">Status</span><span class="badge" :class="statusConfig[selectedRes.status]?.class"><SvgIcon :name="statusConfig[selectedRes.status]?.icon" width="12" height="12" style="margin-right:2px;" />{{ statusConfig[selectedRes.status]?.label }}</span></div>
              <div class="detail-field"><span class="df-label">Check-In</span><span class="df-val">{{ formatDate(selectedRes.checkIn) }}</span></div>
              <div class="detail-field"><span class="df-label">Check-Out</span><span class="df-val">{{ formatDate(selectedRes.checkOut) }}</span></div>
              <div class="detail-field"><span class="df-label">Durasi</span><span class="df-val">{{ selectedRes.nights }} Malam</span></div>
              <div class="detail-field"><span class="df-label">Tamu</span><span class="df-val">{{ selectedRes.guests }} Orang</span></div>
              <div class="detail-field"><span class="df-label">Pembayaran</span><span class="badge" :class="statusConfig[selectedRes.paymentStatus]?.class"><SvgIcon :name="statusConfig[selectedRes.paymentStatus]?.icon" width="12" height="12" style="margin-right:2px;" />{{ statusConfig[selectedRes.paymentStatus]?.label }}</span></div>
              <div class="detail-field"><span class="df-label">Total Tagihan</span><span class="df-val total-val">{{ formatCurrency(selectedRes.totalPrice) }}</span></div>
            </div>
          </div>
          <div class="modal-footer">
            <button v-if="selectedRes && (selectedRes.paymentStatus==='paid' || selectedRes.status==='confirmed' || selectedRes.status==='completed')" class="btn-vermillion" @click="openInvoice(selectedRes)">🖨️ Cetak Nota</button>
            <button class="btn-outline-gold" @click="showDetail=false">Tutup</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Cancel Confirm Modal -->
    <Transition name="modal">
      <div v-if="showCancel" class="modal-overlay" @click.self="showCancel=false">
        <div class="modal-content" style="max-width:400px;">
          <div class="modal-header">
            <div class="modal-title">Batalkan Reservasi</div>
            <button @click="showCancel=false" class="modal-close">✕</button>
          </div>
          <div class="modal-body" style="text-align:center; padding:2rem;">
            <div style="margin-bottom:1rem;"><SvgIcon name="warning" width="48" height="48" class="text-beni" /></div>
            <p style="color:var(--color-sumi-700); font-family:'Cormorant Garamond',serif; font-size:1.1rem;">Apakah Anda yakin ingin membatalkan reservasi <strong>{{ selectedRes?.id }}</strong>?</p>
            <p style="font-size:0.82rem; color:var(--color-sumi-600); margin-top:0.75rem; font-family:'Inter',sans-serif;">Kebijakan pembatalan berlaku. Pengembalian dana diproses 3-7 hari kerja.</p>
          </div>
          <div class="modal-footer">
            <button class="btn-outline-gold" @click="showCancel=false">Tidak, Kembali</button>
            <button class="btn-vermillion" @click="confirmCancel">Ya, Batalkan</button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Invoice / Nota / Ryōshūsho Modal -->
    <Transition name="modal">
      <div v-if="showInvoiceModal" class="modal-overlay" @click.self="closeInvoice">
        <div class="modal-content print-invoice-content" style="max-width:550px;">
          <div class="modal-header no-print">
            <div>
              <div class="modal-title">Nota Bukti Reservasi</div>
              <div class="modal-subtitle">DARMA MIZUKI OMOTENASHI INVOICE</div>
            </div>
            <button @click="closeInvoice" class="modal-close">✕</button>
          </div>

          <div class="modal-body" id="invoice-print-area">
            <!-- Japanese Receipt border frame -->
            <div class="japanese-invoice-frame" v-if="selectedRes">
              <!-- Top Header -->
              <div class="inv-header">
                <div class="inv-logo-kanji">阿</div>
                <div class="inv-title-jp">領収書</div>
                <div class="inv-title-en">RECEIPT / INVOICE</div>
              </div>

              <div class="inv-booking-ref">
                <div>No. Nota: <strong>INV/{{ selectedRes.id }}</strong></div>
                <div>Tanggal Cetak: {{ formatTodayDate() }}</div>
              </div>

              <!-- Guest Name greeting -->
              <div class="inv-recipient">
                Tuan / Nyonya: <strong class="inv-guest-name">{{ guestName }}</strong>
              </div>

              <div class="inv-divider"></div>

              <!-- Items Table -->
              <table class="inv-table">
                <thead>
                  <tr>
                    <th>Detail Kunjungan</th>
                    <th style="text-align:right;">Jumlah</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <strong>Kamar: {{ selectedRes.roomName }}</strong><br/>
                      <span class="inv-sub-desc">Check-in: {{ formatDate(selectedRes.checkIn) }} · Check-out: {{ formatDate(selectedRes.checkOut) }} ({{ selectedRes.nights }} Malam)</span>
                    </td>
                    <td style="text-align:right; font-weight:500;">{{ formatCurrency(selectedRes.totalPrice) }}</td>
                  </tr>
                  <tr>
                    <td>Pajak Hotel (11%)</td>
                    <td style="text-align:right;">{{ formatCurrency(selectedRes.totalPrice * 0.11) }}</td>
                  </tr>
                  <tr>
                    <td>Biaya Layanan Omotenashi (5%)</td>
                    <td style="text-align:right;">{{ formatCurrency(selectedRes.totalPrice * 0.05) }}</td>
                  </tr>
                  <tr class="total-row-bg">
                    <td><strong>TOTAL TAGIHAN (JUMLAH NETT)</strong></td>
                    <td style="text-align:right; font-weight:700; color:var(--color-beni-600);">{{ formatCurrency(selectedRes.totalPrice * 1.16) }}</td>
                  </tr>
                </tbody>
              </table>

              <!-- Stamp & Signature Block -->
              <div class="inv-bottom-row">
                <div class="inv-hotel-details">
                  <strong>DARMA MIZUKI</strong><br/>
                  アルニカ 旅館 • Omotenashi Luxury<br/>
                  Jl. Sakura Indah No. 88, Jakarta Selatan<br/>
                  hello@darmamizuki.com
                </div>
                <div class="inv-stamp-area">
                  <!-- Red Japanese Stamp -->
                  <div class="japanese-red-stamp">
                    <span class="stamp-kanji">領収</span>
                  </div>
                  <span class="stamp-label">TERBAYAR / PAID</span>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer no-print">
            <button class="btn-outline-gold" @click="printInvoice">🖨️ Cetak Nota</button>
            <button class="btn-vermillion" @click="closeInvoice">Selesai & Kembali</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import SvgIcon from '@/Components/UI/SvgIcon.vue'
import { formatCurrency, formatDate, statusConfig } from '@/data/mockData'

const props = defineProps({
  reservations: {
    type: Array,
    default: () => []
  }
})

defineOptions({ layout: AppLayout })

const activeTab = ref('semua')
const showDetail = ref(false)
const showCancel = ref(false)
const showInvoiceModal = ref(false)
const selectedRes = ref(null)
const guestName = ref('Tamu Premium')
const guestEmail = ref('tamu@darmamizuki.com')

onMounted(() => {
  const storedUser = localStorage.getItem('miyabi_user')
  if (storedUser) {
    const user = JSON.parse(storedUser)
    guestName.value = user.name
    guestEmail.value = user.email || 'tamu@darmamizuki.com'
  }
})

const tabs = [
  { key:'semua', label:'Semua' },
  { key:'pending', label:'Menunggu' },
  { key:'confirmed', label:'Dikonfirmasi' },
  { key:'completed', label:'Selesai' },
  { key:'cancelled', label:'Dibatalkan' },
]

const filteredReservations = computed(() =>
  activeTab.value === 'semua' ? props.reservations : props.reservations.filter(r => r.status === activeTab.value)
)

const countByStatus = (key) => key === 'semua' ? props.reservations.length : props.reservations.filter(r => r.status === key).length

const formatMonth = (d) => new Date(d).toLocaleDateString('id-ID', { month:'short' }).toUpperCase()
const formatDay = (d) => new Date(d).getDate()
const formatYear = (d) => new Date(d).getFullYear()

const openDetail = (res) => { selectedRes.value = res; showDetail.value = true }
const openCancel = (res) => { selectedRes.value = res; showCancel.value = true }
const confirmCancel = () => { 
  showCancel.value = false; 
  window.dispatchEvent(new CustomEvent('miyabi-show-toast', { 
    detail: { message: 'Reservasi berhasil dibatalkan.', type: 'success' } 
  }))
}

const openInvoice = (res) => {
  selectedRes.value = res
  showDetail.value = false
  showInvoiceModal.value = true
}
const closeInvoice = () => {
  showInvoiceModal.value = false
}
const printInvoice = () => {
  window.print()
}
const formatTodayDate = () => {
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  }).format(new Date())
}
</script>

<style scoped>
.section-kicker { font-family:'Noto Serif JP',serif; font-size:0.8rem; letter-spacing:0.2em; color:var(--color-beni-500); margin-bottom:0.5rem; display:block; }
.filter-tabs { display:flex; gap:0.4rem; margin-bottom:2rem; border-bottom:2px solid rgba(201,168,76,0.15); padding-bottom:0; flex-wrap:wrap; }
.tab-btn { display:flex; align-items:center; gap:0.4rem; padding:0.6rem 1.25rem; background:transparent; border:none; cursor:pointer; font-family:'Noto Serif JP',serif; font-size:0.82rem; color:var(--color-sumi-600); border-bottom:2px solid transparent; margin-bottom:-2px; transition:0.2s; }
.tab-btn:hover { color:var(--color-beni-600); }
.tab-btn.active { color:var(--color-beni-600); border-bottom-color:var(--color-beni-600); }
.tab-count { background:rgba(155,35,53,0.1); color:var(--color-beni-600); border-radius:999px; padding:0.05rem 0.45rem; font-size:0.68rem; min-width:18px; text-align:center; }

.reservations-list { display:flex; flex-direction:column; gap:1.25rem; }
.reservation-item { display:flex; gap:1.25rem; align-items:stretch; }

.res-date-block { width:80px; flex-shrink:0; border-radius:4px; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:1rem 0.5rem; text-align:center; }
.date-confirmed { background:var(--color-matcha-700); color:#fff; }
.date-pending { background:var(--color-kin-700); color:#fff; }
.date-completed { background:var(--color-ai-600); color:#fff; }
.date-cancelled { background:#6B3333; color:#fff; }
.res-month { font-family:'Noto Serif JP',serif; font-size:0.62rem; letter-spacing:0.1em; opacity:0.8; }
.res-day { font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:700; line-height:1; }
.res-year { font-family:'Inter',sans-serif; font-size:0.6rem; opacity:0.7; }

.res-card { flex:1; padding:1.25rem 1.5rem; border-radius:4px; }
.res-card-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem; }
.res-booking-id { font-family:'Inter',sans-serif; font-size:0.72rem; color:var(--color-kin-500); letter-spacing:0.1em; font-weight:600; }
.res-room-name { font-family:'Cormorant Garamond',serif; font-size:1.2rem; font-weight:600; color:var(--color-sumi-800); }
.res-badges { display:flex; flex-direction:column; gap:0.25rem; align-items:flex-end; }
.res-details-row { display:flex; gap:2rem; flex-wrap:wrap; margin-bottom:0.75rem; }
.res-detail { display:flex; flex-direction:column; gap:2px; }
.rd-label { font-size:0.65rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; letter-spacing:0.06em; }
.rd-val { font-size:0.85rem; color:var(--color-sumi-800); font-weight:500; font-family:'Inter',sans-serif; }
.res-total {}
.total-val { font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:700; color:var(--color-beni-600) !important; }
.res-card-footer { display:flex; justify-content:space-between; align-items:center; border-top:1px solid rgba(201,168,76,0.15); padding-top:0.75rem; }
.res-actions { display:flex; gap:0.5rem; flex-wrap:wrap; }
.res-btn { padding:0.35rem 0.85rem; border-radius:3px; font-size:0.75rem; font-family:'Inter',sans-serif; cursor:pointer; transition:0.2s; text-decoration:none; display:inline-flex; align-items:center; }
.btn-detail { border:1px solid rgba(201,168,76,0.3); color:var(--color-kin-500); background:transparent; }
.btn-detail:hover { background:rgba(201,168,76,0.08); }
.btn-cancel { border:1px solid rgba(155,35,53,0.3); color:var(--color-beni-600); background:transparent; }
.btn-cancel:hover { background:var(--color-beni-600); color:#fff; }
.btn-review { border:1px solid rgba(74,124,89,0.3); color:var(--color-matcha-600,#3D6B4E); background:transparent; }
.btn-review:hover { background:var(--color-matcha-500); color:#fff; }
.btn-pay { background:var(--color-beni-600); color:#fff; border:none; }
.btn-pay:hover { background:var(--color-beni-700); }
.btn-invoice { border:1px solid rgba(201,168,76,0.3); color:var(--color-kin-500); background:transparent; }
.btn-invoice:hover { background:var(--color-kin-500); color:#fff; }

.empty-state { text-align:center; padding:4rem 0; }
.empty-kanji { font-family:'Noto Serif JP',serif; font-size:5rem; color:var(--color-kin-500); opacity:0.2; margin-bottom:1rem; }

/* Modal */
.modal-header { display:flex; justify-content:space-between; align-items:flex-start; padding:1.5rem; border-bottom:1px solid rgba(201,168,76,0.2); }
.modal-title { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--color-sumi-800); }
.modal-subtitle { font-family:'Noto Serif JP',serif; font-size:0.7rem; color:var(--color-kin-500); letter-spacing:0.1em; }
.modal-close { width:32px; height:32px; border:none; background:transparent; cursor:pointer; font-size:1rem; color:var(--color-sumi-600); border-radius:4px; transition:0.2s; }
.modal-close:hover { background:rgba(155,35,53,0.08); color:var(--color-beni-600); }
.modal-body { padding:1.5rem; }
.modal-footer { padding:1rem 1.5rem; border-top:1px solid rgba(201,168,76,0.2); display:flex; justify-content:flex-end; gap:0.75rem; }
.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.detail-field { display:flex; flex-direction:column; gap:4px; }
.df-label { font-size:0.68rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; letter-spacing:0.06em; }
.df-val { font-size:0.9rem; color:var(--color-sumi-800); font-weight:500; font-family:'Inter',sans-serif; }

/* Japanese Invoice styling */
.japanese-invoice-frame {
  background: #fff;
  border: 4px double var(--color-beni-600);
  border-radius: 4px;
  padding: 2.25rem 2rem;
  color: var(--color-sumi-800);
  box-shadow: 0 4px 30px rgba(0,0,0,0.06);
  font-family: 'Inter', sans-serif;
  position: relative;
}
.inv-header {
  text-align: center;
  margin-bottom: 2rem;
}
.inv-logo-kanji {
  font-family: 'Noto Serif JP', serif;
  font-size: 2.4rem;
  font-weight: 700;
  color: var(--color-beni-600);
  line-height: 1;
}
.inv-title-jp {
  font-family: 'Noto Serif JP', serif;
  font-size: 1.5rem;
  letter-spacing: 0.25em;
  font-weight: 600;
  margin-top: 0.5rem;
  color: var(--color-sumi-800);
  border-bottom: 2px solid var(--color-sumi-800);
  display: inline-block;
  padding: 0 1rem 0.25rem;
}
.inv-title-en {
  font-size: 0.65rem;
  letter-spacing: 0.18em;
  color: var(--color-kin-700);
  font-weight: 600;
  margin-top: 4px;
}
.inv-booking-ref {
  display: flex;
  justify-content: space-between;
  font-size: 0.76rem;
  color: var(--color-sumi-600);
  margin-bottom: 1.25rem;
}
.inv-recipient {
  font-family: 'Noto Serif JP', serif;
  font-size: 0.9rem;
  color: var(--color-sumi-800);
  border-bottom: 1px solid var(--color-washi-900);
  padding-bottom: 0.5rem;
  margin-bottom: 1.5rem;
}
.inv-guest-name {
  font-size: 1.05rem;
  text-transform: uppercase;
}
.inv-divider {
  height: 1px;
  background: var(--color-washi-900);
  margin: 1.25rem 0;
}
.inv-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.82rem;
  margin-bottom: 2rem;
}
.inv-table th {
  border-bottom: 2px solid var(--color-sumi-800);
  padding: 0.6rem 0.4rem;
  text-align: left;
  font-weight: 600;
}
.inv-table td {
  padding: 0.85rem 0.4rem;
  border-bottom: 1px solid rgba(26,26,46,0.1);
  vertical-align: top;
}
.inv-sub-desc {
  font-size: 0.72rem;
  color: var(--color-sumi-600);
}
.total-row-bg {
  background: rgba(201,168,76,0.06);
}
.total-row-bg td {
  border-top: 2px solid var(--color-sumi-800);
  border-bottom: 2px solid var(--color-sumi-800);
  font-size: 0.88rem;
  padding: 0.9rem 0.4rem;
}
.inv-bottom-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-top: 2rem;
}
.inv-hotel-details {
  font-size: 0.72rem;
  color: var(--color-sumi-600);
  line-height: 1.5;
}
.inv-stamp-area {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.35rem;
}
.stamp-label {
  font-size: 0.62rem;
  color: var(--color-matcha-600);
  font-weight: 700;
  letter-spacing: 0.04em;
}

/* Traditional Red Japanese Stamp */
.japanese-red-stamp {
  width: 58px;
  height: 58px;
  border: 2px solid rgba(155, 35, 53, 0.85);
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(155, 35, 53, 0.85);
  transform: rotate(-6deg);
}
.stamp-kanji {
  font-family: 'Noto Serif JP', serif;
  font-size: 1.15rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  line-height: 1.1;
  text-align: center;
  border: 1px solid rgba(155, 35, 53, 0.5);
  padding: 2px 4px;
}

/* Print CSS */
@media print {
  body * {
    visibility: hidden;
  }
  #invoice-print-area, #invoice-print-area * {
    visibility: visible;
  }
  #invoice-print-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  .no-print {
    display: none !important;
  }
}

.modal-enter-active, .modal-leave-active { transition:0.3s; }
.modal-enter-from, .modal-leave-to { opacity:0; }
.modal-enter-from .modal-content, .modal-leave-to .modal-content { transform:scale(0.95) translateY(20px); }

@media(max-width:700px) { .reservation-item { flex-direction:column; } .res-date-block { width:auto; flex-direction:row; gap:0.5rem; } .res-details-row { gap:1rem; } }
</style>
