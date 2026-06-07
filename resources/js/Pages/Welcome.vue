<template>
  <div>
    <!-- ========== HERO ========== -->
    <section class="hero-section">
      <!-- Background layers -->
      <div class="hero-bg"/>
      <div class="hero-overlay"/>

      <!-- Decorative large kanji -->
      <div class="hero-kanji-bg" aria-hidden="true">雅</div>

      <!-- Torii SVG decoration -->
      <div class="hero-torii" aria-hidden="true">
        <svg viewBox="0 0 300 220" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="20" y="60" width="260" height="14" rx="7" fill="rgba(155,35,53,0.55)"/>
          <rect x="40" y="80" width="220" height="10" rx="5" fill="rgba(155,35,53,0.35)"/>
          <rect x="50" y="90" width="16" height="130" rx="8" fill="rgba(155,35,53,0.5)"/>
          <rect x="234" y="90" width="16" height="130" rx="8" fill="rgba(155,35,53,0.5)"/>
        </svg>
      </div>

      <!-- Content -->
      <div class="hero-content container-zen">
        <p class="hero-subtitle-jp animate-fade-in">おもてなしの心でお迎えします</p>
        <h1 class="hero-heading animate-slide-up">
          Rasakan Keramahan<br>
          <em>Omotenashi Sejati</em>
        </h1>
        <p class="hero-desc animate-slide-up">Tempat di mana tradisi Jepang bertemu kemewahan modern. Setiap detail dirancang untuk ketenangan jiwa Anda.</p>
        <div class="hero-actions animate-slide-up">
          <Link href="/booking/buat" class="btn-vermillion hero-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Pesan Sekarang
          </Link>
          <Link href="/kamar" class="btn-outline-gold hero-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Lihat Kamar
          </Link>
        </div>
        <!-- Scroll indicator -->
        <div class="hero-scroll">
          <div class="scroll-line"/>
        </div>
      </div>

      <!-- Quick booking bar -->
      <div class="hero-booking-bar">
        <div class="container-zen booking-bar-inner">
          <div class="booking-field">
            <label class="booking-label">Check-In</label>
            <DatePicker v-model="quickBook.checkIn" :min="today" placeholder="Pilih Tanggal" direction="up" />
          </div>
          <div class="booking-divider"/>
          <div class="booking-field">
            <label class="booking-label">Check-Out</label>
            <DatePicker v-model="quickBook.checkOut" :min="quickBook.checkIn || today" placeholder="Pilih Tanggal" direction="up" />
          </div>
          <div class="booking-divider"/>
          <div class="booking-field">
            <label class="booking-label">Tamu</label>
            <CustomSelect v-model="quickBook.guests" :options="guestOptions" placeholder="Pilih Tamu" direction="up" />
          </div>
          <Link href="/kamar" class="btn-vermillion booking-submit">
            Cek Ketersediaan
          </Link>
        </div>
      </div>
    </section>

    <!-- ========== ROOMS ========== -->
    <section class="rooms-section pattern-washi">
      <div class="container-zen">
        <div class="section-header">
          <p class="section-kicker">客室とスイート</p>
          <h2 class="section-title">Kamar & Suite</h2>
          <p class="section-desc">Setiap kamar adalah sebuah karya seni — perpaduan estetika Jepang dan kenyamanan kontemporer</p>
        </div>
        <div class="rooms-grid">
          <div v-for="room in featuredRooms" :key="room.id" class="room-card card-shoji">
            <div class="room-image" :style="room.images && room.images.length > 0 ? { backgroundImage: `url(${room.images[0]})`, backgroundSize: 'cover', backgroundPosition: 'center' } : { background: roomGradient(room.colorTheme) }">
              <div class="room-type-badge">{{ room.type }}</div>
              <div class="room-price-overlay">{{ formatCurrency(room.price) }}<span>/malam</span></div>
              <div v-if="!room.images || room.images.length === 0" class="room-kanji-deco">{{ roomKanji(room.colorTheme) }}</div>
            </div>
            <div class="room-card-body">
              <h3 class="room-name">{{ room.name }}</h3>
              <p class="room-name-jp">{{ room.nameJP }}</p>
              <div class="room-rating">
                <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= Math.round(room.rating) }">★</span>
                <span class="rating-val">{{ room.rating }}</span>
                <span class="review-count">({{ room.reviewCount }} ulasan)</span>
              </div>
              <p class="room-desc">{{ room.description }}</p>
              <div class="room-features">
                <span v-for="f in room.features.slice(0,2)" :key="f" class="feature-tag">{{ f }}</span>
              </div>
              <div class="room-actions">
                <Link :href="`/kamar/${room.id}`" class="btn-vermillion" style="flex:1; text-align:center; font-size:0.82rem; padding:0.6rem 1rem;">
                  Lihat Detail
                </Link>
                <Link href="/booking/buat" class="btn-outline-gold" style="font-size:0.82rem; padding:0.6rem 1rem;">
                  Booking
                </Link>
              </div>
            </div>
          </div>
        </div>
        <div style="text-align:center; margin-top:2.5rem;">
          <Link href="/kamar" class="btn-outline-gold" style="padding:0.85rem 2.5rem;">
            Lihat Semua Kamar & Suite
          </Link>
        </div>
      </div>
    </section>

    <!-- ========== PHILOSOPHY ========== -->
    <section class="philosophy-section">
      <div class="container-zen philosophy-grid">
        <div class="philosophy-left">
          <p class="section-kicker" style="color:var(--color-sakura-200)">おもてなし</p>
          <h2 class="philosophy-heading">Filosofi Omotenashi</h2>
          <p class="philosophy-text">Omotenashi bukan sekadar pelayanan — ini adalah seni memberikan yang terbaik tanpa mengharapkan imbalan. Di Darma Mizuki, setiap anggota tim kami dididik untuk memahami kebutuhan tamu bahkan sebelum diucapkan.</p>
          <p class="philosophy-text">Dari lipatan handuk berbentuk origami, aroma sakura di koridor, hingga secangkir teh matcha hangat yang selalu siap — semuanya adalah bentuk cinta kami kepada setiap tamu.</p>
          <Link href="/tentang-kami" class="btn-outline-gold" style="margin-top:1.5rem; border-color:rgba(201,168,76,0.5); color:var(--color-kin-300);">
            Pelajari Lebih Lanjut
          </Link>
        </div>
        <div class="philosophy-right">
          <div v-for="pillar in pillars" :key="pillar.kanji" class="pillar-card">
            <div class="pillar-kanji">{{ pillar.kanji }}</div>
            <div>
              <div class="pillar-name">{{ pillar.name }}</div>
              <div class="pillar-desc">{{ pillar.desc }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========== FACILITIES ========== -->
    <section class="facilities-section pattern-washi">
      <div class="container-zen">
        <div class="section-header">
          <p class="section-kicker">施設</p>
          <h2 class="section-title">Fasilitas Hotel</h2>
          <p class="section-desc">Nikmati rangkaian fasilitas premium yang terinspirasi dari tradisi Jepang</p>
        </div>
        <div class="facilities-marquee-wrapper">
          <div class="facilities-marquee-content animate-marquee">
            <div v-for="f in activeFacilities" :key="'f1-' + f.id" class="facility-card card-shoji">
              <div class="facility-icon">{{ f.icon }}</div>
              <div class="facility-name">{{ f.name }}</div>
              <div class="facility-name-jp">{{ f.nameJP }}</div>
              <div class="facility-cat">{{ f.category }}</div>
            </div>
          </div>
          <div class="facilities-marquee-content animate-marquee" aria-hidden="true">
            <div v-for="f in activeFacilities" :key="'f2-' + f.id" class="facility-card card-shoji">
              <div class="facility-icon">{{ f.icon }}</div>
              <div class="facility-name">{{ f.name }}</div>
              <div class="facility-name-jp">{{ f.nameJP }}</div>
              <div class="facility-cat">{{ f.category }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========== TESTIMONIALS ========== -->
    <section class="testimonials-section">
      <div class="container-zen">
        <div class="section-header">
          <p class="section-kicker" style="color:var(--color-sakura-200)">お客様の声</p>
          <h2 class="section-title" style="color:#fff">Kata Tamu Kami</h2>
          <p class="section-desc" style="color:rgba(247,243,237,0.7)">Pengalaman nyata dari tamu yang telah merasakan keramahan Omotenashi</p>
        </div>
        <div class="testimonials-grid">
          <div v-for="t in testimonials.slice(0,3)" :key="t.id" class="testimonial-card">
            <div class="testi-quote">❝</div>
            <p class="testi-text">{{ t.comment }}</p>
            <div class="testi-stars">
              <span v-for="n in 5" :key="n" class="star filled">★</span>
            </div>
            <div class="testi-footer">
              <div class="testi-avatar">{{ t.name[0] }}</div>
              <div>
                <div class="testi-name">{{ t.name }}</div>
                <div class="testi-origin">{{ t.origin }} · {{ t.stayType }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ========== CTA ========== -->
    <section class="cta-section">
      <div class="cta-overlay"/>
      <div class="container-zen cta-content">
        <p class="section-kicker" style="color:var(--color-sakura-200)">旅を始めましょう</p>
        <h2 class="cta-heading">Mulai Perjalanan Anda Bersama Kami</h2>
        <p class="cta-desc">Biarkan kami menjadi tuan rumah terbaik dalam setiap momen berharga Anda</p>
        <div class="cta-actions">
          <Link href="/booking/buat" class="btn-vermillion" style="padding:1rem 2.5rem; font-size:1rem;">
            Booking Sekarang
          </Link>
          <a href="tel:+622188887777" class="btn-outline-gold" style="padding:1rem 2.5rem; font-size:1rem; border-color:rgba(201,168,76,0.6); color:var(--color-kin-300);">
            +62 21 8888-7777
          </a>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import { testimonials, facilities, hotelInfo, formatCurrency } from '@/data/mockData'
import { rooms } from '@/data/roomsStore'
import DatePicker from '@/Components/UI/DatePicker.vue'
import CustomSelect from '@/Components/UI/CustomSelect.vue'

defineOptions({ layout: AppLayout })

const today = new Date().toISOString().split('T')[0]
const quickBook = reactive({ checkIn: '', checkOut: '', guests: '2' })
const featuredRooms = computed(() => rooms.value.slice(0, 3))
const activeFacilities = computed(() => facilities.filter(f => f.isActive))

const guestOptions = [
  { value: '1', label: '1 Tamu' },
  { value: '2', label: '2 Tamu' },
  { value: '3', label: '3 Tamu' },
  { value: '4', label: '4 Tamu' }
]

const pillars = [
  { kanji: '誠', name: 'Ketulusan', desc: 'Melayani dengan hati yang tulus, tanpa mengharapkan imbalan apapun.' },
  { kanji: '丁', name: 'Ketelitian', desc: 'Memperhatikan setiap detail kecil demi kenyamanan sempurna tamu.' },
  { kanji: '温', name: 'Kehangatan', desc: 'Menyambut setiap tamu seperti keluarga yang datang ke rumah sendiri.' },
]

const roomGradient = (theme) => ({
  sakura: 'linear-gradient(135deg, #2D1B2E 0%, #8B3352 50%, #D4556A 100%)',
  matcha: 'linear-gradient(135deg, #1A2E1E 0%, #2E5C3A 50%, #4A8A5C 100%)',
  gold:   'linear-gradient(135deg, #1A1A0E 0%, #6B5012 50%, #C9A84C 100%)',
  ai:     'linear-gradient(135deg, #0E1A2E 0%, #1A3060 50%, #2C4A8C 100%)',
  beni:   'linear-gradient(135deg, #2E0E0E 0%, #7A1C28 50%, #B83347 100%)',
})[theme] || 'linear-gradient(135deg, #1A1A2E 0%, #2E2E50 100%)'

const roomKanji = (theme) => ({ sakura:'桜', matcha:'竹', gold:'雅', ai:'鯉', beni:'紅' })[theme] || '間'
</script>

<style scoped>
/* Hero */
.hero-section {
  min-height:100vh; position:relative; overflow:hidden;
  display:flex; flex-direction:column; justify-content:center;
}
.hero-bg {
  position:absolute; inset:0;
  background: linear-gradient(135deg, #0D0D1A 0%, #1A1A2E 35%, #2C4A7C 70%, #7A1C28 100%);
}
.hero-overlay { position:absolute; inset:0; background:rgba(13,13,26,0.55); }
.hero-kanji-bg {
  position:absolute; font-family:'Noto Serif JP',serif;
  font-size:clamp(12rem,25vw,22rem); font-weight:700;
  color:rgba(201,168,76,0.07); top:50%; left:50%;
  transform:translate(-50%,-50%); pointer-events:none; user-select:none;
  line-height:1;
}
.hero-torii {
  position:absolute; bottom:120px; left:50%; transform:translateX(-50%);
  width:min(300px,50vw); opacity:0.3; pointer-events:none;
}
.hero-content {
  position:relative; z-index:2; padding-top:7rem; padding-bottom:12rem;
  max-width:800px;
}
.hero-subtitle-jp {
  font-family:'Noto Serif JP',serif; font-size:0.9rem; letter-spacing:0.2em;
  color:rgba(232,165,165,0.85); margin-bottom:1.25rem;
}
.hero-heading {
  font-family:'Cormorant Garamond',serif; font-size:clamp(2.8rem,6vw,5rem);
  font-weight:600; color:#fff; line-height:1.15; margin-bottom:1.25rem;
}
.hero-heading em { color:var(--color-sakura-200); font-style:italic; }
.hero-desc { color:rgba(247,243,237,0.75); font-size:1.05rem; max-width:520px; margin-bottom:2rem; line-height:1.8; }
.hero-actions { display:flex; gap:1rem; flex-wrap:wrap; }
.hero-btn { padding:0.9rem 2rem; font-size:0.95rem; }
.hero-scroll {
  display:flex; flex-direction:column; align-items:flex-start; gap:0.5rem;
  margin-top:3rem; color:rgba(247,243,237,0.5);
  font-family:'Inter',sans-serif; font-size:0.72rem; letter-spacing:0.15em;
}
.scroll-line { width:1px; height:40px; background:linear-gradient(180deg,rgba(247,243,237,0.5),transparent); animation:float 2s ease-in-out infinite; }

/* Booking bar */
.hero-booking-bar {
  position:absolute; bottom:0; left:0; right:0; z-index:10;
  background:rgba(253,250,245,0.97); backdrop-filter:blur(12px);
  border-top:1px solid rgba(201,168,76,0.2);
  box-shadow:0 -4px 24px rgba(26,26,46,0.1);
}
.booking-bar-inner { display:flex; align-items:center; gap:0; height:72px; }
.booking-field { display:flex; flex-direction:column; justify-content:center; gap:2px; padding:0 1.5rem; flex:1; }
.booking-label { font-family:'Noto Serif JP',serif; font-size:0.65rem; letter-spacing:0.12em; color:var(--color-kin-500); font-weight:500; }
.booking-input { background:transparent; border:none; outline:none; font-family:'Inter',sans-serif; font-size:0.9rem; color:var(--color-sumi-800); cursor:pointer; }
.booking-divider { width:1px; height:36px; background:rgba(201,168,76,0.25); flex-shrink:0; }
.booking-submit { margin:0 1rem; white-space:nowrap; padding:0.7rem 1.5rem; font-size:0.85rem; }

/* Sections common */
.section-header { text-align:center; margin-bottom:3rem; }
.section-kicker { font-family:'Noto Serif JP',serif; font-size:0.8rem; letter-spacing:0.2em; color:var(--color-beni-500); margin-bottom:0.5rem; }
.section-title { font-family:'Cormorant Garamond',serif; font-size:clamp(2rem,4vw,2.8rem); font-weight:600; color:var(--color-sumi-800); }
.section-desc { color:var(--color-sumi-600); font-size:0.95rem; margin-top:0.75rem; max-width:520px; margin-left:auto; margin-right:auto; line-height:1.8; }

/* Rooms */
.rooms-section { padding:5rem 0; }
.rooms-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.75rem; }
.room-card { border-radius:4px; overflow:hidden; }
.room-image { height:220px; position:relative; display:flex; align-items:flex-end; overflow:hidden; }
.room-type-badge { position:absolute; top:1rem; left:1rem; background:rgba(26,26,46,0.7); color:var(--color-kin-300); font-size:0.7rem; letter-spacing:0.12em; padding:0.25rem 0.6rem; border-radius:2px; font-family:'Noto Serif JP',serif; backdrop-filter:blur(4px); }
.room-price-overlay { position:absolute; bottom:1rem; right:1rem; background:var(--color-beni-600); color:#fff; font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:600; padding:0.3rem 0.8rem; border-radius:2px; }
.room-price-overlay span { font-size:0.7rem; font-family:'Inter',sans-serif; opacity:0.8; }
.room-kanji-deco { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-family:'Noto Serif JP',serif; font-size:5rem; font-weight:700; color:rgba(255,255,255,0.08); pointer-events:none; }
.room-card-body { padding:1.25rem; }
.room-name { font-family:'Cormorant Garamond',serif; font-size:1.25rem; font-weight:600; color:var(--color-sumi-800); }
.room-name-jp { font-family:'Noto Serif JP',serif; font-size:0.72rem; color:var(--color-kin-500); letter-spacing:0.12em; margin-bottom:0.5rem; }
.room-rating { display:flex; align-items:center; gap:0.2rem; margin-bottom:0.5rem; }
.star { color:#ddd; font-size:0.85rem; }
.star.filled { color:var(--color-kin-500); }
.rating-val { font-size:0.82rem; font-weight:600; color:var(--color-sumi-700); margin-left:0.25rem; }
.review-count { font-size:0.75rem; color:var(--color-sumi-600); }
.room-desc { font-size:0.82rem; color:var(--color-sumi-600); line-height:1.7; margin-bottom:0.75rem; }
.room-features { display:flex; flex-wrap:wrap; gap:0.35rem; margin-bottom:1rem; }
.feature-tag { font-size:0.7rem; padding:0.2rem 0.55rem; background:var(--color-matcha-100,#D4E8DC); color:var(--color-matcha-700); border-radius:999px; font-family:'Inter',sans-serif; }
.room-actions { display:flex; gap:0.5rem; }

/* Philosophy */
.philosophy-section { background:var(--color-matcha-700); padding:5rem 0; }
.philosophy-grid { display:grid; grid-template-columns:1fr 1fr; gap:4rem; align-items:center; }
.philosophy-heading { font-family:'Cormorant Garamond',serif; font-size:clamp(2rem,4vw,2.8rem); color:#fff; margin-bottom:1.25rem; }
.philosophy-text { color:rgba(247,243,237,0.8); line-height:1.9; font-size:0.95rem; margin-bottom:0.75rem; }
.pillar-card { display:flex; align-items:flex-start; gap:1.25rem; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); border-radius:4px; padding:1.25rem; margin-bottom:1rem; transition:0.3s; }
.pillar-card:hover { background:rgba(255,255,255,0.13); transform:translateX(4px); }
.pillar-kanji { font-family:'Noto Serif JP',serif; font-size:2.5rem; font-weight:700; color:var(--color-sakura-200); opacity:0.9; flex-shrink:0; line-height:1; }
.pillar-name { font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:600; color:#fff; margin-bottom:0.25rem; }
.pillar-desc { font-size:0.82rem; color:rgba(247,243,237,0.7); line-height:1.7; }

/* Facilities */
.facilities-section { padding:5rem 0; }
.facilities-marquee-wrapper { display:flex; overflow:hidden; user-select:none; gap:1.25rem; padding:1rem 0; position:relative; width:100%; }
.facilities-marquee-wrapper::before, .facilities-marquee-wrapper::after { content:''; position:absolute; top:0; width:80px; height:100%; z-index:2; pointer-events:none; }
.facilities-marquee-wrapper::before { left:0; background:linear-gradient(90deg, var(--color-washi-100) 0%, transparent 100%); }
.facilities-marquee-wrapper::after { right:0; background:linear-gradient(270deg, var(--color-washi-100) 0%, transparent 100%); }
.facilities-marquee-content { flex-shrink:0; display:flex; justify-content:space-around; min-width:100%; gap:1.25rem; }
@keyframes marquee { 0% { transform:translateX(0%); } 100% { transform:translateX(calc(-100% - 1.25rem)); } }
.animate-marquee { animation:marquee 30s linear infinite; }
.facilities-marquee-wrapper:hover .animate-marquee { animation-play-state:paused; }
.facility-card { flex-shrink:0; width:160px; text-align:center; padding:1.5rem 1rem; cursor:pointer; }
.facility-icon { font-size:2.2rem; margin-bottom:0.75rem; }
.facility-name { font-family:'Cormorant Garamond',serif; font-size:0.95rem; font-weight:600; color:var(--color-sumi-800); }
.facility-name-jp { font-family:'Noto Serif JP',serif; font-size:0.62rem; color:var(--color-kin-500); letter-spacing:0.12em; margin-top:2px; }
.facility-cat { font-size:0.68rem; color:var(--color-sumi-600); margin-top:0.4rem; font-family:'Inter',sans-serif; }

/* Testimonials */
.testimonials-section { background:var(--color-sumi-800); padding:5rem 0; }
.testimonials-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; }
.testimonial-card { background:rgba(255,255,255,0.05); border:1px solid rgba(201,168,76,0.15); border-radius:4px; padding:2rem; position:relative; transition:0.3s; }
.testimonial-card:hover { background:rgba(255,255,255,0.08); border-color:rgba(201,168,76,0.3); }
.testi-quote { font-family:'Cormorant Garamond',serif; font-size:3.5rem; color:var(--color-beni-500); opacity:0.5; line-height:1; margin-bottom:0.5rem; }
.testi-text { font-size:0.88rem; color:rgba(247,243,237,0.8); line-height:1.85; margin-bottom:1rem; font-style:italic; font-family:'Cormorant Garamond',serif; font-size:1rem; }
.testi-stars { display:flex; gap:0.2rem; margin-bottom:1rem; }
.testi-footer { display:flex; align-items:center; gap:0.75rem; }
.testi-avatar { width:38px; height:38px; border-radius:50%; background:var(--color-beni-600); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:0.9rem; flex-shrink:0; }
.testi-name { font-family:'Cormorant Garamond',serif; font-size:1rem; font-weight:600; color:var(--color-kin-300); }
.testi-origin { font-size:0.72rem; color:rgba(247,243,237,0.5); font-family:'Inter',sans-serif; }

/* CTA */
.cta-section {
  position:relative; padding:6rem 0;
  background:linear-gradient(135deg, #0D0D1A 0%, #7A1C28 60%, #1A1A2E 100%);
  text-align:center;
}
.cta-overlay { position:absolute; inset:0; background:rgba(13,13,26,0.4); }
.cta-content { position:relative; z-index:2; }
.cta-heading { font-family:'Cormorant Garamond',serif; font-size:clamp(2rem,4vw,3rem); color:#fff; margin:0.5rem 0 1rem; }
.cta-desc { color:rgba(247,243,237,0.75); font-size:1rem; margin-bottom:2rem; }
.cta-actions { display:flex; gap:1rem; justify-content:center; flex-wrap:wrap; }

@media(max-width:900px) {
  .rooms-grid { grid-template-columns:1fr; }
  .philosophy-grid { grid-template-columns:1fr; gap:2rem; }
  .testimonials-grid { grid-template-columns:1fr; }
  .booking-bar-inner { flex-wrap:wrap; height:auto; padding:1rem; gap:0.75rem; }
  .booking-field { padding:0; }
  .booking-submit { width:100%; justify-content:center; }
  .booking-divider { display:none; }
  .hero-booking-bar { position:relative; }
  .hero-content { padding-top:5rem; padding-bottom:3rem; }
}

/* Custom styles for integrated datepicker & select inside booking bar */
.hero-booking-bar :deep(.datepicker-input-trigger),
.hero-booking-bar :deep(.select-trigger) {
  border: none !important;
  background: transparent !important;
  padding: 0.2rem 0 !important;
  box-shadow: none !important;
}
.hero-booking-bar :deep(.datepicker-value),
.hero-booking-bar :deep(.select-trigger-label) {
  font-family: 'Inter', sans-serif;
  font-size: 0.9rem;
  color: var(--color-sumi-800);
  font-weight: 500;
}
</style>
