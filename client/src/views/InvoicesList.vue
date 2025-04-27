<template>
  <v-container>
    <v-card class="mt-5">
      <v-card-title class="text-h5">
        Invoices
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="invoices"
        item-value="id"
        class="elevation-1"
      >
        <template #item.actions="{ item }">
          <v-btn size="small" @click="printInvoice(item)">
            <v-icon>mdi-printer</v-icon>
          </v-btn>
        </template>
      </v-data-table>
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
        { title: 'Start Index', value: 'start_index' },
        { title: 'End Index', value: 'end_index' },
        { title: 'Consumption (m³)', value: 'consumption' },
        { title: 'Amount (FCFA)', value: 'amount' },
        { title: 'Actions', value: 'actions', sortable: false },
      ],
    }
  },
  mounted() {
    this.fetchInvoices()
  },
  methods: {
    async fetchInvoices() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/invoices')
        this.invoices = response.data
      } catch (error) {
        console.error('Error fetching invoices:', error)
      }
    },
    printInvoice(invoice) {
      const popup = window.open('', '', 'width=900,height=650')

      const today = new Date().toLocaleDateString()

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
                <td>1000</td>
                <td>${invoice.consumption * 1000}</td>
              </tr>
              <tr>
                <td>Fixed Service Fee</td>
                <td>1</td>
                <td>5000</td>
                <td>5000</td>
              </tr>
              <tr>
                <th colspan="3" style="text-align:right;">Total Due:</th>
                <th>${invoice.amount} FCFA</th>
              </tr>
            </table>
          </div>

          <div class="payment-info">
            <h3>Payment Instructions</h3>
            <p>
              <strong>Bank Account:</strong> 123 456 7890<br/>
              <strong>Mobile Money:</strong> MTN MoMo 6xx xxx xxx<br/>
              <strong>Reference:</strong> Invoice ${invoice.id}
            </p>
          </div>

          <div class="footer">
            Thank you for trusting our services. For any inquiries, contact us at info@utilityservices.com or call +237 6xx xxx xxx.
          </div>

          <script>window.print()<\/script>

        </body>
        </html>
      `;

      popup.document.write(invoiceHtml);
      popup.document.close();
    }

  }
}
</script>
