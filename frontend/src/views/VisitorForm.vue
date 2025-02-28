<template>
  <v-container>
    <h2>{{ isEditing ? 'Редактирование посетителя' : 'Добавление посетителя' }}</h2>

    <v-form ref="form" @submit.prevent="saveVisitor" v-model="isValid">
      <v-text-field v-model="visitor.full_name" label="ФИО" required></v-text-field>
      <v-text-field v-model="visitor.position" label="Должность" required></v-text-field>
      <v-text-field v-model="visitor.phone" label="Телефон" required></v-text-field>

      <!-- Отдел -->
      <v-select
        v-model="visitor.department_id"
        :items="departments"
        item-title="name"
        item-value="id"
        label="Отдел"
        required
      ></v-select>

      <!-- Дата рождения -->
      <v-text-field v-model="visitor.birth_date" label="Дата рождения" type="date" required></v-text-field>

      <!-- Тип документа -->
      <v-select
        v-model="visitor.document_type"
        :items="documentTypes"
        label="Тип документа"
        required
      ></v-select>

      <!-- Серия и номер документа -->
      <v-text-field v-model="visitor.document_series" label="Серия документа" required></v-text-field>
      <v-text-field v-model="visitor.document_number" label="Номер документа" required></v-text-field>

      <!-- Дата выдачи -->
      <v-text-field v-model="visitor.document_issue_date" label="Дата выдачи документа" type="date" required></v-text-field>

      <!-- Кем выдан -->
      <v-text-field v-model="visitor.document_issued_by" label="Кем выдан" required></v-text-field>

      <!-- Ошибки -->
      <v-alert v-if="formError" type="error" class="mt-4 mb-3">{{ formError }}</v-alert>

      <!-- Кнопки -->
      <div class="button-group">
        <v-btn type="submit" color="primary" :disabled="!isValid" class="add-btn">{{ isEditing ? 'Сохранить' : 'Добавить' }}</v-btn>
        <v-btn color="secondary" @click="cancel" class="cancel-btn">Отмена</v-btn>
      </div>
    </v-form>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getVisitorById, getDepartments, createVisitor, updateVisitor } from '@/api';

// Основные переменные
const visitor = ref({
  full_name: '',
  birth_date: '',
  position: '',
  phone: '',
  document_type: '',
  document_series: '',
  document_number: '',
  document_issue_date: '',
  document_issued_by: '',
  department_id: null,
});

const departments = ref([]);
const documentTypes = ref(['passport', 'driver_license', 'other']);
const formError = ref('');  // Сообщение об ошибке
const isEditing = ref(false);
const isValid = ref(false);  // Это будет отслеживать валидность формы

const route = useRoute();
const router = useRouter();

onMounted(async () => {
  try {
    departments.value = await getDepartments();

    if (route.params.id) {
      isEditing.value = true;
      visitor.value = await getVisitorById(route.params.id);
    }
  } catch (err) {
    console.error("Ошибка загрузки данных:", err);
    formError.value = "Ошибка загрузки данных";
  }
});

const saveVisitor = async () => {
  // Проверка на незаполненные обязательные поля (для редактирования и добавления)
  if (!visitor.value.full_name || !visitor.value.position || !visitor.value.phone ||
    !visitor.value.department_id || !visitor.value.birth_date || !visitor.value.document_type ||
    !visitor.value.document_number || !visitor.value.document_issue_date || !visitor.value.document_issued_by ||
    !visitor.value.document_series) {
    formError.value = "Пожалуйста, заполните все обязательные поля.";
    return;  // Не отправляем форму, если есть незаполненные обязательные поля
  }

  try {
    if (isEditing.value) {
      await updateVisitor(route.params.id, visitor.value);
    } else {
      await createVisitor(visitor.value);
    }
    router.push('/visitors');
  } catch (error) {
    console.error("Ошибка сохранения:", error.response?.data || error);
    formError.value = "Произошла ошибка при сохранении данных.";
  }
};

const cancel = () => {
  router.push('/visitors');
};
</script>

<style scoped>
/* Добавим отступ между кнопками */
.button-group {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.add-btn, .cancel-btn {
  font-size: 14px;
  text-transform: none;
}

/* Плашка с ошибкой будет ниже кнопок и будет иметь отступ сверху */
.mt-4 {
  margin-top: 20px;
}

.mb-3 {
  margin-bottom: 15px;
}
</style>
