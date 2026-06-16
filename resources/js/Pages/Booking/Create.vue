<template>
  <div class="booking-page pattern-washi">
    <div class="container-zen booking-layout">
      <!-- Left: Form -->
      <div class="booking-form-area">
        <!-- Skeleton Loading -->
        <div v-if="isLoading" class="skeleton-wrapper">
          <div class="skeleton-steps">
            <Skeleton v-for="n in 3" :key="n" variant="circle" width="38px" height="38px" />
          </div>
          <Skeleton variant="card" height="200px" style="margin-bottom: 1.5rem" />
          <div class="form-grid">
            <Skeleton variant="text" height="40px" />
            <Skeleton variant="text" height="40px" />
          </div>
          <Skeleton variant="text" height="100px" style="margin-top: 1rem" />
        </div>

        <!-- Actual Content -->
        <template v-else>
        <!-- Step Indicator -->
        <div class="step-indicator">
          <div v-for="(s, i) in steps" :key="s.title" class="step-item">
            <div class="step-circle" :class="{ active: currentStep===i+1, done: currentStep>i+1 }">
              <span v-if="currentStep>i+1">✓</span>
              <span v-else>{{ i+1 }}</span>
            </div>
            <div class="step-label">{{ s.title }}</div>
            <div v-if="i<steps.length-1" class="step-line" :class="{ done: currentStep>i+1 }"/>
          </div>
        </div>

        <!-- Step 1: Pilih Kamar & Tanggal -->
        <div v-if="currentStep===1" class="step-content">
          <h2 class="step-heading">Pilih Kamar & Tanggal <span class="step-heading-jp">日程選択</span></h2>
          <!-- Room Card -->
          <div class="selected-room-card">
            <div class="selected-room-img" :style="form.room.images && form.room.images.length > 0 ? { backgroundImage: `url(${form.room.images[0]})`, backgroundSize: 'cover', backgroundPosition: 'center' } : { background: gradient(form.room.colorTheme) }">
              <span v-if="!form.room.images || form.room.images.length === 0" class="selected-room-kanji">{{ kanji(form.room.colorTheme) }}</span>
            </div>
            <div class="selected-room-info">
              <div class="selected-room-type">{{ form.room.type }}</div>
              <div class="selected-room-name">{{ form.room.name }}</div>
              <div class="selected-room-jp">{{ form.room.nameJP }}</div>
              <div class="selected-room-price">
                <span v-if="form.room.isPromoActive && form.room.discountPercentage > 0" style="text-decoration: line-through; opacity: 0.6; font-size: 0.8rem; margin-right: 0.4rem; color: var(--color-sumi-600);">
                  {{ formatCurrency(form.room.price) }}
                </span>
                {{ formatCurrency(currentPrice) }}/malam
              </div>
            </div>
          </div>
          <div class="form-grid">
            <div>
              <label class="label-zen">Tanggal Check-In ✦</label>
              <DatePicker v-model="form.checkIn" :min="today" :disabled-dates="unavailableDates" required />
            </div>
            <div>
              <label class="label-zen">Tanggal Check-Out ✦</label>
              <DatePicker v-model="form.checkOut" :min="form.checkIn || today" :disabled-dates="unavailableDates" required />
            </div>
          </div>
          <div class="form-grid">
            <div>
              <label class="label-zen">Jumlah Dewasa</label>
              <div class="guest-counter">
                <button class="counter-btn" @click="form.adults=Math.max(1,form.adults-1)">−</button>
                <span class="counter-val">{{ form.adults }}</span>
                <button class="counter-btn" @click="form.adults++">+</button>
              </div>
            </div>
            <div>
              <label class="label-zen">Jumlah Anak (< 12 thn)</label>
              <div class="guest-counter">
                <button class="counter-btn" @click="form.children=Math.max(0,form.children-1)">−</button>
                <span class="counter-val">{{ form.children }}</span>
                <button class="counter-btn" @click="form.children++">+</button>
              </div>
            </div>
          </div>
          <div>
            <label class="label-zen">Permintaan Khusus (Opsional)</label>
            <textarea class="input-zen" rows="3" v-model="form.specialRequest" placeholder="Contoh: Kamar non-smoking, tempat tidur terpisah, dll..." style="resize:vertical;"/>
          </div>
        </div>

        <!-- Step 2: Data Tamu -->
        <div v-if="currentStep===2" class="step-content">
          <h2 class="step-heading">Data Tamu <span class="step-heading-jp">ゲスト情報</span></h2>
          <div class="form-grid">
            <div>
              <label class="label-zen">Nama Lengkap ✦</label>
              <input type="text" class="input-zen" v-model="form.guestName" placeholder="Sesuai KTP/Paspor" required/>
            </div>
            <div>
              <label class="label-zen">Email ✦</label>
              <input type="email" class="input-zen" v-model="form.email" placeholder="email@example.com" required/>
            </div>
          </div>
          <div class="form-grid">
            <div>
              <label class="label-zen">Nomor HP ✦</label>
              <input type="tel" class="input-zen" v-model="form.phone" placeholder="+62 812 xxxx xxxx" required/>
            </div>
            <div>
              <label class="label-zen">NIK / No. Paspor ✦</label>
              <input type="text" class="input-zen" v-model="form.idNumber" placeholder="Nomor identitas" required/>
            </div>
          </div>
          <div class="form-grid">
            <div>
              <label class="label-zen">Negara Asal</label>
              <select class="input-zen" v-model="form.country">
                <option value="ID">Indonesia</option>
                <option value="JP">Japan</option>
                <option value="SG">Singapore</option>
                <option value="MY">Malaysia</option>
                <option value="AU">Australia</option>
                <option value="US">United States</option>
                <option value="OTHER">Lainnya</option>
              </select>
            </div>
            <div>
              <label class="label-zen">Waktu Perkiraan Tiba</label>
              <input type="time" class="input-zen" v-model="form.arrivalTime"/>
              <div style="font-size: 0.7rem; color: var(--color-sumi-500); margin-top: 0.3rem;">Waktu check-in standar: 14:00</div>
            </div>
          </div>
          <div>
            <label class="label-zen">Catatan Tambahan</label>
            <textarea class="input-zen" rows="3" v-model="form.notes" placeholder="Informasi tambahan untuk tim kami..." style="resize:vertical;"/>
          </div>
        </div>

        <!-- Step 3: Konfirmasi -->
        <div v-if="currentStep===3" class="step-content">
          <h2 class="step-heading">Konfirmasi Pemesanan <span class="step-heading-jp">予約確認</span></h2>
          <div class="confirm-card card-shoji" style="padding:1.75rem;">
            <div class="confirm-section-title">Detail Kamar</div>
            <div class="confirm-room-row">
              <div class="confirm-room-img" :style="form.room.images && form.room.images.length > 0 ? { backgroundImage: `url(${form.room.images[0]})`, backgroundSize: 'cover', backgroundPosition: 'center' } : { background: gradient(form.room.colorTheme) }"/>
              <div>
                <div style="font-family:'Cormorant Garamond',serif; font-size:1.2rem; font-weight:600; color:var(--color-sumi-800);">{{ form.room.name }}</div>
                <div style="font-family:'Noto Serif JP',serif; font-size:0.7rem; color:var(--color-kin-500); letter-spacing:0.12em;">{{ form.room.nameJP }}</div>
                <div style="font-size:0.8rem; color:var(--color-sumi-600); margin-top:0.3rem;">{{ form.room.type }} · {{ form.room.size }}m² · Max {{ form.room.capacity }} Tamu</div>
              </div>
            </div>
            <div class="divider-ink" style="margin:1rem 0;"/>
            <div class="confirm-section-title">Detail Kunjungan</div>
            <div class="confirm-details-grid">
              <div class="confirm-detail"><span class="cd-label">Check-In</span><span class="cd-val">{{ form.checkIn || '-' }} <span style="font-size:0.75em; color:var(--color-sumi-500)">(14:00)</span></span></div>
              <div class="confirm-detail"><span class="cd-label">Check-Out</span><span class="cd-val">{{ form.checkOut || '-' }} <span style="font-size:0.75em; color:var(--color-sumi-500)">(12:00)</span></span></div>
              <div class="confirm-detail"><span class="cd-label">Durasi</span><span class="cd-val">{{ nights }} Malam</span></div>
              <div class="confirm-detail"><span class="cd-label">Tamu</span><span class="cd-val">{{ form.adults }} Dewasa{{ form.children ? `, ${form.children} Anak` : '' }}</span></div>
            </div>
            <div class="divider-ink" style="margin:1rem 0;"/>
            <div class="confirm-section-title">Data Pemesan</div>
            <div class="confirm-details-grid">
              <div class="confirm-detail"><span class="cd-label">Nama</span><span class="cd-val">{{ form.guestName || '-' }}</span></div>
              <div class="confirm-detail"><span class="cd-label">Email</span><span class="cd-val">{{ form.email || '-' }}</span></div>
              <div class="confirm-detail"><span class="cd-label">No. HP</span><span class="cd-val">{{ form.phone || '-' }}</span></div>
              <div class="confirm-detail"><span class="cd-label">Negara</span><span class="cd-val">{{ form.country }}</span></div>
            </div>
            <div class="divider-ink" style="margin:1rem 0;"/>
            <div class="includes-list">
              <div class="includes-title">✦ Yang Sudah Termasuk</div>
              <div class="includes-item" v-for="inc in includes" :key="inc">✓ {{ inc }}</div>
            </div>
            <div class="terms-row">
              <input type="checkbox" id="terms" v-model="form.agreeTerms"/>
              <label for="terms" class="terms-label">Saya menyetujui <a href="#" class="terms-link">Syarat & Ketentuan</a> serta <a href="#" class="terms-link">Kebijakan Pembatalan</a> Darma Mizuki.</label>
            </div>
          </div>
        </div>

        <!-- Navigation -->
        <div class="step-nav">
          <button v-if="currentStep>1" class="btn-outline-gold" @click="currentStep--">
            ← Kembali
          </button>
          <div style="flex:1;"/>
          <button v-if="currentStep<3" class="btn-vermillion" @click="currentStep++" :disabled="!canNext">
            Lanjutkan →
          </button>
          <div v-if="$page.props.errors.room" class="error-alert" style="margin-bottom:1rem; padding:1rem; background:rgba(155,35,53,0.1); border:1px solid var(--color-beni-600); color:var(--color-beni-600); border-radius:4px; font-size:0.85rem;">
            <SvgIcon name="warning" width="16" height="16" style="margin-right:0.5rem; vertical-align:middle;"/>
            {{ $page.props.errors.room }}
          </div>

          <button v-if="currentStep===3" @click="submitBooking"
            class="btn-vermillion"
            :disabled="!form.agreeTerms || submitting">
            {{ submitting ? 'Memproses...' : 'Konfirmasi & Bayar' }}
          </button>
        </div>
        </template>
      </div>

      <!-- Right: Summary -->
      <div class="booking-summary">
        <div class="summary-card card-shoji">
          <div class="summary-title">Ringkasan Booking <span class="summary-title-jp">予約概要</span></div>
          <div class="divider-ink" style="margin:0.75rem 0;"/>
          <div class="summary-room-name">{{ form.room.name }}</div>
          <div class="summary-room-jp">{{ form.room.nameJP }}</div>
          <div class="summary-dates" v-if="form.checkIn && form.checkOut">
            <div class="summary-date-item">
              <span class="sd-label">Check-In</span><span class="sd-val">{{ form.checkIn }} <br><span style="font-size:0.75rem; color:var(--color-sumi-600); font-weight:normal;">Mulai 14:00</span></span>
            </div>
            <div class="summary-date-sep">→</div>
            <div class="summary-date-item">
              <span class="sd-label">Check-Out</span><span class="sd-val">{{ form.checkOut }} <br><span style="font-size:0.75rem; color:var(--color-sumi-600); font-weight:normal;">Maks 12:00</span></span>
            </div>
          </div>
          <div class="summary-nights" v-if="nights>0">{{ nights }} Malam · {{ form.adults+form.children }} Tamu</div>
          <div class="divider-ink" style="margin:0.75rem 0;"/>
          <div class="summary-price-row"><span>Harga Kamar</span><span>{{ formatCurrency(currentPrice) }}/mlm</span></div>
          <div class="summary-price-row" v-if="nights>0"><span>Subtotal ({{ nights }} mlm)</span><span>{{ formatCurrency(currentPrice * nights) }}</span></div>
          <div class="summary-price-row"><span>Pajak (11%)</span><span>{{ nights>0 ? formatCurrency(Math.round(currentPrice*nights*0.11)) : '-' }}</span></div>
          <div class="summary-price-row"><span>Layanan (5%)</span><span>{{ nights>0 ? formatCurrency(Math.round(currentPrice*nights*0.05)) : '-' }}</span></div>
          <div class="summary-total-row">
            <span>Total Pembayaran</span>
            <span>{{ nights>0 ? formatCurrency(totalPrice) : '-' }}</span>
          </div>
          <div class="summary-note">
            <span>✦</span> Harga sudah termasuk pajak dan biaya layanan
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import Skeleton from '@/Components/UI/Skeleton.vue'
import { formatCurrency } from '@/data/mockData'
import { rooms } from '@/data/roomsStore'
import DatePicker from '@/Components/UI/DatePicker.vue'

defineOptions({ layout: AppLayout })

const page = usePage()
const user = page.props.auth.user

const isLoading = ref(true)
const today = new Date().toISOString().split('T')[0]
const currentStep = ref(1)
const submitting = ref(false)

// Simulate loading
onMounted(() => {
  setTimeout(() => {
    isLoading.value = false
  }, 600)
})

const form = ref({
  room: rooms.value[0],
  checkIn: '', checkOut: '',
  adults: 2, children: 0,
  specialRequest: '',
  guestName: user ? user.name : '', 
  email: user ? user.email : '', 
  phone: '', idNumber: '',
  country: 'ID', arrivalTime: '14:00',
  notes: '', agreeTerms: false,
})

const unavailableDates = ref([])

const fetchUnavailableDates = async () => {
  if (!form.value.room || !form.value.room.id) return
  try {
    const res = await fetch(`/api/kamar/${form.value.room.id}/tanggal-penuh`)
    if (res.ok) {
      unavailableDates.value = await res.json()
    }
  } catch (e) {
    console.error('Failed to fetch unavailable dates', e)
  }
}

watch(() => form.value.room, () => {
  fetchUnavailableDates()
  // Opsional: reset checkIn/checkOut jika mereka sekarang berada di tanggal yang penuh
}, { immediate: true })

const steps = [
  { title: 'Kamar & Tanggal' },
  { title: 'Data Tamu' },
  { title: 'Konfirmasi' },
]

const nights = computed(() => {
  if (!form.value.checkIn || !form.value.checkOut) return 0
  const d = (new Date(form.value.checkOut) - new Date(form.value.checkIn)) / 86400000
  return d > 0 ? d : 0
})

const currentPrice = computed(() => {
  return form.value.room.isPromoActive && form.value.room.discountPercentage > 0 
    ? form.value.room.price * (1 - form.value.room.discountPercentage / 100) 
    : form.value.room.price
})

const totalPrice = computed(() => Math.round(currentPrice.value * nights.value * 1.16))

const submitBooking = () => {
  if (!form.value.agreeTerms) return
  submitting.value = true
  router.post('/booking/simpan', {
    room_id: form.value.room.id,
    check_in: form.value.checkIn,
    check_out: form.value.checkOut,
    nights: nights.value,
    guests: form.value.adults + form.value.children,
    total_price: Math.round(currentPrice.value * nights.value),
    email: form.value.email,
    guest_name: form.value.guestName
  }, {
    onFinish: () => submitting.value = false,
    onError: () => { submitting.value = false; }
  })
}

const canNext = computed(() => {
  if (currentStep.value === 1) return form.value.checkIn && form.value.checkOut && nights.value > 0
  if (currentStep.value === 2) return form.value.guestName && form.value.email && form.value.phone
  return true
})

const includes = ['Sarapan pagi untuk 2 orang', 'Yukata & perlengkapan mandi premium', 'Teh matcha gratis', 'Wi-Fi unlimited', 'Akses fasilitas hotel', 'Layanan kamar 24 jam']

const gradient = (t) => ({
  sakura:'linear-gradient(135deg,#2D1B2E,#D4556A)',
  matcha:'linear-gradient(135deg,#1A2E1E,#4A8A5C)',
  gold:  'linear-gradient(135deg,#1A1A0E,#C9A84C)',
  ai:    'linear-gradient(135deg,#0E1A2E,#2C4A8C)',
  beni:  'linear-gradient(135deg,#2E0E0E,#B83347)',
})[t] || 'linear-gradient(135deg,#1A1A2E,#2E2E50)'
const kanji = (t) => ({sakura:'桜',matcha:'竹',gold:'雅',ai:'鯉',beni:'紅'})[t] || '間'
</script>

<style scoped>
.booking-page { min-height:100vh; padding:6rem 0 4rem; }
.booking-layout { display:grid; grid-template-columns:1fr 340px; gap:2rem; align-items:start; padding-top:2rem; }

/* Skeleton Loading */
.skeleton-wrapper { padding: 1rem 0; }
.skeleton-steps { display: flex; gap: 2rem; justify-content: center; margin-bottom: 2rem; }

/* Step indicator */
.step-indicator { display:flex; align-items:flex-start; justify-content:center; gap:0; margin-bottom:2.5rem; }
.step-item { display:flex; flex-direction:column; align-items:center; gap:0.3rem; }
.step-circle { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:'Noto Serif JP',serif; font-size:0.9rem; border:2px solid var(--color-washi-900); color:var(--color-washi-900); background:var(--color-washi-100); transition:0.3s; }
.step-circle.active { background:var(--color-beni-600); border-color:var(--color-beni-600); color:#fff; }
.step-circle.done { background:var(--color-matcha-500); border-color:var(--color-matcha-500); color:#fff; }
.step-label { font-size:0.7rem; font-family:'Noto Serif JP',serif; color:var(--color-sumi-600); letter-spacing:0.05em; white-space:nowrap; }
.step-line { width:100px; height:2px; background:var(--color-washi-900); margin-bottom:1.8rem; transition:0.3s; }
.step-line.done { background:var(--color-matcha-500); }

.step-content { animation: fadeIn 0.4s ease; }
.step-heading { font-family:'Cormorant Garamond',serif; font-size:1.6rem; font-weight:600; color:var(--color-sumi-800); margin-bottom:1.5rem; }
.step-heading-jp { font-family:'Noto Serif JP',serif; font-size:0.75rem; letter-spacing:0.15em; color:var(--color-kin-500); display:block; margin-top:2px; }

.selected-room-card { display:flex; gap:1rem; background:var(--color-washi-300); border:1px solid rgba(201,168,76,0.25); border-radius:4px; padding:1rem; margin-bottom:1.25rem; }
.selected-room-img { width:90px; height:70px; border-radius:4px; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
.selected-room-kanji { font-family:'Noto Serif JP',serif; font-size:2rem; font-weight:700; color:rgba(255,255,255,0.2); }
.selected-room-type { font-size:0.68rem; letter-spacing:0.1em; color:var(--color-kin-500); font-family:'Noto Serif JP',serif; }
.selected-room-name { font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:600; color:var(--color-sumi-800); }
.selected-room-jp { font-family:'Noto Serif JP',serif; font-size:0.65rem; color:var(--color-kin-500); letter-spacing:0.12em; }
.selected-room-price { font-family:'Cormorant Garamond',serif; font-size:1rem; font-weight:600; color:var(--color-beni-600); margin-top:0.25rem; }

.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
.guest-counter { display:flex; align-items:center; justify-content:space-between; border:1px solid rgba(201,168,76,0.3); border-radius:2px; background:var(--color-washi-300); padding:0.2rem; }
.counter-btn { width:38px; height:38px; border:none; background:transparent; font-size:1.3rem; cursor:pointer; color:var(--color-beni-600); border-radius:2px; transition:0.2s; }
.counter-btn:hover { background:var(--color-beni-600); color:#fff; }
.counter-val { font-family:'Inter',sans-serif; font-size:0.9rem; font-weight:500; color:var(--color-sumi-800); }

/* Confirm */
.confirm-section-title { font-family:'Cormorant Garamond',serif; font-size:1rem; font-weight:600; color:var(--color-sumi-700); letter-spacing:0.05em; margin-bottom:0.75rem; }
.confirm-room-row { display:flex; gap:1rem; align-items:center; margin-bottom:0.5rem; }
.confirm-room-img { width:70px; height:55px; border-radius:4px; flex-shrink:0; }
.confirm-details-grid { display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; }
.confirm-detail { display:flex; flex-direction:column; gap:0.1rem; }
.cd-label { font-size:0.68rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; letter-spacing:0.05em; }
.cd-val { font-size:0.88rem; color:var(--color-sumi-800); font-weight:500; font-family:'Inter',sans-serif; }
.includes-list { background:rgba(74,124,89,0.06); border-left:3px solid var(--color-matcha-500); padding:1rem; border-radius:0 4px 4px 0; margin-bottom:1rem; }
.includes-title { font-family:'Cormorant Garamond',serif; font-size:0.95rem; font-weight:600; color:var(--color-matcha-700); margin-bottom:0.5rem; }
.includes-item { font-size:0.82rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; padding:0.2rem 0; }
.terms-row { display:flex; gap:0.75rem; align-items:flex-start; }
.terms-row input[type=checkbox] { margin-top:3px; accent-color:var(--color-beni-600); flex-shrink:0; }
.terms-label { font-size:0.82rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; line-height:1.6; }
.terms-link { color:var(--color-beni-600); text-decoration:underline; }

.step-nav { display:flex; gap:0.75rem; margin-top:2rem; }
.btn-disabled { opacity:0.4; pointer-events:none; }

/* Summary */
.booking-summary { position:sticky; top:calc(72px + 1rem); }
.summary-card { padding:1.5rem; }
.summary-title { font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:600; color:var(--color-sumi-800); }
.summary-title-jp { font-family:'Noto Serif JP',serif; font-size:0.65rem; color:var(--color-kin-500); letter-spacing:0.12em; display:block; }
.summary-room-name { font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:600; color:var(--color-sumi-800); }
.summary-room-jp { font-family:'Noto Serif JP',serif; font-size:0.65rem; color:var(--color-kin-500); letter-spacing:0.1em; margin-bottom:0.75rem; }
.summary-dates { display:flex; align-items:center; gap:0.5rem; margin-bottom:0.4rem; }
.summary-date-item { display:flex; flex-direction:column; gap:1px; }
.sd-label { font-size:0.62rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; letter-spacing:0.08em; }
.sd-val { font-size:0.82rem; color:var(--color-sumi-800); font-weight:500; font-family:'Inter',sans-serif; }
.summary-date-sep { color:var(--color-kin-500); font-size:0.85rem; margin:0 0.25rem; padding-top:1rem; }
.summary-nights { font-size:0.75rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; margin-bottom:0.25rem; }
.summary-price-row { display:flex; justify-content:space-between; font-size:0.8rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; padding:0.3rem 0; }
.summary-total-row { display:flex; justify-content:space-between; font-weight:700; color:var(--color-sumi-800); padding:0.6rem 0; border-top:1px solid rgba(201,168,76,0.3); margin-top:0.3rem; font-size:0.95rem; font-family:'Cormorant Garamond',serif; }
.summary-note { font-size:0.7rem; color:var(--color-sumi-600); font-family:'Noto Serif JP',serif; margin-top:0.75rem; letter-spacing:0.05em; }

@media(max-width:900px) {
  .booking-layout { grid-template-columns:1fr; }
  .booking-summary { position:static; }
  .form-grid { grid-template-columns:1fr; }
}
</style>
