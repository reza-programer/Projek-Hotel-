<template>
  <div class="custom-datepicker" ref="containerRef">
    <div class="datepicker-input-trigger" @click.stop="showCalendar = !showCalendar" :class="{ active: showCalendar }">
      <span class="datepicker-icon">📅</span>
      <span class="datepicker-value">{{ displayDate }}</span>
      <span class="datepicker-chevron">▼</span>
    </div>
    
    <Transition name="fade-pop">
      <div v-if="showCalendar" class="calendar-popover card-shoji" :class="direction" @click.stop>
        <div class="calendar-header">
          <button type="button" class="month-nav-btn" @click="prevMonth">‹</button>
          <div class="calendar-month-year">{{ currentMonthYearName }}</div>
          <button type="button" class="month-nav-btn" @click="nextMonth">›</button>
        </div>
        
        <div class="calendar-weekdays">
          <span v-for="day in weekdays" :key="day" class="weekday-name">{{ day }}</span>
        </div>
        
        <div class="calendar-days-grid">
          <button
            v-for="(cell, idx) in daysInMonth"
            :key="idx"
            type="button"
            class="calendar-day-cell"
            :class="{
              'other-month': !cell.isCurrentMonth,
              'selected': isSelected(cell.date),
              'today': isToday(cell.date),
              'disabled': isDateDisabled(cell.date)
            }"
            :disabled="isDateDisabled(cell.date)"
            @click="selectDate(cell.date)"
          >
            {{ cell.day }}
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  min: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Pilih Tanggal'
  },
  direction: {
    type: String,
    default: 'down'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const showCalendar = ref(false)
const containerRef = ref(null)

// The month/year view we are currently browsing in the calendar popover
const currentDate = ref(new Date())

// Initialize currentDate to current modelValue if available
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    const d = new Date(newVal)
    if (!isNaN(d.getTime())) {
      currentDate.value = d
    }
  }
}, { immediate: true })

const months = [
  'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
]

const weekdays = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']

const currentMonthYearName = computed(() => {
  const m = months[currentDate.value.getMonth()]
  const y = currentDate.value.getFullYear()
  return `${m} ${y}`
})

const displayDate = computed(() => {
  if (!props.modelValue) return props.placeholder
  const date = new Date(props.modelValue)
  if (isNaN(date.getTime())) return props.placeholder
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  }).format(date)
})

const daysInMonth = computed(() => {
  const year = currentDate.value.getFullYear()
  const month = currentDate.value.getMonth()
  
  // Day of the week for the 1st of the month
  const firstDay = new Date(year, month, 1).getDay()
  
  // Total days in the month
  const totalDays = new Date(year, month + 1, 0).getDate()
  
  // Days from previous month to pad the grid start
  const prevMonthTotalDays = new Date(year, month, 0).getDate()
  const paddingStart = []
  for (let i = firstDay - 1; i >= 0; i--) {
    const prevDay = prevMonthTotalDays - i
    paddingStart.push({
      day: prevDay,
      isCurrentMonth: false,
      date: new Date(year, month - 1, prevDay)
    })
  }

  // Days in current month
  const currentDays = []
  for (let i = 1; i <= totalDays; i++) {
    currentDays.push({
      day: i,
      isCurrentMonth: true,
      date: new Date(year, month, i)
    })
  }

  // Padding end of the grid to make exactly 42 elements (6 rows)
  const remaining = 42 - (paddingStart.length + currentDays.length)
  const paddingEnd = []
  for (let i = 1; i <= remaining; i++) {
    paddingEnd.push({
      day: i,
      isCurrentMonth: false,
      date: new Date(year, month + 1, i)
    })
  }

  return [...paddingStart, ...currentDays, ...paddingEnd]
})

const prevMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1, 1)
}

const nextMonth = () => {
  currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1, 1)
}

const selectDate = (date) => {
  if (isDateDisabled(date)) return
  
  const y = date.getFullYear()
  const m = String(date.getMonth() + 1).padStart(2, '0')
  const d = String(date.getDate()).padStart(2, '0')
  const formatted = `${y}-${m}-${d}`
  
  emit('update:modelValue', formatted)
  emit('change', formatted)
  showCalendar.value = false
}

const isSelected = (date) => {
  if (!props.modelValue) return false
  const selected = new Date(props.modelValue)
  return date.getDate() === selected.getDate() &&
         date.getMonth() === selected.getMonth() &&
         date.getFullYear() === selected.getFullYear()
}

const isToday = (date) => {
  const today = new Date()
  return date.getDate() === today.getDate() &&
         date.getMonth() === today.getMonth() &&
         date.getFullYear() === today.getFullYear()
}

const isDateDisabled = (date) => {
  if (!props.min) return false
  const minDate = new Date(props.min)
  minDate.setHours(0, 0, 0, 0)
  const compareDate = new Date(date)
  compareDate.setHours(0, 0, 0, 0)
  return compareDate < minDate
}

const handleClickOutside = (event) => {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    showCalendar.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.custom-datepicker {
  position: relative;
  width: 100%;
  font-family: 'Inter', sans-serif;
}
.datepicker-input-trigger {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 0.85rem;
  background: #fff;
  border: 1px solid rgba(201, 168, 76, 0.3);
  border-radius: 4px;
  cursor: pointer;
  user-select: none;
  transition: 0.2s;
  font-size: 0.88rem;
  color: var(--color-sumi-800);
}
.datepicker-input-trigger:hover {
  border-color: var(--color-kin-500);
  box-shadow: 0 2px 8px rgba(201, 168, 76, 0.08);
}
.datepicker-input-trigger.active {
  border-color: var(--color-beni-600);
  box-shadow: 0 0 0 2px rgba(155, 35, 53, 0.1);
}
.datepicker-icon {
  font-size: 0.95rem;
  color: var(--color-kin-500);
}
.datepicker-value {
  flex: 1;
}
.datepicker-chevron {
  font-size: 0.65rem;
  color: var(--color-sumi-500);
  transition: transform 0.2s;
}
.datepicker-input-trigger.active .datepicker-chevron {
  transform: rotate(180deg);
}

.calendar-popover {
  position: absolute;
  z-index: 100;
  width: 300px;
  padding: 1rem;
  border-radius: 6px;
  background: rgba(253, 250, 245, 0.98);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(201, 168, 76, 0.25);
  box-shadow: 0 10px 30px rgba(26, 26, 46, 0.15);
}
.calendar-popover.down {
  top: calc(100% + 6px);
  left: 0;
}
.calendar-popover.up {
  bottom: calc(100% + 12px);
  left: 0;
}
.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}
.calendar-month-year {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-sumi-800);
  text-transform: capitalize;
}
.month-nav-btn {
  background: transparent;
  border: 1px solid rgba(201, 168, 76, 0.2);
  width: 28px;
  height: 28px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 1.1rem;
  color: var(--color-sumi-700);
  transition: 0.2s;
}
.month-nav-btn:hover {
  background: var(--color-kin-500);
  color: #fff;
  border-color: var(--color-kin-500);
}
.calendar-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  text-align: center;
  font-size: 0.68rem;
  font-weight: 600;
  color: var(--color-kin-500);
  font-family: 'Noto Serif JP', serif;
  margin-bottom: 0.5rem;
}
.weekday-name {
  padding: 0.25rem 0;
}
.calendar-days-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2px;
}
.calendar-day-cell {
  background: transparent;
  border: none;
  height: 34px;
  border-radius: 4px;
  font-size: 0.8rem;
  color: var(--color-sumi-800);
  cursor: pointer;
  transition: 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}
.calendar-day-cell:hover:not(.disabled) {
  background: rgba(201, 168, 76, 0.1);
  color: var(--color-kin-600);
}
.calendar-day-cell.other-month {
  color: var(--color-sumi-400);
  opacity: 0.5;
}
.calendar-day-cell.selected {
  background: var(--color-beni-600) !important;
  color: #fff !important;
  font-weight: bold;
}
.calendar-day-cell.today {
  border: 1px solid var(--color-kin-500);
  color: var(--color-kin-600);
  font-weight: 600;
}
.calendar-day-cell.disabled {
  color: #ccc;
  cursor: not-allowed;
  opacity: 0.3;
  text-decoration: line-through;
}

/* Pop transition */
.fade-pop-enter-active, .fade-pop-leave-active {
  transition: opacity 0.2s, transform 0.2s;
}
.fade-pop-enter-from, .fade-pop-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
