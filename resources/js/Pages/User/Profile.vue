<template>
  <div class="profile-page pattern-washi" style="min-height:100vh; padding:6rem 0 4rem;">
    <div class="container-zen">
      <div style="margin-bottom:2rem;">
        <p class="section-kicker">プロフィール</p>
        <h1 style="font-family:'Cormorant Garamond',serif; font-size:2.4rem; font-weight:600; color:var(--color-sumi-800);">Profil Saya</h1>
      </div>

      <div class="profile-grid">
        <!-- Left Column: User Identity & Status -->
        <div class="profile-col-left">
          <div class="card-shoji user-card">
            <div class="uc-header">
              <div class="avatar-circle">
                {{ guestName.charAt(0).toUpperCase() }}
              </div>
              <div class="uc-info">
                <h2>{{ guestName }}</h2>
                <div class="uc-email">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                  <span>{{ guestEmail }}</span>
                </div>
              </div>
            </div>
            
            <div class="divider-ink" style="margin: 1.5rem 0;"></div>
            
            <div class="membership-badge">
              <div class="mb-icon">阿</div>
              <div class="mb-details">
                <div class="mb-title">Darma Mizuki Club Member</div>
                <div class="mb-tier">Tier: <strong>Gold (Kiku)</strong></div>
              </div>
            </div>

            <div class="points-section">
              <div class="pt-label">Darma Mizuki Zen Points</div>
              <div class="pt-value">12,450 <span>pts</span></div>
              <p class="pt-desc">Dapat ditukarkan dengan Spa Tradisional atau Upacara Minum Teh.</p>
            </div>

            <button class="btn-outline-gold w-full mt-4" @click="editMode = !editMode">
              {{ editMode ? 'Simpan Perubahan' : 'Edit Profil' }}
            </button>
          </div>
        </div>

        <!-- Right Column: Omotenashi Preferences -->
        <div class="profile-col-right">
          <div class="card-shoji pref-card">
            <div class="pref-header">
              <h3 style="font-family:'Cormorant Garamond',serif; font-size:1.6rem; color:var(--color-sumi-800); font-weight:600;">Preferensi Omotenashi</h3>
              <p style="font-size:0.85rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; margin-top:0.25rem;">Bantu kami menyiapkan kedatangan Anda dengan sempurna sesuai tradisi keramahtamahan Jepang.</p>
            </div>

            <div class="pref-list" v-if="!editMode">
              <div class="pref-item">
                <div class="pi-label">Pilihan Teh Selamat Datang</div>
                <div class="pi-value">Matcha Premium (抹茶)</div>
              </div>
              <div class="pref-item">
                <div class="pi-label">Ukuran Yukata</div>
                <div class="pi-value">L (Large)</div>
              </div>
              <div class="pref-item">
                <div class="pi-label">Jenis Bantal</div>
                <div class="pi-value">Soba Hukura (Bantal Kulit Gandum)</div>
              </div>
              <div class="pref-item">
                <div class="pi-label">Alergi Makanan</div>
                <div class="pi-value text-muted">Tidak ada</div>
              </div>
              <div class="pref-item">
                <div class="pi-label">Waktu Sarapan Favorit</div>
                <div class="pi-value">07:30 Pagi</div>
              </div>
            </div>

            <div class="pref-edit-form" v-else>
              <div class="form-group">
                <label>Pilihan Teh Selamat Datang</label>
                <select class="input-zen">
                  <option>Matcha Premium (抹茶)</option>
                  <option>Sencha (煎茶)</option>
                  <option>Hojicha (ほうじ茶)</option>
                  <option>Genmaicha (玄米茶)</option>
                </select>
              </div>
              <div class="form-group">
                <label>Ukuran Yukata</label>
                <select class="input-zen">
                  <option>S (Small)</option>
                  <option>M (Medium)</option>
                  <option selected>L (Large)</option>
                  <option>LL (Extra Large)</option>
                </select>
              </div>
              <div class="form-group">
                <label>Jenis Bantal</label>
                <select class="input-zen">
                  <option selected>Soba Hukura (Bantal Kulit Gandum)</option>
                  <option>Bantal Bulu Angsa</option>
                  <option>Bantal Memory Foam</option>
                </select>
              </div>
              <div class="form-group">
                <label>Alergi Makanan</label>
                <input type="text" class="input-zen" placeholder="Contoh: Kacang, Seafood..." value="Tidak ada">
              </div>
              <div class="form-group">
                <label>Waktu Sarapan Favorit</label>
                <input type="time" class="input-zen" value="07:30">
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Components/Layout/AppLayout.vue'

defineOptions({ layout: AppLayout })

const guestName = ref('Tamu Premium')
const guestEmail = ref('tamu@darmamizuki.com')
const editMode = ref(false)

onMounted(() => {
  const storedUser = localStorage.getItem('miyabi_user')
  if (storedUser) {
    const user = JSON.parse(storedUser)
    guestName.value = user.name
    guestEmail.value = user.email || 'tamu@darmamizuki.com'
  }
})
</script>

<style scoped>
.section-kicker { 
  font-family:'Noto Serif JP',serif; 
  font-size:0.8rem; 
  letter-spacing:0.2em; 
  color:var(--color-beni-500); 
  margin-bottom:0.5rem; 
  display:block; 
}

.profile-grid {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 2rem;
  align-items: start;
}

/* User Card */
.user-card {
  padding: 2rem;
  border-radius: 8px;
}
.uc-header {
  display: flex;
  align-items: center;
  gap: 1.25rem;
}
.avatar-circle {
  width: 72px;
  height: 72px;
  background: var(--color-beni-600);
  color: white;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 2.4rem;
  font-family: 'Noto Serif JP', serif;
  flex-shrink: 0;
  box-shadow: 0 4px 15px rgba(155, 35, 53, 0.2);
}
.uc-info h2 {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.6rem;
  font-weight: 600;
  color: var(--color-sumi-800);
  margin-bottom: 0.25rem;
  line-height: 1.2;
}
.uc-email {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: var(--color-sumi-600);
  font-family: 'Inter', sans-serif;
  font-size: 0.82rem;
}

/* Membership */
.membership-badge {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: rgba(201,168,76,0.08);
  border: 1px solid rgba(201,168,76,0.3);
  padding: 1rem;
  border-radius: 6px;
  margin-bottom: 1.5rem;
}
.mb-icon {
  width: 40px;
  height: 40px;
  background: var(--color-kin-500);
  color: #fff;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Noto Serif JP', serif;
  font-size: 1.4rem;
  font-weight: 700;
}
.mb-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--color-kin-700);
}
.mb-tier {
  font-family: 'Inter', sans-serif;
  font-size: 0.75rem;
  color: var(--color-sumi-600);
  margin-top: 2px;
}

/* Points */
.points-section {
  text-align: center;
  padding: 1.25rem;
  background: #fff;
  border: 1px dashed rgba(26,26,46,0.15);
  border-radius: 6px;
}
.pt-label {
  font-family: 'Inter', sans-serif;
  font-size: 0.75rem;
  color: var(--color-sumi-600);
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
.pt-value {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.2rem;
  font-weight: 700;
  color: var(--color-sumi-800);
  margin: 0.25rem 0;
}
.pt-value span {
  font-size: 1rem;
  color: var(--color-sumi-500);
  font-weight: 500;
}
.pt-desc {
  font-family: 'Inter', sans-serif;
  font-size: 0.7rem;
  color: var(--color-sumi-500);
  line-height: 1.4;
}

/* Pref Card */
.pref-card {
  padding: 2.5rem;
  border-radius: 8px;
}
.pref-header {
  margin-bottom: 2rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid rgba(201,168,76,0.2);
}
.pref-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.pref-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}
.pi-label {
  font-family: 'Inter', sans-serif;
  font-size: 0.75rem;
  color: var(--color-sumi-500);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.pi-value {
  font-family: 'Inter', sans-serif;
  font-size: 0.95rem;
  color: var(--color-sumi-800);
  font-weight: 500;
}
.text-muted {
  color: var(--color-sumi-500);
  font-style: italic;
}

/* Edit Form */
.pref-edit-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.form-group label {
  font-family: 'Inter', sans-serif;
  font-size: 0.8rem;
  color: var(--color-sumi-700);
}
.w-full { width: 100%; justify-content: center; }
.mt-4 { margin-top: 1rem; }

@media (max-width: 900px) {
  .profile-grid {
    grid-template-columns: 1fr;
  }
  .pref-card {
    padding: 1.5rem;
  }
}
</style>
