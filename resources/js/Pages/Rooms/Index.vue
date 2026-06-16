<template>
  <div>
    <!-- Hero Banner -->
    <section class="rooms-hero">
      <div class="rooms-hero-bg"/>
      <div class="rooms-hero-kanji" aria-hidden="true">室</div>
      <div class="container-zen rooms-hero-content">
        <p class="section-kicker" style="color:var(--color-sakura-200)">客室とスイート</p>
        <h1 class="rooms-hero-title">Kamar & Suite</h1>
        <p style="color:rgba(247,243,237,0.75); font-size:1rem; margin-top:0.5rem;">{{ filteredRooms.length }} kamar tersedia untuk kenyamanan Anda</p>
      </div>
    </section>

    <!-- Filter Bar -->
    <div class="filter-bar">
      <div class="container-zen filter-inner">
        <div class="filter-group">
          <label class="filter-label">Tipe Kamar</label>
          <div class="filter-chips">
            <button v-for="type in roomTypes" :key="type"
              class="chip" :class="{ active: selectedType === type }"
              @click="selectedType = type">{{ type }}</button>
          </div>
        </div>
        <div class="filter-group view-mode-group">
          <button class="view-btn" :class="{ active: viewMode==='grid3' }" @click="viewMode='grid3'" title="3 kolom">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          </button>
          <button class="view-btn" :class="{ active: viewMode==='grid2' }" @click="viewMode='grid2'" title="2 kolom">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="9" height="9" rx="1"/><rect x="14" y="3" width="7" height="18" rx="1"/><rect x="3" y="14" width="9" height="7" rx="1"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Rooms Grid -->
    <section class="pattern-washi rooms-list-section">
      <div class="container-zen">
        <div class="rooms-grid" :class="viewMode">
          <!-- Skeleton Loading -->
          <template v-if="isLoading">
            <div v-for="n in 6" :key="`skeleton-${n}`" class="room-card card-shoji">
              <Skeleton variant="image" height="200px" />
              <div class="room-body">
                <Skeleton variant="text" width="60%" height="24px" style="margin-bottom: 0.5rem" />
                <Skeleton variant="text" width="40%" height="16px" style="margin-bottom: 0.75rem" />
                <Skeleton variant="text" width="100%" height="14px" style="margin-bottom: 0.5rem" />
                <Skeleton variant="text" width="80%" height="14px" style="margin-bottom: 1rem" />
                <div style="display: flex; gap: 0.5rem; margin-top: auto;">
                  <Skeleton variant="button" width="100%" height="40px" />
                  <Skeleton variant="button" width="100px" height="40px" />
                </div>
              </div>
            </div>
          </template>

          <!-- Actual Room Cards -->
          <template v-else>
            <div v-for="room in filteredRooms" :key="room.id" class="room-card card-shoji">
            <!-- Image -->
            <div class="room-img" :style="room.images && room.images.length > 0 ? { backgroundImage: `url(${room.images[0]})`, backgroundSize: 'cover', backgroundPosition: 'center' } : { background: gradient(room.colorTheme) }">
              <div style="position: absolute; top: 0.8rem; left: 0.8rem; display: flex; gap: 0.5rem; z-index: 2;">
                <span class="room-type-tag" style="position: relative; top: 0; left: 0;">{{ room.type }}</span>
                <span v-if="room.isPromoActive && room.discountPercentage > 0" class="room-promo-tag" style="position: relative; top: 0; left: 0; background: var(--color-beni-600); color: #fff; font-size: 0.68rem; padding: 0.2rem 0.55rem; border-radius: 2px; font-family: 'Inter', sans-serif;">PROMO {{ room.discountPercentage }}%</span>
              </div>
              <div class="room-availability" :class="{ unavailable: !room.isAvailable }">
                {{ room.isAvailable ? '✓ Tersedia' : '✗ Penuh' }}
              </div>
              <div v-if="!room.images || room.images.length === 0" class="room-big-kanji">{{ kanji(room.colorTheme) }}</div>
              <div class="room-price-tag">
                <div v-if="room.isPromoActive && room.discountPercentage > 0" style="font-size: 0.7rem; text-decoration: line-through; opacity: 0.8; line-height: 1; margin-bottom: 0.1rem;">
                  {{ formatCurrency(room.price) }}
                </div>
                <span class="price-num">{{ formatCurrency(room.isPromoActive ? room.price * (1 - room.discountPercentage/100) : room.price) }}</span>
                <span class="price-unit">/malam</span>
              </div>
            </div>
            <!-- Body -->
            <div class="room-body">
              <div class="room-header-row">
                <div>
                  <h3 class="room-name-en">{{ room.name }}</h3>
                  <p class="room-name-jp">{{ room.nameJP }}</p>
                </div>
                <div class="room-size-cap">
                  <span>{{ room.size }}m²</span>
                  <span>·</span>
                  <span>{{ room.capacity }} Tamu</span>
                </div>
              </div>
              <div class="room-stars">
                <span v-for="n in 5" :key="n" class="star" :class="{filled: n<=Math.round(room.rating)}">★</span>
                <span class="rating-txt">{{ room.rating }} ({{ room.reviewCount }})</span>
              </div>
              <p class="room-short-desc">{{ room.description }}</p>
              <div class="room-features">
                <span v-for="f in room.features.slice(0,3)" :key="f" class="feat-tag">{{ f }}</span>
              </div>
              <div class="room-card-actions">
                <Link :href="`/kamar/${room.id}`" class="btn-vermillion" style="flex:1;text-align:center;font-size:0.82rem;padding:0.65rem 1rem;">
                  Lihat Detail
                </Link>
                <Link href="/booking/buat"
                  class="btn-outline-gold"
                  style="font-size:0.82rem;padding:0.65rem 1rem;"
                  :class="{ 'btn-disabled': !room.isAvailable }">
                  Booking
                </Link>
              </div>
            </div>
            </div>
          </template>
        </div>

        <!-- Empty -->
        <div v-if="filteredRooms.length === 0" class="empty-state">
          <div class="empty-kanji">間</div>
          <p>Tidak ada kamar yang tersedia untuk filter ini</p>
          <button class="btn-vermillion" @click="selectedType='Semua'" style="margin-top:1rem;">Tampilkan Semua</button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import Skeleton from '@/Components/UI/Skeleton.vue'
import { formatCurrency } from '@/data/mockData'
import { rooms } from '@/data/roomsStore'

defineOptions({ layout: AppLayout })

const isLoading = ref(true)
const selectedType = ref('Semua')
const viewMode = ref('grid3')
const roomTypes = ['Semua', 'Standard', 'Superior', 'Deluxe', 'Junior Suite', 'Suite', 'Presidential']

// Simulate loading
onMounted(() => {
  setTimeout(() => {
    isLoading.value = false
  }, 800)
})

const filteredRooms = computed(() =>
  selectedType.value === 'Semua' ? rooms.value : rooms.value.filter(r => r.type === selectedType.value)
)

const gradient = (t) => ({
  sakura:'linear-gradient(135deg,#2D1B2E 0%,#8B3352 50%,#D4556A 100%)',
  matcha:'linear-gradient(135deg,#1A2E1E 0%,#2E5C3A 50%,#4A8A5C 100%)',
  gold:  'linear-gradient(135deg,#1A1A0E 0%,#6B5012 50%,#C9A84C 100%)',
  ai:    'linear-gradient(135deg,#0E1A2E 0%,#1A3060 50%,#2C4A8C 100%)',
  beni:  'linear-gradient(135deg,#2E0E0E 0%,#7A1C28 50%,#B83347 100%)',
})[t] || 'linear-gradient(135deg,#1A1A2E,#2E2E50)'

const kanji = (t) => ({sakura:'桜',matcha:'竹',gold:'雅',ai:'鯉',beni:'紅'})[t] || '間'
</script>

<style scoped>
.rooms-hero { min-height:40vh; position:relative; display:flex; align-items:center; overflow:hidden; }
.rooms-hero-bg { position:absolute; inset:0; background:linear-gradient(135deg,#1A1A2E 0%,#2C4A7C 60%,#7A1C28 100%); }
.rooms-hero-kanji { position:absolute; font-family:'Noto Serif JP',serif; font-size:18rem; font-weight:700; color:rgba(201,168,76,0.07); top:50%; right:5%; transform:translateY(-50%); pointer-events:none; }
.rooms-hero-content { position:relative; z-index:2; padding:6rem 0 3rem; }
.rooms-hero-title { font-family:'Cormorant Garamond',serif; font-size:clamp(2.5rem,5vw,4rem); font-weight:600; color:#fff; }
.section-kicker { font-family:'Noto Serif JP',serif; font-size:0.8rem; letter-spacing:0.2em; color:var(--color-beni-500); margin-bottom:0.5rem; display:block; }

.filter-bar { background:rgba(253,250,245,0.97); backdrop-filter:blur(12px); border-bottom:1px solid rgba(201,168,76,0.2); position:sticky; top:72px; z-index:50; box-shadow:0 2px 12px rgba(26,26,46,0.06); }
.filter-inner { display:flex; align-items:center; gap:1rem; padding:0.85rem 0; flex-wrap:wrap; }
.filter-label { font-family:'Noto Serif JP',serif; font-size:0.72rem; letter-spacing:0.1em; color:var(--color-kin-500); white-space:nowrap; }
.filter-chips { display:flex; gap:0.4rem; flex-wrap:wrap; }
.chip { padding:0.3rem 0.85rem; border:1px solid rgba(201,168,76,0.3); border-radius:999px; background:transparent; color:var(--color-sumi-700); font-size:0.78rem; cursor:pointer; font-family:'Inter',sans-serif; transition:0.2s; }
.chip:hover { border-color:var(--color-beni-500); color:var(--color-beni-600); }
.chip.active { background:var(--color-beni-600); border-color:var(--color-beni-600); color:#fff; }
.view-mode-group { margin-left:auto; display:flex; gap:0.75rem; align-items:center; }
.view-btn { width:34px; height:34px; border:1px solid rgba(201,168,76,0.3); border-radius:4px; background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--color-sumi-600); transition:0.2s; }
.view-btn.active { background:var(--color-sumi-800); color:var(--color-kin-300); border-color:var(--color-sumi-800); }

.rooms-list-section { padding:3rem 0 5rem; }
.rooms-grid { display:grid; gap:1.5rem; }
.rooms-grid.grid3 { grid-template-columns:repeat(3,1fr); }
.rooms-grid.grid2 { grid-template-columns:repeat(2,1fr); }

.room-card { border-radius:4px; overflow:hidden; display:flex; flex-direction:column; }
.room-img { height:200px; position:relative; overflow:hidden; }
.room-type-tag { position:absolute; top:0.8rem; left:0.8rem; background:rgba(26,26,46,0.75); color:var(--color-kin-300); font-size:0.68rem; letter-spacing:0.12em; padding:0.2rem 0.55rem; border-radius:2px; font-family:'Noto Serif JP',serif; backdrop-filter:blur(4px); }
.room-availability { position:absolute; top:0.8rem; right:0.8rem; font-size:0.68rem; padding:0.2rem 0.55rem; border-radius:2px; background:rgba(74,124,89,0.85); color:#fff; font-family:'Inter',sans-serif; }
.room-availability.unavailable { background:rgba(155,35,53,0.85); }
.room-big-kanji { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-family:'Noto Serif JP',serif; font-size:5rem; font-weight:700; color:rgba(255,255,255,0.07); pointer-events:none; }
.room-price-tag { position:absolute; bottom:0.8rem; right:0.8rem; background:var(--color-beni-600); color:#fff; padding:0.3rem 0.7rem; border-radius:2px; }
.price-num { font-family:'Cormorant Garamond',serif; font-size:1rem; font-weight:600; }
.price-unit { font-size:0.65rem; font-family:'Inter',sans-serif; opacity:0.8; }
.room-body { padding:1.25rem; flex:1; display:flex; flex-direction:column; }
.room-header-row { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.4rem; }
.room-name-en { font-family:'Cormorant Garamond',serif; font-size:1.15rem; font-weight:600; color:var(--color-sumi-800); }
.room-name-jp { font-family:'Noto Serif JP',serif; font-size:0.68rem; color:var(--color-kin-500); letter-spacing:0.12em; }
.room-size-cap { font-size:0.72rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; display:flex; gap:0.3rem; white-space:nowrap; }
.room-stars { display:flex; align-items:center; gap:0.15rem; margin-bottom:0.4rem; }
.star { color:#ddd; font-size:0.8rem; }
.star.filled { color:var(--color-kin-500); }
.rating-txt { font-size:0.72rem; color:var(--color-sumi-600); margin-left:0.25rem; font-family:'Inter',sans-serif; }
.room-short-desc { font-size:0.8rem; color:var(--color-sumi-600); line-height:1.7; margin-bottom:0.75rem; flex:1; }
.room-features { display:flex; flex-wrap:wrap; gap:0.3rem; margin-bottom:1rem; }
.feat-tag { font-size:0.65rem; padding:0.15rem 0.5rem; background:rgba(74,124,89,0.12); color:var(--color-matcha-600); border-radius:999px; font-family:'Inter',sans-serif; }
.room-card-actions { display:flex; gap:0.5rem; margin-top:auto; }
.btn-disabled { opacity:0.4; pointer-events:none; }

.empty-state { text-align:center; padding:5rem 0; }
.empty-kanji { font-family:'Noto Serif JP',serif; font-size:5rem; color:var(--color-kin-500); opacity:0.3; }

@media(max-width:900px) { .rooms-grid.grid3,.rooms-grid.grid2 { grid-template-columns:1fr; } }

@media(max-width: 768px) {
  .filter-inner {
    padding: 0.75rem 0;
  }
  .filter-group {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  .filter-chips {
    flex-wrap: nowrap;
    overflow-x: auto;
    width: 100vw;
    margin-left: -2rem;
    margin-right: -2rem;
    padding: 0.25rem 2rem;
    scrollbar-width: none;
    -ms-overflow-style: none;
    -webkit-overflow-scrolling: touch;
  }
  .filter-chips::-webkit-scrollbar {
    display: none;
  }
  .chip {
    flex-shrink: 0;
  }
  .view-mode-group {
    display: none !important;
  }
}
</style>
