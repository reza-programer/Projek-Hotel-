<template>
  <AdminLayout title="Layanan Concierge">
    <div class="admin-chat-container card-shoji">
      
      <!-- Thread Sidebar (Left) -->
      <aside class="chat-threads-sidebar">
        <div class="sidebar-search-area">
          <h2 class="sidebar-title">Percakapan Tamu</h2>
          <div class="search-input-wrapper">
            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Cari tamu..." 
              class="search-input"
            />
          </div>
        </div>

        <div class="threads-list">
          <div v-if="filteredThreads.length === 0" class="empty-threads">
            <p>Tidak ada percakapan aktif.</p>
          </div>
          <button 
            v-for="thread in filteredThreads" 
            :key="thread.userId"
            class="thread-item"
            :class="{ active: activeUserId === thread.userId }"
            @click="selectThread(thread.userId)"
          >
            <div class="avatar-circle">
              {{ thread.userName.charAt(0).toUpperCase() }}
            </div>
            <div class="thread-info">
              <div class="thread-header-row">
                <span class="thread-name">{{ thread.userName }}</span>
                <span class="thread-time">{{ formatTime(thread.lastTimestamp) }}</span>
              </div>
              <div class="thread-body-row">
                <span class="thread-preview" :class="{ unread: thread.unreadCount > 0 }">
                  {{ thread.lastMessage }}
                </span>
                <span v-if="thread.unreadCount > 0" class="unread-badge">
                  {{ thread.unreadCount }}
                </span>
              </div>
            </div>
          </button>
        </div>
      </aside>

      <!-- Active Chat Window (Right) -->
      <main class="active-chat-window">
        <!-- Thread Selected -->
        <template v-if="activeUser">
          <header class="chat-window-header">
            <div class="header-user-info">
              <div class="avatar-circle">
                {{ activeUser.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <h3 class="header-name">{{ activeUser.name }}</h3>
                <p class="header-meta">ID Tamu: #{{ activeUser.id }} | {{ activeUser.email }}</p>
              </div>
            </div>
            <div class="header-status-indicator">
              <span class="status-dot green"></span>
              <span class="status-text">Darma Mizuki Club Member</span>
            </div>
          </header>

          <!-- Chat Stream -->
          <div class="chat-messages-area" ref="messagesAreaRef">
            <div v-for="msg in activeMessages" :key="msg.id" class="msg-row" :class="msg.senderRole">
              <div class="msg-bubble">
                <div class="msg-sender" v-if="msg.senderRole === 'admin'">{{ msg.senderName }}</div>
                <div class="msg-text">{{ msg.text }}</div>
                <div class="msg-time">{{ formatFullTime(msg.timestamp) }}</div>
              </div>
            </div>
          </div>

          <!-- Typing Footer -->
          <footer class="chat-window-footer">
            <form @submit.prevent="sendReply" class="reply-form">
              <textarea 
                v-model="replyText" 
                placeholder="Ketik tanggapan concierge untuk tamu..." 
                class="reply-textarea"
                rows="2"
                required
                @keydown.enter.prevent="handleEnter"
              ></textarea>
              <button type="submit" class="btn-vermillion reply-send-btn">
                <span>Kirim</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <line x1="22" y1="2" x2="11" y2="13"></line>
                  <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
              </button>
            </form>
          </footer>
        </template>

        <!-- No Thread Selected -->
        <template v-else>
          <div class="no-chat-selected">
            <div class="zen-card">
              <div class="zen-symbol">阿</div>
              <h2 class="zen-title">Layanan Concierge Darma Mizuki</h2>
              <p class="zen-text">Pilih salah satu percakapan tamu dari panel sebelah kiri untuk memulai pelayanan virtual Omotenashi.</p>
              <div class="stats-row">
                <div class="stat-box">
                  <span class="stat-val">{{ threads.length }}</span>
                  <span class="stat-lbl">Percakapan</span>
                </div>
                <div class="stat-box">
                  <span class="stat-val">{{ totalUnread }}</span>
                  <span class="stat-lbl">Belum Dibaca</span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </main>

    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import AdminLayout from '@/Components/Layout/AdminLayout.vue'

const searchQuery = ref('')
const activeUserId = ref(null)
const replyText = ref('')
const allMessages = ref([])
const messagesAreaRef = ref(null)

// Load messages from localStorage
const loadMessages = () => {
  if (typeof window !== 'undefined') {
    const raw = localStorage.getItem('miyabi_chat_messages')
    if (raw) {
      try {
        allMessages.value = JSON.parse(raw)
      } catch (e) {
        console.error('Failed parsing admin chat messages', e)
        allMessages.value = []
      }
    } else {
      allMessages.value = []
    }
  }
}

// Generate threads summarized from messages
const threads = computed(() => {
  const map = {}
  allMessages.value.forEach(msg => {
    const uid = msg.userId
    if (!map[uid]) {
      map[uid] = {
        userId: uid,
        userName: msg.userName,
        lastMessage: msg.text,
        lastTimestamp: msg.timestamp,
        unreadCount: 0
      }
    }
    
    // Update last message
    if (new Date(msg.timestamp) >= new Date(map[uid].lastTimestamp)) {
      map[uid].lastMessage = msg.text
      map[uid].lastTimestamp = msg.timestamp
    }

    // Update unread count
    if (msg.senderRole === 'user' && !msg.readByAdmin) {
      map[uid].unreadCount++
    }
  })

  // Sort by latest message timestamp
  return Object.values(map).sort((a, b) => new Date(b.lastTimestamp) - new Date(a.lastTimestamp))
})

const filteredThreads = computed(() => {
  if (!searchQuery.value.trim()) return threads.value
  const query = searchQuery.value.toLowerCase()
  return threads.value.filter(t => t.userName.toLowerCase().includes(query))
})

// Total unread threads count
const totalUnread = computed(() => {
  return threads.value.reduce((acc, t) => acc + (t.unreadCount > 0 ? 1 : 0), 0)
})

// Currently selected active user details
const activeUser = computed(() => {
  if (!activeUserId.value) return null
  const thread = threads.value.find(t => t.userId === activeUserId.value)
  if (!thread) return null

  // Get user details from messages or fallback
  const firstUserMsg = allMessages.value.find(m => m.userId === activeUserId.value && m.senderRole === 'user')
  return {
    id: thread.userId,
    name: thread.userName,
    email: firstUserMsg ? firstUserMsg.userName.toLowerCase().replace(/\s+/g, '') + '@email.com' : 'tamu@darmamizuki.com'
  }
})

// Active messages stream
const activeMessages = computed(() => {
  if (!activeUserId.value) return []
  return allMessages.value.filter(msg => msg.userId === activeUserId.value)
})

const selectThread = (userId) => {
  activeUserId.value = userId
  markAsRead(userId)
  nextTick(() => {
    scrollToBottom()
  })
}

const markAsRead = (userId) => {
  let updated = false
  allMessages.value = allMessages.value.map(msg => {
    if (msg.userId === userId && msg.senderRole === 'user' && !msg.readByAdmin) {
      updated = true
      return { ...msg, readByAdmin: true }
    }
    return msg
  })
  if (updated) {
    localStorage.setItem('miyabi_chat_messages', JSON.stringify(allMessages.value))
  }
}

const sendReply = () => {
  if (!replyText.value.trim() || !activeUserId.value) return

  const replyMsg = {
    id: Date.now(),
    userId: activeUserId.value,
    userName: activeUser.value.name,
    senderRole: 'admin',
    senderName: 'Ahmad Admin', // Concierge name
    text: replyText.value.trim(),
    timestamp: new Date().toISOString(),
    readByUser: false,
    readByAdmin: true
  }

  allMessages.value.push(replyMsg)
  localStorage.setItem('miyabi_chat_messages', JSON.stringify(allMessages.value))
  replyText.value = ''
  
  nextTick(() => {
    scrollToBottom()
  })
}

const handleEnter = (e) => {
  if (!e.shiftKey) {
    sendReply()
  }
}

const scrollToBottom = () => {
  if (messagesAreaRef.value) {
    messagesAreaRef.value.scrollTop = messagesAreaRef.value.scrollHeight
  }
}

const formatTime = (isoString) => {
  try {
    const date = new Date(isoString)
    const now = new Date()
    
    // If today, just show time
    if (date.toDateString() === now.toDateString()) {
      return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    }
    
    // Otherwise show date
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
  } catch (e) {
    return ''
  }
}

const formatFullTime = (isoString) => {
  try {
    const date = new Date(isoString)
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ' ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  } catch (e) {
    return ''
  }
}

// Storage event listener
const handleStorageChange = (e) => {
  if (e.key === 'miyabi_chat_messages') {
    loadMessages()
    if (activeUserId.value) {
      markAsRead(activeUserId.value)
      nextTick(() => {
        scrollToBottom()
      })
    }
  }
}

onMounted(() => {
  loadMessages()
  window.addEventListener('storage', handleStorageChange)
})

onUnmounted(() => {
  window.removeEventListener('storage', handleStorageChange)
})
</script>

<style scoped>
.admin-chat-container {
  display: flex;
  height: calc(100vh - 120px);
  background: #fff;
  border: 1px solid rgba(201, 168, 76, 0.2);
  border-radius: 4px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(26, 26, 46, 0.05);
}

/* Threads Sidebar */
.chat-threads-sidebar {
  width: 320px;
  border-right: 1px solid rgba(201, 168, 76, 0.2);
  display: flex;
  flex-direction: column;
  background: var(--color-washi-300);
  flex-shrink: 0;
}
.sidebar-search-area {
  padding: 1.25rem 1rem;
  border-bottom: 1px solid rgba(201, 168, 76, 0.2);
  background: #fff;
}
.sidebar-title {
  font-family: var(--font-display);
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--color-sumi-800);
  margin-bottom: 0.75rem;
}
.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.search-icon {
  position: absolute;
  left: 0.75rem;
  color: var(--color-sumi-500);
}
.search-input {
  width: 100%;
  border: 1px solid rgba(201, 168, 76, 0.3);
  border-radius: 4px;
  padding: 0.5rem 0.75rem 0.5rem 2.2rem;
  font-size: 0.82rem;
  outline: none;
  font-family: var(--font-sans);
  background: var(--color-washi-100);
  transition: all 0.2s;
}
.search-input:focus {
  border-color: var(--color-kin-500);
  background: #fff;
  box-shadow: 0 0 0 2px rgba(201, 168, 76, 0.1);
}

.threads-list {
  flex: 1;
  overflow-y: auto;
  padding: 0.5rem 0;
}
.threads-list::-webkit-scrollbar {
  width: 4px;
}
.threads-list::-webkit-scrollbar-thumb {
  background: rgba(201, 168, 76, 0.2);
}
.empty-threads {
  text-align: center;
  padding: 2rem 1rem;
  color: var(--color-sumi-500);
  font-size: 0.85rem;
}

.thread-item {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.9rem 1rem;
  border: none;
  background: transparent;
  cursor: pointer;
  text-align: left;
  border-bottom: 1px solid rgba(201, 168, 76, 0.1);
  transition: background 0.2s;
}
.thread-item:hover {
  background: rgba(201, 168, 76, 0.05);
}
.thread-item.active {
  background: rgba(155, 35, 53, 0.06);
  border-left: 3px solid var(--color-beni-600);
  padding-left: calc(1rem - 3px);
}

.avatar-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--color-kin-500);
  color: var(--color-sumi-900);
  font-weight: 700;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-family: var(--font-display);
}
.active-chat-window .avatar-circle {
  background: var(--color-beni-600);
  color: #fff;
}

.thread-info {
  flex: 1;
  min-width: 0;
}
.thread-header-row {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 2px;
}
.thread-name {
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--color-sumi-800);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.thread-time {
  font-size: 0.7rem;
  color: var(--color-sumi-500);
}
.thread-body-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.thread-preview {
  font-size: 0.76rem;
  color: var(--color-sumi-600);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
  margin-right: 0.5rem;
}
.thread-preview.unread {
  font-weight: 600;
  color: var(--color-sumi-900);
}
.unread-badge {
  background: var(--color-beni-600);
  color: #fff;
  font-size: 0.65rem;
  font-weight: 700;
  min-width: 18px;
  height: 18px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
}

/* Active Chat Window */
.active-chat-window {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: var(--color-washi-100);
  min-width: 0;
}

.chat-window-header {
  background: #fff;
  padding: 0.85rem 1.5rem;
  border-bottom: 1px solid rgba(201, 168, 76, 0.2);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.header-user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.header-name {
  font-family: var(--font-display);
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-sumi-800);
  line-height: 1.2;
}
.header-meta {
  font-size: 0.72rem;
  color: var(--color-sumi-500);
  margin-top: 1px;
}
.header-status-indicator {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(74, 124, 89, 0.08);
  border: 1px solid rgba(74, 124, 89, 0.2);
  padding: 0.35rem 0.75rem;
  border-radius: 4px;
}
.status-dot.green {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--color-matcha-500);
}
.status-text {
  font-size: 0.72rem;
  font-weight: 500;
  color: var(--color-matcha-700);
}

/* Messages stream */
.chat-messages-area {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  background: var(--color-washi-300);
}
.chat-messages-area::-webkit-scrollbar {
  width: 5px;
}
.chat-messages-area::-webkit-scrollbar-thumb {
  background: rgba(201, 168, 76, 0.3);
  border-radius: 2.5px;
}

.msg-row {
  display: flex;
  width: 100%;
}
.msg-row.user {
  justify-content: flex-start;
}
.msg-row.admin {
  justify-content: flex-end;
}

.msg-bubble {
  max-width: 65%;
  padding: 0.75rem 1rem;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(26, 26, 46, 0.04);
}
.user .msg-bubble {
  background: #fff;
  color: var(--color-sumi-800);
  border: 1px solid rgba(201, 168, 76, 0.35);
  border-bottom-left-radius: 0;
}
.admin .msg-bubble {
  background: var(--color-sumi-800);
  color: #fff;
  border-bottom-right-radius: 0;
}

.msg-sender {
  font-family: var(--font-display);
  font-size: 0.68rem;
  font-weight: 600;
  color: var(--color-kin-300);
  margin-bottom: 3px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.msg-text {
  font-size: 0.82rem;
  line-height: 1.5;
  word-break: break-word;
  white-space: pre-wrap;
}
.msg-time {
  font-size: 0.62rem;
  text-align: right;
  margin-top: 5px;
  opacity: 0.65;
}
.user .msg-time {
  color: var(--color-sumi-500);
}
.admin .msg-time {
  color: rgba(255, 255, 255, 0.8);
}

/* Typing area footer */
.chat-window-footer {
  padding: 1rem 1.5rem;
  background: #fff;
  border-top: 1px solid rgba(201, 168, 76, 0.2);
}
.reply-form {
  display: flex;
  gap: 1rem;
  align-items: center;
}
.reply-textarea {
  flex: 1;
  border: 1px solid rgba(201, 168, 76, 0.3);
  border-radius: 4px;
  padding: 0.6rem 0.9rem;
  font-size: 0.85rem;
  outline: none;
  resize: none;
  font-family: var(--font-sans);
  background: var(--color-washi-100);
  transition: all 0.2s;
}
.reply-textarea:focus {
  border-color: var(--color-kin-500);
  background: #fff;
  box-shadow: 0 0 0 2px rgba(201, 168, 76, 0.12);
}
.reply-send-btn {
  height: 48px;
  padding: 0 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-family: var(--font-display);
  font-size: 0.9rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  border-radius: var(--radius-zen);
  border: none;
  cursor: pointer;
}

/* No chat selected zen state */
.no-chat-selected {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: var(--color-washi-300);
}
.zen-card {
  text-align: center;
  max-width: 420px;
  padding: 3rem 2rem;
  background: #fff;
  border: 1px solid rgba(201, 168, 76, 0.2);
  box-shadow: 0 4px 20px rgba(26, 26, 46, 0.04);
}
.zen-symbol {
  font-family: var(--font-serif-jp);
  font-size: 3.5rem;
  font-weight: 300;
  color: var(--color-beni-500);
  line-height: 1;
  margin-bottom: 1.25rem;
}
.zen-title {
  font-family: var(--font-display);
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--color-sumi-800);
  margin-bottom: 0.5rem;
}
.zen-text {
  font-size: 0.8rem;
  color: var(--color-sumi-600);
  line-height: 1.6;
  margin-bottom: 2rem;
}
.stats-row {
  display: flex;
  justify-content: center;
  gap: 2.5rem;
  border-top: 1px solid rgba(201, 168, 76, 0.15);
  padding-top: 1.5rem;
}
.stat-box {
  display: flex;
  flex-direction: column;
}
.stat-val {
  font-family: var(--font-display);
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--color-sumi-800);
  line-height: 1.1;
}
.stat-lbl {
  font-size: 0.68rem;
  color: var(--color-kin-700);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-top: 3px;
}
</style>
