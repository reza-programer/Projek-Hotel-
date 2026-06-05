<template>
  <div class="admin-reports-page">
    <!-- Header -->
    <div class="page-header-flex">
      <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="paid" /> Laporan Pendapatan</h1>
        <p class="page-subtitle">Analisis keuangan hotel, laporan reservasi, dan tren performa bulanan.</p>
      </div>
      <button @click="exportCSV" class="btn-outline-gold">
        <SvgIcon name="completed" width="12" height="12" style="margin-right:2px;" /> Export Laporan (CSV)
      </button>
    </div>

    <!-- Summary Stats cards -->
    <div class="stats-grid">
      <div class="stat-card-zen card-shoji">
        <div class="sc-content">
          <div class="sc-label">TOTAL PENDAPATAN (YTD)</div>
          <div class="sc-value text-beni">{{ formatCurrency(totalYTDRevenue) }}</div>
          <div class="sc-subtext font-green">↑ 18.3% vs target semesteran</div>
        </div>
      </div>
      <div class="stat-card-zen card-shoji">
        <div class="sc-content">
          <div class="sc-label">RATA-RATA PENDAPATAN BULANAN</div>
          <div class="sc-value text-kin">{{ formatCurrency(avgMonthlyRevenue) }}</div>
          <div class="sc-subtext">Dari 6 bulan terakhir</div>
        </div>
      </div>
      <div class="stat-card-zen card-shoji">
        <div class="sc-content">
          <div class="sc-label">TOTAL RESERVASI SELESAI</div>
          <div class="sc-value text-ai">{{ totalReservationsCount }}</div>
          <div class="sc-subtext">Rata-rata 163 reservasi/bulan</div>
        </div>
      </div>
    </div>

    <!-- Interactive SVG Area Chart -->
    <div class="chart-section card-shoji">
      <div class="chart-header">
        <h2 class="chart-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="completed" /> Tren Kurva Pendapatan Bulanan (IDR)</h2>
        <span class="chart-legend">
          <span class="legend-color legend-beni"></span> Pendapatan
        </span>
      </div>

      <div class="chart-area-wrapper">
        <svg class="svg-chart" viewBox="0 0 600 220" preserveAspectRatio="none">
          <!-- Grids -->
          <line x1="40" y1="20" x2="580" y2="20" stroke="rgba(201,168,76,0.1)" stroke-width="1"/>
          <line x1="40" y1="70" x2="580" y2="70" stroke="rgba(201,168,76,0.1)" stroke-width="1"/>
          <line x1="40" y1="120" x2="580" y2="120" stroke="rgba(201,168,76,0.1)" stroke-width="1"/>
          <line x1="40" y1="170" x2="580" y2="170" stroke="rgba(201,168,76,0.1)" stroke-width="1"/>

          <!-- Area under line -->
          <polygon 
            :points="areaPoints" 
            fill="rgba(155, 35, 53, 0.08)"
          />

          <!-- Path line -->
          <polyline 
            :points="linePoints" 
            fill="none" 
            stroke="var(--color-beni-600)" 
            stroke-width="3"
            stroke-linecap="round"
          />

          <!-- Value Dots -->
          <circle 
            v-for="(pt, idx) in chartPoints" 
            :key="idx"
            :cx="pt.x" 
            :cy="pt.y" 
            r="5" 
            fill="var(--color-kin-500)" 
            stroke="#fff" 
            stroke-width="1.5"
            class="chart-dot"
            @mouseover="activeDotIndex = idx"
            @mouseleave="activeDotIndex = null"
          />
        </svg>

        <!-- X-Axis Labels -->
        <div class="chart-x-labels">
          <span v-for="d in revenueData.monthly" :key="d.month">{{ d.month }}</span>
        </div>

        <!-- Tooltip overlay -->
        <div 
          v-if="activeDotIndex !== null" 
          class="chart-tooltip"
          :style="{ left: `${(chartPoints[activeDotIndex].x / 600) * 100}%`, top: `${(chartPoints[activeDotIndex].y / 220) * 100 - 15}%` }"
        >
          <div class="ct-month">{{ revenueData.monthly[activeDotIndex].month }}</div>
          <div class="ct-val">{{ formatCurrency(revenueData.monthly[activeDotIndex].revenue) }}</div>
        </div>
      </div>
    </div>

    <!-- Revenue Detail Table -->
    <div class="table-section card-shoji">
      <h2 class="section-title" style="display:flex; align-items:center; gap:0.4rem;"><SvgIcon name="paid" /> Rincian Keuangan Per Bulan</h2>
      <div style="overflow-x: auto; margin-top: 1rem;">
        <table class="table-zen">
          <thead>
            <tr>
              <th>Bulan</th>
              <th>Reservasi Terpenuhi</th>
              <th>Total Omset Pendapatan</th>
              <th>Rata-Rata Tiket Kamar</th>
              <th>Pertumbuhan Bulanan</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(data, idx) in revenueData.monthly" :key="data.month">
              <td class="font-bold text-sumi">{{ data.month }}</td>
              <td>{{ data.reservations }} Reservasi</td>
              <td class="font-bold text-beni">{{ formatCurrency(data.revenue) }}</td>
              <td>{{ formatCurrency(Math.round(data.revenue / data.reservations)) }}</td>
              <td>
                <span v-if="idx === 0" style="color:var(--color-sumi-600);">—</span>
                <span v-else :class="getGrowthClass(data.revenue, revenueData.monthly[idx-1].revenue)">
                  {{ getGrowthPercentage(data.revenue, revenueData.monthly[idx-1].revenue) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import AdminLayout from '@/Components/Layout/AdminLayout.vue'
import SvgIcon from '@/Components/UI/SvgIcon.vue'
import { revenueData, formatCurrency } from '@/data/mockData'

defineOptions({ layout: AdminLayout })

const activeDotIndex = ref(null)

// Math calculations
const totalYTDRevenue = computed(() => revenueData.monthly.reduce((sum, d) => sum + d.revenue, 0))
const avgMonthlyRevenue = computed(() => Math.round(totalYTDRevenue.value / revenueData.monthly.length))
const totalReservationsCount = computed(() => revenueData.monthly.reduce((sum, d) => sum + d.reservations, 0))

// SVG path helper points mapping
const chartPoints = computed(() => {
  const width = 600
  const height = 220
  const paddingX = 50
  const stepX = (width - paddingX * 2) / (revenueData.monthly.length - 1)
  
  // Find max value to scale chart proportionally
  const maxRevenue = Math.max(...revenueData.monthly.map(d => d.revenue)) * 1.15
  
  return revenueData.monthly.map((d, index) => {
    const x = paddingX + index * stepX
    // SVG coordinate y grows downwards, so we subtract scaled height from bottom
    const y = height - 40 - ((d.revenue / maxRevenue) * (height - 80))
    return { x, y }
  })
})

const linePoints = computed(() => chartPoints.value.map(pt => `${pt.x},${pt.y}`).join(' '))

const areaPoints = computed(() => {
  if (chartPoints.value.length === 0) return ''
  const first = chartPoints.value[0]
  const last = chartPoints.value[chartPoints.value.length - 1]
  // Start from bottom-left corner, traverse the curve, and close at bottom-right corner
  return `40,180 ${linePoints.value} ${last.x},180`
})

const getGrowthPercentage = (current, previous) => {
  const diff = current - previous
  const percentage = ((diff / previous) * 100).toFixed(1)
  return diff >= 0 ? `↑ +${percentage}%` : `↓ ${percentage}%`
}

const getGrowthClass = (current, previous) => {
  return current >= previous ? 'font-green' : 'font-red'
}

const exportCSV = () => {
  // Generate CSV text contents dynamically
  let csvContent = 'data:text/csv;charset=utf-8,' 
    + 'Bulan,Reservasi Terpenuhi,Total Omset Pendapatan,Rata-Rata Tiket Kamar\n'
    + revenueData.monthly.map(d => `${d.month},${d.reservations},${d.revenue},${Math.round(d.revenue / d.reservations)}`).join('\n')
  
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', 'Miyabi_Hotel_Revenue_Report_2025.csv')
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  alert('Laporan pendapatan berhasil diexport dan diunduh.')
}
</script>

<style scoped>
.admin-reports-page { display:flex; flex-direction:column; gap:1.75rem; }

.page-header-flex { display:flex; justify-content:space-between; align-items:center; }
.page-title { font-family:'Cormorant Garamond',serif; font-size:2rem; font-weight:600; color:var(--color-sumi-800); }
.page-subtitle { font-size:0.85rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; margin-top:2px; }

/* Stats cards layout */
.stats-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:1.25rem; }
.stat-card-zen { padding:1.5rem; border-radius:4px; display:flex; flex-direction:column; justify-content:center; }
.sc-label { font-size:0.7rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; margin-bottom:0.4rem; }
.sc-value { font-family:'Cormorant Garamond',serif; font-size:2.2rem; font-weight:700; line-height:1; margin-bottom:0.25rem; }
.sc-subtext { font-size:0.75rem; color:var(--color-sumi-600); font-family:'Inter',sans-serif; }
.font-green { color:var(--color-matcha-600) !important; font-weight:600; }
.font-red { color:var(--color-beni-600) !important; font-weight:600; }

/* Interactive SVG Area Chart section */
.chart-section { padding:1.5rem; border-radius:4px; display:flex; flex-direction:column; gap:1rem; }
.chart-header { display:flex; justify-content:space-between; align-items:center; }
.chart-title { font-family:'Cormorant Garamond',serif; font-size:1.3rem; font-weight:600; color:var(--color-sumi-800); }
.chart-legend { display:flex; align-items:center; gap:0.4rem; font-size:0.75rem; font-family:'Inter',sans-serif; color:var(--color-sumi-600); }
.legend-color { width:10px; height:10px; border-radius:2px; display:inline-block; }
.legend-beni { background:var(--color-beni-600); }

.chart-area-wrapper { position:relative; padding-bottom:1rem; }
.svg-chart { width:100%; height:220px; overflow:visible; }
.chart-dot { cursor:pointer; transition: r 0.15s, fill 0.15s; }
.chart-dot:hover { r:8px; fill:var(--color-beni-500); }

.chart-x-labels { display:flex; justify-content:space-between; padding:0.5rem 50px 0; border-top:1px solid rgba(201,168,76,0.15); }
.chart-x-labels span { font-size:0.75rem; font-family:'Inter',sans-serif; color:var(--color-sumi-600); font-weight:600; }

.chart-tooltip {
  position:absolute; background:var(--color-sumi-800); color:#fff;
  border:1px solid rgba(201,168,76,0.3); border-radius:3px;
  padding:0.4rem 0.6rem; font-family:'Inter',sans-serif; font-size:0.7rem;
  pointer-events:none; z-index:10; transform:translateX(-50%) translateY(-100%);
  box-shadow:0 6px 20px rgba(0,0,0,0.15); transition:all 0.15s ease-out;
}
.ct-month { color:var(--color-kin-300); font-weight:bold; text-transform:uppercase; font-size:0.6rem; letter-spacing:0.04em; }
.ct-val { font-weight:bold; margin-top:2px; }

/* Table styling adjustments */
.table-section { padding:1.5rem; border-radius:4px; }
.section-title { font-family:'Cormorant Garamond',serif; font-size:1.3rem; font-weight:600; color:var(--color-sumi-800); }

@media(max-width:768px) {
  .page-header-flex { flex-direction:column; align-items:stretch; gap:1rem; }
  .chart-x-labels { padding:0.5rem 20px 0; }
}
</style>
