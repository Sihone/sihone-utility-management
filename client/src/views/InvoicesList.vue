<template>
  <v-container fluid style="margin-top: 60px;">
    
    <v-row>
      <v-col>
        <h2>Invoices</h2>
      </v-col>
    </v-row>
    
    <!-- full screen horizontal line -->
    <v-divider class="my-4" />
    
    <!-- Filters -->
    <v-row class="mb-4" dense>
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

    <v-card class="mt-5">
      <v-data-table
        :headers="headers"
        :items="filteredInvoices"
        item-value="id"
        class="elevation-1"
        :loading="loadingInvoices || loadingSettings"
      >
        <template #item.actions="{ item }" class="d-flex align-center">
          <v-btn size="small" @click="printInvoice(item)" class="mr-2">
            <v-icon>mdi-printer</v-icon>
          </v-btn>
          <v-btn size="small" color="success" v-if="item.status === 'Pending'" @click="openMarkDialog(item.id, 'Paid')">
            <v-icon>mdi-cash-check</v-icon>
          </v-btn>
          <v-btn size="small" color="error" v-if="item.status !== 'Pending'" @click="openMarkDialog(item.id, 'Pending')">
            <v-icon>mdi-cash-remove</v-icon>
          </v-btn>
        </template>
        <template #item.status="{ item }">
          <v-chip
            :color="item.status === 'Paid' ? 'green' : 'orange'"
            text-color="white"
            small
          >
            {{ item.status }}
          </v-chip>
        </template>
      </v-data-table>
      <v-dialog v-model="markDialog" max-width="400px">
        <v-card>
          <v-card-title class="headline">
            Confirm Status Change
          </v-card-title>

          <v-card-text>
            Are you sure you want to mark this invoice as <strong>{{ markAs }}</strong>?
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn text @click="markDialog = false" :disabled="saving">Cancel</v-btn>
            <v-btn color="primary" text @click="confirmMarkStatus" :loading="saving">Yes, Confirm</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

    </v-card>
  </v-container>
</template>

<script>
import axios from 'axios'

export default {
  name: 'InvoicesList',
  data() {
    return {
      invoices: [],
      headers: [
        { title: 'Apartment', value: 'apartment.name' },
        { title: 'Invoice Date', value: 'invoice_date' },
        { title: 'Consumption (m³)', value: 'consumption' },
        { title: 'Amount (FCFA)', value: 'amount' },
        { title: 'Status', value: 'status' },
        { title: 'Actions', value: 'actions', sortable: false },
      ],
      selectedApartment: null,
      startDate: '',
      endDate: '',
      loadingInvoices: false,
      loadingSettings: false,
      loadingApartments: false,
      markDialog: false,
      selectedInvoiceId: null,
      markAs: '', // 'Paid' or 'Pending'
      saving: false,
    }
  },
  computed: {
    filteredInvoices() {
      return this.invoices.filter(invoice => {
        const byApartment = this.selectedApartment ? invoice.apartment.id === this.selectedApartment : true;
        const byStartDate = this.startDate ? invoice.invoice_date >= this.startDate : true;
        const byEndDate = this.endDate ? invoice.invoice_date <= this.endDate : true;
        return byApartment && byStartDate && byEndDate;
      });
    }
  },
  mounted() {
    this.fetchInvoices();
    this.fetchSettings();
    this.fetchApartments();
  },
  methods: {
    async fetchInvoices() {
      this.loadingInvoices = true
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/invoices')
        this.invoices = response.data
      } catch (error) {
        console.error('Error fetching invoices:', error)
      } finally {
        this.loadingInvoices = false
      }
    },
    async fetchSettings() {
      this.loadingSettings = true
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/settings')
        this.settings = {};
        this.settings.payment_options = JSON.parse(response.data.payment_options || '{}')
      } catch (error) {
        console.error('Error loading settings:', error)
      } finally {
        this.loadingSettings = false
      }
    },

    async fetchApartments() {
      this.loadingApartments = true
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/apartments')
        this.apartments = response.data
      } catch (error) {
        console.error('Error fetching apartments:', error)
      } finally {
        this.loadingApartments = false
      }
    },
    openMarkDialog(invoiceId, status) {
      this.selectedInvoiceId = invoiceId;
      this.markAs = status;
      this.markDialog = true;
    },
    async confirmMarkStatus() {
      this.saving = true;
      try {
        await axios.put(`http://127.0.0.1:8000/api/invoices/${this.selectedInvoiceId}`, { status: this.markAs });
        this.fetchInvoices();
        this.markDialog = false;
      } catch (error) {
        console.error('Error updating invoice status:', error);
      } finally {
        this.saving = false;
      }
    },
    printInvoice(invoice) {
      const popup = window.open('', '', 'width=900,height=650')

      const today = new Date().toLocaleDateString();
      const waterCharge = invoice.consumption * invoice.rate_per_m3_used;
      const totalDue = waterCharge + invoice.fixed_fee_used;

      let paymentDetailsHtml = '';
      for (const [key, value] of Object.entries(this.settings.payment_options)) {
        paymentDetailsHtml += `<strong>${key}:</strong> ${value}<br/>`;
      }

      const invoiceHtml = `
        <html>
        <head>
          <title>Invoice</title>
          <style>
            body { font-family: Arial, sans-serif; padding: 30px; }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { margin: 0; }
            .details, .charges, .payment-info { margin-top: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f4f4f4; }
            .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #666; }
          </style>
        </head>
        <body>

          <div class="header">
            <h1>Utility Management Services</h1>
            <p>INVOICE</p>
          </div>

          <div class="details">
            <strong>Invoice Date:</strong> ${today} <br/>
            <strong>Apartment:</strong> ${invoice.apartment.name} <br/>
          </div>

          <div class="charges">
            <h3>Meter Readings</h3>
            <table>
              <tr>
                <th>Start Index</th>
                <th>End Index</th>
                <th>Consumption (m³)</th>
              </tr>
              <tr>
                <td>${invoice.start_index}</td>
                <td>${invoice.end_index}</td>
                <td>${invoice.consumption}</td>
              </tr>
            </table>

            <h3>Charges</h3>
            <table>
              <tr>
                <th>Description</th>
                <th>Unit</th>
                <th>Rate (FCFA)</th>
                <th>Amount (FCFA)</th>
              </tr>
              <tr>
                <td>Water Consumption</td>
                <td>${invoice.consumption} m³</td>
                <td>${invoice.rate_per_m3_used}</td>
                <td>${waterCharge}</td>
              </tr>
              <tr>
                <td>Fixed Service Fee</td>
                <td>1</td>
                <td>${invoice.fixed_fee_used}</td>
                <td>${invoice.fixed_fee_used}</td>
              </tr>
              <tr>
                <th colspan="3" style="text-align:right;">Total Due:</th>
                <th>${totalDue} FCFA</th>
              </tr>
            </table>
          </div>

          <div class="payment-info">
            <h3>Payment Instructions</h3>
            <p>
              ${paymentDetailsHtml}
            </p>
          </div>

          <div class="footer">
            Thank you for trusting our services. For any inquiries, contact us at 696509794.
          </div>

          <script>window.print()<\/script>

        </body>
        </html>
      `;

      popup.document.write(invoiceHtml);
      popup.document.close();
    },
    clearFilters() {
      this.selectedApartment = null
      this.startDate = ''
      this.endDate = ''
    }
  }
}
</script>
