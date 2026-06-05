<template>
  <div class="reviews-page pattern-washi" style="min-height:100vh; padding:6rem 0 4rem;">
    <div class="container-zen">
      <div style="margin-bottom:2rem;">
        <p class="section-kicker">お客様の声</p>
        <h1 style="font-family:'Cormorant Garamond',serif; font-size:2.4rem; font-weight:600; color:var(--color-sumi-800);">Ulasan & Kritik</h1>
        <p style="color:var(--color-sumi-600); font-size:0.9rem; margin-top:0.25rem;">Bagikan pengalaman Anda menginap bersama pelayanan tulus Omotenashi kami.</p>
      </div>

      <!-- Main Layout Grid -->
      <div class="reviews-grid">
        <!-- Left: Form to Write Review -->
        <div class="review-form-card card-shoji">
          <h2 class="card-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="edit" /> Tulis Ulasan Baru</h2>
          <div class="divider-ink" style="margin: 1rem 0;"></div>

          <form @submit.prevent="submitReview" class="zen-form">
            <!-- Reservation / Room Selection -->
            <div class="form-group">
              <label class="label-zen">KUNJUNGAN TERBARU / KAMAR</label>
              <select v-model="form.reservationId" class="input-zen" required>
                <option value="" disabled>Pilih kunjungan kamar untuk diulas</option>
                <option v-for="res in eligibleReservations" :key="res.id" :value="res.id">
                  {{ res.roomName }} ({{ formatDateShort(res.checkIn) }})
                </option>
              </select>
            </div>

            <!-- Star Rating -->
            <div class="form-group">
              <label class="label-zen">RATING PENGALAMAN</label>
              <div class="rating-selector">
                <span 
                  v-for="star in 5" 
                  :key="star" 
                  class="star-btn"
                  :class="{ filled: star <= form.rating }"
                  @click="form.rating = star"
                >
                  ★
                </span>
                <span class="rating-label">{{ ratingText }}</span>
              </div>
            </div>

            <!-- Comment -->
            <div class="form-group">
              <label class="label-zen">ULASAN ANDA</label>
              <textarea 
                v-model="form.comment" 
                class="input-zen text-area-zen" 
                rows="4" 
                placeholder="Bagaimana pelayanan kami? Apa yang paling membuat Anda terkesan? (kebersihan, kenyamanan ofuro, keramahan staf, hidangan kaiseki...)"
                required
              ></textarea>
            </div>

            <!-- Suggestion -->
            <div class="form-group">
              <label class="label-zen">SARAN PENINGKATAN (OPSIONAL)</label>
              <input 
                v-model="form.suggestion" 
                type="text" 
                class="input-zen" 
                placeholder="Hal-hal yang bisa kami tingkatkan untuk kunjungan berikutnya"
              />
            </div>

            <!-- Action -->
            <div style="margin-top:1.5rem; text-align:right;">
              <button type="submit" class="btn-vermillion" :disabled="submitting">
                {{ submitting ? 'Mengirim...' : 'Kirim Ulasan' }}
              </button>
            </div>
          </form>
        </div>

        <!-- Right: Past Reviews History -->
        <div class="reviews-history">
          <h2 class="history-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="pending" /> Riwayat Ulasan Anda</h2>
          
          <div v-if="myReviews.length === 0" class="empty-state card-shoji">
            <div class="empty-kanji">無</div>
            <p style="font-family:'Cormorant Garamond',serif; font-size:1.1rem; color:var(--color-sumi-600);">Anda belum pernah menulis ulasan.</p>
          </div>

          <div v-else class="reviews-list">
            <div v-for="rev in myReviews" :key="rev.id" class="past-review-card card-shoji">
              <div class="rev-header">
                <div>
                  <h3 class="rev-room">{{ rev.roomName }}</h3>
                  <p class="rev-date">Menginap pada {{ formatDateShort(rev.stayDate) }} • Diulas {{ formatDateShort(rev.date) }}</p>
                </div>
                <div class="rev-stars">
                  <span v-for="star in 5" :key="star" class="star" :class="{ filled: star <= rev.rating }">★</span>
                </div>
              </div>
              <p class="rev-comment">"{{ rev.comment }}"</p>
              <div v-if="rev.reply" class="admin-reply">
                <div class="reply-header">
                  <span class="reply-avatar" style="display:inline-flex;"><SvgIcon name="ikebana" width="14" height="14" class="text-beni" /></span>
                  <span class="reply-author">Tanggapan dari Miyabi Host (Omotenashi Team)</span>
                </div>
                <p class="reply-content">{{ rev.reply }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import SvgIcon from '@/Components/UI/SvgIcon.vue'
import { reservations, formatCurrency } from '@/data/mockData'

defineOptions({ layout: AppLayout })

const submitting = ref(false)

// Select completed reservations
const eligibleReservations = computed(() => {
  return reservations.filter(r => r.status === 'completed')
})

const form = ref({
  reservationId: '',
  rating: 5,
  comment: '',
  suggestion: ''
})

const ratingTexts = {
  1: 'Sangat Mengecewakan',
  2: 'Kurang Memuaskan',
  3: 'Cukup Baik',
  4: 'Sangat Memuaskan',
  5: 'Luar Biasa (Omotenashi Yang Sempurna)'
}

const ratingText = computed(() => ratingTexts[form.value.rating])

// Mock past reviews for this user
const myReviews = ref([
  {
    id: 101,
    roomName: 'Zen Garden Suite',
    stayDate: '2024-12-24',
    date: '2024-12-28',
    rating: 5,
    comment: 'Puncak kemewahan tradisi Jepang yang sesungguhnya. Zen garden pribadinya sungguh menenangkan, dan ofuro bathtub-nya sangat membantu melepas lelah. Makanan Kaiseki lezat tiada tanding. Kami dilayani bagai kaisar.',
    reply: 'Terima kasih banyak atas ulasan indah Anda. Kami sangat senang taman zen dan pelayanan kami mendatangkan ketenangan bagi Anda. Kami menantikan kehadiran Anda kembali di kediaman Miyabi.',
    suggestion: ''
  }
])

const submitReview = () => {
  if (!form.value.reservationId) {
    alert('Silakan pilih kamar kunjungan terlebih dahulu.')
    return
  }

  submitting.value = true
  
  // Simulate API Call
  setTimeout(() => {
    const selectedRes = eligibleReservations.value.find(r => r.id === form.value.reservationId)
    
    const newRev = {
      id: Date.now(),
      roomName: selectedRes ? selectedRes.roomName : 'Room',
      stayDate: selectedRes ? selectedRes.checkIn : '2025-01-01',
      date: new Date().toISOString().split('T')[0],
      rating: form.value.rating,
      comment: form.value.comment,
      suggestion: form.value.suggestion,
      reply: null
    }

    myReviews.value.unshift(newRev)
    
    // Reset Form
    form.value = {
      reservationId: '',
      rating: 5,
      comment: '',
      suggestion: ''
    }

    submitting.value = false
    alert('Ulasan Anda telah berhasil terkirim. Terima kasih atas apresiasi Anda!')
  }, 1000)
}

const formatDateShort = (dateStr) => {
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'short', year: 'numeric'
  }).format(new Date(dateStr))
}
</script>

<style scoped>
.section-kicker { font-family:'Noto Serif JP',serif; font-size:0.8rem; letter-spacing:0.2em; color:var(--color-beni-500); margin-bottom:0.5rem; display:block; }
.reviews-grid { display:grid; grid-template-columns: 1fr 1.2fr; gap:2.5rem; align-items:start; margin-top:1rem; }

.review-form-card { padding:2rem; border-radius:4px; }
.card-title { font-family:'Cormorant Garamond',serif; font-size:1.5rem; font-weight:600; color:var(--color-sumi-800); }

.form-group { margin-bottom:1.25rem; }
.rating-selector { display:flex; align-items:center; gap:0.5rem; margin-top:0.25rem; }
.star-btn { font-size:1.8rem; color:#E0E0E0; cursor:pointer; transition:color 0.15s, transform 0.1s; }
.star-btn.filled { color:var(--color-kin-500); }
.star-btn:hover { transform:scale(1.15); color:var(--color-kin-300); }
.rating-label { font-size:0.8rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; margin-left:0.5rem; font-weight:500; }

.text-area-zen { resize:vertical; min-height:100px; }

.reviews-history { display:flex; flex-direction:column; gap:1.25rem; }
.history-title { font-family:'Cormorant Garamond',serif; font-size:1.5rem; font-weight:600; color:var(--color-sumi-800); }

.reviews-list { display:flex; flex-direction:column; gap:1rem; }
.past-review-card { padding:1.5rem; border-radius:4px; }
.rev-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem; }
.rev-room { font-family:'Cormorant Garamond',serif; font-size:1.2rem; font-weight:600; color:var(--color-sumi-800); }
.rev-date { font-size:0.72rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; }
.rev-stars { display:flex; gap:2px; }
.rev-stars .star { color:#E0E0E0; font-size:1rem; }
.rev-stars .star.filled { color:var(--color-kin-500); }

.rev-comment { font-size:0.88rem; color:var(--color-sumi-700); font-style:italic; line-height:1.6; }

.admin-reply { margin-top:1rem; padding:0.85rem 1.1rem; background:rgba(201,168,76,0.06); border-left:2px solid var(--color-kin-500); border-radius:2px; }
.reply-header { display:flex; align-items:center; gap:0.4rem; margin-bottom:0.4rem; }
.reply-avatar { font-size:0.9rem; }
.reply-author { font-size:0.72rem; font-family:'Noto Serif JP',serif; font-weight:600; color:var(--color-kin-700); }
.reply-content { font-size:0.8rem; color:var(--color-sumi-700); line-height:1.5; }

.empty-state { text-align:center; padding:3rem 1.5rem; border-radius:4px; }
.empty-kanji { font-family:'Noto Serif JP',serif; font-size:4rem; color:var(--color-kin-500); opacity:0.2; margin-bottom:0.5rem; }

@media(max-width:991px) {
  .reviews-grid { grid-template-columns: 1fr; gap:2rem; }
}
</style>
