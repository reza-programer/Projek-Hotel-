<template>
  <div class="admin-rooms-page">
    <!-- Header with Action Button -->
    <div class="page-header-flex">
      <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="rooms" /> Manajemen Kamar</h1>
        <p class="page-subtitle">Kelola daftar kamar hotel, harga, kapasitas, dan ketersediaan.</p>
      </div>
      <button @click="openCreateModal" class="btn-vermillion btn-sm">
        <span>+</span> Tambah Kamar Baru
      </button>
    </div>

    <!-- Filters & Search Bar -->
    <div class="filters-card card-shoji">
      <div class="search-box">
        <span class="search-icon"><SvgIcon name="search" width="14" height="14" /></span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Cari nama kamar atau tipe..." 
          class="input-zen search-input"
        />
      </div>
      <div class="filter-options">
        <select v-model="filterType" class="input-zen select-filter">
          <option value="All">Semua Tipe</option>
          <option value="Standard">Standard</option>
          <option value="Deluxe">Deluxe</option>
          <option value="Suite">Suite</option>
          <option value="Junior Suite">Junior Suite</option>
          <option value="Presidential">Presidential</option>
          <option value="Superior">Superior</option>
        </select>
        <select v-model="filterStatus" class="input-zen select-filter">
          <option value="All">Semua Ketersediaan</option>
          <option value="Available">Tersedia</option>
          <option value="Unavailable">Penuh / Dipesan</option>
        </select>
      </div>
    </div>

    <!-- Rooms Table -->
    <div class="table-card card-shoji">
      <div style="overflow-x: auto;">
        <table class="table-zen">
          <thead>
            <tr>
              <th>ID</th>
              <th>Thumbnail</th>
              <th>Nama Kamar</th>
              <th>Tipe</th>
              <th>Harga / Malam</th>
              <th>Kapasitas</th>
              <th>Ukuran (m²)</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredRooms.length === 0">
              <td colspan="9" style="text-align: center; padding: 3rem 0; color: var(--color-sumi-600);">
                Tidak ada kamar yang cocok dengan kriteria pencarian.
              </td>
            </tr>
            <tr v-for="room in filteredRooms" :key="room.id">
              <td class="font-bold text-sumi">#{{ room.id }}</td>
              <td>
                <div class="room-thumb-preview" :style="room.images && room.images.length > 0 ? { backgroundImage: `url(${room.images[0]})`, backgroundSize: 'cover', backgroundPosition: 'center' } : {}">
                  <div v-if="!room.images || room.images.length === 0" class="thumb-japanese-kanji">{{ room.nameJP?.charAt(0) }}</div>
                </div>
              </td>
              <td>
                <div>
                  <div class="room-name-primary">{{ room.name }}</div>
                  <div class="room-name-jp">{{ room.nameJP }}</div>
                </div>
              </td>
              <td class="text-sumi font-medium">{{ room.type }}</td>
              <td class="font-bold text-beni">{{ formatCurrency(room.price) }}</td>
              <td>{{ room.capacity }} Orang</td>
              <td>{{ room.size }} m²</td>
              <td>
                <span class="badge" :class="room.isAvailable ? 'badge-confirmed' : 'badge-cancelled'">
                  <SvgIcon :name="room.isAvailable ? 'confirmed' : 'cancelled'" width="12" height="12" style="margin-right:2px;" />
                  {{ room.isAvailable ? 'Tersedia' : 'Penuh' }}
                </span>
              </td>
              <td>
                <div class="action-buttons-cell">
                  <button @click="openEditModal(room)" class="btn-action edit-btn" title="Edit Kamar"><SvgIcon name="edit" width="12" height="12" style="margin-right:2px;" /> Edit</button>
                  <button @click="deleteRoom(room.id)" class="btn-action delete-btn" title="Hapus Kamar"><SvgIcon name="delete" width="12" height="12" style="margin-right:2px;" /> Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create / Edit Room Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="modal-overlay" @click.self="showModal=false">
        <div class="modal-content" style="max-width: 700px;">
          <div class="modal-header">
            <div>
              <div class="modal-title">{{ isEditMode ? 'Edit Kamar' : 'Tambah Kamar Baru' }}</div>
              <div class="modal-subtitle">DARMA MIZUKI LUXURY ROOMS</div>
            </div>
            <button @click="showModal=false" class="modal-close">✕</button>
          </div>

          <form @submit.prevent="saveRoom">
            <div class="modal-body">
              <div class="form-grid-2">
                <div class="form-group">
                  <label class="label-zen">NAMA KAMAR (INDONESIA)</label>
                  <input v-model="form.name" type="text" class="input-zen" required placeholder="Contoh: Sakura Premium" />
                </div>
                <div class="form-group">
                  <label class="label-zen">NAMA KAMAR (JEPANG/KANJI)</label>
                  <input v-model="form.nameJP" type="text" class="input-zen" required placeholder="Contoh: 桜プレミアム" />
                </div>
              </div>

              <div class="form-grid-3">
                <div class="form-group">
                  <label class="label-zen">TIPE KAMAR</label>
                  <select v-model="form.type" class="input-zen" required>
                    <option value="Standard">Standard</option>
                    <option value="Superior">Superior</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="Junior Suite">Junior Suite</option>
                    <option value="Suite">Suite</option>
                    <option value="Presidential">Presidential</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="label-zen">HARGA PER MALAM (IDR)</label>
                  <input v-model.number="form.price" type="number" class="input-zen" required min="100000" />
                </div>
                <div class="form-group">
                  <label class="label-zen">KETERSEDIAAN</label>
                  <select v-model="form.isAvailable" class="input-zen">
                    <option :value="true">Tersedia</option>
                    <option :value="false">Penuh / Dipesan</option>
                  </select>
                </div>
              </div>

              <div class="form-grid-2">
                <div class="form-group">
                  <label class="label-zen">STATUS PROMO</label>
                  <select v-model="form.isPromoActive" class="input-zen">
                    <option :value="true">Promo Aktif</option>
                    <option :value="false">Tidak Ada Promo</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="label-zen">DISKON (%)</label>
                  <input v-model.number="form.discountPercentage" type="number" class="input-zen" min="0" max="100" :disabled="!form.isPromoActive" />
                </div>
              </div>

              <div class="form-grid-3">
                <div class="form-group">
                  <label class="label-zen">KAPASITAS (TAMU)</label>
                  <input v-model.number="form.capacity" type="number" class="input-zen" required min="1" max="10" />
                </div>
                <div class="form-group">
                  <label class="label-zen">UKURAN (M²)</label>
                  <input v-model.number="form.size" type="number" class="input-zen" required min="10" />
                </div>
                <div class="form-group">
                  <label class="label-zen">LANTAI</label>
                  <input v-model="form.floor" type="text" class="input-zen" required placeholder="Contoh: 3-5" />
                </div>
              </div>

              <div class="form-group">
                <label class="label-zen">DESKRIPSI SINGKAT</label>
                <textarea v-model="form.description" class="input-zen" rows="2" placeholder="Deskripsi singkat mengenai tema kamar..." required></textarea>
              </div>

              <div class="form-group">
                <label class="label-zen">DESKRIPSI LENGKAP (HTML/TEXT)</label>
                <textarea v-model="form.longDescription" class="input-zen" rows="4" placeholder="Detail lengkap mengenai nuansa, tempat tidur, yukata, ofuro..." required></textarea>
              </div>

              <div class="form-group">
                <label class="label-zen">FASILITAS KAMAR (PISAHKAN DENGAN KOMA)</label>
                <input v-model="form.amenitiesString" type="text" class="input-zen" placeholder="Wi-Fi, AC, Minibar, Yukata, Bathtub..." />
              </div>

              <div class="form-group" style="margin-top: 1rem;">
                <label class="label-zen">FOTO KAMAR</label>
                <div class="image-upload-wrapper">
                  <div class="upload-dropzone" @click="$refs.fileInput.click()">
                    <input type="file" ref="fileInput" class="hidden-input" accept="image/*" multiple @change="handleImageUpload" />
                    <div class="upload-icon">📷</div>
                    <div class="upload-text">Klik untuk mengunggah foto kamar</div>
                    <div class="upload-hint">Format: JPG, PNG, WEBP. Maks 2MB per gambar.</div>
                  </div>
                  
                  <!-- Image Previews -->
                  <div v-if="form.images && form.images.length > 0" class="preview-grid">
                    <div v-for="(img, idx) in form.images" :key="idx" class="preview-item">
                      <img :src="img" class="preview-img-tag" />
                      <button type="button" class="btn-remove-preview" @click="removeImage(idx)" title="Hapus foto">✕</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-outline-gold" @click="showModal=false">Batal</button>
              <button type="submit" class="btn-vermillion">
                {{ isEditMode ? 'Simpan Perubahan' : 'Tambah Kamar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
    <!-- Delete Confirmation Modal -->
    <Transition name="modal">
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal=false">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
          <div class="modal-body" style="padding: 2rem;">
            <div style="font-size: 3rem; color: var(--color-beni-600); margin-bottom: 1rem; display: flex; justify-content: center;">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
            <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.6rem; font-weight: 600; color: var(--color-sumi-800); margin-bottom: 0.5rem;">Konfirmasi Hapus</h3>
            <p style="color: var(--color-sumi-600); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.5rem;">
              Apakah Anda yakin ingin menghapus kamar <strong>#{{ roomToDelete?.id }} - {{ roomToDelete?.name }}</strong>?<br>Tindakan ini tidak dapat dibatalkan.
            </p>
            <div style="display: flex; gap: 0.75rem; justify-content: center;">
              <button class="btn-outline-gold" @click="showDeleteModal=false">Batal</button>
              <button class="btn-vermillion" @click="confirmDelete" style="background: var(--color-beni-600); border-color: var(--color-beni-600);">Ya, Hapus</button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AdminLayout from '@/Components/Layout/AdminLayout.vue'
import SvgIcon from '@/Components/UI/SvgIcon.vue'
import { formatCurrency } from '@/data/mockData'
import { rooms, saveRooms } from '@/data/roomsStore'

defineOptions({ layout: AdminLayout })

const searchQuery = ref('')
const filterType = ref('All')
const filterStatus = ref('All')

const showModal = ref(false)
const showDeleteModal = ref(false)
const isEditMode = ref(false)
const currentRoomId = ref(null)
const roomToDelete = ref(null)

const form = ref({
  name: '',
  nameJP: '',
  type: 'Standard',
  price: 1000000,
  capacity: 2,
  size: 30,
  floor: '3',
  description: '',
  longDescription: '',
  isAvailable: true,
  isPromoActive: false,
  discountPercentage: 0,
  amenitiesString: '',
  images: []
})

const filteredRooms = computed(() => {
  return rooms.value.filter(room => {
    const matchesSearch = room.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          room.type.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    const matchesType = filterType.value === 'All' || room.type === filterType.value
    
    const matchesStatus = filterStatus.value === 'All' || 
                          (filterStatus.value === 'Available' && room.isAvailable) || 
                          (filterStatus.value === 'Unavailable' && !room.isAvailable)
    
    return matchesSearch && matchesType && matchesStatus
  })
})

const fileInput = ref(null)

const handleImageUpload = (e) => {
  const files = e.target.files
  if (!files || files.length === 0) return

  for (let i = 0; i < files.length; i++) {
    const file = files[i]
    if (file.size > 2 * 1024 * 1024) {
      alert(`Berkas ${file.name} melebihi batas 2MB.`)
      continue
    }

    const reader = new FileReader()
    reader.onload = (event) => {
      if (!form.value.images) {
        form.value.images = []
      }
      form.value.images.push(event.target.result)
    }
    reader.readAsDataURL(file)
  }
  
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const removeImage = (index) => {
  if (form.value.images) {
    form.value.images.splice(index, 1)
  }
}

const openCreateModal = () => {
  isEditMode.value = false
  currentRoomId.value = null
  form.value = {
    name: '',
    nameJP: '',
    type: 'Standard',
    price: 1000000,
    capacity: 2,
    size: 30,
    floor: '3',
    description: '',
    longDescription: '',
    isAvailable: true,
    isPromoActive: false,
    discountPercentage: 0,
    amenitiesString: 'Wi-Fi Gratis, AC, TV, Minibar, Yukata, Shower & Bathtub',
    images: []
  }
  showModal.value = true
}

const openEditModal = (room) => {
  isEditMode.value = true
  currentRoomId.value = room.id
  form.value = {
    name: room.name,
    nameJP: room.nameJP,
    type: room.type,
    price: room.price,
    capacity: room.capacity,
    size: room.size,
    floor: room.floor,
    description: room.description,
    longDescription: room.longDescription,
    isAvailable: room.isAvailable,
    isPromoActive: room.isPromoActive || false,
    discountPercentage: room.discountPercentage || 0,
    amenitiesString: room.amenities ? room.amenities.join(', ') : '',
    images: room.images ? [...room.images] : []
  }
  showModal.value = true
}

const saveRoom = () => {
  const amenitiesList = form.value.amenitiesString
    ? form.value.amenitiesString.split(',').map(s => s.trim()).filter(s => s.length > 0)
    : []

  if (isEditMode.value) {
    // Update
    const index = rooms.value.findIndex(r => r.id === currentRoomId.value)
    if (index !== -1) {
      rooms.value[index] = {
        ...rooms.value[index],
        name: form.value.name,
        nameJP: form.value.nameJP,
        type: form.value.type,
        price: form.value.price,
        capacity: form.value.capacity,
        size: form.value.size,
        floor: form.value.floor,
        description: form.value.description,
        longDescription: form.value.longDescription,
        isAvailable: form.value.isAvailable,
        isPromoActive: form.value.isPromoActive,
        discountPercentage: form.value.discountPercentage,
        amenities: amenitiesList,
        images: form.value.images || []
      }
    }
    saveRooms()
    window.dispatchEvent(new CustomEvent('miyabi-show-toast', {
      detail: { message: 'Kamar berhasil diperbarui!', type: 'success' }
    }))
  } else {
    // Create
    const newRoom = {
      id: rooms.value.length > 0 ? Math.max(...rooms.value.map(r => r.id)) + 1 : 1,
      name: form.value.name,
      nameJP: form.value.nameJP,
      type: form.value.type,
      price: form.value.price,
      capacity: form.value.capacity,
      size: form.value.size,
      floor: form.value.floor,
      description: form.value.description,
      longDescription: form.value.longDescription,
      isAvailable: form.value.isAvailable,
      isPromoActive: form.value.isPromoActive,
      discountPercentage: form.value.discountPercentage,
      amenities: amenitiesList,
      rating: 5.0,
      reviewCount: 0,
      features: ['New Room'],
      images: form.value.images && form.value.images.length > 0 ? form.value.images : ['/images/rooms/standard.jpg'],
      thumbnail: form.value.images && form.value.images.length > 0 ? form.value.images[0] : '/images/rooms/standard-thumb.jpg',
      colorTheme: 'sakura'
    }
    rooms.value.push(newRoom)
    saveRooms()
    window.dispatchEvent(new CustomEvent('miyabi-show-toast', {
      detail: { message: 'Kamar baru berhasil ditambahkan!', type: 'success' }
    }))
  }
  showModal.value = false
}

const deleteRoom = (id) => {
  const room = rooms.value.find(r => r.id === id)
  if (room) {
    roomToDelete.value = room
    showDeleteModal.value = true
  }
}

const confirmDelete = () => {
  if (roomToDelete.value) {
    rooms.value = rooms.value.filter(r => r.id !== roomToDelete.value.id)
    saveRooms()
    showDeleteModal.value = false
    window.dispatchEvent(new CustomEvent('miyabi-show-toast', {
      detail: { message: 'Kamar berhasil dihapus.', type: 'success' }
    }))
    roomToDelete.value = null
  }
}
</script>

<style scoped>
.admin-rooms-page { display:flex; flex-direction:column; gap:1.5rem; }

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

/* Table */
.table-card { padding:0.5rem; border-radius:4px; }
.room-thumb-preview { width:60px; height:45px; border-radius:3px; background:var(--color-sumi-900); display:flex; align-items:center; justify-content:center; border:1px solid rgba(201,168,76,0.15); }
.thumb-japanese-kanji { color:var(--color-kin-500); font-family:'Noto Serif JP',serif; font-weight:600; font-size:1.1rem; }

.room-name-primary { font-family:'Cormorant Garamond',serif; font-size:1.1rem; font-weight:600; color:var(--color-sumi-800); }
.room-name-jp { font-family:'Noto Serif JP',serif; font-size:0.68rem; color:var(--color-kin-500); letter-spacing:0.04em; }

.action-buttons-cell { display:flex; gap:0.5rem; }
.btn-action { padding:0.3rem 0.65rem; font-size:0.75rem; border-radius:3px; cursor:pointer; font-family:'Inter',sans-serif; transition:0.2s; border:1px solid transparent; background:transparent; }
.edit-btn { color:var(--color-kin-700); border-color:rgba(201,168,76,0.3); }
.edit-btn:hover { background:rgba(201,168,76,0.08); }
.delete-btn { color:var(--color-beni-600); border-color:rgba(155,35,53,0.3); }
.delete-btn:hover { background:var(--color-beni-600); color:#fff; }

/* Grid Forms */
.form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
.form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; margin-bottom:1rem; }
.form-group { display:flex; flex-direction:column; margin-bottom:1rem; }

/* Modals */
.modal-header { display:flex; justify-content:space-between; align-items:flex-start; padding:1.5rem; border-bottom:1px solid rgba(201,168,76,0.2); }
.modal-title { font-family:'Cormorant Garamond',serif; font-size:1.4rem; font-weight:600; color:var(--color-sumi-800); }
.modal-subtitle { font-family:'Noto Serif JP',serif; font-size:0.7rem; color:var(--color-kin-500); letter-spacing:0.1em; }
.modal-close { width:32px; height:32px; border:none; background:transparent; cursor:pointer; font-size:1rem; color:var(--color-sumi-600); border-radius:4px; transition:0.2s; }
.modal-close:hover { background:rgba(155,35,53,0.08); color:var(--color-beni-600); }
.modal-body { padding:1.5rem; max-height:60vh; overflow-y:auto; }
.modal-footer { padding:1.25rem 1.5rem; border-top:1px solid rgba(201,168,76,0.2); display:flex; justify-content:flex-end; gap:0.75rem; }

@media(max-width:768px) {
  .filters-card { flex-direction:column; align-items:stretch; }
  .filter-options { flex-direction:column; }
  .select-filter { width:100%; }
  .form-grid-2, .form-grid-3 { grid-template-columns:1fr; gap:0.5rem; margin-bottom:0.5rem; }
  .page-header-flex { flex-direction:column; align-items:flex-start; gap:1rem; }
  .btn-sm { width:100%; }
}

/* Small button variant */
.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
  height: auto;
}

/* Image upload styles */
.image-upload-wrapper { display:flex; flex-direction:column; gap:0.75rem; }
.upload-dropzone { border:1px dashed rgba(201,168,76,0.4); background:var(--color-washi-100,#fcfaf7); border-radius:4px; padding:1.5rem; text-align:center; cursor:pointer; transition:0.3s; }
.upload-dropzone:hover { border-color:var(--color-kin-500); background:rgba(201,168,76,0.05); }
.hidden-input { display:none; }
.upload-icon { font-size:1.8rem; margin-bottom:0.5rem; color:var(--color-kin-500); }
.upload-text { font-size:0.85rem; font-weight:500; color:var(--color-sumi-700); }
.upload-hint { font-size:0.7rem; color:var(--color-sumi-500); margin-top:0.25rem; }
.preview-grid { display:flex; flex-wrap:wrap; gap:0.5rem; margin-top:0.5rem; }
.preview-item { position:relative; width:90px; height:65px; border-radius:4px; overflow:hidden; border:1px solid rgba(201,168,76,0.2); }
.preview-img-tag { width:100%; height:100%; object-fit:cover; }
.btn-remove-preview { position:absolute; top:2px; right:2px; width:18px; height:18px; border-radius:50%; background:rgba(155,35,53,0.9); color:#fff; border:none; font-size:0.65rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:0.2s; }
.btn-remove-preview:hover { background:var(--color-beni-600); }
</style>
