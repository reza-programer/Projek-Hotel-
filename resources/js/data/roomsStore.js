// resources/js/data/roomsStore.js
import { ref } from 'vue'
import { rooms as initialRooms } from './mockData'

const KEY = 'miyabi_rooms_data_v4'

const loadRooms = () => {
  if (typeof window !== 'undefined') {
    const stored = localStorage.getItem(KEY)
    if (stored) {
      try {
        const parsed = JSON.parse(stored)
        if (Array.isArray(parsed) && parsed.length > 0) {
          return parsed
        }
      } catch (e) {
        console.error('Failed to parse rooms from localStorage', e)
      }
    }
  }
  return initialRooms
}

export const rooms = ref(loadRooms())

export const saveRooms = () => {
  if (typeof window !== 'undefined') {
    localStorage.setItem(KEY, JSON.stringify(rooms.value))
  }
}
