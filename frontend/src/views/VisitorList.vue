<template>
  <v-container class="reference-container">
    <!-- Серая область с заголовком и кнопкой -->
    <div class="header-container">
      <v-toolbar flat class="toolbar-clean">
        <v-toolbar-title class="toolbar-title">Список посетителей</v-toolbar-title>
        <v-spacer></v-spacer>
        <!-- Кнопка "Добавить" с синим текстом -->
        <v-btn @click="addVisitor" class="add-btn">
          Добавить
        </v-btn>
      </v-toolbar>
    </div>

    <VisitorTable :visitors="visitors" />
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router'; // Импортируем useRouter для использования маршрутизации
import VisitorTable from '@/components/VisitorTable.vue';
import { getVisitors } from '@/api';

const visitors = ref([]);
const router = useRouter(); // Получаем объект маршрутизатора

// Функция для перехода на страницу добавления посетителя
const addVisitor = () => {
  router.push('/visitors/add'); // Переход на страницу добавления посетителя
};

// Загружаем данные при монтировании компонента
onMounted(async () => {
  try {
    const data = await getVisitors();
    visitors.value = data;
  } catch (error) {
    console.error('Ошибка при загрузке данных:', error);
  }
});
</script>

<style scoped>
.header-container {
  background-color: transparent;
  padding-bottom: 10px;
}

/* Кнопка "Добавить" с синим текстом и прозрачным фоном */
.add-btn {
  margin-left: 10px;
  color: #1976D2; /* Синий цвет текста */
  background-color: transparent; /* Прозрачный фон */
  border: none; /* Убираем обводку */
  font-weight: 500;
}

.add-btn:hover {
  background-color: rgba(25, 118, 210, 0.1); /* Легкий синий фон при наведении */
}
</style>
