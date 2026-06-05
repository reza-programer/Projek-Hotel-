<template>
  <div class="miyabi-chat-widget">
    <!-- Floating Toggle Button -->
    <button class="chat-toggle-btn" @click="toggleChat" aria-label="Hubungi Concierge">
      <div class="btn-content">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <span class="btn-text">Concierge</span>
      </div>
      <span v-if="unreadCount > 0" class="chat-badge">{{ unreadCount }}</span>
    </button>

    <!-- Chat Window Drawer -->
    <Transition name="chat-slide">
      <div v-if="isOpen" class="chat-window card-shoji">
        <!-- Header -->
        <div class="chat-header">
          <div class="header-profile">
            <span class="header-kanji">雅</span>
            <div>
              <div class="header-title">Miyabi Concierge</div>
              <div class="header-status">
                <span class="status-dot"></span>
                <span>Online (Omotenashi)</span>
              </div>
            </div>
          </div>
          <button class="chat-close-btn" @click="isOpen = false" aria-label="Tutup Chat">✕</button>
        </div>

        <!-- Chat Body -->
        <div class="chat-body" ref="bodyRef">
          <!-- Auth Gate: Not Logged In -->
          <div v-if="!currentUser" class="auth-required-state">
            <div class="auth-box">
              <span class="auth-icon">🔒</span>
              <h3>Layanan Khusus Anggota</h3>
              <p>Silakan masuk ke akun Miyabi Club Anda untuk dapat mengobrol dengan Concierge kami yang ramah.</p>
              <a href="/login" class="btn-vermillion btn-auth-action">Masuk Ke Akun</a>
              <a href="/register" class="auth-register-link">Belum punya akun? Daftar gratis</a>
            </div>
          </div>

          <!-- Logged In State -->
          <div v-else class="chat-messages-container">
            <div v-for="msg in userMessages" :key="msg.id" class="message-row" :class="msg.senderRole">
              <div class="message-bubble">
                <div class="message-sender" v-if="msg.senderRole === 'admin'">{{ msg.senderName }}</div>
                <div class="message-text">{{ msg.text }}</div>
                <div class="message-time">{{ formatTime(msg.timestamp) }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Chat Footer (Input) -->
        <div v-if="currentUser" class="chat-footer">
          <form @submit.prevent="sendMessage" class="chat-input-form">
            <input 
              v-model="inputText" 
              type="text" 
              placeholder="Ketik pesan untuk concierge..." 
              class="chat-input"
              maxlength="300"
              required 
            />
            <button type="submit" class="chat-send-btn" aria-label="Kirim Pesan">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
              </svg>
            </button>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const isOpen = ref(false)
const inputText = ref('')
const allMessages = ref([])
const localUser = ref(null)
const bodyRef = ref(null)

const page = usePage()

const currentUser = computed(() => {
  if (page.props.auth && page.props.auth.user) {
    return page.props.auth.user
  }
  return localUser.value
})

// Filter messages specifically for the logged-in user
const userMessages = computed(() => {
  if (!currentUser.value) return []
  return allMessages.value.filter(msg => msg.userId === currentUser.value.id)
})

// Count unread messages from admin
const unreadCount = computed(() => {
  if (!currentUser.value || isOpen.value) return 0
  return userMessages.value.filter(msg => msg.senderRole === 'admin' && !msg.readByUser).length
})

const loadMessages = () => {
  if (typeof window !== 'undefined') {
    const raw = localStorage.getItem('miyabi_chat_messages')
    if (raw) {
      try {
        allMessages.value = JSON.parse(raw)
      } catch (e) {
        console.error('Failed parsing chat messages', e)
        allMessages.value = []
      }
    } else {
      allMessages.value = []
    }

    // Auto welcome message if empty for this user
    if (currentUser.value && userMessages.value.length === 0) {
      const welcomeMsg = {
        id: Date.now(),
        userId: currentUser.value.id,
        userName: currentUser.value.name,
        senderRole: 'admin',
        senderName: 'Miyabi Concierge',
        text: `Selamat datang di Miyabi Hotel Concierge, Bapak/Ibu ${currentUser.value.name}. Ada yang bisa kami bantu untuk kunjungan Anda hari ini?`,
        timestamp: new Date().toISOString(),
        readByUser: false,
        readByAdmin: true
      }
      allMessages.value.push(welcomeMsg)
      localStorage.setItem('miyabi_chat_messages', JSON.stringify(allMessages.value))
    }
  }
}

const toggleChat = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    markAsRead()
    nextTick(() => {
      scrollToBottom()
    })
  }
}

const markAsRead = () => {
  if (!currentUser.value) return
  let updated = false
  allMessages.value = allMessages.value.map(msg => {
    if (msg.userId === currentUser.value.id && msg.senderRole === 'admin' && !msg.readByUser) {
      updated = true
      return { ...msg, readByUser: true }
    }
    return msg
  })
  if (updated) {
    localStorage.setItem('miyabi_chat_messages', JSON.stringify(allMessages.value))
  }
}

const sendMessage = () => {
  if (!inputText.value.trim() || !currentUser.value) return

  const newMsg = {
    id: Date.now(),
    userId: currentUser.value.id,
    userName: currentUser.value.name,
    senderRole: 'user',
    senderName: currentUser.value.name,
    text: inputText.value.trim(),
    timestamp: new Date().toISOString(),
    readByUser: true,
    readByAdmin: false
  }

  allMessages.value.push(newMsg)
  localStorage.setItem('miyabi_chat_messages', JSON.stringify(allMessages.value))
  inputText.value = ''
  
  nextTick(() => {
    scrollToBottom()
  })

  // Simulated auto-response if they are offline
  // Only trigger if no active tab updates. We can do a small delay
  setTimeout(() => {
    // Check if admin has responded, otherwise reply with automated help
    const raw = localStorage.getItem('miyabi_chat_messages')
    const currentMsgs = raw ? JSON.parse(raw) : []
    const userMsgs = currentMsgs.filter(m => m.userId === currentUser.value.id)
    const lastMsg = userMsgs[userMsgs.length - 1]
    
    if (lastMsg && lastMsg.senderRole === 'user') {
      // Simulate admin typings
      const reply = {
        id: Date.now() + 1,
        userId: currentUser.value.id,
        userName: currentUser.value.name,
        senderRole: 'admin',
        senderName: 'Miyabi Concierge (Auto)',
        text: 'Pesan Anda telah diterima oleh Concierge. Staf kami akan segera menanggapi permintaan Anda di portal admin dalam beberapa saat. Terima kasih atas kesabaran Anda.',
        timestamp: new Date().toISOString(),
        readByUser: false,
        readByAdmin: true
      }
      allMessages.value.push(reply)
      localStorage.setItem('miyabi_chat_messages', JSON.stringify(allMessages.value))
      nextTick(() => {
        scrollToBottom()
      })
    }
  }, 10000) // 10 seconds auto-response simulation if admin tab is closed
}

const scrollToBottom = () => {
  if (bodyRef.value) {
    bodyRef.value.scrollTop = bodyRef.value.scrollHeight
  }
}

const formatTime = (isoString) => {
  try {
    const date = new Date(isoString)
    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  } catch (e) {
    return ''
  }
}

// Watchers and Storage Listeners
const handleStorageChange = (e) => {
  if (e.key === 'miyabi_chat_messages') {
    loadMessages()
    if (isOpen.value) {
      markAsRead()
      nextTick(() => {
        scrollToBottom()
      })
    }
  }
  if (e.key === 'miyabi_user') {
    checkUser()
  }
}

const checkUser = () => {
  const stored = localStorage.getItem('miyabi_user')
  if (stored) {
    localUser.value = JSON.parse(stored)
    loadMessages()
  } else {
    localUser.value = null
    allMessages.value = []
  }
}

onMounted(() => {
  checkUser()
  loadMessages()
  window.addEventListener('storage', handleStorageChange)
})

onUnmounted(() => {
  window.removeEventListener('storage', handleStorageChange)
})

watch(isOpen, (newVal) => {
  if (newVal) {
    nextTick(() => {
      scrollToBottom()
    })
  }
})
</script>

<style scoped>
.miyabi-chat-widget {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  z-index: 999;
  font-family: var(--font-sans);
}

/* Floating Button */
.chat-toggle-btn {
  background: var(--color-beni-600);
  border: 1px solid var(--color-kin-500);
  color: #fff;
  padding: 0 1.25rem;
  height: 52px;
  border-radius: 26px;
  cursor: pointer;
  box-shadow: 0 4px 18px rgba(155, 35, 53, 0.35);
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  position: relative;
}
.chat-toggle-btn:hover {
  background: var(--color-beni-700);
  box-shadow: 0 6px 22px rgba(155, 35, 53, 0.45);
  transform: translateY(-2px);
}
.btn-content {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.btn-text {
  font-family: var(--font-display);
  font-size: 0.95rem;
  font-weight: 600;
  letter-spacing: 0.05em;
}
.chat-badge {
  position: absolute;
  top: -6px;
  right: -2px;
  background: var(--color-kin-500);
  border: 2px solid var(--color-beni-600);
  color: var(--color-sumi-900);
  font-size: 0.72rem;
  font-weight: 700;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Chat Window Box */
.chat-window {
  position: absolute;
  bottom: 64px;
  right: 0;
  width: 360px;
  height: 500px;
  background: rgba(253, 250, 245, 0.99);
  border: 1px solid rgba(201, 168, 76, 0.3);
  border-radius: 8px;
  box-shadow: 0 10px 35px rgba(26, 26, 46, 0.2);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

/* Header */
.chat-header {
  background: var(--color-sumi-800);
  padding: 0.9rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 2px solid var(--color-kin-500);
}
.header-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.header-kanji {
  font-family: var(--font-serif-jp);
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-kin-300);
  line-height: 1;
}
.header-title {
  font-family: var(--font-display);
  font-weight: 600;
  color: #fff;
  font-size: 0.95rem;
  letter-spacing: 0.03em;
}
.header-status {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.68rem;
  color: rgba(255, 255, 255, 0.6);
}
.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--color-matcha-500);
  box-shadow: 0 0 8px var(--color-matcha-500);
}
.chat-close-btn {
  background: transparent;
  border: none;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
  cursor: pointer;
  padding: 0.25rem;
  transition: color 0.2s;
}
.chat-close-btn:hover {
  color: var(--color-beni-400);
}

/* Body */
.chat-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.25rem;
  background: var(--color-washi-300);
}
.chat-body::-webkit-scrollbar {
  width: 4px;
}
.chat-body::-webkit-scrollbar-thumb {
  background: rgba(201, 168, 76, 0.3);
  border-radius: 2px;
}

/* Auth required */
.auth-required-state {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}
.auth-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
  padding: 1rem;
}
.auth-icon {
  font-size: 2.2rem;
  margin-bottom: 0.25rem;
}
.auth-box h3 {
  font-family: var(--font-display);
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--color-sumi-800);
}
.auth-box p {
  font-size: 0.76rem;
  color: var(--color-sumi-600);
  line-height: 1.5;
}
.btn-auth-action {
  margin-top: 0.5rem;
  padding: 0.5rem 1.5rem;
  font-size: 0.82rem;
  width: 100%;
  justify-content: center;
}
.auth-register-link {
  font-size: 0.72rem;
  color: var(--color-kin-700);
  text-decoration: none;
  transition: text-decoration 0.2s;
}
.auth-register-link:hover {
  text-decoration: underline;
}

/* Chat Messages */
.chat-messages-container {
  display: flex;
  flex-direction: column;
  gap: 0.9rem;
}
.message-row {
  display: flex;
  width: 100%;
}
.message-row.user {
  justify-content: flex-end;
}
.message-row.admin {
  justify-content: flex-start;
}

.message-bubble {
  max-width: 80%;
  padding: 0.65rem 0.85rem;
  border-radius: 4px;
  position: relative;
  box-shadow: 0 2px 8px rgba(26, 26, 46, 0.05);
}

.user .message-bubble {
  background: var(--color-beni-600);
  color: #fff;
  border-bottom-right-radius: 0;
}
.admin .message-bubble {
  background: #fff;
  color: var(--color-sumi-800);
  border: 1px solid rgba(201, 168, 76, 0.3);
  border-bottom-left-radius: 0;
}

.message-sender {
  font-family: var(--font-display);
  font-size: 0.65rem;
  font-weight: 600;
  color: var(--color-kin-700);
  margin-bottom: 2px;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}
.message-text {
  font-size: 0.78rem;
  line-height: 1.45;
  word-break: break-word;
}
.message-time {
  font-size: 0.6rem;
  text-align: right;
  margin-top: 4px;
  opacity: 0.65;
}
.user .message-time {
  color: rgba(255, 255, 255, 0.8);
}
.admin .message-time {
  color: var(--color-sumi-500);
}

/* Footer */
.chat-footer {
  padding: 0.75rem 1rem;
  background: #fff;
  border-top: 1px solid rgba(201, 168, 76, 0.2);
}
.chat-input-form {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}
.chat-input {
  flex: 1;
  border: 1px solid rgba(201, 168, 76, 0.3);
  border-radius: 4px;
  padding: 0.5rem 0.75rem;
  font-size: 0.8rem;
  outline: none;
  font-family: var(--font-sans);
  background: var(--color-washi-100);
  transition: all 0.2s;
}
.chat-input:focus {
  border-color: var(--color-kin-500);
  background: #fff;
  box-shadow: 0 0 0 2px rgba(201, 168, 76, 0.15);
}
.chat-send-btn {
  width: 34px;
  height: 34px;
  border-radius: 4px;
  border: none;
  background: var(--color-beni-600);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s;
  flex-shrink: 0;
}
.chat-send-btn:hover {
  background: var(--color-beni-700);
}

/* Animation slide */
.chat-slide-enter-active {
  transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.chat-slide-leave-active {
  transition: all 0.25s cubic-bezier(0.36, 0.07, 0.19, 0.97);
}
.chat-slide-enter-from {
  opacity: 0;
  transform: translateY(30px) scale(0.95);
}
.chat-slide-leave-to {
  opacity: 0;
  transform: translateY(15px) scale(0.97);
}

@media(max-width: 480px) {
  .miyabi-chat-widget {
    bottom: 1.5rem;
    right: 1.5rem;
  }
  .chat-window {
    width: calc(100vw - 3rem);
    height: 440px;
    right: 0;
  }
}
</style>
