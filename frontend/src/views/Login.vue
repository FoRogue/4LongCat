<template>
    <v-container>
      <h1>Авторизация</h1>
      <v-form @submit.prevent="loginUser">
        <v-text-field v-model="login" label="Логин" required class="mb-4"></v-text-field>
        <!-- Используем VTextField для пароля с type="password" -->
        <v-text-field v-model="password" label="Пароль" type="password" required class="mb-4"></v-text-field>
        <v-btn type="submit" color="primary">Войти</v-btn>
        <v-alert v-if="error" type="error">{{ error }}</v-alert>
      </v-form>
    </v-container>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  
  const login = ref('');
  const password = ref('');
  const error = ref('');
  
  // Логика для входа
  const loginUser = async () => {
    try {
      // Отправка POST-запроса на сервер для авторизации
      const response = await axios.post('http://127.0.0.1:8000/api/login', {
        login: login.value,
        password: password.value,
      });
  
      // Сохраняем токен в localStorage для дальнейшего использования
      localStorage.setItem('authToken', response.data.token);
  
      // Перенаправляем на страницу с посетителями
      window.location.href = '/visitors';  // Это можно заменить на router.push('/visitors') если используешь Vue Router
    } catch (err) {
      error.value = 'Неверный логин или пароль'; // Отображаем ошибку, если авторизация не удалась
    }
  };
  
  // Лог, когда компонент загружен
  onMounted(() => {
    console.log("Компонент 'Login' загружен и готов к использованию");
  });
  </script>
  
  <style scoped>
  .v-btn {
    margin-top: 20px;
  }
  
  .mb-4 {
    margin-bottom: 1rem;
  }
  
  .v-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
  }
  
  .v-form {
    width: 100%;
    max-width: 400px; /* Ограничиваем максимальную ширину формы */
  }
  </style>
  