<template>
  <div class="login-wrapper">
    <div class="login-container">
      <div class="brand-header">
        <div class="brand-badge">
          <svg class="coffee-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
            <line x1="6" y1="1" x2="6" y2="4"></line>
            <line x1="10" y1="1" x2="10" y2="4"></line>
            <line x1="14" y1="1" x2="14" y2="4"></line>
          </svg>
        </div>
        <h1 class="brand-title">Katalog Kopi Nusantara</h1>
        <p class="brand-subtitle">Specialty Roastery & Inventory Management System</p>
      </div>

      <div class="login-card">
        <div class="card-header">
          <h2>Masuk ke Dashboard</h2>
          <p>Kelola katalog biji kopi specialty, profil sangrai, dan stok roastery.</p>
        </div>

        <div v-if="errorMessage" class="alert-error">
          <svg viewBox="0 0 24 24" class="alert-icon" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span>{{ errorMessage }}</span>
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email">Alamat Email</label>
            <input
              id="email"
              v-model="email"
              type="email"
              placeholder="admin@kopinusantara.id"
              required
              autocomplete="email"
            />
          </div>

          <div class="form-group">
            <label for="password">Kata Sandi</label>
            <input
              id="password"
              v-model="password"
              type="password"
              placeholder="••••••••"
              required
              autocomplete="current-password"
            />
          </div>

          <button type="submit" class="btn-submit" :disabled="loading">
            <span v-if="loading" class="spinner"></span>
            <span v-else>Masuk ke Akun Roastery</span>
          </button>
        </form>

        <div class="quick-creds">
          <span class="badge-hint">Kredensial Default Roaster:</span>
          <p>Email: <code>admin@kopinusantara.id</code></p>
          <p>Password: <code>password123</code></p>
          <button @click="fillSampleCredentials" class="btn-auto-fill" type="button">
            Gunakan Kredensial Ini
          </button>
        </div>
      </div>

      <div class="academic-footer">
        <p><strong>Akmal Manggala Putra</strong> (24/536182/SV/24402) - Kelas PLBB</p>
        <p>Praktikum Konstruksi & Evolusi Perangkat Lunak - UTS 2026</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';

const router = useRouter();

const email = ref('admin@kopinusantara.id');
const password = ref('password123');
const loading = ref(false);
const errorMessage = ref('');

const fillSampleCredentials = () => {
  email.value = 'admin@kopinusantara.id';
  password.value = 'password123';
};

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';

  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    });

    const { token, user } = response.data;

    // Save token and user into localStorage
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));

    // Redirect to main inventory dashboard
    router.push({ name: 'Inventory' });
  } catch (error) {
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value = 'Koneksi gagal. Pastikan backend server aktif.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at 50% 20%, #462c14 0%, #1e1309 100%);
  padding: 24px;
}

.login-container {
  width: 100%;
  max-width: 460px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.brand-header {
  text-align: center;
  color: #fff;
}

.brand-badge {
  display: inline-flex;
  padding: 12px;
  background: rgba(217, 131, 36, 0.15);
  border: 1px solid rgba(217, 131, 36, 0.4);
  border-radius: 16px;
  margin-bottom: 12px;
}

.coffee-icon {
  width: 34px;
  height: 34px;
  stroke: #e89547;
}

.brand-title {
  font-size: 26px;
  font-weight: 800;
  letter-spacing: -0.5px;
  color: #f7ede2;
}

.brand-subtitle {
  font-size: 13px;
  color: #bda087;
  margin-top: 4px;
}

.login-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.card-header h2 {
  font-size: 20px;
  font-weight: 700;
  color: #291c10;
}

.card-header p {
  font-size: 13px;
  color: #715f50;
  margin-top: 4px;
  margin-bottom: 20px;
}

.alert-error {
  display: flex;
  align-items: center;
  gap: 10px;
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #f87171;
  padding: 12px 14px;
  border-radius: 10px;
  font-size: 13px;
  margin-bottom: 16px;
}

.alert-icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label {
  font-size: 13px;
  font-weight: 600;
  color: #42352b;
}

.form-group input {
  padding: 11px 14px;
  border-radius: 10px;
  border: 1px solid #d4c4b5;
  font-size: 14px;
  color: #231709;
  outline: none;
  transition: all 0.2s ease;
  background-color: #fcfbfa;
}

.form-group input:focus {
  border-color: #c47d34;
  box-shadow: 0 0 0 3px rgba(196, 125, 52, 0.2);
  background-color: #ffffff;
}

.btn-submit {
  margin-top: 8px;
  padding: 13px;
  background-color: #8b4a1b;
  background: linear-gradient(135deg, #a65820 0%, #753b12 100%);
  color: #ffffff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-submit:hover:not(:disabled) {
  opacity: 0.95;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(117, 59, 18, 0.3);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.quick-creds {
  margin-top: 22px;
  padding-top: 18px;
  border-top: 1px dashed #e6ded5;
  font-size: 12px;
  color: #635245;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.badge-hint {
  font-weight: 700;
  color: #a65820;
  text-transform: uppercase;
  font-size: 11px;
  letter-spacing: 0.5px;
}

code {
  background: #f1eae2;
  padding: 2px 6px;
  border-radius: 4px;
  font-family: monospace;
  font-size: 12px;
}

.btn-auto-fill {
  margin-top: 8px;
  background: none;
  border: 1px solid #cbbaa8;
  color: #724218;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  align-self: flex-start;
  transition: all 0.2s;
}

.btn-auto-fill:hover {
  background: #f5ebe0;
  border-color: #a65820;
}

.academic-footer {
  text-align: center;
  font-size: 11px;
  color: #8e7c6d;
  line-height: 1.5;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
