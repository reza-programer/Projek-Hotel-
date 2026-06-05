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
          <div class="os-booking-id">Kode: <strong>MYB-{{ bookingCode }}</strong></div>
          <div class="os-divider"/>
          <div class="os-room">
            <div class="os-room-img" style="background:linear-gradient(135deg,#2E0E0E,#B83347)">
              <span style="font-family:'Noto Serif JP',serif; font-size:1.5rem; color:rgba(255,255,255,0.2);">紅</span>
            </div>
            <div>
              <div class="os-room-name">Bamboo Deluxe</div>
              <div class="os-room-jp">竹デラックス</div>
              <div class="os-room-type">Deluxe · 38m² · Max 2 Tamu</div>
            </div>
          </div>
          <div class="os-divider"/>
          <div class="os-detail-row"><span>Check-In</span><span>10 Juni 2025</span></div>
          <div class="os-detail-row"><span>Check-Out</span><span>13 Juni 2025</span></div>
          <div class="os-detail-row"><span>Durasi</span><span>3 Malam</span></div>
          <div class="os-detail-row"><span>Tamu</span><span>2 Dewasa</span></div>
          <div class="os-divider"/>
          <div class="os-detail-row"><span>Harga Kamar</span><span>Rp 1.350.000/mlm</span></div>
          <div class="os-detail-row"><span>Subtotal (3 mlm)</span><span>Rp 4.050.000</span></div>
          <div class="os-detail-row"><span>Pajak (11%)</span><span>Rp 445.500</span></div>
          <div class="os-detail-row"><span>Layanan (5%)</span><span>Rp 202.500</span></div>
          <div class="os-total-row"><span>Total</span><span>Rp 4.698.000</span></div>
          <div class="os-note">✦ Harga sudah termasuk pajak dan biaya layanan</div>
        </div>

        <!-- Right: Payment Form -->
        <div class="payment-form-area">
          <!-- Method Selection -->
          <div class="payment-card card-shoji" style="padding:1.75rem; margin-bottom:1.25rem;">
            <h3 class="payment-section-title">Metode Pembayaran <span class="pay-jp">支払い方法</span></h3>
            <div class="payment-methods">
              <label v-for="m in paymentMethods" :key="m.id" class="method-option" :class="{ selected: selectedMethod===m.id }">
                <input type="radio" :value="m.id" v-model="selectedMethod" style="display:none;"/>
                <div class="method-icon"><img :src="m.logoUrl" :alt="m.name" style="width: 48px; height: auto; max-height: 24px; object-fit: contain;" /></div>
                <div class="method-info">
                  <div class="method-name">{{ m.name }}</div>
                  <div class="method-desc">{{ m.desc }}</div>
                </div>
                <div class="method-radio" :class="{ active: selectedMethod===m.id }"/>
              </label>
            </div>
          </div>

          <!-- Bank Details (if transfer) -->
          <div v-if="currentMethod && currentMethod.accountNumber" class="payment-card card-shoji bank-detail-card">
            <h4 class="bank-detail-title">{{ currentMethod.name }}</h4>
            <div class="bank-info">
              <div class="bank-logo"><img :src="currentMethod.logoUrl" :alt="currentMethod.name" style="width: 80px; height: auto; max-height: 40px; object-fit: contain;" /></div>
              <div>
                <div class="bank-label">Nomor Rekening</div>
                <div class="bank-number">{{ currentMethod.accountNumber }}</div>
                <div class="bank-name">{{ currentMethod.accountName }}</div>
              </div>
              <button class="copy-btn" @click="copyAccount" title="Salin nomor rekening">
                <SvgIcon name="completed" width="14" height="14" />
              </button>
            </div>
            <div class="bank-amount">
              <div class="bank-label">Jumlah Transfer (Tepat)</div>
              <div class="bank-amount-val">Rp 4.698.000</div>
            </div>
            <div class="bank-warning" style="display:flex; align-items:flex-start; gap:0.4rem;">
              <SvgIcon name="warning" width="14" height="14" class="text-beni" style="margin-top:2px;" />
              <span>Transfer harus sesuai nominal persis. Pembayaran diproses dalam 1×24 jam kerja.</span>
            </div>
          </div>

          <!-- Upload Bukti -->
          <div class="payment-card card-shoji" style="padding:1.75rem; margin-bottom:1.25rem;">
            <h3 class="payment-section-title">Upload Bukti Pembayaran <span class="pay-jp">支払い証明</span></h3>
            <div class="upload-area" :class="{ 'has-file': uploadedFile }" @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop">
              <input ref="fileInput" type="file" accept="image/*,.pdf" style="display:none;" @change="handleFile"/>
              <div v-if="!uploadedFile">
                <div style="margin-bottom:0.5rem;"><SvgIcon name="completed" width="36" height="36" class="text-kin" /></div>
                <div style="font-family:'Cormorant Garamond',serif; font-size:1.1rem; color:var(--color-sumi-700);">Klik atau seret file di sini</div>
                <div style="font-size:0.78rem; color:var(--color-sumi-600); margin-top:0.25rem;">PNG, JPG, PDF · Maks 5MB</div>
              </div>
              <div v-else class="upload-preview">
                <div><SvgIcon name="confirmed" width="32" height="32" class="text-matcha" /></div>
                <div style="font-size:0.85rem; color:var(--color-matcha-600,#3D6B4E); font-weight:500;">{{ uploadedFile.name }}</div>
                <button @click.stop="uploadedFile=null" class="remove-file">Hapus</button>
              </div>
            </div>
            <div>
              <label class="label-zen" style="margin-top:1rem; display:block;">Catatan (Opsional)</label>
              <textarea class="input-zen" rows="2" v-model="notes" placeholder="Nama pengirim, catatan transfer..." style="resize:vertical;"/>
            </div>
          </div>

          <!-- Submit -->
          <button class="btn-vermillion confirm-pay-btn" @click="submitPayment" :disabled="!canSubmit">
            <span v-if="!submitting">Konfirmasi Pembayaran</span>
            <span v-else>⏳ Memproses...</span>
          </button>
          <p style="text-align:center; font-size:0.75rem; color:var(--color-sumi-600); margin-top:0.75rem; font-family:'Noto Serif JP',serif; display:flex; align-items:center; justify-content:center; gap:0.25rem;">
            <SvgIcon name="key" width="11" height="11" /> Transaksi ini dilindungi dengan enkripsi SSL 256-bit
          </p>
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
              <div class="modal-subtitle">MIYABI OMOTENASHI INVOICE</div>
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
                  <strong>MIYABI HOTEL</strong><br/>
                  雅ホテル • Omotenashi Luxury<br/>
                  Jl. Sakura Indah No. 88, Jakarta Selatan<br/>
                  hello@miyabihotel.com
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

const bookingCode = Math.floor(Math.random() * 9000 + 1000)
const selectedMethod = ref('bca')
const uploadedFile = ref(null)
const notes = ref('')
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

const paymentMethods = [
  { id:'bca', name:'Transfer Bank BCA', desc:'Bank Central Asia', logoUrl:'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="80" height="40" viewBox="0 0 80 40"><text x="50%" y="50%" font-family="Arial, sans-serif" font-weight="bold" font-style="italic" font-size="22" fill="%23003399" text-anchor="middle" dominant-baseline="central">BCA</text></svg>', accountNumber:'1234567890', accountName:'PT Arunika Ryoka Indonesia' },
  { id:'bni', name:'Transfer Bank BNI', desc:'Bank Negara Indonesia', logoUrl:'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="80" height="40" viewBox="0 0 80 40"><text x="50%" y="50%" font-family="Arial, sans-serif" font-style="italic" font-weight="bold" font-size="24" fill="%23005E6A" text-anchor="middle" dominant-baseline="central">BNI</text></svg>', accountNumber:'9876543210', accountName:'PT Arunika Ryoka Indonesia' },
  { id:'mandiri', name:'Virtual Account Mandiri', desc:'Bayar via ATM / mobile banking', logoUrl:'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="80" height="40" viewBox="0 0 80 40"><text x="50%" y="50%" font-family="Arial, sans-serif" font-weight="bold" font-size="20" fill="%230F4C81" text-anchor="middle" dominant-baseline="central">mandiri</text></svg>', accountNumber:'88801234567890', accountName:'Arunika Ryoka' },
  { id:'gopay', name:'GoPay', desc:'Bayar via aplikasi Gojek', logoUrl:'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="80" height="40" viewBox="0 0 80 40"><text x="50%" y="50%" font-family="Arial, sans-serif" font-weight="bold" font-size="20" fill="%2300AED6" text-anchor="middle" dominant-baseline="central">gopay</text></svg>', accountNumber: null },
  { id:'ovo', name:'OVO', desc:'Bayar via aplikasi OVO', logoUrl:'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="80" height="40" viewBox="0 0 80 40"><text x="50%" y="50%" font-family="Arial, sans-serif" font-weight="bold" font-size="22" fill="%234C3494" text-anchor="middle" dominant-baseline="central">OVO</text></svg>', accountNumber: null },
]

const currentMethod = computed(() => paymentMethods.find(m => m.id === selectedMethod.value))
const canSubmit = computed(() => selectedMethod.value && (uploadedFile.value || ['gopay','ovo'].includes(selectedMethod.value)))

const handleFile = (e) => { if (e.target.files[0]) uploadedFile.value = e.target.files[0] }
const handleDrop = (e) => { if (e.dataTransfer.files[0]) uploadedFile.value = e.dataTransfer.files[0] }
const copyAccount = () => { navigator.clipboard.writeText(currentMethod.value.accountNumber).catch(() => {}) }

const submitPayment = () => {
  if (!canSubmit.value) return
  submitting.value = true
  setTimeout(() => {
    submitting.value = false
    showInvoiceModal.value = true

    // Save to admin notifications in localStorage
    const notifItem = {
      id: Date.now(),
      text: `Tamu ${guestName.value} baru saja melakukan pemesanan Kamar Bamboo Deluxe! (Kode: MYB-${bookingCode.value})`,
      timestamp: new Date().toISOString(),
      read: false
    }
    const rawNotifs = localStorage.getItem('miyabi_admin_notifications')
    const notifs = rawNotifs ? JSON.parse(rawNotifs) : []
    notifs.unshift(notifItem)
    localStorage.setItem('miyabi_admin_notifications', JSON.stringify(notifs))
  }, 1200)
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
