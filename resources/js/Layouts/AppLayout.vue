<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const scrolled = ref(false)
const mobileOpen = ref(false)

const handleScroll = () => {
  scrolled.value = window.scrollY > 40
}

onMounted(() => window.addEventListener('scroll', handleScroll))
onUnmounted(() => window.removeEventListener('scroll', handleScroll))

const navLinks = [
  { label: 'Beranda', labelJP: 'ホーム', href: '/' },
  { label: 'Kamar', labelJP: '客室', href: '/rooms' },
  { label: 'Fasilitas', labelJP: '施設', href: '/facilities' },
  { label: 'Tentang', labelJP: '概要', href: '/about' },
  { label: 'Kontak', labelJP: '連絡', href: '/contact' },
]
</script>

<template>
  <div class="min-h-screen pattern-washi flex flex-col">
    <!-- Navbar -->
    <nav class="navbar-zen" :class="{ scrolled }">
      <div class="container-zen flex items-center justify-between py-4">
        <!-- Logo -->
        <Link href="/" class="flex items-center gap-3 group no-underline">
          <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 group-hover:scale-110"
               style="background: linear-gradient(135deg, var(--color-beni-600), var(--color-sumi-800));">
            <span class="text-white font-jp text-sm font-bold">雅</span>
          </div>
          <div>
            <div class="font-display text-xl font-semibold leading-none"
                 :style="scrolled ? 'color: var(--color-sumi-800)' : 'color: white'">
              Miyabi Hotel
            </div>
            <div class="text-kanji text-xs leading-none mt-0.5"
                 :style="scrolled ? '' : 'color: rgba(255,255,255,0.6); opacity:1'">
              雅ホテル
            </div>
          </div>
        </Link>

        <!-- Desktop Nav -->
        <div class="hidden md:flex items-center gap-8">
          <Link
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            class="nav-link-zen group"
            :style="scrolled ? 'color: var(--color-sumi-800)' : 'color: rgba(255,255,255,0.9)'"
          >
            <span>{{ link.label }}</span>
            <span class="block text-center text-kanji" style="font-size:0.6rem; letter-spacing:0.15em; opacity:0.5; margin-top:1px;">
              {{ link.labelJP }}
            </span>
          </Link>
        </div>

        <!-- CTA -->
        <div class="hidden md:flex items-center gap-3">
          <Link href="/reservations" class="btn-outline-gold text-sm py-2 px-5"
                :style="scrolled ? '' : 'color: var(--color-kin-300); border-color: var(--color-kin-300)'">
            Reservasi Saya
          </Link>
          <Link href="/rooms" class="btn-vermillion text-sm py-2 px-5">
            Book Sekarang
          </Link>
        </div>

        <!-- Mobile Toggle -->
        <button class="md:hidden p-2 rounded"
                :style="scrolled ? 'color: var(--color-sumi-800)' : 'color: white'"
                @click="mobileOpen = !mobileOpen">
          <svg v-if="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileOpen"
           class="md:hidden border-t"
           style="background: rgba(253,250,245,0.98); backdrop-filter: blur(12px); border-color: rgba(201,168,76,0.2)">
        <div class="container-zen py-4 flex flex-col gap-1">
          <Link v-for="link in navLinks" :key="link.href" :href="link.href"
                class="nav-link-zen py-3 border-b flex justify-between items-center"
                style="color: var(--color-sumi-800); border-color: rgba(201,168,76,0.15)">
            <span>{{ link.label }}</span>
            <span class="text-kanji text-xs">{{ link.labelJP }}</span>
          </Link>
          <div class="flex gap-3 mt-4">
            <Link href="/reservations" class="btn-outline-gold text-sm py-2 flex-1 justify-center">Reservasi</Link>
            <Link href="/rooms" class="btn-vermillion text-sm py-2 flex-1 justify-center">Book Sekarang</Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer style="background: var(--color-sumi-800);" class="text-white">
      <div class="container-zen py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
          <!-- Brand -->
          <div class="md:col-span-1">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 rounded-full flex items-center justify-center"
                   style="background: linear-gradient(135deg, var(--color-beni-600), var(--color-sumi-600));">
                <span class="font-jp text-sm font-bold">雅</span>
              </div>
              <div>
                <div class="font-display text-lg font-semibold">Miyabi Hotel</div>
                <div class="text-xs" style="color: rgba(255,255,255,0.4); font-family: var(--font-serif-jp)">雅ホテル</div>
              </div>
            </div>
            <p class="text-sm leading-relaxed" style="color: rgba(255,255,255,0.5)">
              Pengalaman menginap berkelas dunia dengan sentuhan Omotenashi — keramahan tulus dari hati.
            </p>
            <p class="text-xs mt-3 font-jp" style="color: var(--color-kin-300); letter-spacing:0.15em;">おもてなしの心</p>
          </div>

          <!-- Links -->
          <div>
            <h4 class="font-jp text-sm font-medium mb-4" style="color: var(--color-kin-300); letter-spacing:0.1em;">Navigasi</h4>
            <ul class="space-y-2">
              <li v-for="link in navLinks" :key="link.href">
                <Link :href="link.href" class="text-sm hover:text-white transition-colors"
                      style="color: rgba(255,255,255,0.5); text-decoration:none">
                  {{ link.label }}
                </Link>
              </li>
            </ul>
          </div>

          <!-- Contact -->
          <div>
            <h4 class="font-jp text-sm font-medium mb-4" style="color: var(--color-kin-300); letter-spacing:0.1em;">Kontak</h4>
            <ul class="space-y-3 text-sm" style="color: rgba(255,255,255,0.5)">
              <li class="flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Jl. Sakura Indah No. 88,<br>Jakarta Selatan 12140</span>
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span>+62 21 8888-7777</span>
              </li>
              <li class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>hello@miyabihotel.com</span>
              </li>
            </ul>
          </div>

          <!-- Social -->
          <div>
            <h4 class="font-jp text-sm font-medium mb-4" style="color: var(--color-kin-300); letter-spacing:0.1em;">Sosial Media</h4>
            <div class="flex gap-3">
              <a v-for="s in ['IG','FB','TW']" :key="s" href="#"
                 class="w-9 h-9 rounded flex items-center justify-center text-xs font-medium transition-all hover:scale-110"
                 style="background: rgba(201,168,76,0.1); color: var(--color-kin-300); border: 1px solid rgba(201,168,76,0.2)">
                {{ s }}
              </a>
            </div>
            <div class="mt-6 p-3 rounded" style="background: rgba(155,35,53,0.15); border: 1px solid rgba(155,35,53,0.3)">
              <p class="text-xs" style="color: rgba(255,255,255,0.5)">Buka 24 Jam</p>
              <p class="font-jp text-sm mt-1" style="color: var(--color-sakura-300)">いつでもご連絡ください</p>
            </div>
          </div>
        </div>

        <div class="divider-ink mt-10" style="--tw-bg-opacity:1"></div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-3 pt-4 text-xs"
             style="color: rgba(255,255,255,0.35)">
          <span>© {{ new Date().getFullYear() }} Miyabi Hotel. Hak Cipta Dilindungi.</span>
          <span class="font-jp" style="color: var(--color-kin-500); letter-spacing:0.15em">雅ホテル ‒ おもてなし</span>
        </div>
      </div>
    </footer>
  </div>
</template>
