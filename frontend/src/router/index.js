import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/LoginView.vue';
import CoffeeInventoryView from '../views/CoffeeInventoryView.vue';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta: { guestOnly: true },
  },
  {
    path: '/',
    name: 'Inventory',
    component: CoffeeInventoryView,
    meta: { requiresAuth: true },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');

  if (to.meta.requiresAuth && !token) {
    return next({ name: 'Login' });
  }

  if (to.meta.guestOnly && token) {
    return next({ name: 'Inventory' });
  }

  next();
});

export default router;
