import { createRouter, createWebHistory } from 'vue-router';
import Login from '@/views/Login.vue';
import VisitorList from '@/views/VisitorList.vue';
import VisitorForm from '@/views/VisitorForm.vue';
import Dashboard from '@/views/Dashboard.vue';
import Reference from '@/views/Reference.vue';

// Функция для проверки авторизации
const isAuthenticated = () => {
  const token = localStorage.getItem('authToken');
  return token !== null;  // Проверяем, есть ли токен
};

const routes = [
  { 
    path: '/', 
    component: Login,
    meta: { requiresAuth: false },  // Не требует авторизации
  },
  { 
    path: '/visitors', 
    component: VisitorList,
    meta: { requiresAuth: true },
    beforeEnter: (to, from, next) => {
      if (isAuthenticated()) {
        next();  // Авторизован, переходим к маршруту
      } else {
        next('/');  // Не авторизован, перенаправляем на страницу логина
      }
    }
  },
  { 
    path: '/visitors/add', 
    component: VisitorForm,
    meta: { requiresAuth: true },
    beforeEnter: (to, from, next) => {
      if (isAuthenticated()) {
        next();  // Авторизован, переходим к маршруту
      } else {
        next('/');  // Не авторизован, перенаправляем на страницу логина
      }
    }
  },
  { 
    path: '/visitors/edit/:id', 
    component: VisitorForm,
    meta: { requiresAuth: true },
    beforeEnter: (to, from, next) => {
      if (isAuthenticated()) {
        next();  // Авторизован, переходим к маршруту
      } else {
        next('/');  // Не авторизован, перенаправляем на страницу логина
      }
    }
  },
  { 
    path: '/dashboard', 
    component: Dashboard,
    meta: { requiresAuth: true },
    beforeEnter: (to, from, next) => {
      if (isAuthenticated()) {
        next();  // Авторизован, переходим к маршруту
      } else {
        next('/');  // Не авторизован, перенаправляем на страницу логина
      }
    }
  },
  { 
    path: '/reference', 
    component: Reference,
    meta: { requiresAuth: true },
    beforeEnter: (to, from, next) => {
      if (isAuthenticated()) {
        next();  // Авторизован, переходим к маршруту
      } else {
        next('/');  // Не авторизован, перенаправляем на страницу логина
      }
    }
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
