import axios from 'axios';

const api = axios.create({
    baseURL: 'http://127.0.0.1:8000/api',
});

// Добавляем токен в каждый запрос автоматически
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('authToken');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Получение списка всех посетителей
export function getVisitors() {
    return api.get('/visitors')
        .then(response => response.data)
        .catch(error => {
            console.error('Ошибка при загрузке данных:', error);
            throw error;
        });
}

// Получение данных одного посетителя по ID
export function getVisitorById(id) {
    return api.get(`/visitors/${id}`)
        .then(response => response.data)
        .catch(error => {
            console.error(`Ошибка загрузки посетителя ${id}:`, error);
            throw error;
        });
}

// Создание нового посетителя
export function createVisitor(visitor) {
    return api.post("/visitors", visitor)
        .catch(error => {
            console.error("Ошибка при создании посетителя:", error);
            throw error;
        });
}

// Обновление существующего посетителя
export function updateVisitor(id, visitor) {
    return api.put(`/visitors/${id}`, visitor)
        .catch(error => {
            console.error(`Ошибка при обновлении посетителя ${id}:`, error);
            throw error;
        });
}

// Удаление посетителя
export function deleteVisitor(id) {
    return api.delete(`/visitors/${id}`)  // Делаем DELETE запрос на API
        .then(response => response.data)
        .catch(error => {
            console.error(`Ошибка при удалении посетителя ${id}:`, error);
            throw error;
        });
}

// Получение списка отделов
export function getDepartments() {
    return api.get("/departments")
        .then(response => response.data)
        .catch(error => {
            console.error("Ошибка загрузки отделов:", error);
            throw error;
        });
}

// Создание нового отдела
export function createDepartment(department) {
    return api.post("/departments", { name: department.name })
        .then(response => response.data)
        .catch(error => {
            console.error("Ошибка при создании отдела:", error.response?.data);
            throw error;
        });
}


// Обновление отдела
export function updateDepartment(id, department) {
    return api.put(`/departments/${id}`, department)
        .then(response => response.data)
        .catch(error => {
            console.error(`Ошибка при обновлении отдела ${id}:`, error);
            throw error;
        });
}

// Удаление отдела
export function deleteDepartment(id) {
    return api.delete(`/departments/${id}`)
        .then(response => response.data)
        .catch(error => {
            console.error(`Ошибка при удалении отдела ${id}:`, error);
            throw error;
        });
}

// Получение статистики для дашборда (среднее время, количество пользователей, замечания)
export function getDashboardStatistics() {
    return api.get('/dashboard/statistics')
        .then(response => response.data)
        .catch(error => {
            console.error('Ошибка при загрузке статистики:', error);
            throw error;
        });
}

// Получение лучших показателей (по времени входа и количеству замечаний)
export function getBestPerformers() {
    return api.get('/dashboard/best-performers')
        .then(response => response.data)
        .catch(error => {
            console.error('Ошибка при загрузке лучших сотрудников:', error);
            throw error;
        });
}

export function getGraphData(dataType = 'entry_exit') {
    return api.get('/dashboard/graph', { params: { data_type: dataType } })
        .then(response => {
            console.log(`График для ${dataType}:`, response.data);
            return response.data;
        })
        .catch(error => {
            console.error('Ошибка при загрузке данных для графика:', error);
            return { labels: [], values: [] }; // Вернуть пустые данные, чтобы не ломать график
        });
}

// Получение текущего аутентифицированного пользователя
export function getCurrentUser() {
    return api.get('/user')
        .then(response => response.data)
        .catch(error => {
            console.error('Ошибка при получении данных пользователя:', error);
            throw error;
        });
}

export function updateUser(id, user) {
    return api.put(`/users/${id}`, user)  
        .catch(error => {
            console.error(`Ошибка при обновлении пользователя ${id}:`, error);
            throw error;
        });
}


