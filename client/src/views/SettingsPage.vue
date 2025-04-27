<template>
  <v-container fluid style="margin-top: 60px;">
    <v-row>
      <v-col>
        <h2>Settings</h2>
      </v-col>
    </v-row>
    <v-card class="mt-5" :loading="loading || saving">
      <v-card-text>
        <h3 class="mb-3">Rates</h3>
        <v-row>
          <v-text-field v-model="settings.fixed_fee" label="Fixed Service Fee (FCFA)" type="number" class="mr-2" />
          <v-text-field v-model="settings.rate_per_m3" label="Rate per m³ (FCFA)" type="number" class="mr-2" />
        </v-row>
        

        <v-divider class="my-5"></v-divider>

        <h3 class="mb-3">Payment Options</h3>
        <v-row v-for="(value, key, index) in paymentOptions" :key="index">
          <v-text-field
            v-model="paymentOptions[key]"
            :label="key"
            class="flex-grow-1 mr-2"
          />
          <v-btn icon color="red" class="ml-2" @click="removePaymentOption(key)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </v-row>
        
        <v-divider class="my-5"></v-divider>

        
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn text color="secondary" @click="addPaymentOption" :disabled="saving">Add Payment Option</v-btn>
        <v-btn color="primary" @click="saveSettings" :disabled="saving">Save Settings</v-btn>
      </v-card-actions>
    </v-card>
    <v-dialog v-model="dialogAddOption" max-width="500px">
      <v-card>
        <v-card-title>Add Payment Option</v-card-title>
        <v-card-text>
          <v-text-field
            v-model="newPaymentKey"
            label="Payment Method Name (e.g., Bank Account, MTN MoMo)"
            autofocus
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="dialogAddOption = false">Cancel</v-btn>
          <v-btn color="primary" text @click="confirmAddPaymentOption">Add</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-container>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast();

export default {
  name: 'SettingsPage',
  data() {
    return {
      settings: {
        fixed_fee: '',
        rate_per_m3: '',
      },
      paymentOptions: {},
      loading: false,
      saving: false,
      dialogAddOption: false,
      newPaymentKey: '',
    }
  },
  mounted() {
    this.fetchSettings()
  },
  methods: {
    async fetchSettings() {
      this.loading = true
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/settings')
        this.settings.fixed_fee = response.data.fixed_fee
        this.settings.rate_per_m3 = response.data.rate_per_m3
        this.paymentOptions = JSON.parse(response.data.payment_options || '{}')
      } catch (error) {
        console.error('Error loading settings:', error)
      } finally {
        this.loading = false
      }
    },
    addPaymentOption() {
      this.newPaymentKey = '';
      this.dialogAddOption = true;
    },
    confirmAddPaymentOption() {
      const key = this.newPaymentKey.trim();
      if (key) {
        this.paymentOptions[key] = '';
        this.dialogAddOption = false;
      }
    },
    removePaymentOption(key) {
      delete this.paymentOptions[key]
    },
    async saveSettings() {
      this.saving = true
      try {
        await axios.post('http://127.0.0.1:8000/api/settings', {
          fixed_fee: this.settings.fixed_fee,
          rate_per_m3: this.settings.rate_per_m3,
          payment_options: this.paymentOptions,
        })
        toast.success('Settings saved successfully')
        
      } catch (error) {
        console.error('Error saving settings:', error)
        toast.error('Failed to save settings.')
      } finally {
        this.saving = false
      }
    }
  }
}
</script>
