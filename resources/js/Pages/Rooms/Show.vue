<template>
  <div class="pattern-washi room-show-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
      <div class="container-zen breadcrumb-inner">
        <Link href="/" class="bc-link">Beranda</Link>
        <span class="bc-sep">›</span>
        <Link href="/kamar" class="bc-link">Kamar</Link>
        <span class="bc-sep">›</span>
        <span class="bc-current">{{ room.name }}</span>
      </div>
    </div>

    <div class="container-zen show-layout">
      <!-- Left: Gallery + Details -->
      <div class="show-left">
        <!-- Gallery -->
        <div class="gallery">
          <div class="gallery-main" :style="room.images && room.images.length > 0 ? { backgroundImage: `url(${room.images[activeImageIndex]})`, backgroundSize: 'cover', backgroundPosition: 'center' } : { background: gradient(room.colorTheme) }">
            <div v-if="!room.images || room.images.length === 0" class="gallery-main-kanji">{{ kanji(room.colorTheme) }}</div>
            <div class="gallery-main-overlay">
              <span class="room-type-badge">{{ room.type }}</span>
              <div class="gallery-floor">Lantai {{ room.floor }}</div>
            </div>
          </div>
          <div class="gallery-thumbs">
            <template v-if="room.images && room.images.length > 0">
              <div v-for="(img, idx) in room.images" :key="idx" class="gallery-thumb"
                @click="activeImageIndex = idx"
                :style="{ backgroundImage: `url(${img})`, backgroundSize: 'cover', backgroundPosition: 'center', border: activeImageIndex === idx ? '2px solid var(--color-kin-500)' : '1px solid rgba(201,168,76,0.15)' }">
                <span class="thumb-num">0{{ idx + 1 }}</span>
              </div>
            </template>
            <template v-else>
              <div v-for="n in 3" :key="n" class="gallery-thumb"
                :style="{ background: `linear-gradient(${135+n*30}deg, ${thumbColors[room.colorTheme][n-1]})` }">
                <span class="thumb-num">0{{ n }}</span>
              </div>
            </template>
          </div>
        </div>

        <!-- Description -->
        <div class="room-detail-card card-shoji" style="margin-top:1.5rem; padding:2rem;">
          <h2 class="detail-section-title">Deskripsi Kamar</h2>
          <div class="divider-ink" style="margin:1rem 0;"/>
          <p class="detail-desc">{{ room.longDescription }}</p>
        </div>

        <!-- Amenities -->
        <div class="room-detail-card card-shoji" style="margin-top:1.25rem; padding:2rem;">
          <h2 class="detail-section-title">Fasilitas & Amenitas <span style="font-family:'Noto Serif JP',serif; font-size:0.75rem; color:var(--color-kin-500); letter-spacing:0.1em;">設備</span></h2>
          <div class="divider-ink" style="margin:1rem 0;"/>
          <div class="amenities-grid">
            <div v-for="a in room.amenities" :key="a" class="amenity-item">
              <span class="amenity-check">✓</span>
              <span>{{ a }}</span>
            </div>
          </div>
        </div>

        <!-- Specs -->
        <div class="room-detail-card card-shoji" style="margin-top:1.25rem; padding:2rem;">
          <h2 class="detail-section-title">Spesifikasi</h2>
          <div class="divider-ink" style="margin:1rem 0;"/>
          <div class="specs-grid">
            <div class="spec-item">
              <div class="spec-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21.3 8.3l-5.6-5.6c-.39-.39-1.02-.39-1.41 0L2.7 14.3c-.39.39-.39 1.02 0 1.41l5.6 5.6c.39.39 1.02.39 1.41 0L21.3 9.7c.39-.39.39-1.02 0-1.41z"/>
                  <path d="M7 17l4-4M10 14l3-3M13 11l3-3M16 8l3-3"/>
                </svg>
              </div>
              <div class="spec-label">Luas</div>
              <div class="spec-val">{{ room.size }} m²</div>
            </div>
            <div class="spec-item">
              <div class="spec-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
              </div>
              <div class="spec-label">Kapasitas</div>
              <div class="spec-val">{{ room.capacity }} Tamu</div>
            </div>
            <div class="spec-item">
              <div class="spec-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <polygon points="12 2 2 7 12 12 22 7 12 2"/>
                  <polyline points="2 17 12 22 22 17"/>
                  <polyline points="2 12 12 17 22 12"/>
                </svg>
              </div>
              <div class="spec-label">Lantai</div>
              <div class="spec-val">{{ room.floor }}</div>
            </div>
            <div class="spec-item">
              <div class="spec-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
              </div>
              <div class="spec-label">Rating</div>
              <div class="spec-val">{{ room.rating }} / 5.0</div>
            </div>
          </div>
          <div class="features-row">
            <span v-for="f in room.features" :key="f" class="feat-pill">{{ f }}</span>
          </div>
        </div>
      </div>

      <!-- Right: Booking Sidebar -->
      <div class="show-right">
        <div class="booking-sidebar card-shoji">
          <div class="sidebar-room-name">
            <p class="sidebar-room-jp">{{ room.nameJP }}</p>
            <h3 class="sidebar-room-en">{{ room.name }}</h3>
            <div class="sidebar-stars">
              <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= Math.round(room.rating) }">★</span>
              <span class="sidebar-reviews">{{ room.reviewCount }} ulasan</span>
            </div>
          </div>

          <div class="sidebar-price">
            <span class="price-from">Mulai dari</span>
            <div class="price-big">{{ formatCurrency(room.price) }}</div>
            <div class="price-per">per malam</div>
          </div>

          <div class="sidebar-form">
            <div class="form-row">
              <div class="form-col">
                <label class="label-zen">Check-In</label>
                <DatePicker v-model="bookForm.checkIn" :min="today" @change="calcPrice" />
              </div>
              <div class="form-col">
                <label class="label-zen">Check-Out</label>
                <DatePicker v-model="bookForm.checkOut" :min="bookForm.checkIn || today" @change="calcPrice" />
              </div>
            </div>
            <div>
              <label class="label-zen">Jumlah Tamu</label>
              <div class="guest-counter">
                <button class="counter-btn" @click="dec" :disabled="bookForm.guests<=1">−</button>
                <span class="counter-val">{{ bookForm.guests }} Tamu</span>
                <button class="counter-btn" @click="inc" :disabled="bookForm.guests>=room.capacity">+</button>
              </div>
            </div>
          </div>

          <!-- Price Breakdown -->
          <div v-if="nights > 0" class="price-breakdown">
            <div class="breakdown-row">
              <span>{{ formatCurrency(room.price) }} × {{ nights }} malam</span>
              <span>{{ formatCurrency(room.price * nights) }}</span>
            </div>
            <div class="breakdown-row">
              <span>Pajak (11%)</span>
              <span>{{ formatCurrency(Math.round(room.price * nights * 0.11)) }}</span>
            </div>
            <div class="breakdown-row">
              <span>Layanan (5%)</span>
              <span>{{ formatCurrency(Math.round(room.price * nights * 0.05)) }}</span>
            </div>
            <div class="breakdown-total">
              <span>Total</span>
              <span>{{ formatCurrency(totalPrice) }}</span>
            </div>
          </div>

          <div class="sidebar-avail" :class="{ unavailable: !room.isAvailable }">
            <span>{{ room.isAvailable ? '✓ Kamar Tersedia' : '✗ Sedang Penuh' }}</span>
          </div>

          <Link href="/booking/buat"
            class="btn-vermillion sidebar-book-btn"
            :class="{ 'btn-disabled': !room.isAvailable }">
            Booking Sekarang
          </Link>
          <Link href="/kamar" class="btn-outline-gold sidebar-back-btn">
            ← Kembali ke Daftar Kamar
          </Link>
        </div>
      </div>
    </div>

    <!-- Related Rooms -->
    <section style="padding:3rem 0 5rem;" class="pattern-washi">
      <div class="container-zen">
        <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.8rem; color:var(--color-sumi-800); margin-bottom:1.5rem;">Kamar Lainnya <span style="font-family:'Noto Serif JP',serif; font-size:0.8rem; color:var(--color-kin-500); letter-spacing:0.12em;">他の客室</span></h2>
        <div class="related-grid">
          <div v-for="r in relatedRooms" :key="r.id" class="related-card card-shoji">
            <div class="related-img" :style="r.images && r.images.length > 0 ? { backgroundImage: `url(${r.images[0]})`, backgroundSize: 'cover', backgroundPosition: 'center' } : { background: gradient(r.colorTheme) }">
              <span class="related-price">{{ formatCurrency(r.price) }}/mlm</span>
            </div>
            <div style="padding:1rem;">
              <div style="font-family:'Cormorant Garamond',serif; font-size:1.05rem; font-weight:600; color:var(--color-sumi-800);">{{ r.name }}</div>
              <div style="font-family:'Noto Serif JP',serif; font-size:0.65rem; color:var(--color-kin-500); letter-spacing:0.1em; margin-bottom:0.5rem;">{{ r.nameJP }}</div>
              <Link :href="`/kamar/${r.id}`" class="btn-vermillion" style="width:100%; text-align:center; display:block; font-size:0.78rem; padding:0.5rem;">
                Lihat Detail
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import { formatCurrency } from '@/data/mockData'
import { rooms } from '@/data/roomsStore'
import DatePicker from '@/Components/UI/DatePicker.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({ roomId: { type: [String, Number], default: 1 } })
const room = computed(() => rooms.value.find(r => r.id == props.roomId) || rooms.value[0])
const relatedRooms = computed(() => rooms.value.filter(r => r.id !== room.value.id).slice(0, 3))

const activeImageIndex = ref(0)

watch(() => room.value.id, () => {
  activeImageIndex.value = 0
})

const today = new Date().toISOString().split('T')[0]
const bookForm = ref({ checkIn: '', checkOut: '', guests: 2 })
const nights = computed(() => {
  if (!bookForm.value.checkIn || !bookForm.value.checkOut) return 0
  const d = (new Date(bookForm.value.checkOut) - new Date(bookForm.value.checkIn)) / 86400000
  return d > 0 ? d : 0
})
const totalPrice = computed(() => {
  const base = room.value.price * nights.value
  return Math.round(base * 1.16)
})
const calcPrice = () => {}
const inc = () => { if (bookForm.value.guests < room.value.capacity) bookForm.value.guests++ }
const dec = () => { if (bookForm.value.guests > 1) bookForm.value.guests-- }

const gradient = (t) => ({
  sakura:'linear-gradient(135deg,#2D1B2E 0%,#8B3352 50%,#D4556A 100%)',
  matcha:'linear-gradient(135deg,#1A2E1E 0%,#2E5C3A 50%,#4A8A5C 100%)',
  gold:  'linear-gradient(135deg,#1A1A0E 0%,#6B5012 50%,#C9A84C 100%)',
  ai:    'linear-gradient(135deg,#0E1A2E 0%,#1A3060 50%,#2C4A8C 100%)',
  beni:  'linear-gradient(135deg,#2E0E0E 0%,#7A1C28 50%,#B83347 100%)',
})[t] || 'linear-gradient(135deg,#1A1A2E,#2E2E50)'

const kanji = (t) => ({sakura:'桜',matcha:'竹',gold:'雅',ai:'鯉',beni:'紅'})[t] || '間'

const thumbColors = {
  sakura: ['#8B3352,#D4556A','#2D1B2E,#8B3352','#D4556A,#F4A0A0'],
  matcha: ['#2E5C3A,#4A8A5C','#1A2E1E,#2E5C3A','#4A8A5C,#7ABB90'],
  gold:   ['#6B5012,#C9A84C','#1A1A0E,#6B5012','#C9A84C,#E8D080'],
  ai:     ['#1A3060,#2C4A8C','#0E1A2E,#1A3060','#2C4A8C,#4A6AB0'],
  beni:   ['#7A1C28,#B83347','#2E0E0E,#7A1C28','#B83347,#D46075'],
}
</script>

<style scoped>
.room-show-page { min-height:100vh; }
.breadcrumb-bar { background:rgba(253,250,245,0.97); backdrop-filter:blur(8px); border-bottom:1px solid rgba(201,168,76,0.2); padding:0.75rem 0; position:sticky; top:72px; z-index:40; }
.breadcrumb-inner { display:flex; align-items:center; gap:0.5rem; font-size:0.82rem; font-family:'Inter',sans-serif; }
.bc-link { color:var(--color-beni-600); text-decoration:none; }
.bc-link:hover { text-decoration:underline; }
.bc-sep { color:var(--color-kin-500); }
.bc-current { color:var(--color-sumi-700); font-weight:500; }

.show-layout { display:grid; grid-template-columns:1fr 380px; gap:2rem; padding:2rem 0; align-items:start; }
.show-left {}
.show-right { position:sticky; top:calc(72px + 48px + 1rem); }

.gallery { display:flex; flex-direction:column; gap:0.75rem; }
.gallery-main { height:360px; border-radius:4px; position:relative; overflow:hidden; }
.gallery-main-kanji { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-family:'Noto Serif JP',serif; font-size:8rem; font-weight:700; color:rgba(255,255,255,0.07); }
.gallery-main-overlay { position:absolute; inset:0; display:flex; justify-content:space-between; align-items:flex-start; padding:1rem; }
.room-type-badge { background:rgba(26,26,46,0.75); color:var(--color-kin-300); font-size:0.72rem; letter-spacing:0.12em; padding:0.3rem 0.75rem; border-radius:2px; font-family:'Noto Serif JP',serif; backdrop-filter:blur(4px); }
.gallery-floor { background:rgba(26,26,46,0.6); color:rgba(247,243,237,0.8); font-size:0.72rem; padding:0.3rem 0.6rem; border-radius:2px; font-family:'Inter',sans-serif; }
.gallery-thumbs { display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem; }
.gallery-thumb { height:120px; border-radius:4px; position:relative; overflow:hidden; cursor:pointer; transition:0.2s; }
.gallery-thumb:hover { transform:scale(1.02); }
.thumb-num { position:absolute; bottom:0.5rem; right:0.5rem; font-family:'Cormorant Garamond',serif; font-size:1.2rem; color:rgba(255,255,255,0.4); }

.detail-section-title { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--color-sumi-800); }
.detail-desc { color:var(--color-sumi-600); line-height:1.9; font-size:0.9rem; }
.amenities-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:0.6rem; }
.amenity-item { display:flex; align-items:center; gap:0.5rem; font-size:0.85rem; color:var(--color-sumi-700); font-family:'Inter',sans-serif; }
.amenity-check { color:var(--color-matcha-500); font-weight:bold; flex-shrink:0; }
.specs-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1rem; }
.spec-icon { display:flex; justify-content:center; align-items:center; margin-bottom:0.4rem; color:var(--color-kin-700); height:24px; }
.spec-icon svg { width:22px; height:22px; }
.spec-label { font-size:0.7rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; letter-spacing:0.05em; }
.spec-val { font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:600; color:var(--color-sumi-800); margin-top:0.15rem; }
.features-row { display:flex; flex-wrap:wrap; gap:0.4rem; }
.feat-pill { font-size:0.72rem; padding:0.25rem 0.65rem; background:rgba(155,35,53,0.08); color:var(--color-beni-600); border-radius:999px; font-family:'Inter',sans-serif; border:1px solid rgba(155,35,53,0.2); }

/* Booking Sidebar */
.booking-sidebar { padding:1.75rem; border-radius:4px; }
.sidebar-room-jp { font-family:'Noto Serif JP',serif; font-size:0.72rem; letter-spacing:0.15em; color:var(--color-kin-500); margin-bottom:0.2rem; }
.sidebar-room-en { font-family:'Cormorant Garamond',serif; font-size:1.5rem; font-weight:600; color:var(--color-sumi-800); margin-bottom:0.4rem; }
.sidebar-stars { display:flex; align-items:center; gap:0.15rem; margin-bottom:1.25rem; }
.star { color:#ddd; font-size:0.85rem; }
.star.filled { color:var(--color-kin-500); }
.sidebar-reviews { font-size:0.72rem; color:var(--color-sumi-600); margin-left:0.25rem; font-family:'Inter',sans-serif; }
.sidebar-price { background:var(--color-sumi-800); color:#fff; padding:1.25rem; border-radius:4px; margin-bottom:1.25rem; text-align:center; }
.price-from { font-size:0.7rem; color:rgba(247,243,237,0.6); font-family:'Inter',sans-serif; letter-spacing:0.1em; }
.price-big { font-family:'Cormorant Garamond',serif; font-size:1.8rem; font-weight:600; color:var(--color-kin-300); line-height:1.2; }
.price-per { font-size:0.72rem; color:rgba(247,243,237,0.5); font-family:'Inter',sans-serif; }
.sidebar-form { display:flex; flex-direction:column; gap:0.75rem; margin-bottom:1rem; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; }
.form-col {}
.guest-counter { display:flex; align-items:center; justify-content:space-between; background:var(--color-washi-300); border:1px solid rgba(201,168,76,0.3); border-radius:2px; padding:0.2rem; }
.counter-btn { width:34px; height:34px; border:none; background:transparent; font-size:1.2rem; cursor:pointer; color:var(--color-beni-600); transition:0.2s; border-radius:2px; }
.counter-btn:hover:not(:disabled) { background:var(--color-beni-600); color:#fff; }
.counter-btn:disabled { opacity:0.3; cursor:not-allowed; }
.counter-val { font-family:'Inter',sans-serif; font-size:0.85rem; font-weight:500; color:var(--color-sumi-800); }
.price-breakdown { background:var(--color-washi-300); border-radius:4px; padding:1rem; margin-bottom:1rem; font-size:0.82rem; font-family:'Inter',sans-serif; }
.breakdown-row { display:flex; justify-content:space-between; color:var(--color-sumi-600); padding:0.25rem 0; }
.breakdown-total { display:flex; justify-content:space-between; font-weight:700; color:var(--color-sumi-800); padding-top:0.5rem; border-top:1px solid rgba(201,168,76,0.3); margin-top:0.3rem; font-size:0.9rem; }
.sidebar-avail { text-align:center; font-size:0.78rem; color:var(--color-matcha-600,#3D6B4E); font-family:'Inter',sans-serif; margin-bottom:0.75rem; }
.sidebar-avail.unavailable { color:var(--color-beni-600); }
.sidebar-book-btn { width:100%; justify-content:center; padding:0.9rem; font-size:0.9rem; margin-bottom:0.5rem; }
.sidebar-back-btn { width:100%; justify-content:center; padding:0.7rem; font-size:0.8rem; }
.btn-disabled { opacity:0.4; pointer-events:none; }

.related-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; }
.related-card { border-radius:4px; overflow:hidden; }
.related-img { height:140px; position:relative; }
.related-price { position:absolute; bottom:0.6rem; right:0.6rem; background:rgba(26,26,46,0.8); color:var(--color-kin-300); font-size:0.72rem; padding:0.2rem 0.5rem; border-radius:2px; font-family:'Cormorant Garamond',serif; }

@media(max-width:1100px) { .show-layout { grid-template-columns:1fr; } .show-right { position:static; } }
@media(max-width:700px) { .amenities-grid { grid-template-columns:1fr; } .specs-grid { grid-template-columns:repeat(2,1fr); } .related-grid { grid-template-columns:1fr; } }
</style>
