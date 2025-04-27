<template>
  <v-container>
    <v-card class="mt-5">
      <v-card-title class="text-h5">Settings</v-card-title>

      <v-card-text>
        <v-text-field v-model="settings.fixed_fee" label="Fixed Service Fee (FCFA)" type="number" />
        <v-text-field v-model="settings.rate_per_m3" label="Rate per m³ (FCFA)" type="number" />

        <v-divider class="my-5"></v-divider>

        <h3>Payment Options</h3>
        <div v-for="(value, key, index) in paymentOptions" :key="index" class="d-flex align-center mb-3">
          <v-text-field
            v-model="paymentOptions[key]"
            :label="key"
            class="flex-grow-1 mr-2"
          />
          <v-btn icon color="red" @click="removePaymentOption(key)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </div>

        <v-btn text color="primary" @click="addPaymentOption">Add Payment Option</v-btn>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="primary" @click="saveSettings">Save Settings</v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script>
import axios from 'axios'

export default {
  name: 'SettingsPage',
  data() {
    return {
      settings: {
        fixed_fee: '',
        rate_per_m3: '',
      },
      paymentOptions: {},
    }
  },
  mounted() {
    this.fetchSettings()
  },
  methods: {
    async fetchSettings() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/settings')
        this.settings.fixed_fee = response.data.fixed_fee
        this.settings.rate_per_m3 = response.data.rate_per_m3
        this.paymentOptions = JSON.parse(response.data.payment_options || '{}')
      } catch (error) {
        console.error('Error loading settings:', error)
      }
    },
    addPaymentOption() {
      const key = prompt('Enter payment method name:')
      if (key) {
        this.paymentOptions[key] = ''
      }
    },
    removePaymentOption(key) {
      delete this.paymentOptions[key]
    },
    async saveSettings() {
      try {
        await axios.post('http://127.0.0.1:8000/api/settings', {
          fixed_fee: this.settings.fixed_fee,
          rate_per_m3: this.settings.rate_per_m3,
          payment_options: this.paymentOptions,
        })
        alert('Settings saved successfully')
      } catch (error) {
        console.error('Error saving settings:', error)
      }
    }
  }
}
</script>
