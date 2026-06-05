<template>
  <svg 
    xmlns="http://www.w3.org/2000/svg" 
    :viewBox="viewBox" 
    :width="width" 
    :height="height" 
    :fill="fillColor" 
    :stroke="strokeColor" 
    :stroke-width="strokeWidth"
    stroke-linecap="round" 
    stroke-linejoin="round"
    class="svg-icon"
  >
    <!-- Onsen (Hot Spring Steam) -->
    <g v-if="name === 'onsen'">
      <path d="M12 2v4M17 3v4M7 3v4M2 20h20M4 16c0-4 3-4 3-8M9 16c0-4 3-4 3-8M14 16c0-4 3-4 3-8M19 16c0-4 3-4 3-8" />
    </g>

    <!-- Dining (Plate, Spoon & Fork) -->
    <g v-else-if="name === 'dining'">
      <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5" />
      <path d="M12 12h5a3.5 3.5 0 0 1 0 7H12M8 5v14" />
    </g>

    <!-- Garden (Zen Stones/Leaves) -->
    <g v-else-if="name === 'garden'">
      <path d="M12 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM6 21a5 5 0 1 0 0-10 5 5 0 0 0 0 10zM17 22a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
    </g>

    <!-- Tea Ceremony Room (Teacup) -->
    <g v-else-if="name === 'tea'">
      <path d="M18 8H6c0 4.42 3.58 8 8 8h4c4.42 0 8-3.58 8-8zM6 8a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4" />
      <path d="M2 20h20" />
    </g>

    <!-- Koi Pond (Fish) -->
    <g v-else-if="name === 'koi'">
      <path d="M23 3a10.9 10.9 0 0 0-9.37 3.73c-2.33-.23-4.72.6-6.49 2.37L2 14.24l6.4 1.35 1.35 6.4 5.14-5.14c1.77-1.77 2.6-4.16 2.37-6.49A10.9 10.9 0 0 0 21 1z" />
      <path d="M12.24 7.76c.42.42.42 1.1 0 1.52L9 12.5" />
    </g>

    <!-- Ikebana (Flower) -->
    <g v-else-if="name === 'ikebana'">
      <circle cx="12" cy="12" r="3" />
      <path d="M12 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3zm0 12a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3zm-7-5a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3zm14 0a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z" />
    </g>

    <!-- Jacuzzi (Tub with bubbles) -->
    <g v-else-if="name === 'jacuzzi'">
      <path d="M2 12h20M2 16h20M2 20h20" />
      <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm7 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm7 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" />
    </g>

    <!-- Fitness (Dumbbell) -->
    <g v-else-if="name === 'fitness'">
      <path d="M6.5 6.5h11M18 4h3v16h-3zM3 4h3v16H3zM6.5 12h11" />
    </g>

    <!-- Business (Briefcase) -->
    <g v-else-if="name === 'business'">
      <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
      <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
    </g>

    <!-- Parking (Car) -->
    <g v-else-if="name === 'parking'">
      <rect x="1" y="3" width="22" height="13" rx="2" />
      <path d="M7 21h10M5 16v5M19 16v5" />
      <circle cx="7" cy="10" r="1" />
      <circle cx="17" cy="10" r="1" />
    </g>

    <!-- Calligraphy (Brush) -->
    <g v-else-if="name === 'calligraphy'">
      <path d="m12 3-1.912 5.736A2 2 0 0 1 8.18 10.08L3 12l5.18 1.92a2 2 0 0 1 1.908 1.344L12 21l1.912-5.736a2 2 0 0 1 1.908-1.344L21 12l-5.18-1.92a2 2 0 0 1-1.908-1.344z" />
    </g>

    <!-- Sake (Bottle & Cup) -->
    <g v-else-if="name === 'sake'">
      <path d="M8 22h8a2 2 0 0 0 2-2V9.5a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3V20a2 2 0 0 0 2 2zM12 2v4" />
      <circle cx="12" cy="14" r="2" />
    </g>

    <!-- Pending (Clock) -->
    <g v-else-if="name === 'pending'">
      <circle cx="12" cy="12" r="10" />
      <polyline points="12 6 12 12 16 14" />
    </g>

    <!-- Confirmed / Verified (Check/Success) -->
    <g v-else-if="name === 'confirmed' || name === 'verified'">
      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
      <polyline points="22 4 12 14.01 9 11.01" />
    </g>

    <!-- Completed (Flag/Finish) -->
    <g v-else-if="name === 'completed'">
      <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1zM4 22v-7" />
    </g>

    <!-- Cancelled / Rejected (X/Error) -->
    <g v-else-if="name === 'cancelled' || name === 'rejected'">
      <circle cx="12" cy="12" r="10" />
      <line x1="15" y1="9" x2="9" y2="15" />
      <line x1="9" y1="9" x2="15" y2="15" />
    </g>

    <!-- Refunded (U-turn Arrow) -->
    <g v-else-if="name === 'refunded'">
      <path d="M9 14 4 9l5-5" />
      <path d="M4 9h12a5 5 0 0 1 5 5v3a2 2 0 0 1-2 2h-1" />
    </g>

    <!-- Paid / Revenue (Dollar Sign / Coins) -->
    <g v-else-if="name === 'paid' || name === 'revenue'">
      <line x1="12" y1="1" x2="12" y2="23" />
      <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
    </g>

    <!-- Search (Magnifying Glass) -->
    <g v-else-if="name === 'search'">
      <circle cx="11" cy="11" r="8" />
      <line x1="21" y1="21" x2="16.65" y2="16.65" />
    </g>

    <!-- Edit (Pencil) -->
    <g v-else-if="name === 'edit'">
      <path d="M12 20h9" />
      <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
    </g>

    <!-- Delete (Trash) -->
    <g v-else-if="name === 'delete'">
      <polyline points="3 6 5 6 21 6" />
      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
      <line x1="10" y1="11" x2="10" y2="17" />
      <line x1="14" y1="11" x2="14" y2="17" />
    </g>

    <!-- Gate (Torii Gate Silhouette) -->
    <g v-else-if="name === 'gate'">
      <path d="M2 6h20M2 10h20M6 10v12M18 10v12M4 3a8 8 0 0 0 4 3M20 3a8 8 0 0 1-4 3" />
    </g>

    <!-- Key (Password/Key) -->
    <g v-else-if="name === 'key'">
      <path d="m21 2-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0 1.5 1.5M15.5 7.5 14 6" />
    </g>

    <!-- Door (Check-out) -->
    <g v-else-if="name === 'door'">
      <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" />
    </g>

    <!-- Warning (Exclamation Mark in Triangle) -->
    <g v-else-if="name === 'warning'">
      <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
      <line x1="12" y1="9" x2="12" y2="13" />
      <line x1="12" y1="17" x2="12.01" y2="17" />
    </g>

    <!-- Star (Rating) -->
    <g v-else-if="name === 'star'">
      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
    </g>

    <!-- Info (Informative hint) -->
    <g v-else-if="name === 'info'">
      <circle cx="12" cy="12" r="10" />
      <line x1="12" y1="16" x2="12" y2="12" />
      <line x1="12" y1="8" x2="12.01" y2="8" />
    </g>

    <!-- Google Icon -->
    <g v-else-if="name === 'google'">
      <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" stroke="none" />
      <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" stroke="none" />
      <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05" stroke="none" />
      <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335" stroke="none" />
    </g>

    <!-- Fallback general flower shape -->
    <g v-else>
      <circle cx="12" cy="12" r="3" />
      <path d="M12 2a3 3 0 0 0-3 3v2a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3zM12 14a3 3 0 0 0-3 3v2a3 3 0 0 0 6 0v-2a3 3 0 0 0-3-3zM2 12a3 3 0 0 0 3 3h2a3 3 0 0 0 0-6H5a3 3 0 0 0-3 3zm12 0a3 3 0 0 0 3 3h2a3 3 0 0 0 0-6h-2a3 3 0 0 0-3 3z" />
    </g>
  </svg>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  name: { type: String, required: true },
  width: { type: [Number, String], default: 18 },
  height: { type: [Number, String], default: 18 },
  stroke: { type: String, default: 'currentColor' },
  fill: { type: String, default: 'none' },
  strokeWidth: { type: [Number, String], default: 1.8 }
})

// Determine proper stroke and fill colors based on name to make SVGs look unified
const strokeColor = computed(() => {
  if (props.name === 'star' || props.name === 'google') return 'none'
  return props.stroke
})

const fillColor = computed(() => {
  if (props.name === 'star') return 'currentColor'
  if (props.name === 'google') return 'none'
  return props.fill
})

const viewBox = computed(() => '0 0 24 24')
</script>

<style scoped>
.svg-icon {
  display: inline-block;
  vertical-align: middle;
  flex-shrink: 0;
}
</style>
