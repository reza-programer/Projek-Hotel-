<template>
  <div>
    <!-- Hero -->
    <section class="about-hero">
      <div class="about-hero-bg"/>
      <div class="about-hero-kanji" aria-hidden="true">雅</div>
      <div class="container-zen about-hero-content">
        <p class="section-kicker" style="color:var(--color-sakura-200)">アルニカ 旅館について</p>
        <h1 class="about-hero-title">Tentang Darma Mizuki</h1>
        <p class="about-hero-subtitle">Warisan keramahan Jepang yang hidup di jantung kota</p>
      </div>
    </section>

    <!-- Our Story -->
    <section style="padding:5rem 0; background:var(--color-washi-100)">
      <div class="container-zen story-grid">
        <div class="story-image">
          <div class="story-img-inner">
            <div class="story-img-kanji">語</div>
            <div class="story-img-text">私たちの物語</div>
          </div>
          <div class="story-img-frame"/>
        </div>
        <div class="story-text">
          <p class="section-kicker">私たちの物語</p>
          <h2 style="font-family:'Cormorant Garamond',serif; font-size:2.4rem; color:var(--color-sumi-800); margin-bottom:1.25rem;">Kisah Kami</h2>
          <p style="color:var(--color-sumi-600); line-height:1.9; margin-bottom:1rem;">Darma Mizuki lahir pada tahun 2018 dari sebuah mimpi sederhana namun mendalam: menghadirkan jiwa ryokan Jepang di Indonesia. Didirikan oleh pasangan yang jatuh cinta dengan filosofi Omotenashi selama tinggal di Kyoto, hotel ini dibangun di atas fondasi ketulusan dan perhatian tanpa syarat.</p>
          <p style="color:var(--color-sumi-600); line-height:1.9; margin-bottom:1rem;">Setiap sudut hotel mencerminkan "wabi-sabi" — keindahan dalam kesederhanaan. Dari taman batu yang dirancang oleh maestro zen dari Nara, hingga koleksi sake premium dari berbagai prefektur Jepang, semua hadir untuk menciptakan pengalaman yang tak terlupakan.</p>
          <div class="story-badge">
            <span>✦</span> Berdiri sejak 2018
            <span style="margin:0 0.5rem;">·</span>
            <span>✦</span> 84 Kamar Premium
            <span style="margin:0 0.5rem;">·</span>
            <span>✦</span> Rating 5.0 Bintang
          </div>
        </div>
      </div>
    </section>

    <!-- Omotenashi Pillars -->
    <section style="padding:5rem 0; background:var(--color-washi-300);">
      <div class="container-zen">
        <div class="section-header">
          <p class="section-kicker">おもてなしの三柱</p>
          <h2 style="font-family:'Cormorant Garamond',serif; font-size:2.4rem; color:var(--color-sumi-800);">Tiga Pilar Omotenashi</h2>
          <p style="color:var(--color-sumi-600); margin-top:0.75rem; max-width:500px; margin-left:auto; margin-right:auto; line-height:1.8;">Filosofi yang menjadi jiwa dari setiap pelayanan kami</p>
        </div>
        <div class="pillars-grid">
          <div v-for="(pillar, i) in omotenashiPillars" :key="pillar.kanji" class="pillar-full-card">
            <div class="pillar-number">0{{ i+1 }}</div>
            <div class="pillar-kanji-lg">{{ pillar.kanji }}</div>
            <h3 class="pillar-title">{{ pillar.name }}</h3>
            <p class="pillar-title-jp">{{ pillar.jp }}</p>
            <p class="pillar-body">{{ pillar.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Team -->
    <section style="padding:5rem 0; background:var(--color-washi-100)">
      <div class="container-zen">
        <div class="section-header">
          <p class="section-kicker">チーム</p>
          <h2 style="font-family:'Cormorant Garamond',serif; font-size:2.4rem; color:var(--color-sumi-800);">Tim Kami</h2>
          <p style="color:var(--color-sumi-600); margin-top:0.75rem;">Orang-orang berdedikasi di balik pengalaman Omotenashi Anda</p>
        </div>
        <div class="team-grid">
          <div v-for="member in team" :key="member.name" class="team-card card-shoji">
            <div class="team-avatar" :style="{ background: member.gradient }">
              {{ member.initials }}
            </div>
            <div class="team-name">{{ member.name }}</div>
            <div class="team-role">{{ member.role }}</div>
            <div class="team-role-jp">{{ member.roleJP }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Awards -->
    <section style="background:var(--color-washi-300); padding:4rem 0; overflow:hidden;">
      <div class="container-zen">
        <div class="section-header">
          <p class="section-kicker">受賞歴</p>
          <h2 style="font-family:'Cormorant Garamond',serif; font-size:2.2rem; color:var(--color-sumi-800);">Penghargaan & Pengakuan</h2>
        </div>
        <div class="awards-scroll">
          <div v-for="award in awards" :key="award.title" class="award-card card-shoji">
            <div class="award-icon">{{ award.icon }}</div>
            <div class="award-title">{{ award.title }}</div>
            <div class="award-year">{{ award.year }}</div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import { hotelInfo } from '@/data/mockData'

defineOptions({ layout: AppLayout })

const omotenashiPillars = [
  { kanji:'誠', name:'Ketulusan', jp:'Makoto', desc:'Pelayanan yang tulus lahir dari hati, bukan dari kewajiban. Setiap senyum, setiap sapaan, adalah ekspresi ketulusan yang tak dibuat-buat.' },
  { kanji:'丁', name:'Ketelitian', jp:'Teinei', desc:'Kesempurnaan tersembunyi di detail. Dari lipatan handuk yang presisi hingga suhu air minum yang ideal — kami memperhatikan yang luput dari perhatian.' },
  { kanji:'温', name:'Kehangatan', jp:'Atatakasa', desc:'Menyambut setiap tamu bukan sebagai pelanggan, melainkan sebagai tamu kehormatan yang datang ke rumah sendiri, penuh kehangatan dan keakraban.' },
]

const team = [
  { name:'Hiroshi Watanabe', role:'General Manager', roleJP:'総支配人', initials:'HW', gradient:'linear-gradient(135deg,#7A1C28,#B83347)' },
  { name:'Sari Indraswari', role:'Head of Hospitality', roleJP:'おもてなし部門長', initials:'SI', gradient:'linear-gradient(135deg,#2E5C3A,#4A8A5C)' },
  { name:'Kenji Matsumoto', role:'Executive Chef', roleJP:'料理長', initials:'KM', gradient:'linear-gradient(135deg,#6B5012,#C9A84C)' },
  { name:'Dewi Rahayu', role:'Spa Director', roleJP:'スパ部門長', initials:'DR', gradient:'linear-gradient(135deg,#1A3060,#2C4A8C)' },
]

const awards = [
  { icon:'🏆', title:'Best Japanese Concept Hotel', year:'2024 · TripAdvisor' },
  { icon:'⭐', title:'Five Star Luxury Award', year:'2023 · Forbes Travel' },
  { icon:'🌸', title:'Most Authentic Omotenashi', year:'2023 · Asia Hotel Awards' },
  { icon:'🍵', title:'Best Tea Experience in Asia', year:'2022 · Tea Culture Magazine' },
  { icon:'♨️', title:'Best Onsen Hotel Indonesia', year:'2022 · Wellness Indonesia' },
  { icon:'🎌', title:'Japanese Cultural Ambassador', year:'2021 · JNTO Indonesia' },
]
</script>

<style scoped>
.about-hero { min-height:55vh; position:relative; display:flex; align-items:center; overflow:hidden; }
.about-hero-bg { position:absolute; inset:0; background:linear-gradient(135deg, #0D0D1A 0%, #2C4A7C 60%, #7A1C28 100%); }
.about-hero-kanji { position:absolute; font-family:'Noto Serif JP',serif; font-size:22rem; font-weight:700; color:rgba(201,168,76,0.06); top:50%; right:-2rem; transform:translateY(-50%); pointer-events:none; }
.about-hero-content { position:relative; z-index:2; padding:6rem 0 4rem; }
.about-hero-title { font-family:'Cormorant Garamond',serif; font-size:clamp(2.5rem,5vw,4rem); font-weight:600; color:#fff; margin:0.5rem 0; }
.about-hero-subtitle { color:rgba(247,243,237,0.7); font-size:1.1rem; font-family:'Cormorant Garamond',serif; font-style:italic; }

.story-grid { display:grid; grid-template-columns:1fr 1fr; gap:4rem; align-items:center; }
.story-image { position:relative; }
.story-img-inner { height:420px; background:linear-gradient(135deg,#7A1C28,#2C4A7C); border-radius:4px; display:flex; flex-direction:column; align-items:center; justify-content:center; position:relative; z-index:1; }
.story-img-kanji { font-family:'Noto Serif JP',serif; font-size:7rem; font-weight:700; color:rgba(255,255,255,0.2); }
.story-img-text { font-family:'Noto Serif JP',serif; font-size:0.8rem; letter-spacing:0.25em; color:rgba(255,255,255,0.5); }
.story-img-frame { position:absolute; top:1.5rem; left:1.5rem; right:-1.5rem; bottom:-1.5rem; border:1px solid rgba(201,168,76,0.4); border-radius:4px; }
.story-badge { margin-top:1.5rem; font-family:'Noto Serif JP',serif; font-size:0.78rem; color:var(--color-beni-600); letter-spacing:0.08em; }

.pillars-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:2rem; margin-top:1rem; }
.pillar-full-card { background:rgba(253,250,245,0.9); border:1px solid rgba(201,168,76,0.2); border-radius:4px; padding:2.5rem 2rem; text-align:center; transition:0.3s; }
.pillar-full-card:hover { transform:translateY(-6px); box-shadow:0 12px 40px rgba(26,26,46,0.12); }
.pillar-number { font-family:'Cormorant Garamond',serif; font-size:0.8rem; letter-spacing:0.2em; color:var(--color-kin-500); margin-bottom:0.75rem; }
.pillar-kanji-lg { font-family:'Noto Serif JP',serif; font-size:4rem; font-weight:700; color:var(--color-beni-500); margin-bottom:0.5rem; line-height:1; }
.pillar-title { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--color-sumi-800); }
.pillar-title-jp { font-family:'Noto Serif JP',serif; font-size:0.72rem; color:var(--color-kin-500); letter-spacing:0.15em; margin:0.25rem 0 0.75rem; }
.pillar-body { font-size:0.85rem; color:var(--color-sumi-600); line-height:1.8; }

.team-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1.5rem; }
.team-card { padding:2rem; text-align:center; }
.team-avatar { width:80px; height:80px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:'Cormorant Garamond',serif; font-size:1.8rem; font-weight:700; color:#fff; margin:0 auto 1rem; }
.team-name { font-family:'Cormorant Garamond',serif; font-size:1.05rem; font-weight:600; color:var(--color-sumi-800); }
.team-role { font-size:0.8rem; color:var(--color-beni-600); margin-top:0.25rem; font-family:'Inter',sans-serif; }
.team-role-jp { font-family:'Noto Serif JP',serif; font-size:0.65rem; color:var(--color-kin-500); letter-spacing:0.1em; margin-top:2px; }

.awards-scroll { display:flex; gap:1.25rem; overflow-x:auto; padding-bottom:1rem; }
.awards-scroll::-webkit-scrollbar { height:4px; }
.awards-scroll::-webkit-scrollbar-thumb { background:rgba(201,168,76,0.3); border-radius:2px; }
.award-card { flex-shrink:0; width:200px; padding:1.5rem; text-align:center; }
.award-icon { font-size:2rem; margin-bottom:0.75rem; }
.award-title { font-family:'Cormorant Garamond',serif; font-size:0.95rem; font-weight:600; color:var(--color-sumi-800); line-height:1.4; }
.award-year { font-size:0.72rem; color:var(--color-kin-500); margin-top:0.4rem; font-family:'Inter',sans-serif; }

.section-kicker { font-family:'Noto Serif JP',serif; font-size:0.8rem; letter-spacing:0.2em; color:var(--color-beni-500); margin-bottom:0.5rem; }
.section-header { text-align:center; margin-bottom:3rem; }

@media(max-width:900px) {
  .story-grid { grid-template-columns:1fr; }
  .pillars-grid { grid-template-columns:1fr; }
  .team-grid { grid-template-columns:repeat(2,1fr); }
}
</style>
