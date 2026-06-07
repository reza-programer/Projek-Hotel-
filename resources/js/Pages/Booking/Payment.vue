<template>
  <div class="payment-page pattern-washi">
    <div class="container-zen" style="padding-top:6rem; padding-bottom:4rem;">
      <div style="text-align:center; margin-bottom:2rem;">
        <p class="section-kicker">お支払い</p>
        <h1 style="font-family:'Cormorant Garamond',serif; font-size:2.4rem; font-weight:600; color:var(--color-sumi-800);">Pembayaran</h1>
        <!-- Countdown -->
        <div class="countdown-bar">
          <span class="countdown-icon"><SvgIcon name="pending" width="14" height="14" /></span>
          <span>Selesaikan pembayaran dalam</span>
          <span class="countdown-timer">{{ countdownDisplay }}</span>
        </div>
      </div>

      <div class="payment-layout">
        <!-- Left: Order Summary -->
        <div class="order-summary card-dark">
          <div class="order-summary-header">
            <div class="os-kanji">雅</div>
            <div>
              <div class="os-title">Ringkasan Pesanan</div>
              <div class="os-subtitle">予約サマリー</div>
            </div>
          </div>
          <div class="os-booking-id">Kode: <strong>{{ reservation.booking_code }}</strong></div>
          <div class="os-divider"/>
          <div class="os-room">
            <div class="os-room-img" style="background:linear-gradient(135deg,#2E0E0E,#B83347)">
              <span style="font-family:'Noto Serif JP',serif; font-size:1.5rem; color:rgba(255,255,255,0.2);">紅</span>
            </div>
            <div>
              <div class="os-room-name">{{ reservation.room.name }}</div>
              <div class="os-room-jp">{{ reservation.room.name_jp }}</div>
              <div class="os-room-type">{{ reservation.room.type }} · Max {{ reservation.room.capacity }} Tamu</div>
            </div>
          </div>
          <div class="os-divider"/>
          <div class="os-detail-row"><span>Check-In</span><span>{{ formatDate(reservation.check_in) }}</span></div>
          <div class="os-detail-row"><span>Check-Out</span><span>{{ formatDate(reservation.check_out) }}</span></div>
          <div class="os-detail-row"><span>Durasi</span><span>{{ reservation.nights }} Malam</span></div>
          <div class="os-detail-row"><span>Tamu</span><span>{{ reservation.guests }} Dewasa</span></div>
          <div class="os-divider"/>
          <div class="os-detail-row"><span>Harga Kamar</span><span>Rp {{ formatPrice(reservation.room.price) }}/mlm</span></div>
          <div class="os-detail-row"><span>Subtotal ({{ reservation.nights }} mlm)</span><span>Rp {{ formatPrice(reservation.total_price) }}</span></div>
          <div class="os-detail-row"><span>Pajak (11%)</span><span>Rp {{ formatPrice(reservation.total_price * 0.11) }}</span></div>
          <div class="os-detail-row"><span>Layanan (5%)</span><span>Rp {{ formatPrice(reservation.total_price * 0.05) }}</span></div>
          <div class="os-total-row"><span>Total</span><span>Rp {{ formatPrice(totalGrossAmount) }}</span></div>
          <div class="os-note">✦ Harga sudah termasuk pajak dan biaya layanan</div>
        </div>

        <!-- Right: Payment Form -->
        <div class="payment-form-area">
          <div class="payment-card card-shoji" style="padding:2.5rem 1.75rem; margin-bottom:1.25rem; text-align:center;">
            <div style="margin-bottom:1.5rem;">
              <SvgIcon name="completed" width="48" height="48" class="text-kin" style="margin: 0 auto; display:block;" />
            </div>
            <h3 class="payment-section-title">Pembayaran Aman <span class="pay-jp">安全な支払い</span></h3>
            <p style="font-size:0.85rem; color:var(--color-sumi-600); line-height:1.7; margin-bottom:1.5rem;">
              Anda akan diarahkan ke sistem pembayaran aman Midtrans. Anda dapat memilih berbagai metode pembayaran seperti Virtual Account, Kartu Kredit, GoPay, ShopeePay, atau QRIS.
            </p>
            
            <div style="background:rgba(201,168,76,0.05); border:1px dashed rgba(201,168,76,0.4); padding:1rem; border-radius:4px; margin-bottom:1.5rem;">
               <div style="display:flex; justify-content:center; gap:0.5rem; align-items:center;">
                 <span style="font-size:0.75rem; font-weight:600; color:var(--color-sumi-800); font-family:'Inter', sans-serif; opacity: 0.7;">Powered by</span>
                 <strong style="font-family:'Inter', sans-serif; color:#0F4C81; font-size:1.1rem; font-weight:800; letter-spacing: -0.5px;">midtrans</strong>
               </div>
            </div>

            <!-- Submit -->
            <button class="btn-vermillion confirm-pay-btn" @click="submitPayment" :disabled="submitting">
              <span v-if="!submitting">Lanjutkan ke Pembayaran</span>
              <span v-else>⏳ Membuka Sistem Pembayaran...</span>
            </button>
            <p style="text-align:center; font-size:0.75rem; color:var(--color-sumi-600); margin-top:0.75rem; font-family:'Noto Serif JP',serif; display:flex; align-items:center; justify-content:center; gap:0.25rem;">
              <SvgIcon name="warning" width="11" height="11" /> Transaksi ini dilindungi enkripsi kelas bank
            </p>
          </div>
        </div>
      </div>
    </div>

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
            <div class="japanese-invoice-frame">
              <!-- Top Header -->
              <div class="inv-header">
                <div class="inv-logo-kanji">雅</div>
                <div class="inv-title-jp">領収書</div>
                <div class="inv-title-en">RECEIPT / INVOICE</div>
              </div>

              <div class="inv-booking-ref">
                <div>No. Nota: <strong>INV/MYB-{{ bookingCode }}</strong></div>
                <div>Tanggal: {{ formatTodayDate() }}</div>
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
                      <strong>Kamar: Bamboo Deluxe (Deluxe Room)</strong><br/>
                      <span class="inv-sub-desc">Check-in: 10 Juni 2025 · Check-out: 13 Juni 2025 (3 Malam)</span>
                    </td>
                    <td style="text-align:right; font-weight:500;">Rp 4.050.000</td>
                  </tr>
                  <tr>
                    <td>Pajak Hotel (11%)</td>
                    <td style="text-align:right;">Rp 445.500</td>
                  </tr>
                  <tr>
                    <td>Biaya Layanan Omotenashi (5%)</td>
                    <td style="text-align:right;">Rp 202.500</td>
                  </tr>
                  <tr class="total-row-bg">
                    <td><strong>TOTAL TAGIHAN (JUMLAH NETT)</strong></td>
                    <td style="text-align:right; font-weight:700; color:var(--color-beni-600);">Rp 4.698.000</td>
                  </tr>
                </tbody>
              </table>

              <!-- Stamp & Signature Block -->
              <div class="inv-bottom-row">
                <div class="inv-hotel-details">
                  <strong>DARMA MIZUKI</strong><br/>
                  雅ホテル • Omotenashi Luxury<br/>
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import SvgIcon from '@/Components/UI/SvgIcon.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  reservation: {
    type: Object,
    required: true
  }
})

const submitting = ref(false)
const showInvoiceModal = ref(false)
const guestName = ref('Tamu Premium')

// Countdown: 24 hours
const totalSeconds = ref(24 * 60 * 60)
let timer
onMounted(() => { 
  timer = setInterval(() => { if (totalSeconds.value > 0) totalSeconds.value-- }, 1000)
  
  // Load local logged in user name
  const storedUser = localStorage.getItem('miyabi_user')
  if (storedUser) {
    const user = JSON.parse(storedUser)
    guestName.value = user.name
  }
})
onUnmounted(() => clearInterval(timer))
const countdownDisplay = computed(() => {
  const h = Math.floor(totalSeconds.value / 3600)
  const m = Math.floor((totalSeconds.value % 3600) / 60)
  const s = totalSeconds.value % 60
  return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`
})

const totalGrossAmount = computed(() => {
  const sub = props.reservation.total_price
  return sub + (sub * 0.11) + (sub * 0.05)
})

const formatPrice = (price) => {
  return Math.round(price).toLocaleString('id-ID')
}

const formatDate = (dateStr) => {
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(dateStr))
}

const submitPayment = async () => {
  submitting.value = true

  try {
    const response = await fetch('/api/midtrans/snap-token', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify({
        reservation_id: props.reservation.id
      })
    })

    const data = await response.json()

    if (data.snap_token) {
      window.snap.pay(data.snap_token, {
        onSuccess: async function(result) {
          // Panggil route local fallback agar email bisa dikirim meski tanpa webhook ngrok
          await fetch('/api/midtrans/local-success', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({ reservation_id: props.reservation.id, gross_amount: result.gross_amount })
          });

          submitting.value = false
          showInvoiceModal.value = true
        },
        onPending: function(result) {
          submitting.value = false
          alert('Menunggu pembayaran diselesaikan...')
        },
        onError: function(result) {
          submitting.value = false
          alert('Pembayaran gagal!')
        },
        onClose: function() {
          submitting.value = false
        }
      })
    } else {
      submitting.value = false
      alert('Gagal mendapatkan token pembayaran.')
    }
  } catch (error) {
    submitting.value = false
    console.error('Error fetching token:', error)
    alert('Terjadi kesalahan jaringan.')
  }
}

const closeInvoice = () => {
  showInvoiceModal.value = false
  window.location.href = '/akun/reservasi'
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
.payment-page { min-height:100vh; }
.section-kicker { font-family:'Noto Serif JP',serif; font-size:0.8rem; letter-spacing:0.2em; color:var(--color-beni-500); margin-bottom:0.5rem; display:block; }
.countdown-bar { display:inline-flex; align-items:center; gap:0.5rem; background:rgba(155,35,53,0.08); border:1px solid rgba(155,35,53,0.2); border-radius:999px; padding:0.4rem 1.25rem; margin-top:0.75rem; font-family:'Inter',sans-serif; font-size:0.82rem; color:var(--color-sumi-700); }
.countdown-icon { font-size:1rem; }
.countdown-timer { font-family:'Cormorant Garamond',serif; font-size:1.3rem; font-weight:600; color:var(--color-beni-600); letter-spacing:0.08em; }

.payment-layout { display:grid; grid-template-columns:380px 1fr; gap:2rem; align-items:start; }

/* Order Summary */
.order-summary { padding:1.75rem; border-radius:4px; color:var(--color-washi-300); }
.order-summary-header { display:flex; align-items:center; gap:1rem; margin-bottom:1rem; }
.os-kanji { font-family:'Noto Serif JP',serif; font-size:2.5rem; font-weight:700; color:var(--color-beni-500); line-height:1; }
.os-title { font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:600; color:#fff; }
.os-subtitle { font-family:'Noto Serif JP',serif; font-size:0.65rem; color:var(--color-kin-500); letter-spacing:0.15em; }
.os-booking-id { font-size:0.78rem; font-family:'Inter',sans-serif; color:var(--color-kin-300); margin-bottom:0.75rem; }
.os-booking-id strong { font-size:0.9rem; }
.os-divider { height:1px; background:rgba(201,168,76,0.15); margin:0.75rem 0; }
.os-room { display:flex; gap:0.75rem; align-items:center; margin-bottom:0.25rem; }
.os-room-img { width:60px; height:48px; border-radius:4px; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
.os-room-name { font-family:'Cormorant Garamond',serif; font-size:1.05rem; font-weight:600; color:#fff; }
.os-room-jp { font-family:'Noto Serif JP',serif; font-size:0.65rem; color:var(--color-kin-500); letter-spacing:0.1em; }
.os-room-type { font-size:0.72rem; color:rgba(247,243,237,0.5); font-family:'Inter',sans-serif; }
.os-detail-row { display:flex; justify-content:space-between; font-size:0.8rem; padding:0.25rem 0; color:rgba(247,243,237,0.7); font-family:'Inter',sans-serif; }
.os-total-row { display:flex; justify-content:space-between; font-family:'Cormorant Garamond',serif; font-size:1.2rem; font-weight:600; color:var(--color-kin-300); padding:0.6rem 0; border-top:1px solid rgba(201,168,76,0.2); margin-top:0.25rem; }
.os-note { font-size:0.68rem; color:rgba(201,168,76,0.5); font-family:'Noto Serif JP',serif; margin-top:0.5rem; letter-spacing:0.05em; }

/* Payment Methods */
.payment-section-title { font-family:'Cormorant Garamond',serif; font-size:1.2rem; font-weight:600; color:var(--color-sumi-800); margin-bottom:1rem; }
.pay-jp { font-family:'Noto Serif JP',serif; font-size:0.68rem; color:var(--color-kin-500); letter-spacing:0.12em; display:block; }
.payment-methods { display:flex; flex-direction:column; gap:0.5rem; }
.method-option { display:flex; align-items:center; gap:0.85rem; padding:0.85rem 1rem; border:1px solid rgba(201,168,76,0.2); border-radius:4px; cursor:pointer; transition:0.2s; }
.method-option.selected { border-color:var(--color-beni-500); background:rgba(155,35,53,0.04); }
.method-option:hover { background:rgba(201,168,76,0.04); }
.method-icon { font-size:1.4rem; flex-shrink:0; }
.method-info { flex:1; }
.method-name { font-family:'Inter',sans-serif; font-size:0.88rem; font-weight:500; color:var(--color-sumi-800); }
.method-desc { font-size:0.72rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; }
.method-radio { width:18px; height:18px; border-radius:50%; border:2px solid rgba(201,168,76,0.3); flex-shrink:0; transition:0.2s; }
.method-radio.active { background:var(--color-beni-600); border-color:var(--color-beni-600); }

/* Bank Details */
.bank-detail-card { padding:1.5rem; margin-bottom:1.25rem; background:rgba(255,249,240,0.9); }
.bank-detail-title { font-family:'Cormorant Garamond',serif; font-size:1rem; font-weight:600; color:var(--color-sumi-800); margin-bottom:1rem; }
.bank-info { display:flex; align-items:center; gap:1rem; background:var(--color-washi-300); padding:1rem; border-radius:4px; margin-bottom:0.75rem; }
.bank-logo { font-size:2rem; }
.bank-label { font-size:0.68rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; letter-spacing:0.06em; margin-bottom:2px; }
.bank-number { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:700; color:var(--color-sumi-800); letter-spacing:0.08em; }
.bank-name { font-size:0.78rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; }
.copy-btn { margin-left:auto; width:36px; height:36px; border:none; background:transparent; cursor:pointer; font-size:1.1rem; border-radius:4px; transition:0.2s; }
.copy-btn:hover { background:rgba(201,168,76,0.15); }
.bank-amount { background:rgba(155,35,53,0.06); border-left:3px solid var(--color-beni-500); padding:0.75rem 1rem; border-radius:0 4px 4px 0; margin-bottom:0.75rem; }
.bank-amount-val { font-family:'Cormorant Garamond',serif; font-size:1.5rem; font-weight:700; color:var(--color-beni-600); }
.bank-warning { font-size:0.75rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; line-height:1.5; }

/* Upload */
.upload-area { border:2px dashed rgba(201,168,76,0.4); border-radius:4px; padding:2rem; text-align:center; cursor:pointer; transition:0.3s; background:var(--color-washi-300); }
.upload-area:hover, .upload-area.has-file { border-color:var(--color-kin-500); background:rgba(201,168,76,0.05); }
.upload-preview { display:flex; flex-direction:column; align-items:center; gap:0.5rem; }
.remove-file { background:transparent; border:1px solid var(--color-beni-500); color:var(--color-beni-600); font-size:0.72rem; padding:0.25rem 0.75rem; border-radius:999px; cursor:pointer; transition:0.2s; }
.remove-file:hover { background:var(--color-beni-600); color:#fff; }

.confirm-pay-btn { width:100%; justify-content:center; padding:1.1rem; font-size:1rem; }
.confirm-pay-btn:disabled { opacity:0.5; cursor:not-allowed; transform:none !important; }

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

@media(max-width:900px) { .payment-layout { grid-template-columns:1fr; } }
</style>
