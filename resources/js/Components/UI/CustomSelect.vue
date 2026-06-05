<template>
  <div class="custom-select" ref="containerRef">
    <div class="select-trigger" @click.stop="isOpen = !isOpen" :class="{ active: isOpen }">
      <span class="select-trigger-label">{{ selectedLabel }}</span>
      <span class="select-trigger-chevron">▼</span>
    </div>
    
    <Transition name="fade-pop">
      <div v-if="isOpen" class="select-options-list card-shoji" :class="direction" @click.stop>
        <div
          v-for="(option, idx) in formattedOptions"
          :key="idx"
          class="select-option-item"
          :class="{ selected: option.value == modelValue }"
          @click="selectOption(option.value)"
        >
          {{ option.label }}
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: [String, Number],
  options: {
    type: Array,
    required: true
  },
  placeholder: {
    type: String,
    default: 'Pilih opsi'
  },
  direction: {
    type: String,
    default: 'down'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const isOpen = ref(false)
const containerRef = ref(null)

const formattedOptions = computed(() => {
  return props.options.map(opt => {
    if (typeof opt === 'object' && opt !== null) {
      return { value: opt.value, label: opt.label }
    }
    return { value: opt, label: String(opt) }
  })
})

const selectedLabel = computed(() => {
  const matched = formattedOptions.value.find(opt => opt.value == props.modelValue)
  return matched ? matched.label : props.placeholder
})

const selectOption = (val) => {
  emit('update:modelValue', val)
  emit('change', val)
  isOpen.value = false
}

const handleClickOutside = (event) => {
  if (containerRef.value && !containerRef.value.contains(event.target)) {
    isOpen.value = false
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
.custom-select {
  position: relative;
  width: 100%;
  font-family: 'Inter', sans-serif;
}
.select-trigger {
  display: flex;
  align-items: center;
  justify-content: space-between;
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
.select-trigger:hover {
  border-color: var(--color-kin-500);
  box-shadow: 0 2px 8px rgba(201, 168, 76, 0.08);
}
.select-trigger.active {
  border-color: var(--color-beni-600);
  box-shadow: 0 0 0 2px rgba(155, 35, 53, 0.1);
}
.select-trigger-label {
  flex: 1;
}
.select-trigger-chevron {
  font-size: 0.65rem;
  color: var(--color-sumi-500);
  transition: transform 0.2s;
}
.select-trigger.active .select-trigger-chevron {
  transform: rotate(180deg);
}

.select-options-list {
  position: absolute;
  z-index: 100;
  max-height: 200px;
  overflow-y: auto;
  border-radius: 4px;
  background: rgba(253, 250, 245, 0.98);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(201, 168, 76, 0.25);
  box-shadow: 0 8px 24px rgba(26, 26, 46, 0.15);
  padding: 0.25rem 0;
}
.select-options-list.down {
  top: calc(100% + 6px);
  left: 0;
  right: 0;
}
.select-options-list.up {
  bottom: calc(100% + 12px);
  left: 0;
  right: 0;
}
.select-option-item {
  padding: 0.6rem 0.85rem;
  font-size: 0.85rem;
  color: var(--color-sumi-700);
  cursor: pointer;
  transition: 0.2s;
}
.select-option-item:hover {
  background: rgba(201, 168, 76, 0.08);
  color: var(--color-kin-600);
}
.select-option-item.selected {
  background: var(--color-beni-600) !important;
  color: #fff !important;
  font-weight: bold;
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
