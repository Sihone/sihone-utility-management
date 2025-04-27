import { createRouter, createWebHistory } from 'vue-router';
import ApartmentsList from '../views/ApartmentsList.vue'; // We'll create this soon

const routes = [
  {
    path: '/apartments',
    name: 'ApartmentsList',
    component: ApartmentsList
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
