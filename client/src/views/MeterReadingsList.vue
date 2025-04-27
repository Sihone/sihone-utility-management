<template>
  <v-container fluid style="margin-top: 60px;">
    <v-row>
      <v-col>
        <h2>Meter Readings</h2>
      </v-col>
      <v-col class="text-right">
        <v-btn color="primary" @click="createDialog = true">Add Meter Reading</v-btn>
      </v-col>
    </v-row>
    
    <!-- full screen horizontal line -->
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
    
    <v-card class="mt-5">
      <!-- Data Table -->
      <v-data-table
        :headers="headers"
        :items="filteredMeterReadings"
        item-value="id"
        class="elevation-1"
        :loading="loadingMeterReadings"
      >
        <template #item.actions="{ item }">
          <!-- <v-btn icon size="small" @click="startEdit(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn> -->
          <v-btn icon size="small" color="red" @click="startDelete(item)">
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
          <v-btn text @click="createDialog = false" :disabled="saving">Cancel</v-btn>
          <v-btn color="primary" @click="addMeterReading" :loading="saving">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    
    <!-- Delete Meter Reading Modal -->
    <v-dialog v-model="deleteDialog" max-width="400px">
      <v-card>
        <v-card-title class="headline">Delete Meter Reading</v-card-title>
        <v-card-text>Are you sure you want to delete the meter reading?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="deleteDialog = false" :disabled="saving">Cancel</v-btn>
          <v-btn color="primary" text @click="confirmDeleteMeterReading(editingMeterReading.id)" :loading="saving">Yes, Delete</v-btn>
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
          <v-btn color="primary" @click="updateMeterReading(editingMeterReading.id)">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog> -->
  </v-container>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast();

export default {
  name: 'MeterReadingsList',
  data() {
    return {
      meterReadings: [],
      apartments: [],
      createDialog: false,
      editDialog: false,
      editingMeterReading: null,
      saving: false,
      deleteDialog: false,
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
      loadingMeterReadings: false,
      loadingApartments: false,
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
      this.loadingMeterReadings = true
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/meter-readings')
        this.meterReadings = response.data
      } catch (error) {
        console.error('Error fetching meter readings:', error)
      } finally {
        this.loadingMeterReadings = false
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
    async addMeterReading() {
      this.saving = true
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
      } finally {
        this.saving = false
      }
    },
    startDelete(meterReading) {
      this.editingMeterReading = meterReading
      this.deleteDialog = true
    },
    async confirmDeleteMeterReading(id) {
      this.saving = true
      try {
        await axios.delete(`http://127.0.0.1:8000/api/meter-readings/${id}`)
        this.deleteDialog = false
        this.fetchMeterReadings()
        toast.success('Meter reading deleted successfully!')
      } catch (error) {
        console.error('Error deleting meter reading:', error)
        toast.error('Failed to delete meter reading.')
      } finally {
        this.saving = false
      }
    },
    // startEdit(meterReading) {
    //   this.editingMeterReading = meterReading
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
    clearFilters() {
      this.selectedApartment = null
      this.startDate = ''
      this.endDate = ''
    }
  }
}
</script>
