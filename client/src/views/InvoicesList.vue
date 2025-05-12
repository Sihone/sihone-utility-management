<template>
  <v-container fluid style="margin-top: 60px;">
    
    <v-row>
      <v-col>
        <h2>Invoices</h2>
      </v-col>
      <v-col class="text-right">
        <v-btn color="primary" @click="openPaymentDialog()">Add Payment</v-btn>
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
          item-title="fullName"
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
        </template>
        <template #item.status="{ item }">
          <v-chip
            :color="item.color"
            text-color="white"
            small
          >
            {{ item.status }}
          </v-chip>
        </template>
      </v-data-table>
      
      <v-dialog v-model="paymentDialog" max-width="500px">
        <v-card>
          <v-card-title>Record Payment</v-card-title>
          <v-card-text>
            <v-select
              v-model="paymentForm.apartment_id"
              :items="apartments"
              item-title="fullName"
              item-value="id"
              label="Select Apartment"
              return-object
              :loading="loadingApartments"
            />
            <v-text-field v-model="paymentForm.amount" label="Amount Paid (FCFA)" type="number" />
            <v-text-field v-model="paymentForm.payment_date" label="Payment Date" type="date" />
            <v-textarea v-model="paymentForm.notes" label="Notes (optional)" />
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn text @click="paymentDialog = false">Cancel</v-btn>
            <v-btn color="primary" :loading="savingPayment" @click="savePayment">Save Payment</v-btn>
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
        { title: 'Apartment', value: 'fullName' },
        { title: 'Invoice Date', value: 'invoice_date' },
        { title: 'Consumption (m³)', value: 'consumption' },
        { title: 'Amount (FCFA)', value: 'amount' },
        { title: 'Balance (FCFA)', value: 'balance' },
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
      paymentDialog: false,
      paymentForm: {
        apartment_id: null,
        amount: '',
        payment_date: '',
        notes: '',
      },
      savingPayment: false,
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
        this.invoices = response.data.map((dt) => {
          const total = response.data.reduce((acc, invoice) => {
            if (invoice.apartment.id === dt.apartment.id && invoice.id <= dt.id) {
              return acc + invoice.amount + invoice.registration_fee
            }
            return acc;
          }, 0);
          const balance =  total - dt.apartment.payments.reduce((acc, payment) => acc + payment.amount, 0)
          dt.amount += dt.registration_fee
          return {
            ...dt,
            balance: balance >= 0 ? balance : 0,
            status: balance <= 0 ? 'Paid' : balance > 0  && balance < dt.amount ? 'Partial' : 'Unpaid',
            color: balance <= 0 ? 'green' : balance > 0  && balance < dt.amount ? 'orange' : 'red',
            fullName: dt.apartment.name + ' - ' + dt.apartment.tenant_name,
          }
        })
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
        this.apartments = response.data.map((dt) => {
          return {
            ...dt,
            fullName: dt.name + ' - ' + dt.tenant_name,
          }
        })
      } catch (error) {
        console.error('Error fetching apartments:', error)
      } finally {
        this.loadingApartments = false
      }
    },
    printInvoice(invoice) {
      const popup = window.open('', '', 'width=900,height=650')

      const today = new Date().toLocaleDateString();
      const waterCharge = invoice.consumption * invoice.rate_per_m3_used;

      let paymentDetailsHtml = '';
      for (const [key, value] of Object.entries(this.settings.payment_options)) {
        paymentDetailsHtml += `<strong>${key}:</strong> ${value}<br/>`;
      }
      
      const consumption = invoice.consumption ? (
        `<tr>
          <td>Water Consumption</td>
          <td>${invoice.consumption} m³</td>
          <td style="text-align:right;">${invoice.rate_per_m3_used}</td>
          <td style="text-align:right;">${waterCharge}</td>
        </tr>`
      ) : '';
      
      const fixed_fee = invoice.fixed_fee_used ? (
        `<tr>
          <td>Fixed Fee</td>
          <td>1</td>
          <td style="text-align:right;">${invoice.fixed_fee_used.toLocaleString()} FCFA</td>
          <td style="text-align:right;">${invoice.fixed_fee_used.toLocaleString()} FCFA</td>
        </tr>`
      ) : '';
      
      const registration_fee = invoice.registration_fee? (
        `<tr>
          <td>Registration Fee</td>
          <td>1</td>
          <td style="text-align:right;">${invoice.registration_fee.toLocaleString()} FCFA</td>
          <td style="text-align:right;">${invoice.registration_fee.toLocaleString()} FCFA</td>
        </tr>`
      ) : '';
      
      let totalPaid = 0;
      let payments = '';
      invoice.apartment.payments.forEach((payment) => {
        totalPaid += payment.amount;
        payments += `
          <tr>
            <td>${payment.payment_date}</td>
            <td style="text-align:right;">${payment.amount.toLocaleString()} FCFA</td>
          </tr>
        `
      });
      
      let previousBillBalance = this.invoices.reduce((acc, inv) => {
        if (inv.apartment.id === invoice.apartment.id && inv.id < invoice.id) {
          if (inv.registration_fee) {
            return acc + Number(inv.registration_fee)
          }
          return acc + Number(inv.amount)
        }
        return acc;
      }, 0);
      
      const totalAmount = waterCharge + invoice.fixed_fee_used + invoice.registration_fee;
      
      const totalDue = totalAmount + previousBillBalance - totalPaid;

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
                <th>Rate</th>
                <th>Amount</th>
              </tr>
              ${consumption}
              ${fixed_fee}
              ${registration_fee}
              <tr>
                <td colspan="3">Total Charges:</td>
                <td style="text-align:right;">${totalAmount.toLocaleString()} FCFA</td>
              </tr>
              <tr>
                <td colspan="3">Previous bill balance:</td>
                <td style="text-align:right;">${(previousBillBalance > 0 ? previousBillBalance : 0).toLocaleString()} FCFA</td>
              </tr>
            </table>
          </div>
          
          <div class="payment-info">
            <h3>Payments</h3>
            <table>
              ${payments}
              <tr>
                <th>Total Paid:</th>
                <th style="text-align:right;">${totalPaid.toLocaleString()} FCFA</th>
              </tr>
            </table>
          </div>
          
          <div class="payment-info">
            <h3>Balance</h3>
            <table>
              <tr>
                <th>Total Due:</th>
                <th style="text-align:right;">${totalDue.toLocaleString()} FCFA</th>
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
    },
    openPaymentDialog() {
      this.paymentForm = {
        amount: 0,
        payment_date: new Date().toISOString().split('T')[0],
        notes: '',
      };
      this.paymentDialog = true;
    },

    async savePayment() {
      if (!this.paymentForm.amount || !this.paymentForm.payment_date) {
        alert('Please fill in all required fields.');
        return;
      }

      this.savingPayment = true;
      try {
        const apartmentId = this.paymentForm.apartment_id.id;
        await axios.post(
          'http://127.0.0.1:8000/api/payments',
          {
            ...this.paymentForm,
            apartment_id: apartmentId,
          }
        );
        this.paymentDialog = false;
        this.fetchInvoices(); // Refresh invoices (optional, in case status changes)
      } catch (error) {
        console.error('Error saving payment:', error);
      } finally {
        this.savingPayment = false;
      }
    },

  }
}
</script>
