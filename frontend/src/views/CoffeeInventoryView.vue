<template>
  <div class="inventory-layout">
    <!-- Top Navigation Bar -->
    <header class="top-navbar">
      <div class="nav-brand">
        <div class="nav-logo">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
            <line x1="6" y1="1" x2="6" y2="4"></line>
            <line x1="10" y1="1" x2="10" y2="4"></line>
            <line x1="14" y1="1" x2="14" y2="4"></line>
          </svg>
        </div>
        <div>
          <h1 class="nav-title">Katalog Kopi Nusantara</h1>
          <span class="nav-badge">Roastery Inventory & Specialty Management</span>
        </div>
      </div>

      <div class="nav-user">
        <div class="user-info">
          <span class="user-name">{{ user?.name || 'Roaster Admin' }}</span>
          <span class="user-role">NIM: 24/536182/SV/24402 | PLBB</span>
        </div>
        <button @click="handleLogout" class="btn-logout" title="Keluar dari Aplikasi">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Keluar</span>
        </button>
      </div>
    </header>

    <main class="main-content">
      <!-- Notification Banner -->
      <transition name="fade">
        <div v-if="toast.message" :class="['toast-banner', toast.type]">
          <span>{{ toast.message }}</span>
          <button @click="toast.message = ''" class="toast-close">&times;</button>
        </div>
      </transition>

      <!-- Stats Overview Cards -->
      <section class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
              <line x1="3" y1="6" x2="21" y2="6"></line>
              <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
          </div>
          <div>
            <div class="stat-label">Total Varian Kopi</div>
            <div class="stat-value">{{ coffees.length }}</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-emerald">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
              <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
          </div>
          <div>
            <div class="stat-label">Total Stok Fisik</div>
            <div class="stat-value">{{ totalStock }} <span class="stat-unit">Bungkus</span></div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="1" x2="12" y2="23"></line>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </div>
          <div>
            <div class="stat-label">Rata-rata Harga</div>
            <div class="stat-value">{{ formatCurrency(averagePrice) }}</div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon bg-purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </div>
          <div>
            <div class="stat-label">Stok Kritis (&lt; 25)</div>
            <div class="stat-value text-red">{{ lowStockCount }} <span class="stat-unit">Varian</span></div>
          </div>
        </div>
      </section>

      <!-- Action & Filter Bar -->
      <section class="action-bar">
        <div class="filter-group">
          <div class="search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="search-icon">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama, SKU, daerah asal, flavor..."
            />
          </div>

          <select v-model="selectedRoast" class="filter-select">
            <option value="">Semua Roast Level</option>
            <option value="Light">Light Roast</option>
            <option value="Medium">Medium Roast</option>
            <option value="Medium-Dark">Medium-Dark</option>
            <option value="Dark">Dark Roast</option>
          </select>
        </div>

        <button @click="openCreateModal" class="btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          <span>Tambah Biji Kopi</span>
        </button>
      </section>

      <!-- Coffee Table -->
      <section class="table-container">
        <div v-if="loading" class="table-loading">
          <span class="spinner-dark"></span>
          <span>Memuat katalog kopi nusantara...</span>
        </div>

        <div v-else-if="filteredCoffees.length === 0" class="table-empty">
          <p>Belum ada produk kopi yang sesuai dengan pencarian atau filter.</p>
        </div>

        <table v-else class="coffee-table">
          <thead>
            <tr>
              <th>SKU</th>
              <th>Nama Kopi & Profil Rasa</th>
              <th>Asal & Proses</th>
              <th>Roast Level</th>
              <th>Harga</th>
              <th>Stok</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="coffee in filteredCoffees" :key="coffee.id">
              <td>
                <span class="sku-badge">{{ coffee.sku }}</span>
              </td>
              <td>
                <div class="coffee-name">{{ coffee.name }}</div>
                <div class="flavor-notes" v-if="coffee.flavor_notes">
                  <span class="note-pill">{{ coffee.flavor_notes }}</span>
                </div>
              </td>
              <td>
                <div class="origin-text">{{ coffee.origin }}</div>
                <div class="process-text">{{ coffee.process }}</div>
              </td>
              <td>
                <span :class="['roast-pill', getRoastClass(coffee.roast_level)]">
                  {{ coffee.roast_level }}
                </span>
              </td>
              <td class="price-cell">
                {{ formatCurrency(coffee.price) }}
              </td>
              <td>
                <span :class="['stock-pill', coffee.stock < 25 ? 'stock-low' : 'stock-ok']">
                  {{ coffee.stock }} pcs
                </span>
              </td>
              <td class="text-right">
                <div class="action-buttons">
                  <button @click="openEditModal(coffee)" class="btn-action edit" title="Edit Kopi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                  </button>
                  <button @click="openDeleteModal(coffee)" class="btn-action delete" title="Hapus Kopi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>

    <!-- Create / Edit Modal Dialog -->
    <div v-if="isModalOpen" class="modal-backdrop">
      <div class="modal-card">
        <div class="modal-header">
          <h3>{{ isEditing ? 'Edit Varian Kopi' : 'Tambah Varian Kopi Baru' }}</h3>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>

        <div v-if="validationErrors.length" class="modal-errors">
          <ul>
            <li v-for="(err, index) in validationErrors" :key="index">{{ err }}</li>
          </ul>
        </div>

        <form @submit.prevent="saveCoffee" class="modal-form">
          <div class="form-row">
            <div class="form-group flex-2">
              <label>Nama Produk Kopi *</label>
              <input v-model="form.name" type="text" placeholder="Contoh: Gayo Anaerobic Natural" required />
            </div>
            <div class="form-group flex-1">
              <label>SKU (Kode Unik) *</label>
              <input v-model="form.sku" type="text" placeholder="Contoh: KOP-GYO-001" required />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group flex-1">
              <label>Asal Daerah *</label>
              <input v-model="form.origin" type="text" placeholder="Contoh: Takengon, Aceh Tengah" required />
            </div>
            <div class="form-group flex-1">
              <label>Metode Pasca Panen *</label>
              <input v-model="form.process" type="text" placeholder="Contoh: Natural, Washed, Honey" required />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group flex-1">
              <label>Tingkat Sangrai (Roast Level) *</label>
              <select v-model="form.roast_level" required>
                <option value="Light">Light</option>
                <option value="Medium">Medium</option>
                <option value="Medium-Dark">Medium-Dark</option>
                <option value="Dark">Dark</option>
              </select>
            </div>
            <div class="form-group flex-1">
              <label>Harga (IDR) *</label>
              <input v-model.number="form.price" type="number" min="0" placeholder="Contoh: 125000" required />
            </div>
            <div class="form-group flex-1">
              <label>Jumlah Stok (Pcs) *</label>
              <input v-model.number="form.stock" type="number" min="0" placeholder="Contoh: 30" required />
            </div>
          </div>

          <div class="form-group">
            <label>Flavor Notes (Profil Rasa)</label>
            <input v-model="form.flavor_notes" type="text" placeholder="Contoh: Jasmine, Peach, Dark Chocolate, Honey" />
          </div>

          <div class="modal-footer">
            <button type="button" @click="closeModal" class="btn-secondary">Batal</button>
            <button type="submit" class="btn-primary" :disabled="formSubmitting">
              <span v-if="formSubmitting" class="spinner"></span>
              <span v-else>{{ isEditing ? 'Simpan Perubahan' : 'Tambah ke Katalog' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="isDeleteModalOpen" class="modal-backdrop">
      <div class="modal-card delete-card">
        <div class="delete-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
        </div>
        <h3>Hapus Biji Kopi?</h3>
        <p>Apakah Anda yakin ingin menghapus <strong>{{ coffeeToDelete?.name }}</strong> (SKU: {{ coffeeToDelete?.sku }}) dari katalog inventory? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-footer delete-footer">
          <button @click="isDeleteModalOpen = false" class="btn-secondary">Batal</button>
          <button @click="confirmDelete" class="btn-danger" :disabled="deleteSubmitting">
            <span v-if="deleteSubmitting" class="spinner"></span>
            <span v-else>Hapus Sekarang</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';

const router = useRouter();

// State
const coffees = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const selectedRoast = ref('');
const user = ref(null);

const toast = ref({ message: '', type: 'success' });

// Modal states
const isModalOpen = ref(false);
const isEditing = ref(false);
const formSubmitting = ref(false);
const validationErrors = ref([]);
const form = ref({
  id: null,
  name: '',
  sku: '',
  origin: '',
  process: '',
  roast_level: 'Light',
  price: 100000,
  stock: 20,
  flavor_notes: '',
});

// Delete Modal states
const isDeleteModalOpen = ref(false);
const coffeeToDelete = ref(null);
const deleteSubmitting = ref(false);

// Formatters & Computeds
const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(val || 0);
};

const totalStock = computed(() => coffees.value.reduce((acc, c) => acc + Number(c.stock || 0), 0));
const averagePrice = computed(() => {
  if (!coffees.value.length) return 0;
  return Math.round(coffees.value.reduce((acc, c) => acc + Number(c.price || 0), 0) / coffees.value.length);
});
const lowStockCount = computed(() => coffees.value.filter((c) => Number(c.stock) < 25).length);

const filteredCoffees = computed(() => {
  return coffees.value.filter((c) => {
    const matchSearch =
      !searchQuery.value ||
      c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      c.sku.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      c.origin.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (c.flavor_notes && c.flavor_notes.toLowerCase().includes(searchQuery.value.toLowerCase()));

    const matchRoast = !selectedRoast.value || c.roast_level === selectedRoast.value;

    return matchSearch && matchRoast;
  });
});

const getRoastClass = (roast) => {
  switch (roast) {
    case 'Light':
      return 'roast-light';
    case 'Medium':
      return 'roast-medium';
    case 'Medium-Dark':
      return 'roast-medium-dark';
    case 'Dark':
      return 'roast-dark';
    default:
      return '';
  }
};

const showToast = (message, type = 'success') => {
  toast.value = { message, type };
  setTimeout(() => {
    if (toast.value.message === message) {
      toast.value.message = '';
    }
  }, 4000);
};

// API Actions
const fetchCoffees = async () => {
  loading.value = true;
  try {
    const res = await api.get('/coffees');
    coffees.value = res.data.data || [];
  } catch (err) {
    showToast('Gagal memuat katalog kopi: ' + (err.response?.data?.message || err.message), 'error');
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  validationErrors.value = [];
  form.value = {
    id: null,
    name: '',
    sku: `KOP-${Math.floor(100 + Math.random() * 900)}`,
    origin: '',
    process: 'Natural',
    roast_level: 'Medium',
    price: 120000,
    stock: 30,
    flavor_notes: '',
  };
  isModalOpen.value = true;
};

const openEditModal = (coffee) => {
  isEditing.value = true;
  validationErrors.value = [];
  form.value = { ...coffee };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const saveCoffee = async () => {
  formSubmitting.value = true;
  validationErrors.value = [];

  try {
    if (isEditing.value) {
      const res = await api.put(`/coffees/${form.value.id}`, form.value);
      showToast(res.data.message || 'Kopi berhasil diperbarui!');
    } else {
      const res = await api.post('/coffees', form.value);
      showToast(res.data.message || 'Kopi baru berhasil ditambahkan!');
    }
    closeModal();
    await fetchCoffees();
  } catch (err) {
    if (err.response && err.response.status === 422) {
      const errors = err.response.data.errors || {};
      validationErrors.value = Object.values(errors).flat();
      showToast('Gagal validasi: Periksa input form Anda.', 'error');
    } else {
      showToast('Terjadi kesalahan sistem.', 'error');
    }
  } finally {
    formSubmitting.value = false;
  }
};

const openDeleteModal = (coffee) => {
  coffeeToDelete.value = coffee;
  isDeleteModalOpen.value = true;
};

const confirmDelete = async () => {
  if (!coffeeToDelete.value) return;
  deleteSubmitting.value = true;

  try {
    const res = await api.delete(`/coffees/${coffeeToDelete.value.id}`);
    showToast(res.data.message || 'Kopi berhasil dihapus!');
    isDeleteModalOpen.value = false;
    await fetchCoffees();
  } catch (err) {
    showToast('Gagal menghapus produk.', 'error');
  } finally {
    deleteSubmitting.value = false;
  }
};

const handleLogout = async () => {
  try {
    await api.post('/logout');
  } catch (err) {
    console.error('Logout error:', err);
  } finally {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push({ name: 'Login' });
  }
};

onMounted(() => {
  const cachedUser = localStorage.getItem('user');
  if (cachedUser) {
    try {
      user.value = JSON.parse(cachedUser);
    } catch (e) {}
  }
  fetchCoffees();
});
</script>

<style scoped>
.inventory-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Top Navbar */
.top-navbar {
  background: #231709;
  color: #fff;
  padding: 16px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #a65820;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 14px;
}

.nav-logo {
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, #a65820 0%, #d47833 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-logo svg {
  width: 24px;
  height: 24px;
  stroke: #ffffff;
}

.nav-title {
  font-size: 18px;
  font-weight: 800;
  letter-spacing: -0.3px;
  color: #f7ede2;
}

.nav-badge {
  font-size: 11px;
  color: #d1b8a5;
  display: block;
}

.nav-user {
  display: flex;
  align-items: center;
  gap: 20px;
}

.user-info {
  text-align: right;
}

.user-name {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #f4ece4;
}

.user-role {
  display: block;
  font-size: 11px;
  color: #ba9f8b;
}

.btn-logout {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #f3dfcf;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-logout:hover {
  background: #b91c1c;
  border-color: #b91c1c;
  color: #fff;
}

.btn-logout svg {
  width: 15px;
  height: 15px;
}

/* Main Content */
.main-content {
  flex: 1;
  max-width: 1280px;
  width: 100%;
  margin: 0 auto;
  padding: 28px 24px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Toast */
.toast-banner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
}

.toast-banner.success {
  background: #dcfce7;
  color: #166534;
  border: 1px solid #86efac;
}

.toast-banner.error {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

.toast-close {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  color: inherit;
}

/* Stats */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.stat-card {
  background: #ffffff;
  padding: 20px;
  border-radius: 14px;
  border: 1px solid #e7ded4;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon svg {
  width: 24px;
  height: 24px;
}

.bg-amber { background: #fef3c7; color: #b45309; }
.bg-emerald { background: #d1fae5; color: #047857; }
.bg-blue { background: #e0f2fe; color: #0369a1; }
.bg-purple { background: #f3e8ff; color: #7e22ce; }

.stat-label {
  font-size: 12px;
  font-weight: 600;
  color: #7b695b;
}

.stat-value {
  font-size: 20px;
  font-weight: 800;
  color: #231709;
  margin-top: 2px;
}

.stat-unit {
  font-size: 12px;
  font-weight: 500;
  color: #8c7868;
}

.text-red {
  color: #b91c1c;
}

/* Action Bar */
.action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  gap: 12px;
  flex: 1;
  max-width: 650px;
}

.search-box {
  position: relative;
  flex: 1;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 18px;
  height: 18px;
  stroke: #9c8a7b;
}

.search-box input {
  width: 100%;
  padding: 10px 14px 10px 38px;
  border-radius: 10px;
  border: 1px solid #dcd1c5;
  font-size: 13px;
  background: #ffffff;
  outline: none;
}

.search-box input:focus {
  border-color: #a65820;
}

.filter-select {
  padding: 10px 14px;
  border-radius: 10px;
  border: 1px solid #dcd1c5;
  font-size: 13px;
  background: #ffffff;
  outline: none;
  color: #3b2e23;
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #a65820 0%, #823e10 100%);
  color: #ffffff;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary:hover:not(:disabled) {
  opacity: 0.95;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(130, 62, 16, 0.25);
}

.btn-primary svg {
  width: 18px;
  height: 18px;
}

/* Table */
.table-container {
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e7ded4;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

.table-loading, .table-empty {
  padding: 48px;
  text-align: center;
  color: #7b695b;
  font-size: 14px;
}

.coffee-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.coffee-table th {
  background: #faf7f2;
  padding: 14px 18px;
  font-size: 12px;
  font-weight: 700;
  color: #5d4c3f;
  border-bottom: 1px solid #ebdcd0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.coffee-table td {
  padding: 14px 18px;
  font-size: 13px;
  border-bottom: 1px solid #f2e9e1;
  vertical-align: middle;
}

.coffee-table tbody tr:hover {
  background-color: #fdfaf6;
}

.sku-badge {
  font-family: monospace;
  background: #ede3d7;
  color: #4b3624;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
}

.coffee-name {
  font-weight: 700;
  color: #231709;
}

.flavor-notes {
  margin-top: 4px;
}

.note-pill {
  font-size: 11px;
  color: #7a6350;
  background: #f7eee4;
  padding: 2px 8px;
  border-radius: 4px;
  display: inline-block;
}

.origin-text {
  font-weight: 600;
  color: #3b2e23;
}

.process-text {
  font-size: 11px;
  color: #8c7868;
}

.roast-pill {
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-block;
}

.roast-light { background: #fef3c7; color: #92400e; }
.roast-medium { background: #fed7aa; color: #9a3412; }
.roast-medium-dark { background: #e2c2a4; color: #632d0c; }
.roast-dark { background: #3e2716; color: #fdf3eb; }

.price-cell {
  font-weight: 700;
  color: #1e1309;
}

.stock-pill {
  font-size: 12px;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 6px;
}

.stock-ok { background: #dcfce7; color: #166534; }
.stock-low { background: #fee2e2; color: #991b1b; }

.text-right {
  text-align: right;
}

.action-buttons {
  display: inline-flex;
  gap: 8px;
}

.btn-action {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid #d4c4b5;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-action svg {
  width: 15px;
  height: 15px;
}

.btn-action.edit:hover {
  background: #fdf2e9;
  border-color: #a65820;
  color: #a65820;
}

.btn-action.delete:hover {
  background: #fee2e2;
  border-color: #dc2626;
  color: #dc2626;
}

/* Modals */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(18, 12, 6, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  z-index: 100;
}

.modal-card {
  background: #ffffff;
  border-radius: 18px;
  max-width: 600px;
  width: 100%;
  padding: 28px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  font-size: 18px;
  font-weight: 800;
  color: #231709;
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  color: #9c8a7b;
  cursor: pointer;
}

.modal-errors {
  background: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
  padding: 12px 16px;
  border-radius: 8px;
  font-size: 12px;
}

.modal-errors ul {
  padding-left: 18px;
}

.modal-form {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.form-row {
  display: flex;
  gap: 14px;
}

.flex-1 { flex: 1; }
.flex-2 { flex: 2; }

.form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.form-group label {
  font-size: 12px;
  font-weight: 700;
  color: #4b3624;
}

.form-group input, .form-group select {
  padding: 9px 12px;
  border-radius: 8px;
  border: 1px solid #d4c4b5;
  font-size: 13px;
  outline: none;
}

.form-group input:focus, .form-group select:focus {
  border-color: #a65820;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
}

.btn-secondary {
  background: #f1eae2;
  border: 1px solid #cbbaa8;
  color: #4b3624;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
}

.btn-danger {
  background: #dc2626;
  border: none;
  color: #ffffff;
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
}

.delete-card {
  max-width: 440px;
  text-align: center;
  align-items: center;
}

.delete-icon {
  width: 52px;
  height: 52px;
  background: #fee2e2;
  color: #dc2626;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.delete-icon svg {
  width: 28px;
  height: 28px;
}

.delete-card p {
  font-size: 13px;
  color: #6b5c50;
  line-height: 1.5;
}

.delete-footer {
  width: 100%;
  justify-content: center;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.spinner-dark {
  width: 24px;
  height: 24px;
  border: 3px solid rgba(166, 88, 32, 0.2);
  border-top-color: #a65820;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  display: inline-block;
  margin-bottom: 8px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
