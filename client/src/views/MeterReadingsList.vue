<template>
  <v-container>
    <v-card class="mt-5">
      <v-card-title class="text-h5">
        Meter Readings
        <v-spacer />
        <v-btn color="primary" icon @click="createDialog = true">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>

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
          <v-btn text color="primary" @click="clearFilters">Clear Filters</v-btn>
        </v-col>
      </v-row>

      <!-- Data Table -->
      <v-data-table
        :headers="headers"
        :items="filteredMeterReadings"
        item-value="id"
        class="elevation-1"
      >
        <template #item.actions="{ item }">
          <!-- <v-btn icon size="small" @click="startEdit(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn> -->
          <v-btn icon size="small" color="red" @click="deleteMeterReading(item.id)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <!-- Create Meter Reading Modal -->
    <v-dialog v-model="createDialog" max-width="500px">
      <v-card>
        <v-card-title>Add New Meter Reading</v-card-title>
        <v-card-text>
          <v-select
            v-model="newMeterReading.apartment_id"
            :items="apartments"
            item-title="name"
            item-value="id"
            label="Select Apartment"
            return-object
          />
          <v-text-field v-model="newMeterReading.reading_date" label="Reading Date" type="date" />
          <v-text-field v-model="newMeterReading.meter_index" label="Meter Index" type="number" />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="createDialog = false">Cancel</v-btn>
          <v-btn color="primary" @click="addMeterReading">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Edit Meter Reading Modal -->
    <!-- <v-dialog v-model="editDialog" max-width="500px">
      <v-card>
        <v-card-title>Edit Meter Reading</v-card-title>
        <v-card-text>
          <v-select
            v-model="editedMeterReading.apartment_id"
            :items="apartments"
            item-title="name"
            item-value="id"
            label="Select Apartment"
            return-object
          />
          <v-text-field v-model="editedMeterReading.reading_date" label="Reading Date" type="date" />
          <v-text-field v-model="editedMeterReading.meter_index" label="Meter Index" type="number" />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="editDialog = false">Cancel</v-btn>
          <v-btn color="primary" @click="updateMeterReading(editingMeterReadingId)">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog> -->
  </v-container>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  name: 'MeterReadingsList',
  data() {
    return {
      meterReadings: [],
      apartments: [],
      createDialog: false,
      editDialog: false,
      editingMeterReadingId: null,
      newMeterReading: {
        apartment_id: null,
        reading_date: new Date().toISOString().split('T')[0],
        meter_index: '',
      },
      editedMeterReading: {
        apartment_id: null,
        reading_date: '',
        meter_index: '',
      },
      selectedApartment: null,
      startDate: '',
      endDate: '',
      headers: [
        { title: 'Apartment', value: 'apartment.name' },
        { title: 'Reading Date', value: 'reading_date' },
        { title: 'Meter Index', value: 'meter_index' },
        { title: 'Actions', value: 'actions', sortable: false },
      ],
    }
  },
  computed: {
    filteredMeterReadings() {
      return this.meterReadings.filter(reading => {
        const byApartment = this.selectedApartment ? reading.apartment.id === this.selectedApartment : true;
        const byStartDate = this.startDate ? reading.reading_date >= this.startDate : true;
        const byEndDate = this.endDate ? reading.reading_date <= this.endDate : true;
        return byApartment && byStartDate && byEndDate;
      });
    }
  },
  mounted() {
    this.fetchMeterReadings()
    this.fetchApartments()
  },
  methods: {
    async fetchMeterReadings() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/meter-readings')
        this.meterReadings = response.data
      } catch (error) {
        console.error('Error fetching meter readings:', error)
      }
    },
    async fetchApartments() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/apartments')
        this.apartments = response.data
      } catch (error) {
        console.error('Error fetching apartments:', error)
      }
    },
    async addMeterReading() {
      const toast = useToast()
      try {
        await axios.post('http://127.0.0.1:8000/api/meter-readings', {
          apartment_id: this.newMeterReading.apartment_id.id || this.newMeterReading.apartment_id,
          reading_date: this.newMeterReading.reading_date,
          meter_index: this.newMeterReading.meter_index,
        })
        this.newMeterReading = { apartment_id: null, reading_date: '', meter_index: '' }
        this.createDialog = false
        this.fetchMeterReadings()
        toast.success('Meter reading added successfully!')
      } catch (error) {
        console.error('Error adding meter reading:', error)
        toast.error('Failed to add meter reading.')
      }
    },
    // startEdit(meterReading) {
    //   this.editingMeterReadingId = meterReading.id
    //   this.editedMeterReading = {
    //     apartment_id: meterReading.apartment,
    //     reading_date: meterReading.reading_date,
    //     meter_index: meterReading.meter_index,
    //   }
    //   this.editDialog = true
    // },
    // async updateMeterReading(id) {
    //   const toast = useToast()
    //   try {
    //     await axios.put(`http://127.0.0.1:8000/api/meter-readings/${id}`, {
    //       apartment_id: this.editedMeterReading.apartment_id.id || this.editedMeterReading.apartment_id,
    //       reading_date: this.editedMeterReading.reading_date,
    //       meter_index: this.editedMeterReading.meter_index,
    //     })
    //     this.editDialog = false
    //     this.fetchMeterReadings()
    //     toast.success('Meter reading updated successfully!')
    //   } catch (error) {
    //     console.error('Error updating meter reading:', error)
    //     toast.error('Failed to update meter reading.')
    //   }
    // },
    async deleteMeterReading(id) {
      const toast = useToast()
      if (!confirm('Are you sure you want to delete this meter reading?')) {
        return
      }
      try {
        await axios.delete(`http://127.0.0.1:8000/api/meter-readings/${id}`)
        this.fetchMeterReadings()
        toast.success('Meter reading deleted successfully!')
      } catch (error) {
        console.error('Error deleting meter reading:', error)
        toast.error('Failed to delete meter reading.')
      }
    },
    clearFilters() {
      this.selectedApartment = null
      this.startDate = ''
      this.endDate = ''
    }
  }
}
</script>
