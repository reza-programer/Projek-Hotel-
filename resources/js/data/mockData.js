// resources/js/data/mockData.js
// Data dummy untuk development frontend Darma Mizuki

export const hotelInfo = {
  name: 'Darma Mizuki',
  nameJP: 'アルニカ 旅館',
  tagline: 'Where Omotenashi Meets Elegance',
  taglineJP: 'おもてなしが優雅さに出会う場所',
  description: 'Darma Mizuki menawarkan pengalaman menginap kelas dunia yang terinspirasi dari filosofi keramahan Jepang — Omotenashi. Setiap sudut hotel dirancang untuk memberikan ketenangan, keindahan, dan pelayanan yang tulus dari hati.',
  address: 'Jl. Sakura Indah No. 88, Jakarta Selatan, Indonesia 12140',
  phone: '+62 21 8888-7777',
  email: 'hello@darmamizuki.com',
  established: 2018,
  totalRooms: 84,
  stars: 5,
  socials: {
    instagram: '@arunikaryoka',
    facebook: 'arunikaryoka',
    twitter: '@arunikaryoka',
  },
  stats: [
    { label: 'Kamar Premium', labelJP: 'プレミアムルーム', value: '84' },
    { label: 'Tamu Puas', labelJP: '満足したゲスト', value: '12K+' },
    { label: 'Tahun Berdiri', labelJP: '設立年数', value: '6+' },
  ]
}

export const rooms = [
  {
    id: 1,
    name: 'Sakura Standard',
    nameJP: '桜スタンダード',
    type: 'Standard',
    price: 850000,
    capacity: 2,
    size: 28,
    floor: '3-5',
    description: 'Kamar nyaman dengan nuansa sakura yang elegan. Dilengkapi dengan pemandangan taman Jepang yang menenangkan.',
    longDescription: 'Sakura Standard hadir sebagai pengalaman menginap sempurna bagi Anda yang mendambakan ketenangan. Interior kamar dirancang dengan motif bunga sakura yang lembut, paduan warna krem dan merah muda yang memanjakan mata. Nikmati istirahat berkualitas di atas futon premium dengan selimut sutra terbaik.',
    amenities: ['Wi-Fi Gratis', 'AC', 'TV 43"', 'Minibar', 'Safe Box', 'Shower & Bathtub', 'Yukata', 'Teh Matcha Gratis'],
    images: [
      '/images/rooms/sakura-1.png',
      '/images/rooms/generic-bathroom.png',
      '/images/rooms/generic-balcony.png',
    ],
    thumbnail: '/images/rooms/sakura-1.png',
    isAvailable: true,
    isPromoActive: true,
    discountPercentage: 20,
    rating: 4.8,
    reviewCount: 124,
    features: ['Garden View', 'Futon Option'],
    colorTheme: 'sakura',
  },
  {
    id: 2,
    name: 'Bamboo Deluxe',
    nameJP: '竹デラックス',
    type: 'Deluxe',
    price: 1350000,
    capacity: 2,
    size: 38,
    floor: '6-9',
    description: 'Kamar deluxe terinspirasi dari ketenangan hutan bambu. Menawarkan pemandangan kota yang menakjubkan.',
    longDescription: 'Bamboo Deluxe adalah perpaduan sempurna antara modernitas dan tradisi Jepang. Motif bambu vertikal menghiasi dinding kamar, menciptakan suasana tenang dan sejuk. Dilengkapi dengan sudut baca (reading nook) bergaya Jepang dan koleksi buku pilihan tentang budaya dan filosofi Timur.',
    amenities: ['Wi-Fi Gratis', 'AC', 'TV 55"', 'Minibar Premium', 'Safe Box', 'Bathtub Mewah', 'Yukata & Slippers', 'Teh Matcha & Sake', 'Reading Nook', 'Coffee Maker'],
    images: [
      '/images/rooms/bamboo-1.png',
      '/images/rooms/generic-bathroom.png',
      '/images/rooms/generic-balcony.png',
    ],
    thumbnail: '/images/rooms/bamboo-1.png',
    isAvailable: true,
    rating: 4.9,
    reviewCount: 89,
    features: ['City View', 'Reading Nook', 'Premium Minibar'],
    colorTheme: 'matcha',
  },
  {
    id: 3,
    name: 'Zen Garden Suite',
    nameJP: '禅ガーデンスイート',
    type: 'Suite',
    price: 2800000,
    capacity: 4,
    size: 68,
    floor: '10-14',
    description: 'Suite mewah dengan taman zen pribadi. Pengalaman menginap yang tak terlupakan dengan layanan butler 24 jam.',
    longDescription: 'Zen Garden Suite adalah puncak kemewahan Omotenashi. Suite dua lantai ini memiliki taman zen miniatur pribadi yang dapat Anda rawat selama menginap. Ruang makan Jepang terpisah, kamar mandi dengan ofuro (bathtub tradisional Jepang), dan akses ke spa privat tersedia untuk kenyamanan Anda.',
    amenities: ['Wi-Fi Gratis', 'AC Multi-zone', 'TV 65" + Proyektor', 'Bar Sake Premium', 'Butler 24 Jam', 'Ofuro (Bathtub Jepang)', 'Yukata Sutra', 'Taman Zen Privat', 'Ruang Makan Privat', 'Spa Akses', 'Limousine Transfer'],
    images: [
      '/images/rooms/zen-1.png',
      '/images/rooms/generic-bathroom.png',
      '/images/rooms/generic-balcony.png',
    ],
    thumbnail: '/images/rooms/zen-1.png',
    isAvailable: false,
    rating: 5.0,
    reviewCount: 47,
    features: ['Private Garden', 'Butler Service', 'Ofuro Bathtub', 'Spa Access'],
    colorTheme: 'gold',
  },
  {
    id: 4,
    name: 'Koi Pond Junior Suite',
    nameJP: '鯉の池ジュニアスイート',
    type: 'Junior Suite',
    price: 1900000,
    capacity: 3,
    size: 52,
    floor: '7-10',
    description: 'Junior Suite dengan pemandangan langsung ke kolam koi legendaris hotel.',
    longDescription: 'Menghadap langsung ke kolam koi legendaris milik hotel, Koi Pond Junior Suite menawarkan pemandangan yang meditatif. Desain interior mengambil inspirasi dari motif koi — dinamis namun harmonis. Ruang tamu terpisah dengan kursi rotan premium dan meja kerja bergaya Jepang.',
    amenities: ['Wi-Fi Gratis', 'AC', 'TV 55"', 'Minibar', 'Safe Box', 'Bathtub Oval', 'Yukata', 'Teh Matcha', 'Ruang Tamu Terpisah', 'Koi View Balcony'],
    images: [
      '/images/rooms/koi-1.png',
      '/images/rooms/generic-bathroom.png',
      '/images/rooms/generic-balcony.png',
    ],
    thumbnail: '/images/rooms/koi-1.png',
    isAvailable: true,
    rating: 4.7,
    reviewCount: 63,
    features: ['Koi View', 'Balcony', 'Separate Living Room'],
    colorTheme: 'ai',
  },
  {
    id: 5,
    name: 'Mount Fuji Presidential',
    nameJP: '富士山プレジデンシャル',
    type: 'Presidential',
    price: 6500000,
    capacity: 6,
    size: 120,
    floor: '15 (Top Floor)',
    description: 'Presidential suite eksklusif di lantai tertinggi. Panorama 360° dan layanan tertinggi ala istana Jepang.',
    longDescription: 'Persembahan tertinggi Darma Mizuki. Presidential Suite hadir di lantai paling atas dengan panorama 360 derajat yang memukau. Tiga kamar tidur, ruang resepsi privat, dapur lengkap, dan jakuzi rooftop menghadap langit malam. Tim butler pribadi siap 24 jam memenuhi setiap kebutuhan Anda.',
    amenities: ['Wi-Fi Ultra-Speed', 'AC Cerdas', 'Home Theater 8K', 'Wine Cellar', 'Butler Tim Pribadi', 'Jakuzi Rooftop', 'Yukata Sutra Premium', 'Chef Privat On-Request', 'Helipad Access', 'Limousine Fleet', 'Spa Suite Privat', 'Airport Transfer Premium'],
    images: [
      '/images/rooms/fuji-1.png',
      '/images/rooms/generic-bathroom.png',
      '/images/rooms/generic-balcony.png',
    ],
    thumbnail: '/images/rooms/fuji-1.png',
    isAvailable: true,
    rating: 5.0,
    reviewCount: 12,
    features: ['360° Panorama', 'Rooftop Jacuzzi', 'Private Chef', 'Helipad Access'],
    colorTheme: 'beni',
  },
  {
    id: 6,
    name: 'Autumn Maples Superior',
    nameJP: '紅葉スーペリア',
    type: 'Superior',
    price: 1050000,
    capacity: 2,
    size: 34,
    floor: '3-6',
    description: 'Kamar superior dengan tema momiji (daun maple musim gugur). Nuansa hangat dan romantis.',
    longDescription: 'Autumn Maples Superior menghadirkan kehangatan musim gugur Jepang. Palet warna merah, oranye, dan emas dari daun maple menghiasi setiap sudut kamar. Cocok untuk pasangan yang mencari suasana romantis dan intim.',
    amenities: ['Wi-Fi Gratis', 'AC', 'TV 49"', 'Minibar', 'Safe Box', 'Shower Premium', 'Yukata Couple Set', 'Teh & Wagashi'],
    images: [
      '/images/rooms/maple-1.png',
      '/images/rooms/generic-bathroom.png',
      '/images/rooms/generic-balcony.png',
    ],
    thumbnail: '/images/rooms/maple-1.png',
    isAvailable: true,
    rating: 4.6,
    reviewCount: 98,
    features: ['Romantic Setting', 'Couple Package'],
    colorTheme: 'beni',
  }
]

export const facilities = [
  { id: 1, name: 'Onsen & Spa', nameJP: '温泉・スパ', icon: 'onsen', description: 'Pemandian air panas alami gaya Jepang', isActive: true, category: 'Wellness' },
  { id: 2, name: 'Kaiseki Restaurant', nameJP: '懐石レストラン', icon: 'dining', description: 'Restoran fine dining masakan Jepang multi-course', isActive: true, category: 'Dining' },
  { id: 3, name: 'Zen Garden', nameJP: '禅の庭', icon: 'garden', description: 'Taman batu zen untuk meditasi dan relaksasi', isActive: true, category: 'Leisure' },
  { id: 4, name: 'Tea Ceremony Room', nameJP: '茶室', icon: 'tea', description: 'Ruang upacara teh tradisional Jepang', isActive: true, category: 'Culture' },
  { id: 5, name: 'Koi Pond Garden', nameJP: '鯉の池', icon: 'koi', description: 'Taman kolam koi yang menenangkan', isActive: true, category: 'Leisure' },
  { id: 6, name: 'Ikebana Studio', nameJP: '生け花スタジオ', icon: 'ikebana', description: 'Studio seni merangkai bunga Jepang', isActive: true, category: 'Culture' },
  { id: 7, name: 'Rooftop Jakuzi', nameJP: 'ルーフトップジャグジー', icon: 'jacuzzi', description: 'Kolam jakuzi privat di rooftop dengan pemandangan kota', isActive: true, category: 'Wellness' },
  { id: 8, name: 'Fitness Center', nameJP: 'フィットネスセンター', icon: 'fitness', description: 'Pusat kebugaran modern 24 jam', isActive: true, category: 'Wellness' },
  { id: 9, name: 'Business Lounge', nameJP: 'ビジネスラウンジ', icon: 'business', description: 'Ruang kerja premium dengan fasilitas lengkap', isActive: true, category: 'Business' },
  { id: 10, name: 'Parking Area', nameJP: '駐車場', icon: 'parking', description: 'Area parkir luas dengan valet service', isActive: true, category: 'Service' },
  { id: 11, name: 'Calligraphy Class', nameJP: '書道クラス', icon: 'calligraphy', description: 'Kelas kaligrafi Jepang untuk tamu', isActive: false, category: 'Culture' },
  { id: 12, name: 'Sake Bar', nameJP: '酒バー', icon: 'sake', description: 'Bar sake premium dengan koleksi dari seluruh Jepang', isActive: true, category: 'Dining' },
]

export const testimonials = [
  {
    id: 1,
    name: 'Hiroshi Tanaka',
    origin: 'Tokyo, Japan',
    avatar: null,
    rating: 5,
    comment: 'Sungguh luar biasa! Pelayanan di sini benar-benar mencerminkan semangat Omotenashi yang sesungguhnya. Staf begitu perhatian hingga detail terkecil. Saya merasa seperti sedang berada di Kyoto.',
    date: '2025-03-15',
    stayType: 'Zen Garden Suite',
  },
  {
    id: 2,
    name: 'Sarah Mitchell',
    origin: 'Melbourne, Australia',
    avatar: null,
    rating: 5,
    comment: 'The onsen experience was absolutely transcendent. The bamboo room had such a calming energy, and the kaiseki dinner was a culinary journey I will never forget. This is not just a hotel — it\'s an experience.',
    date: '2025-04-02',
    stayType: 'Bamboo Deluxe',
  },
  {
    id: 3,
    name: 'Ahmad Faruqi',
    origin: 'Jakarta, Indonesia',
    avatar: null,
    rating: 5,
    comment: 'Pertama kalinya saya merasakan penginapan yang benar-benar memperhatikan tamu sampai detail sekecil lipatan handuk berbentuk burung origami. Darma Mizuki adalah definisi kemewahan yang sesungguhnya.',
    date: '2025-04-18',
    stayType: 'Sakura Standard',
  },
  {
    id: 4,
    name: 'Li Wei',
    origin: 'Shanghai, China',
    avatar: null,
    rating: 5,
    comment: '服务无可挑剔，完全体现了日本待客之道。温泉浴池让我身心完全放松，早餐的抹茶仪式更是锦上添花。强烈推荐！',
    date: '2025-05-05',
    stayType: 'Koi Pond Junior Suite',
  },
  {
    id: 5,
    name: 'Isabella Rossi',
    origin: 'Milan, Italy',
    avatar: null,
    rating: 5,
    comment: 'Un\'esperienza indimenticabile. L\'atmosfera giapponese è autentica, il personale è straordinariamente cordiale e i dettagli sono curati in modo impeccabile. Il giardino Zen è un capolavoro.',
    date: '2025-05-20',
    stayType: 'Zen Garden Suite',
  },
]

export const reservations = [
  { id: 'MYB-2025-001', userId: 1, roomId: 2, roomName: 'Bamboo Deluxe', checkIn: '2025-06-10', checkOut: '2025-06-13', nights: 3, guests: 2, totalPrice: 4050000, status: 'confirmed', paymentStatus: 'paid', bookingDate: '2025-05-28' },
  { id: 'MYB-2025-002', userId: 1, roomId: 1, roomName: 'Sakura Standard', checkIn: '2025-07-01', checkOut: '2025-07-03', nights: 2, guests: 2, totalPrice: 1700000, status: 'pending', paymentStatus: 'pending', bookingDate: '2025-06-01' },
  { id: 'MYB-2024-089', userId: 1, roomId: 3, roomName: 'Zen Garden Suite', checkIn: '2024-12-24', checkOut: '2024-12-27', nights: 3, guests: 2, totalPrice: 8400000, status: 'completed', paymentStatus: 'paid', bookingDate: '2024-12-01' },
  { id: 'MYB-2024-045', userId: 1, roomId: 1, roomName: 'Sakura Standard', checkIn: '2024-09-15', checkOut: '2024-09-16', nights: 1, guests: 1, totalPrice: 850000, status: 'cancelled', paymentStatus: 'refunded', bookingDate: '2024-09-10' },
]

export const adminStats = {
  today: {
    newReservations: 8,
    checkIns: 12,
    checkOuts: 9,
    revenue: 28500000,
  },
  monthly: {
    totalReservations: 186,
    totalRevenue: 542000000,
    occupancyRate: 78,
    avgRating: 4.9,
    growthReservations: 12.5,
    growthRevenue: 18.3,
  },
  roomsAvailable: 24,
  activeGuests: 67,
}

export const revenueData = {
  monthly: [
    { month: 'Jan', revenue: 385000000, reservations: 142 },
    { month: 'Feb', revenue: 412000000, reservations: 158 },
    { month: 'Mar', revenue: 398000000, reservations: 149 },
    { month: 'Apr', revenue: 445000000, reservations: 167 },
    { month: 'Mei', revenue: 502000000, reservations: 178 },
    { month: 'Jun', revenue: 542000000, reservations: 186 },
  ]
}

export const users = [
  { id: 1, name: 'Budi Santoso', email: 'budi@email.com', role: 'user', status: 'active', joined: '2024-01-15', reservations: 7 },
  { id: 2, name: 'Siti Rahayu', email: 'siti@email.com', role: 'user', status: 'active', joined: '2024-02-20', reservations: 3 },
  { id: 3, name: 'Ahmad Admin', email: 'admin@darmamizuki.com', role: 'admin', status: 'active', joined: '2023-12-01', reservations: 0 },
  { id: 4, name: 'Dewi Lestari', email: 'dewi@email.com', role: 'user', status: 'inactive', joined: '2024-03-08', reservations: 1 },
  { id: 5, name: 'Michael Chen', email: 'michael@email.com', role: 'user', status: 'active', joined: '2024-04-12', reservations: 12 },
  { id: 6, name: 'Yuki Tanaka', email: 'yuki@email.com', role: 'user', status: 'active', joined: '2024-05-01', reservations: 5 },
]

export const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount)
}

export const formatDate = (dateStr) => {
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  }).format(new Date(dateStr))
}

export const statusConfig = {
  pending:   { label: 'Menunggu',   class: 'badge-pending',   icon: 'pending' },
  confirmed: { label: 'Dikonfirmasi', class: 'badge-confirmed', icon: 'confirmed' },
  completed: { label: 'Selesai',    class: 'badge-completed', icon: 'completed' },
  cancelled: { label: 'Dibatalkan', class: 'badge-cancelled', icon: 'cancelled' },
  refunded:  { label: 'Dikembalikan', class: 'badge-completed', icon: 'refunded' },
  paid:      { label: 'Lunas',      class: 'badge-confirmed', icon: 'paid' },
  verified:  { label: 'Terverifikasi', class: 'badge-verified', icon: 'verified' },
  rejected:  { label: 'Ditolak',   class: 'badge-rejected',  icon: 'rejected' },
}
