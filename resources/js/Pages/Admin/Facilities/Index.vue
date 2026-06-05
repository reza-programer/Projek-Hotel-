<template>
  <div class="admin-facilities-page">
    <!-- Header with Action Button -->
    <div class="page-header-flex">
      <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="facilities" /> Laporan Fasilitas</h1>
        <p class="page-subtitle">Kelola fasilitas hotel, ikon, kategori, dan status operasional.</p>
      </div>
      <button @click="openCreateModal" class="btn-vermillion">
        <span>+</span> Tambah Fasilitas Baru
      </button>
    </div>

    <!-- Filters & Search Bar -->
    <div class="filters-card card-shoji">
      <div class="search-box">
        <span class="search-icon"><SvgIcon name="search" width="14" height="14" /></span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Cari fasilitas..." 
          class="input-zen search-input"
        />
      </div>
      <div class="filter-options">
        <select v-model="filterCategory" class="input-zen select-filter">
          <option value="All">Semua Kategori</option>
          <option value="Wellness">Wellness</option>
          <option value="Dining">Dining</option>
          <option value="Leisure">Leisure</option>
          <option value="Culture">Culture</option>
          <option value="Business">Business</option>
          <option value="Service">Service</option>
        </select>
        <select v-model="filterStatus" class="input-zen select-filter">
          <option value="All">Semua Status</option>
          <option value="Active">Aktif</option>
          <option value="Inactive">Nonaktif</option>
        </select>
      </div>
    </div>

    <!-- Facilities Grid -->
    <div class="facilities-grid">
      <div v-if="filteredFacilities.length === 0" class="empty-state-container card-shoji">
        <div class="empty-kanji">無</div>
        <p style="font-family:'Cormorant Garamond',serif; font-size:1.1rem; color:var(--color-sumi-600);">Tidak ada fasilitas yang cocok dengan kriteria pencarian.</p>
      </div>

      <div v-for="fac in filteredFacilities" :key="fac.id" class="facility-card card-shoji" :class="{ 'is-inactive': !fac.isActive }">
        <div class="facility-card-header">
          <div class="fac-icon-circle"><SvgIcon :name="fac.icon" width="24" height="24" /></div>
          <div class="fac-status-toggle">
            <label class="switch">
              <input type="checkbox" :checked="fac.isActive" @change="toggleFacilityStatus(fac)">
              <span class="slider round"></span>
            </label>
            <span class="status-label">{{ fac.isActive ? 'Aktif' : 'Tutup' }}</span>
          </div>
        </div>

        <div class="facility-card-body">
          <div class="fac-category-tag">{{ fac.category }}</div>
          <h3 class="fac-title">{{ fac.name }}</h3>
          <h4 class="fac-title-jp">{{ fac.nameJP }}</h4>
          <p class="fac-desc">{{ fac.description }}</p>
        </div>

        <div class="facility-card-footer">
          <button @click="openEditModal(fac)" class="btn-action edit-btn"><SvgIcon name="edit" width="12" height="12" style="margin-right:2px;" /> Edit</button>
          <button @click="deleteFacility(fac.id)" class="btn-action delete-btn"><SvgIcon name="delete" width="12" height="12" style="margin-right:2px;" /> Hapus</button>
        </div>
      </div>
    </div>

    <!-- Create / Edit Facility Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="modal-overlay" @click.self="showModal=false">
        <div class="modal-content" style="max-width: 500px;">
          <div class="modal-header">
            <div>
              <div class="modal-title">{{ isEditMode ? 'Edit Fasilitas' : 'Tambah Fasilitas Baru' }}</div>
              <div class="modal-subtitle">MIYABI OMOTENASHI FACILITIES</div>
            </div>
            <button @click="showModal=false" class="modal-close">✕</button>
          </div>

          <form @submit.prevent="saveFacility">
            <div class="modal-body">
              <div class="form-grid-2">
                <div class="form-group">
                  <label class="label-zen">NAMA FASILITAS</label>
                  <input v-model="form.name" type="text" class="input-zen" required placeholder="Contoh: Zen Garden" />
                </div>
                <div class="form-group">
                  <label class="label-zen">NAMA JEPANG (KANJI)</label>
                  <input v-model="form.nameJP" type="text" class="input-zen" required placeholder="Contoh: 禅の庭" />
                </div>
              </div>

              <div class="form-grid-2">
                <div class="form-group">
                  <label class="label-zen">IKON (ID IKON JEPANG)</label>
                  <input v-model="form.icon" type="text" class="input-zen" required placeholder="Contoh: onsen atau dining" />
                </div>
                <div class="form-group">
                  <label class="label-zen">KATEGORI</label>
                  <select v-model="form.category" class="input-zen" required>
                    <option value="Wellness">Wellness</option>
                    <option value="Dining">Dining</option>
                    <option value="Leisure">Leisure</option>
                    <option value="Culture">Culture</option>
                    <option value="Business">Business</option>
                    <option value="Service">Service</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="label-zen">DESKRIPSI FASILITAS</label>
                <textarea v-model="form.description" class="input-zen" rows="3" placeholder="Jelaskan fasilitas secara singkat untuk kenyamanan tamu..." required></textarea>
              </div>

              <div class="form-group">
                <label class="label-zen">STATUS AKTIF</label>
                <select v-model="form.isActive" class="input-zen">
                  <option :value="true">Aktif (Bisa digunakan tamu)</option>
                  <option :value="false">Tutup (Pemeliharaan / Perbaikan)</option>
                </select>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-outline-gold" @click="showModal=false">Batal</button>
              <button type="submit" class="btn-vermillion">
                {{ isEditMode ? 'Simpan' : 'Tambah' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AdminLayout from '@/Components/Layout/AdminLayout.vue'
import SvgIcon from '@/Components/UI/SvgIcon.vue'
import { facilities as initialFacilities } from '@/data/mockData'

defineOptions({ layout: AdminLayout })

const facilities = ref([...initialFacilities])
const searchQuery = ref('')
const filterCategory = ref('All')
const filterStatus = ref('All')

const showModal = ref(false)
const isEditMode = ref(false)
const currentFacilityId = ref(null)

const form = ref({
  name: '',
  nameJP: '',
  icon: '🌸',
  category: 'Wellness',
  description: '',
  isActive: true
})

const filteredFacilities = computed(() => {
  return facilities.value.filter(fac => {
    const matchesSearch = fac.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          fac.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    const matchesCategory = filterCategory.value === 'All' || fac.category === filterCategory.value
    
    const matchesStatus = filterStatus.value === 'All' || 
                          (filterStatus.value === 'Active' && fac.isActive) || 
                          (filterStatus.value === 'Inactive' && !fac.isActive)
    
    return matchesSearch && matchesCategory && matchesStatus
  })
})

const openCreateModal = () => {
  isEditMode.value = false
  currentFacilityId.value = null
  form.value = {
    name: '',
    nameJP: '',
    icon: '🌸',
    category: 'Leisure',
    description: '',
    isActive: true
  }
  showModal.value = true
}

const openEditModal = (fac) => {
  isEditMode.value = true
  currentFacilityId.value = fac.id
  form.value = {
    name: fac.name,
    nameJP: fac.nameJP,
    icon: fac.icon,
    category: fac.category,
    description: fac.description,
    isActive: fac.isActive
  }
  showModal.value = true
}

const saveFacility = () => {
  if (isEditMode.value) {
    // Update
    const index = facilities.value.findIndex(f => f.id === currentFacilityId.value)
    if (index !== -1) {
      facilities.value[index] = {
        ...facilities.value[index],
        name: form.value.name,
        nameJP: form.value.nameJP,
        icon: form.value.icon,
        category: form.value.category,
        description: form.value.description,
        isActive: form.value.isActive
      }
    }
  } else {
    // Create
    const newFacility = {
      id: facilities.value.length > 0 ? Math.max(...facilities.value.map(f => f.id)) + 1 : 1,
      name: form.value.name,
      nameJP: form.value.nameJP,
      icon: form.value.icon,
      category: form.value.category,
      description: form.value.description,
      isActive: form.value.isActive
    }
    facilities.value.push(newFacility)
  }
  showModal.value = false
}

const toggleFacilityStatus = (fac) => {
  fac.isActive = !fac.isActive
}

const deleteFacility = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')) {
    facilities.value = facilities.value.filter(f => f.id !== id)
  }
}
</script>

<style scoped>
.admin-facilities-page { display:flex; flex-direction:column; gap:1.5rem; }

.page-header-flex { display:flex; justify-content:space-between; align-items:center; }
.page-title { font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:600; color:var(--color-sumi-800); }
.page-subtitle { font-size:0.85rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; margin-top:2px; }

/* Filters */
.filters-card { padding:1.25rem 1.5rem; border-radius:4px; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
.search-box { position:relative; flex:1; min-width:280px; }
.search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); font-size:0.9rem; color:var(--color-sumi-600); }
.search-input { padding-left:2.5rem; }
.filter-options { display:flex; gap:0.75rem; }
.select-filter { width:170px; }

/* Grid */
.facilities-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:1.25rem; }
.empty-state-container { grid-column: 1 / -1; text-align:center; padding:4rem; border-radius:4px; }
.empty-kanji { font-family:'Noto Serif JP',serif; font-size:5rem; color:var(--color-kin-500); opacity:0.15; margin-bottom:1rem; }

.facility-card { display:flex; flex-direction:column; padding:1.5rem; border-radius:4px; position:relative; }
.facility-card.is-inactive { opacity:0.65; }

.facility-card-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
.fac-icon-circle { width:48px; height:48px; border-radius:50%; background:rgba(201,168,76,0.1); border:1px solid rgba(201,168,76,0.2); display:flex; align-items:center; justify-content:center; font-size:1.5rem; }

/* Toggle switch design */
.fac-status-toggle { display:flex; align-items:center; gap:0.4rem; }
.switch { position:relative; display:inline-block; width:34px; height:20px; }
.switch input { opacity:0; width:0; height:0; }
.slider { position:absolute; cursor:pointer; inset:0; background-color:#ccc; transition:.4s; }
.slider:before { position:absolute; content:""; height:14px; width:14px; left:3px; bottom:3px; background-color:white; transition:.4s; }
input:checked + .slider { background-color:var(--color-matcha-500); }
input:checked + .slider:before { transform:translateX(14px); }
.slider.round { border-radius:20px; }
.slider.round:before { border-radius:50%; }
.status-label { font-size:0.7rem; font-family:'Inter',sans-serif; color:var(--color-sumi-600); min-width:32px; font-weight:500; }

.fac-category-tag { font-family:'Inter',sans-serif; font-size:0.65rem; color:var(--color-kin-500); font-weight:600; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:0.25rem; }
.fac-title { font-family:'Cormorant Garamond',serif; font-size:1.35rem; font-weight:600; color:var(--color-sumi-800); }
.fac-title-jp { font-family:'Noto Serif JP',serif; font-size:0.75rem; color:var(--color-sumi-600); font-weight:400; opacity:0.8; margin-bottom:0.75rem; }
.fac-desc { font-family:'Inter',sans-serif; font-size:0.8rem; color:var(--color-sumi-700); line-height:1.5; flex:1; margin-bottom:1.25rem; }

.facility-card-footer { display:flex; justify-content:flex-end; gap:0.5rem; border-top:1px solid rgba(201,168,76,0.12); padding-top:0.75rem; margin-top:auto; }

.btn-action { padding:0.3rem 0.65rem; font-size:0.75rem; border-radius:3px; cursor:pointer; font-family:'Inter',sans-serif; transition:0.2s; border:1px solid transparent; background:transparent; }
.edit-btn { color:var(--color-kin-700); border-color:rgba(201,168,76,0.3); }
.edit-btn:hover { background:rgba(201,168,76,0.08); }
.delete-btn { color:var(--color-beni-600); border-color:rgba(155,35,53,0.3); }
.delete-btn:hover { background:var(--color-beni-600); color:#fff; }

.form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
.form-group { display:flex; flex-direction:column; margin-bottom:1rem; }

/* Modals */
.modal-header { display:flex; justify-content:space-between; align-items:flex-start; padding:1.5rem; border-bottom:1px solid rgba(201,168,76,0.2); }
.modal-title { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--color-sumi-800); }
.modal-subtitle { font-family:'Noto Serif JP',serif; font-size:0.7rem; color:var(--color-kin-500); letter-spacing:0.1em; }
.modal-close { width:32px; height:32px; border:none; background:transparent; cursor:pointer; font-size:1rem; color:var(--color-sumi-600); border-radius:4px; transition:0.2s; }
.modal-close:hover { background:rgba(155,35,53,0.08); color:var(--color-beni-600); }
.modal-body { padding:1.5rem; }
.modal-footer { padding:1.25rem 1.5rem; border-top:1px solid rgba(201,168,76,0.2); display:flex; justify-content:flex-end; gap:0.75rem; }

@media(max-width:768px) {
  .filters-card { flex-direction:column; align-items:stretch; }
  .filter-options { flex-direction:column; }
  .select-filter { width:100%; }
  .form-grid-2 { grid-template-columns:1fr; gap:0.5rem; margin-bottom:0.5rem; }
}
</style>
