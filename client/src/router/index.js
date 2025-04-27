import { createRouter, createWebHistory } from 'vue-router';
import ApartmentsList from '../views/ApartmentsList.vue'; // We'll create this soon
import MeterReadingsList from '../views/MeterReadingsList.vue';

const routes = [
  {
    path: '/apartments',
    name: 'ApartmentsList',
    component: ApartmentsList
  },
  {
    path: '/',
    name: 'MeterReadings',
    component: MeterReadingsList,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
