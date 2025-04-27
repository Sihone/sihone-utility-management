<template>
  <v-container fluid style="margin-top: 60px;">
    <v-row>
      <v-col>
        <h2>Payments</h2>
      </v-col>
    </v-row>

    <v-divider class="my-4" />
    
    <!-- Filters -->
    <v-row class="mb-4">
      <v-col cols="12" md="4">
        <v-select
          v-model="selectedApartment"
          :items="apartments"
          item-title="name"
          item-value="id"
          label="Filter by Apartment"
          clearable
          :loading="loadingApartments"
        />
      </v-col>
      <v-col cols="12" md="3">
        <v-text-field
          v-model="startDate"
          label="Start Date"
          type="date"
          clearable
        />
      </v-col>
      <v-col cols="12" md="3">
        <v-text-field
          v-model="endDate"
          label="End Date"
          type="date"
          clearable
        />
      </v-col>
      <v-col cols="12" md="2">
        <v-btn text color="primary" class="mt-4" @click="clearFilters">Clear Filters</v-btn>
      </v-col>
    </v-row>

    <v-card>
      <v-data-table
        :headers="headers"
        :items="filteredPayments"
        item-value="id"
        class="elevation-1"
        :loading="loadingPayments"
      >
        <template #item.actions="{ item }">
          <v-btn icon size="small" color="red" @click="startDelete(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
    
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title class="headline">Confirm Delete</v-card-title>
        <v-card-text>Are you sure you want to delete this payment?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" text @click="deleteDialog = false" :disabled="saving">Cancel</v-btn>
          <v-btn color="red darken-1" text @click="confirmDeletePayment(deletingPayment.id)" :loading="saving">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  name: 'PaymentsList',
  data() {
    return {
      payments: [],
      loadingPayments: false,
      headers: [
        { title: 'Payment Date', value: 'payment_date' },
        { title: 'Apartment', value: 'invoice.apartment.name' },
        { title: 'Invoice ID', value: 'invoice.id' },
        { title: 'Amount Paid (FCFA)', value: 'amount' },
        { title: 'Actions', value: 'actions', sortable: false },
      ],
      deleteDialog: false,
      deletingPayment: null,
      saving: false,
      loadingApartments: false,
      apartments: [],
      selectedApartment: null,
      startDate: '',
      endDate: '',
    };
  },
  computed: {
    filteredPayments() {
      return this.payments.filter(payment => {
        const byApartment = this.selectedApartment ? payment.invoice.apartment_id === this.selectedApartment : true;
        const byStartDate = this.startDate ? payment.payment_date >= this.startDate : true;
        const byEndDate = this.endDate ? payment.payment_date <= this.endDate : true;
        return byApartment && byStartDate && byEndDate;
      });
    }
  },
  mounted() {
    this.fetchPayments();
    this.fetchApartments();
  },
  methods: {
    async fetchPayments() {
      this.loadingPayments = true;
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/payments');
        this.payments = response.data;
      } catch (error) {
        console.error('Error fetching payments:', error);
      } finally {
        this.loadingPayments = false;
      }
    },
    async fetchApartments() {
      this.loadingApartments = true;
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/apartments');
        this.apartments = response.data;
      } catch (error) {
        console.error('Error fetching apartments:', error);
      } finally {
        this.loadingApartments = false;
      }
    },
    startDelete(payment) {
      this.deletingPayment = payment;
      this.deleteDialog = true;
    },
    async confirmDeletePayment(id) {
      this.saving = true;
      try {
        await axios.delete(`http://127.0.0.1:8000/api/payments/${id}`);
        this.deleteDialog = false;
        this.fetchPayments();
        toast.success('Payment deleted successfully!');
      } catch (error) {
        console.error('Error deleting payment:', error);
        toast.error('Failed to delete payment.');
      } finally {
        this.saving = false;
      }
    },
  },
};
</script>
