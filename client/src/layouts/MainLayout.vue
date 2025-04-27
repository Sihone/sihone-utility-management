<template>
  <v-app>
    <!-- Top Navigation Bar -->
    <v-app-bar app color="primary" dark>
      <v-toolbar-title class="text-h5 app-title">Utility Manager</v-toolbar-title>

      <v-spacer />
      
      <v-btn
        variant="text"
        :to="{ path: '/meter-readings' }"
        :class="{ 'v-btn--active': $route.path.startsWith('/meter-readings') }"
      >
        Meter Readings
      </v-btn>

      <v-btn
        variant="text"
        :to="{ path: '/apartments' }"
        :class="{ 'v-btn--active': $route.path.startsWith('/apartments') }"
      >
        Apartments
      </v-btn>
      
      <v-btn
        variant="text"
        :to="{ path: '/invoices' }"
        :class="{ 'v-btn--active': $route.path.startsWith('/invoices') }"
      >
        Invoices
      </v-btn>
      
      <v-btn
        variant="text"
        :to="{ path: '/settings' }"
        :class="{ 'v-btn--active': $route.path.startsWith('/settings') }"
      >
        Settings
      </v-btn>
    </v-app-bar>

    <!-- Main Page Content -->
    <v-main class="pa-4">
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'MainLayout'
}
</script>

<style scoped>
.v-btn--active {
  background-color: rgba(255, 255, 255, 0.2);
  font-weight: bold;
}
</style>
