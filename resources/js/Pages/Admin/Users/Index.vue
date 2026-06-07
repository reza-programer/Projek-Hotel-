<template>
  <div class="admin-users-page">
    <!-- Header -->
    <div class="page-header-flex">
      <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="users" /> Manajemen Pengguna</h1>
        <p class="page-subtitle">Kelola hak akses admin, profile pengguna terdaftar, dan status akun.</p>
      </div>
      <button @click="openCreateModal" class="btn-vermillion">
        <span>+</span> Tambah Akun Pengguna
      </button>
    </div>

    <!-- Filters & Search Bar -->
    <div class="filters-card card-shoji">
      <div class="search-box">
        <span class="search-icon"><SvgIcon name="search" width="14" height="14" /></span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Cari nama atau email..." 
          class="input-zen search-input"
        />
      </div>
      <div class="filter-options">
        <select v-model="filterRole" class="input-zen select-filter">
          <option value="All">Semua Peran (Role)</option>
          <option value="user">Tamu (User)</option>
          <option value="admin">Administrator</option>
        </select>
        <select v-model="filterStatus" class="input-zen select-filter">
          <option value="All">Semua Status</option>
          <option value="active">Aktif</option>
          <option value="inactive">Nonaktif (Suspended)</option>
        </select>
      </div>
    </div>

    <!-- Users Table -->
    <div class="table-card card-shoji">
      <div style="overflow-x: auto;">
        <table class="table-zen">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nama Pengguna</th>
              <th>Alamat Email</th>
              <th>Peran (Role)</th>
              <th>Bergabung Pada</th>
              <th>Reservasi Dibuat</th>
              <th>Status Akun</th>
              <th>Aksi Pengguna</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredUsers.length === 0">
              <td colspan="8" style="text-align: center; padding: 3rem 0; color: var(--color-sumi-600);">
                Tidak ada pengguna yang ditemukan.
              </td>
            </tr>
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="font-bold text-sumi">#{{ user.id }}</td>
              <td>
                <div class="user-profile-flex">
                  <div class="user-avatar-circle" :class="`role-${user.role}`">
                    {{ user.name.charAt(0) }}
                  </div>
                  <div>
                    <div class="user-primary-name">{{ user.name }}</div>
                  </div>
                </div>
              </td>
              <td class="text-sumi font-medium">{{ user.email }}</td>
              <td>
                <span class="badge" :class="user.role === 'admin' ? 'badge-confirmed' : 'badge-completed'">
                  <SvgIcon :name="user.role === 'admin' ? 'key' : 'users'" width="11" height="11" style="margin-right:2px;" />
                  {{ user.role === 'admin' ? 'Administrator' : 'Tamu (User)' }}
                </span>
              </td>
              <td>{{ formatDateShort(user.joined) }}</td>
              <td class="font-bold text-sumi">{{ user.reservations }} Kali</td>
              <td>
                <span class="badge" :class="user.status === 'active' ? 'badge-verified' : 'badge-rejected'">
                  {{ user.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td>
                <div class="action-buttons-cell">
                  <button @click="openEditModal(user)" class="btn-action edit-btn" title="Edit Pengguna"><SvgIcon name="edit" width="12" height="12" style="margin-right:2px;" /> Edit</button>
                  <button 
                    @click="toggleUserStatus(user)" 
                    class="btn-action" 
                    :class="user.status === 'active' ? 'cancel-btn' : 'approve-btn'"
                    :title="user.status === 'active' ? 'Nonaktifkan Akun' : 'Aktifkan Akun'"
                  >
                    <SvgIcon :name="user.status === 'active' ? 'cancelled' : 'confirmed'" width="12" height="12" style="margin-right:2px;" />
                    {{ user.status === 'active' ? 'Suspend' : 'Aktifkan' }}
                  </button>
                  <button @click="deleteUser(user.id)" class="btn-action delete-btn" title="Hapus Akun"><SvgIcon name="delete" width="12" height="12" style="margin-right:2px;" /> Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create / Edit User Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="modal-overlay" @click.self="showModal=false">
        <div class="modal-content" style="max-width: 500px;">
          <div class="modal-header">
            <div>
              <div class="modal-title">{{ isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</div>
              <div class="modal-subtitle">DARMA MIZUKI MEMBER & STAFF MANAGEMENT</div>
            </div>
            <button @click="showModal=false" class="modal-close">✕</button>
          </div>

          <form @submit.prevent="saveUser">
            <div class="modal-body">
              <div class="form-group">
                <label class="label-zen">NAMA LENGKAP</label>
                <input v-model="form.name" type="text" class="input-zen" required placeholder="Contoh: Budi Santoso" />
              </div>

              <div class="form-group">
                <label class="label-zen">ALAMAT EMAIL</label>
                <input v-model="form.email" type="email" class="input-zen" required placeholder="Contoh: budi@email.com" />
              </div>

              <div class="form-grid-2">
                <div class="form-group">
                  <label class="label-zen">PERAN (ROLE)</label>
                  <select v-model="form.role" class="input-zen" required>
                    <option value="user">Tamu (User)</option>
                    <option value="admin">Administrator</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="label-zen">STATUS AKUN</label>
                  <select v-model="form.status" class="input-zen" required>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif (Suspended)</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn-outline-gold" @click="showModal=false">Batal</button>
              <button type="submit" class="btn-vermillion">
                {{ isEditMode ? 'Simpan Akun' : 'Tambah Akun' }}
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
import { users as initialUsers } from '@/data/mockData'

defineOptions({ layout: AdminLayout })

const users = ref([...initialUsers])
const searchQuery = ref('')
const filterRole = ref('All')
const filterStatus = ref('All')

const showModal = ref(false)
const isEditMode = ref(false)
const currentUserId = ref(null)

const form = ref({
  name: '',
  email: '',
  role: 'user',
  status: 'active'
})

const filteredUsers = computed(() => {
  return users.value.filter(user => {
    const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                          user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    const matchesRole = filterRole.value === 'All' || user.role === filterRole.value
    const matchesStatus = filterStatus.value === 'All' || user.status === filterStatus.value
    
    return matchesSearch && matchesRole && matchesStatus
  })
})

const formatDateShort = (dateStr) => {
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  }).format(new Date(dateStr))
}

const openCreateModal = () => {
  isEditMode.value = false
  currentUserId.value = null
  form.value = {
    name: '',
    email: '',
    role: 'user',
    status: 'active'
  }
  showModal.value = true
}

const openEditModal = (user) => {
  isEditMode.value = true
  currentUserId.value = user.id
  form.value = {
    name: user.name,
    email: user.email,
    role: user.role,
    status: user.status
  }
  showModal.value = true
}

const saveUser = () => {
  if (isEditMode.value) {
    const index = users.value.findIndex(u => u.id === currentUserId.value)
    if (index !== -1) {
      users.value[index] = {
        ...users.value[index],
        name: form.value.name,
        email: form.value.email,
        role: form.value.role,
        status: form.value.status
      }
    }
    alert('Informasi pengguna berhasil diupdate.')
  } else {
    const newUser = {
      id: users.value.length > 0 ? Math.max(...users.value.map(u => u.id)) + 1 : 1,
      name: form.value.name,
      email: form.value.email,
      role: form.value.role,
      status: form.value.status,
      joined: new Date().toISOString().split('T')[0],
      reservations: 0
    }
    users.value.push(newUser)
    alert('Pengguna baru berhasil ditambahkan.')
  }
  showModal.value = false
}

const toggleUserStatus = (user) => {
  user.status = user.status === 'active' ? 'inactive' : 'active'
  alert(`Status akun ${user.name} sekarang: ${user.status === 'active' ? 'AKTIF' : 'NONAKTIF (SUSPEND)'}`)
}

const deleteUser = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?')) {
    users.value = users.value.filter(u => u.id !== id)
    alert('Pengguna berhasil dihapus.')
  }
}
</script>

<style scoped>
.admin-users-page { display:flex; flex-direction:column; gap:1.5rem; }

.page-header-flex { display:flex; justify-content:space-between; align-items:center; }
.page-title { font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:600; color:var(--color-sumi-800); }
.page-subtitle { font-size:0.85rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; margin-top:2px; }

/* Filters */
.filters-card { padding:1.25rem 1.5rem; border-radius:4px; display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; }
.search-box { position:relative; flex:1; min-width:280px; }
.search-icon { position:absolute; left:1rem; top:50%; transform:translateY(-50%); font-size:0.9rem; color:var(--color-sumi-600); }
.search-input { padding-left:2.5rem; }
.filter-options { display:flex; gap:0.75rem; }
.select-filter { width:180px; }

/* Table components */
.table-card { padding:0.5rem; border-radius:4px; }
.user-profile-flex { display:flex; align-items:center; gap:0.75rem; }
.user-avatar-circle { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:bold; font-size:0.85rem; font-family:'Inter',sans-serif; }
.user-avatar-circle.role-admin { background:var(--color-beni-600); }
.user-avatar-circle.role-user { background:var(--color-matcha-600); }
.user-primary-name { font-family:'Inter',sans-serif; font-weight:600; color:var(--color-sumi-800); font-size:0.9rem; }

.action-buttons-cell { display:flex; gap:0.35rem; }
.btn-action { padding:0.25rem 0.6rem; font-size:0.72rem; border-radius:3px; cursor:pointer; font-family:'Inter',sans-serif; transition:0.2s; border:1px solid transparent; background:transparent; }
.edit-btn { color:var(--color-kin-700); border-color:rgba(201,168,76,0.3); }
.edit-btn:hover { background:rgba(201,168,76,0.08); }
.approve-btn { color:var(--color-matcha-700); border-color:rgba(74,124,89,0.3); }
.approve-btn:hover { background:var(--color-matcha-500); color:#fff; }
.cancel-btn { color:var(--color-beni-600); border-color:rgba(155,35,53,0.3); }
.cancel-btn:hover { background:var(--color-beni-600); color:#fff; }
.delete-btn { color:var(--color-sumi-600); border-color:rgba(26,26,46,0.2); }
.delete-btn:hover { background:var(--color-sumi-800); color:#fff; }

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
}
</style>
