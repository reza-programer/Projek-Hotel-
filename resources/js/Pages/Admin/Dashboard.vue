<template>
  <div class="admin-dashboard-page">
    <!-- Skeleton Loading -->
    <div v-if="isLoading" class="skeleton-dashboard">
      <div class="stats-grid">
        <div v-for="n in 4" :key="n" class="stat-card-zen card-shoji">
          <Skeleton variant="circle" width="56px" height="56px" />
          <div style="flex: 1">
            <Skeleton variant="text" width="60%" height="16px" style="margin-bottom: 0.5rem" />
            <Skeleton variant="text" width="40%" height="24px" />
            <Skeleton variant="text" width="50%" height="14px" style="margin-top: 0.25rem" />
          </div>
        </div>
      </div>
      <div class="dashboard-middle-row">
        <Skeleton variant="card" height="300px" />
        <Skeleton variant="card" height="300px" />
      </div>
      <Skeleton variant="card" height="200px" />
    </div>

    <!-- Actual Content -->
    <template v-else>
    <!-- Top Stats Row -->
    <div class="stats-grid">
      <div class="stat-card-zen card-shoji">
        <div class="sc-icon text-beni"><SvgIcon name="gate" width="28" height="28" /></div>
        <div class="sc-content">
          <div class="sc-label">Reservasi Baru (Hari Ini)</div>
          <div class="sc-value">{{ adminStats.today.newReservations }}</div>
          <div class="sc-subtext font-green">↑ 12% dari kemarin</div>
        </div>
      </div>
      <div class="stat-card-zen card-shoji">
        <div class="sc-icon text-kin"><SvgIcon name="key" width="28" height="28" /></div>
        <div class="sc-content">
          <div class="sc-label">Check-In Hari Ini</div>
          <div class="sc-value">{{ adminStats.today.checkIns }}</div>
          <div class="sc-subtext">9 Tamu sudah tiba</div>
        </div>
      </div>
      <div class="stat-card-zen card-shoji">
        <div class="sc-icon text-ai"><SvgIcon name="door" width="28" height="28" /></div>
        <div class="sc-content">
          <div class="sc-label">Check-Out Hari Ini</div>
          <div class="sc-value">{{ adminStats.today.checkOuts }}</div>
          <div class="sc-subtext">5 Kamar sudah kosong</div>
        </div>
      </div>
      <div class="stat-card-zen card-shoji">
        <div class="sc-icon text-matcha"><SvgIcon name="paid" width="28" height="28" /></div>
        <div class="sc-content">
          <div class="sc-label">Pendapatan Hari Ini</div>
          <div class="sc-value">{{ formatCurrency(adminStats.today.revenue) }}</div>
          <div class="sc-subtext font-green">↑ 8.3% target harian</div>
        </div>
      </div>
    </div>

    <!-- Middle Row: Chart & Stats Summary -->
    <div class="dashboard-middle-row">
      <!-- SVG Interactive Revenue Chart -->
      <div class="chart-container card-shoji">
        <div class="chart-header">
          <h2 class="chart-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="paid" /> Tren Pendapatan Bulanan</h2>
          <span class="chart-year">Tahun 2025 (Semester 1)</span>
        </div>
        
        <div class="chart-wrapper">
          <!-- Simple CSS/SVG Bar Chart with premium hover tooltips -->
          <div class="bar-chart-custom">
            <div 
              v-for="data in revenueData.monthly" 
              :key="data.month" 
              class="bar-column"
            >
              <div class="bar-tooltip">
                <div class="tt-rev">{{ formatCurrency(data.revenue) }}</div>
                <div class="tt-res">{{ data.reservations }} Reservasi</div>
              </div>
              <div class="bar-fill-container">
                <div 
                  class="bar-fill bg-beni" 
                  :style="{ height: `${(data.revenue / 600000000) * 100}%` }"
                >
                  <div class="bar-glow"></div>
                </div>
              </div>
              <span class="bar-label">{{ data.month }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Occupancy & Ratings Circular Progress -->
      <div class="occupancy-stats card-shoji">
        <h2 class="chart-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="completed" /> Status Keterisian</h2>
        <div class="divider-ink" style="margin: 0.75rem 0;"></div>
        
        <div class="occupancy-radial">
          <div class="radial-ring">
            <svg width="120" height="120" viewBox="0 0 120 120">
              <circle cx="60" cy="60" r="50" fill="none" stroke="rgba(201,168,76,0.15)" stroke-width="10" />
              <circle 
                cx="60" cy="60" r="50" fill="none" 
                stroke="var(--color-beni-600)" 
                stroke-width="10" 
                stroke-dasharray="314" 
                stroke-dashoffset="69" 
                stroke-linecap="round" 
                transform="rotate(-90 60 60)"
              />
            </svg>
            <div class="radial-text">
              <span class="rad-val">{{ adminStats.monthly.occupancyRate }}%</span>
              <span class="rad-label">Occupancy</span>
            </div>
          </div>
        </div>

        <div class="occupancy-details">
          <div class="od-item">
            <span class="od-label">Kamar Kosong</span>
            <span class="od-val text-matcha">{{ adminStats.roomsAvailable }} Kamar</span>
          </div>
          <div class="od-item">
            <span class="od-label">Tamu Menginap</span>
            <span class="od-val text-ai">{{ adminStats.activeGuests }} Tamu</span>
          </div>
          <div class="od-item">
            <span class="od-label">Rata-rata Rating</span>
            <span class="od-val text-kin">★ {{ adminStats.monthly.avgRating }} / 5.0</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Row: Recent Reservations Table -->
    <div class="recent-reservations-section card-shoji">
      <div class="section-header-flex">
        <h2 class="section-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="pending" /> Reservasi Terbaru</h2>
        <a href="/admin/reservasi" class="btn-outline-gold" style="padding:0.4rem 1rem; font-size:0.75rem;">Semua Reservasi</a>
      </div>

      <div style="overflow-x: auto; margin-top: 1rem;">
        <table class="table-zen">
          <thead>
            <tr>
              <th>ID Reservasi</th>
              <th>Kamar</th>
              <th>Check In</th>
              <th>Check Out</th>
              <th>Tamu</th>
              <th>Total Biaya</th>
              <th>Status</th>
              <th>Status Bayar</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="res in reservations.slice(0, 3)" :key="res.id">
              <td class="font-bold text-sumi">{{ res.id }}</td>
              <td>{{ res.roomName }}</td>
              <td>{{ formatDateShort(res.checkIn) }}</td>
              <td>{{ formatDateShort(res.checkOut) }}</td>
              <td>{{ res.guests }} Tamu</td>
              <td class="font-bold text-beni">{{ formatCurrency(res.totalPrice) }}</td>
              <td>
                <span class="badge" :class="statusConfig[res.status]?.class">
                  <SvgIcon :name="statusConfig[res.status]?.icon" width="12" height="12" style="margin-right:2px;" /> {{ statusConfig[res.status]?.label }}
                </span>
              </td>
              <td>
                <span class="badge" :class="statusConfig[res.paymentStatus]?.class" style="font-size:0.68rem;">
                  {{ statusConfig[res.paymentStatus]?.label }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '@/Components/Layout/AdminLayout.vue'
import SvgIcon from '@/Components/UI/SvgIcon.vue'
import Skeleton from '@/Components/UI/Skeleton.vue'
import { adminStats, revenueData, reservations, formatCurrency, statusConfig } from '@/data/mockData'

defineOptions({ layout: AdminLayout })

const isLoading = ref(true)

// Simulate loading
onMounted(() => {
  setTimeout(() => {
    isLoading.value = false
  }, 700)
})

const formatDateShort = (dateStr) => {
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'short'
  }).format(new Date(dateStr))
}
</script>

<style scoped>
.admin-dashboard-page { display:flex; flex-direction:column; gap:1.75rem; }

/* Stats grid */
.stats-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap:1.25rem; }
.stat-card-zen { display:flex; align-items:center; gap:1.25rem; padding:1.5rem; border-radius:4px; }
.sc-icon { font-size:2.2rem; width:56px; height:56px; display:flex; align-items:center; justify-content:center; background:rgba(201,168,76,0.06); border-radius:4px; }
.sc-content { display:flex; flex-direction:column; gap:2px; }
.sc-label { font-size:0.75rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; font-weight:500; text-transform:uppercase; letter-spacing:0.04em; }
.sc-value { font-family:'Cormorant Garamond',serif; font-size:1.8rem; font-weight:700; color:var(--color-sumi-800); line-height:1.1; }
.sc-subtext { font-size:0.72rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; }
.font-green { color:var(--color-matcha-600) !important; font-weight:500; }

/* Middle Row */
.dashboard-middle-row { display:grid; grid-template-columns: 2fr 1fr; gap:1.25rem; }
.chart-container { padding:1.5rem; border-radius:4px; display:flex; flex-direction:column; gap:1.5rem; }
.chart-header { display:flex; justify-content:space-between; align-items:center; }
.chart-title { font-family:'Cormorant Garamond',serif; font-size:1.3rem; font-weight:600; color:var(--color-sumi-800); }
.chart-year { font-size:0.75rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; }

.chart-wrapper { height:220px; display:flex; align-items:flex-end; }
.bar-chart-custom { display:flex; justify-content:space-between; align-items:flex-end; width:100%; height:180px; padding:0 1rem; position:relative; border-bottom:1px solid rgba(201,168,76,0.2); }
.bar-column { display:flex; flex-direction:column; align-items:center; flex:1; position:relative; cursor:pointer; }
.bar-fill-container { width:32px; height:150px; display:flex; align-items:flex-end; background:rgba(201,168,76,0.04); border-radius:2px; margin-bottom:0.5rem; }
.bar-fill { width:100%; border-radius:2px; transition: height 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94); position:relative; }
.bar-glow { position:absolute; top:0; left:0; width:100%; height:100%; background:linear-gradient(180deg, rgba(255,255,255,0.15), transparent); }

.bar-column:hover .bar-fill { filter:brightness(1.1); }
.bar-column:hover .bar-tooltip { opacity:1; transform:translateX(-50%) translateY(-8px); pointer-events:auto; }

.bar-label { font-size:0.75rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; font-weight:500; }

.bar-tooltip {
  position:absolute; bottom:100%; left:50%; transform:translateX(-50%) translateY(0);
  background:var(--color-sumi-800); color:#fff; padding:0.5rem 0.75rem; border-radius:3px;
  font-family:'Inter',sans-serif; font-size:0.7rem; white-space:nowrap;
  box-shadow:0 6px 16px rgba(0,0,0,0.25); border:1px solid rgba(201,168,76,0.3);
  opacity:0; pointer-events:none; transition:all 0.25s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  z-index:10;
}
.tt-rev { font-weight:700; color:var(--color-kin-300); }
.tt-res { color:var(--color-washi-300); margin-top:2px; }

/* Occupancy Stats */
.occupancy-stats { padding:1.5rem; border-radius:4px; display:flex; flex-direction:column; justify-content:space-between; }
.occupancy-radial { display:flex; justify-content:center; align-items:center; margin:1rem 0; }
.radial-ring { position:relative; display:flex; justify-content:center; align-items:center; }
.radial-text { position:absolute; display:flex; flex-direction:column; align-items:center; text-align:center; }
.rad-val { font-family:'Cormorant Garamond',serif; font-size:1.6rem; font-weight:700; color:var(--color-sumi-800); }
.rad-label { font-size:0.65rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; text-transform:uppercase; font-weight:600; letter-spacing:0.04em; }

.occupancy-details { display:flex; flex-direction:column; gap:0.5rem; }
.od-item { display:flex; justify-content:space-between; font-size:0.8rem; font-family:'Inter',sans-serif; border-bottom:1px dashed rgba(201,168,76,0.15); padding-bottom:0.25rem; }
.od-label { color:var(--color-sumi-600); }
.od-val { font-weight:600; }

/* Bottom Table Row */
.recent-reservations-section { padding:1.5rem; border-radius:4px; }
.section-header-flex { display:flex; justify-content:space-between; align-items:center; }
.section-title { font-family:'Cormorant Garamond',serif; font-size:1.3rem; font-weight:600; color:var(--color-sumi-800); }

@media(max-width:991px) {
  .dashboard-middle-row { grid-template-columns: 1fr; }
}
</style>
