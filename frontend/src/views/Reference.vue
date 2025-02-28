<template>
  <v-container class="reference-container">
    <!-- Заголовок и поиск -->
    <div class="header-container">
      <v-toolbar flat class="toolbar-clean">
        <v-toolbar-title>Справочник "Отделы"</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-text-field 
          v-model="search" 
          label="Поиск" 
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          hide-details
          density="comfortable"
          class="search-field"
        ></v-text-field>
        <v-btn color="primary" class="add-btn" @click="openDialog(null)">Добавить</v-btn>
      </v-toolbar>
    </div>

    <!-- Таблица с отделами -->
    <v-data-table 
      :items="departments" 
      :headers="headers" 
      :search="search"
      density="compact"
      class="custom-table"
      style="text-align: center;"
    >

    <template v-slot:headers>
      <tr>
        <th v-for="header in headers" :key="header.key" class="header-center">
          <span v-if="header.key !== 'actions'">{{ header.title }}</span>
          <div v-else>
            <span>{{ header.title }}</span>
          </div>
        </th>
      </tr>
    </template>


    <template v-slot:item.actions="{ item }">
      <div class="actions">
        <v-btn color="primary" class="action-btn">
          Редактировать
        </v-btn>
        <v-btn color="red" class="action-btn delete-btn">
          Удалить
        </v-btn>
      </div>
    </template>


    </v-data-table>

    <!-- Модальное окно для добавления/редактирования отдела -->
    <v-dialog v-model="dialog" max-width="500px" persistent>
      <v-card>
        <v-card-title>{{ editedItem.id ? "Редактировать отдел" : "Добавить отдел" }}</v-card-title>
        <v-card-text>
          <v-text-field v-model="editedItem.name" label="Название отдела" required></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn color="blue darken-1" @click="saveDepartment">Сохранить</v-btn>
          <v-btn color="grey" @click="dialog = false">Отмена</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Подтверждение удаления -->
    <v-dialog v-model="deleteDialog" max-width="400px" persistent>
      <v-card>
        <v-card-title>Удаление отдела</v-card-title>
        <v-card-text>Вы уверены, что хотите удалить этот отдел?</v-card-text>
        <v-card-actions>
          <v-btn color="red" @click="deleteDepartmentHandler">Удалить</v-btn>
          <v-btn color="grey" @click="deleteDialog = false">Отмена</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import { ref, onMounted } from "vue";
import { getDepartments, createDepartment, updateDepartment, deleteDepartment } from "@/api";

export default {
  setup() {
    const departments = ref([]);
    const search = ref("");
    const dialog = ref(false);
    const deleteDialog = ref(false);
    const editedItem = ref({});
    const deleteId = ref(null);

    const headers = [
      { title: "ID", key: "id" },
      { title: "Название отдела", key: "name" },
      { title: "Действия", key: "actions", sortable: false }
    ];

    // Получение списка отделов
    const fetchDepartments = async () => {
      try {
        departments.value = await getDepartments();
      } catch (error) {
        console.error("Ошибка загрузки отделов:", error);
      }
    };

    // Открытие модального окна (добавление/редактирование)
    const openDialog = (item) => {
      editedItem.value = item ? { ...item } : { name: "" };
      dialog.value = true;
    };

    // Сохранение отдела (добавление/редактирование)
    const saveDepartment = async () => {
      if (!editedItem.value.name || editedItem.value.name.trim() === "") {
        console.error("Ошибка: название отдела не может быть пустым");
        return;
      }

      try {
        if (editedItem.value.id) {
          await updateDepartment(editedItem.value.id, { name: editedItem.value.name });
        } else {
          await createDepartment({ name: editedItem.value.name });
        }
        dialog.value = false;
        fetchDepartments();
      } catch (error) {
        console.error("Ошибка при сохранении отдела:", error);
      }
    };

    // Открытие окна подтверждения удаления
    const confirmDelete = (id) => {
      deleteId.value = id;
      deleteDialog.value = true;
    };

    // Метод удаления
    const deleteDepartmentHandler = async () => {
      try {
        await deleteDepartment(deleteId.value);
        deleteDialog.value = false;
        fetchDepartments();
      } catch (error) {
        console.error("Ошибка при удалении отдела:", error);
      }
    };

    onMounted(fetchDepartments);

    return {
      departments,
      search,
      headers,
      dialog,
      deleteDialog,
      editedItem,
      deleteId,
      openDialog,
      saveDepartment,
      confirmDelete,
      deleteDepartmentHandler,
    };
  },
};
</script>

<style scoped>
  /* Убираем серый фон сверху */
.header-container {
  background-color: transparent;
  padding-bottom: 10px;
}

/* Делаем кнопку "Добавить" такой же, как в другой странице */
.add-btn {
  margin-left: 10px;
}

/* Настраиваем поиск, чтобы был по центру */
.search-field {
  max-width: 250px;
  margin-bottom: 5px;
}

/* Выровнять ВСЕ элементы таблицы по центру */
.v-data-table th,
.v-data-table td {
  text-align: center !important;
  vertical-align: middle !important;
}

/* Специфичное выравнивание для тела таблицы */
.v-data-table__wrapper table {
  width: 100%;
}

.v-data-table__wrapper td {
  text-align: center !important;
}

/* Оформление кнопок "Редактировать" / "Удалить" */
.actions {
  display: flex;
  justify-content: center;
  gap: 8px;
}

/*Кнопки*/
.action-btn {
  min-width: 90px;
  height: 28px;
  font-size: 12px;
  text-transform: none;
  padding: 2px 6px;
  line-height: normal;
}



.delete-btn {
  background-color: #d32f2f;
  color: white;
}

</style>
