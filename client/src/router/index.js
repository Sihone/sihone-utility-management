import { createRouter, createWebHistory } from 'vue-router';
import ApartmentsList from '../views/ApartmentsList.vue'; // We'll create this soon
import MeterReadingsList from '../views/MeterReadingsList.vue';
import MainLayout from '../layouts/MainLayout.vue';
import InvoicesList from '../views/InvoicesList.vue';

const routes = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', redirect: '/meter-readings' }, // default redirect
      { path: 'apartments', component: ApartmentsList },
      { path: 'meter-readings', component: MeterReadingsList },
      { path: 'invoices', component: InvoicesList },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router;
