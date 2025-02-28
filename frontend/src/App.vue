<template>
  <v-app>
    <v-navigation-drawer app>
      <v-list>
        <v-list-item to="/">Авторизация</v-list-item>
        <v-list-item to="/visitors">Список посетителей</v-list-item>
        <v-list-item to="/dashboard">Дашборды</v-list-item>
        <v-list-item to="/reference">Справочник</v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup>
// Импортируем Vuetify компоненты
import { VApp, VNavigationDrawer, VList, VListItem, VMain } from 'vuetify/components';
</script>
