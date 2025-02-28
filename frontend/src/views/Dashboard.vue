<template>
  <v-container>
    <!-- Заголовок и приветствие -->
    <v-row>
      <v-col cols="9">
        <v-card class="pa-5">
          <v-card-title>Дашборд</v-card-title>
          <v-card-subtitle>Добро пожаловать, {{ userName }}</v-card-subtitle>
        </v-card>
      </v-col>
      <v-col cols="3" class="d-flex justify-end align-center">
        <!-- Кнопка редактирования -->
        <v-btn color="primary" @click="editUser" class="mt-2">
          Редактировать
        </v-btn>
      </v-col>
    </v-row>

    <v-dialog v-model="editDialog" max-width="500px">
      <v-card>
        <v-card-title class="headline">Редактирование пользователя</v-card-title>
        <v-card-text>
          <v-form ref="form" v-model="valid">
            <v-text-field v-model="user.name" label="Имя" :rules="[rules.required]" required></v-text-field>            
            <v-text-field v-model="user.login" label="Логин" :rules="[rules.required]" required></v-text-field>
            <v-text-field v-model="user.password" label="Пароль" type="password"></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn color="green darken-1" text @click="saveUser">Сохранить</v-btn>
          <v-btn color="red darken-1" text @click="editDialog = false">Закрыть</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>




    <!-- KPI за текущий и прошлый месяц -->
    <v-row>
      <v-col cols="3">
        <v-card :color="entryTimeColor" class="pa-3 kpi-card">
          <v-card-title class="kpi-title">Среднее время входа</v-card-title>
          <v-card-text class="kpi-text">
            {{ formattedAvgEntryTime }}
            <span :style="{ color: entryChangeColor }">({{ formattedPrevEntryTime }})</span>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="3">
        <v-card :color="exitTimeColor" class="pa-3 kpi-card">
          <v-card-title class="kpi-title">Среднее время выхода</v-card-title>
          <v-card-text class="kpi-text">
            {{ formattedAvgExitTime }}
            <span :style="{ color: exitChangeColor }">({{ formattedPrevExitTime }})</span>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="3">
        <v-card class="pa-3 kpi-card">
          <v-card-title class="kpi-title">Количество посетителей</v-card-title>
          <v-card-text class="kpi-text">
            {{ uniqueVisitors }}
            <span :style="{ color: visitorsChangeColor }">({{ prevUniqueVisitors }})</span>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="3">
        <v-card :color="notesColor" class="pa-3 kpi-card">
          <v-card-title class="kpi-title">Количество замечаний</v-card-title>
          <v-card-text class="kpi-text">
            {{ notesCount }}
            <span :style="{ color: notesChangeColor }">({{ prevNotesCount }})</span>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- График для выбранного типа данных -->
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            <v-select v-model="selectedDataType" :items="dataTypes" label="Тип данных" @change="onDataTypeChange"></v-select>
          </v-card-title>
          <v-card-text>
            <p v-if="graphError" style="color: red;">Ошибка загрузки графика</p>
            <p v-else-if="!graphDataReady">Загрузка...</p>
            <p v-else-if="!chartData || chartData.labels.length === 0">Нет данных для отображения</p>

            <template v-else>
              <div style="height: 400px; width: 100%;">
                <BarChart v-if="chartData.labels.length > 0" :data="chartData" :options="chartOptions" />
              </div>
            </template>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <!-- Лучший сотрудник -->
      <v-col cols="12" md="6">
        <v-card :color="'green'" class="pa-5">
          <v-card-title>Лучшие показатели</v-card-title>
          <v-card-subtitle>{{ bestPerformer?.full_name }}</v-card-subtitle>
          <v-card-text>
            Среднее время входа: {{ bestPerformer?.avg_entry_time }}<br />
            Замечаний: {{ bestPerformer?.notes_count }}
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Худший сотрудник -->
      <v-col cols="12" md="6">
        <v-card :color="'red'" class="pa-5">
          <v-card-title>Обратить внимание</v-card-title>
          <v-card-subtitle>{{ worstPerformer?.full_name }}</v-card-subtitle>
          <v-card-text>
            Среднее время входа: {{ worstPerformer?.avg_entry_time }}<br />
            Замечаний: {{ worstPerformer?.notes_count }}
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>



  </v-container>
</template>

<script>
import { ref, onMounted, computed, watch } from "vue";
import { getGraphData, getDashboardStatistics, getCurrentUser, updateUser, getBestPerformers   } from "@/api.js";
import { Chart, registerables } from "chart.js";
import { Bar } from "vue-chartjs";

Chart.register(...registerables);

export default {
  components: {
    BarChart: Bar,
  },

  setup() {
    const rules = {
      required: value => !!value || 'Это поле обязательно'
    };

    const userName = ref("Гость");  // По умолчанию "Гость"
    const user = ref({
      name: "",
      login: "",
      password: ""
    });

    
    
    const valid = ref(false); // Валидация формы

    // KPI текущего месяца
    const avgEntryTime = ref(0);
    const avgExitTime = ref(0);
    const uniqueVisitors = ref(0);
    const notesCount = ref(0);

    // KPI прошлого месяца
    const prevAvgEntryTime = ref(0);
    const prevAvgExitTime = ref(0);
    const prevUniqueVisitors = ref(0);
    const prevNotesCount = ref(0);

    // Формат времени (часы:минуты)
    const formatTime = (minutes) => {
    const hours = Math.floor(minutes / 60);  // Получаем количество часов
    const mins = Math.round(minutes % 60);  // Округляем минуты до целого числа
    return `${hours}:${mins.toString().padStart(2, '0')}`;  // Форматируем в строку
  };


    // Форматированные KPI
    const formattedAvgEntryTime = computed(() => formatTime(avgEntryTime.value));
    const formattedAvgExitTime = computed(() => formatTime(avgExitTime.value));
    const formattedPrevEntryTime = computed(() => formatTime(prevAvgEntryTime.value));
    const formattedPrevExitTime = computed(() => formatTime(prevAvgExitTime.value));

    // Цвета KPI
    const entryTimeColor = computed(() => (avgEntryTime.value > prevAvgEntryTime.value ? "#D9001B" : "#13BC75"));
    const exitTimeColor = computed(() => (avgExitTime.value < prevAvgExitTime.value ? "#D9001B" : "#13BC75"));
    const visitorsChangeColor = computed(() => (uniqueVisitors.value >= prevUniqueVisitors.value ? "#13BC75" : "#D9001B"));
    const notesColor = computed(() => (notesCount.value > prevNotesCount.value ? "#D9001B" : "#13BC75"));

    const entryChangeColor = computed(() => (avgEntryTime.value > prevAvgEntryTime.value ? "red" : "green"));
    const exitChangeColor = computed(() => (avgExitTime.value > prevAvgExitTime.value ? "red" : "green"));
    const notesChangeColor = computed(() => (notesCount.value > prevNotesCount.value ? "red" : "green"));

    // Данные для графиков
    const selectedDataType = ref("entry_exit");
    const graphDataReady = ref(false);
    const graphError = ref(false);
    const rawChartData = ref({ labels: [], datasets: [] });

    const bestPerformer = ref(null);
    const worstPerformer = ref(null);

    const chartData = computed(() => {
      console.log('chartData:', rawChartData.value); 
      return {
        labels: rawChartData.value.labels,
        datasets: [
          {
            label: selectedDataType.value === 'notes' ? "Количество замечаний" : 
                  selectedDataType.value === 'visitors' ? "Количество посетителей" : 
                  "Среднее время входа",
            backgroundColor: selectedDataType.value === 'notes' ? "#FF6384" :
                            selectedDataType.value === 'visitors' ? "#FFCD56" :
                            "#42A5F5",
            data: rawChartData.value.datasets.length > 0 ? rawChartData.value.datasets[0].data : [],
          },
        ],
      };
    });


    const dataTypes = ref(["entry_exit", "notes", "visitors"]); // Добавляем тип "visitors"

    // Загрузка статистики
    const loadStatistics = async () => {
      try {
        const data = await getDashboardStatistics();
        avgEntryTime.value = Math.round(data.avg_entry_time);
        avgExitTime.value = Math.round(data.avg_exit_time);
        uniqueVisitors.value = data.unique_visitors;
        notesCount.value = data.notes_count;

        prevAvgEntryTime.value = Math.round(data.prev_avg_entry_time || 0);
        prevAvgExitTime.value = Math.round(data.prev_avg_exit_time || 0);
        prevUniqueVisitors.value = data.prev_unique_visitors || 0;
        prevNotesCount.value = data.prev_notes_count || 0;
      } catch (error) {
        console.error("Ошибка загрузки статистики:", error);
      }
    };

    const chartOptions = computed(() => ({
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          ticks: {
            callback: function(value) {
              if (selectedDataType.value === 'entry_exit') {
                const hours = Math.floor(value / 60);
                const minutes = value % 60;
                return `${hours}:${minutes.toString().padStart(2, '0')}`;
              }
              return value;
            }
          }
        }
      }
    }));

    const loadGraphData = async () => {
      graphError.value = false;
      graphDataReady.value = false;
      
      try {
        // Загружаем данные для выбранного типа данных
        const data = await getGraphData(selectedDataType.value);

        // Проверяем, что данные корректно загружены
        if (!data || !data.labels.length) {
          rawChartData.value = { labels: [], datasets: [] };
          graphDataReady.value = true;
          console.log("Нет данных для графика", data); // Лог для диагностики
          return;
        }

        // Форматируем значения графика
        const formattedValues = data.values.map(value => {
          // Здесь можно добавить дополнительную обработку данных, если нужно
          return value;
        });

        // Обновляем данные для графика
        rawChartData.value = {
          labels: data.labels,  // Массив меток для графика
          datasets: [{
            label: selectedDataType.value === 'notes' ? "Количество замечаний" :
                  selectedDataType.value === 'visitors' ? "Количество посетителей" :
                  "Среднее время входа",  // Русские названия
            backgroundColor: selectedDataType.value === 'notes' ? "#FF6384" :
                            selectedDataType.value === 'visitors' ? "#FFCD56" :
                            "#42A5F5",  // Цвета для разных типов данных
            data: formattedValues,  // Массив значений для графика
          }],
        };

        graphDataReady.value = true;  // Данные для графика готовы
        console.log("Данные для графика обновлены:", rawChartData.value); // Лог для проверки

      } catch (error) {
        graphError.value = true;  // Устанавливаем ошибку при загрузке
        graphDataReady.value = true;  // Помечаем, что данные больше не загружаются
        console.error('Ошибка при загрузке данных для графика:', error); // Лог для ошибки
      }
    };


    const onDataTypeChange = () => {
      console.log('Selected data type:', selectedDataType.value);  // Логируем тип данных
      loadGraphData();  // Загружаем новые данные при изменении типа
    };


    // Получение данных текущего пользователя
    const loadUserData = async () => {
      try {
        const data = await getCurrentUser();
        userName.value = data.name;  // Сохраняем имя пользователя
        user.value = { ...data };  // Сохраняем другие данные о пользователе
      } catch (error) {
        console.error('Ошибка при получении данных пользователя:', error);
      }
    };

    const editDialog = ref(false);  // Для управления состоянием модального окна редактирования

    // Метод для открытия модального окна редактирования
    const editUser = () => {
      editDialog.value = true;
    };

    // Метод для сохранения изменений пользователя
    const saveUser = async () => {
      try {
        const response = await updateUser(user.value.id, user.value);  // Обновляем данные пользователя
        userName.value = user.value.name;  // Обновляем имя пользователя в переменной
        editDialog.value = false;  // Закрываем окно редактирования
      } catch (error) {
        console.error('Ошибка при сохранении данных пользователя:', error);
      }
    };

    // Загрузка данных о лучших и худших сотрудниках
    const loadBestPerformers = async () => {
      try {
        const response = await getBestPerformers();
        bestPerformer.value = response.best_performer;
        worstPerformer.value = response.worst_performer;

        // Преобразуем время в формат hh:mm
        if (bestPerformer.value) {
          bestPerformer.value.avg_entry_time = formatTime(bestPerformer.value.avg_entry_time);
        }
        if (worstPerformer.value) {
          worstPerformer.value.avg_entry_time = formatTime(worstPerformer.value.avg_entry_time);
        }
      } catch (error) {
        console.error("Ошибка при получении данных о лучших сотрудниках:", error);
      }
    };

    // Добавляем watcher для отслеживания изменений в выбранном типе данных
    watch(selectedDataType, () => {
      loadGraphData();  // Загружаем данные при изменении типа данных
    });

    onMounted(async () => {
      
      await loadUserData();
      await loadStatistics();
      await loadGraphData();
      await loadBestPerformers();
    });

    return {
      userName,
      user,
      valid,
      editDialog,
      editUser,
      saveUser,
      rules, 
      bestPerformer,
      worstPerformer,
      formattedAvgEntryTime,
      formattedAvgExitTime,
      formattedPrevEntryTime,
      formattedPrevExitTime,
      entryTimeColor,
      exitTimeColor,
      visitorsChangeColor,
      notesColor,
      entryChangeColor,
      exitChangeColor,
      notesChangeColor,
      uniqueVisitors,
      prevUniqueVisitors,
      notesCount,
      prevNotesCount,
      selectedDataType,
      chartData,
      chartOptions,
      graphDataReady,
      graphError,
      dataTypes,
      loadGraphData,
      onDataTypeChange,
    };
  },
};
</script>

