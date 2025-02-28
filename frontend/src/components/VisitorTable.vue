<template>
  <div class="table-wrapper">
    <v-data-table
      :items="visitors"
      :headers="headers"
      item-value="id"
      density="compact"
      class="custom-table"
      fixed-header
      height="auto"
    >
      <!-- Заголовки таблицы -->
      <template v-slot:headers>
        <tr>
          <th v-for="header in headers" :key="header.value" class="header-center">
            {{ header.text }}
          </th>
        </tr>
      </template>

      <!-- Тело таблицы -->
      <template v-slot:item="{ item }">
        <tr>
          <td>{{ item.full_name }}</td>
          <td>{{ item.position }}</td>
          <td>{{ item.phone }}</td>
          <td>{{ item.document_type }}</td>
          <td>{{ item.department.name }}</td>
          <td>{{ item.birth_date }}</td>
          <td>{{ item.document_series }}</td>
          <td>{{ item.document_number }}</td>
          <td>{{ item.document_issued_by }}</td>
          <td>{{ item.document_issue_date }}</td>
          <td class="actions">
            <v-btn @click="editVisitor(item)" class="action-btn edit-btn">
              Редактировать
            </v-btn>
            <v-btn @click="deleteVisitor(item.id)" class="action-btn delete-btn">
              Удалить
            </v-btn>
          </td>
        </tr>
      </template>
    </v-data-table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getVisitors, deleteVisitor as deleteVisitorAPI } from '@/api';
import { useRouter } from 'vue-router';

const visitors = ref([]);
const headers = [
  { text: 'ФИО', value: 'full_name', align: 'center' },
  { text: 'Должность', value: 'position', align: 'center' },
  { text: 'Телефон', value: 'phone', align: 'center' },
  { text: 'Тип документа', value: 'document_type', align: 'center' },
  { text: 'Отдел', value: 'department.name', align: 'center' },
  { text: 'Дата рождения', value: 'birth_date', align: 'center' },
  { text: 'Серия документа', value: 'document_series', align: 'center' },
  { text: 'Номер документа', value: 'document_number', align: 'center' },
  { text: 'Кем выдан', value: 'document_issued_by', align: 'center' },
  { text: 'Дата выдачи', value: 'document_issue_date', align: 'center' },
  { text: 'Действия', value: 'actions', align: 'center', sortable: false }
];

const router = useRouter();

onMounted(async () => {
  try {
    const data = await getVisitors();
    visitors.value = data;
  } catch (error) {
    console.error('Ошибка при загрузке данных:', error);
  }
});

const editVisitor = (visitor) => {
  router.push(`/visitors/edit/${visitor.id}`);
};

const deleteVisitor = async (id) => {
  try {
    await deleteVisitorAPI(id);
    visitors.value = visitors.value.filter(visitor => visitor.id !== id);
  } catch (error) {
    console.error('Ошибка при удалении посетителя:', error);
  }
};
</script>

<style scoped>
/* Стиль для таблицы */
.table-wrapper {
  width: 100%;
  max-width: 100%;
  margin-top: 20px;
  background-color: #fff;
}

/* Центрируем заголовки таблицы */
.v-data-table th {
  text-align: center !important;
  font-weight: normal;
}

/* Центрируем данные таблицы */
.v-data-table td {
  text-align: center !important;
}

/* Кнопки "Редактировать" и "Удалить" */
.action-btn {
  min-width: 120px;
  font-size: 12px;
  margin: 4px 0;
  font-weight: 500;
  text-transform: none;
  padding: 6px 10px;
  border-radius: 4px;
}

/* Кнопка "Редактировать" (синий) */
.edit-btn {
  background-color: #1976D2;
  color: white;
  border: none;
}



/* Кнопка "Удалить" (красный) */
.delete-btn {
  background-color: #d32f2f;
  color: white;
}


</style>
