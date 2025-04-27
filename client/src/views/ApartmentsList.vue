<template>
  <v-container v-if="loading" class="flex justify-center items-center min-h-[300px]">
    <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
  </v-container>
  <v-container v-else>
    <v-card class="mt-5">
      <v-card-title class="text-h5">
        Apartments
        <v-spacer />
        <v-btn color="primary" icon @click="createDialog = true">
          <v-icon>mdi-plus</v-icon>
        </v-btn>
      </v-card-title>

      <v-data-table
        :headers="headers"
        :items="apartments"
        class="elevation-1"
      >
        <template #item.actions="{ item }">
          <v-btn icon size="small" @click="startEdit(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn icon size="small" color="red" @click="deleteApartment(item.id)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <!-- Create Apartment Modal -->
    <v-dialog v-model="createDialog" max-width="500px">
      <v-card>
        <v-card-title>Add New Apartment</v-card-title>
        <v-card-text>
          <v-text-field v-model="newApartment.name" label="Apartment Name" />
          <v-text-field v-model="newApartment.location" label="Location" />
          <v-text-field v-model="newApartment.tenant_name" label="Tenant Name" />
          <v-text-field v-model="newApartment.tenant_phone" label="Tenant Phone" />
          <v-text-field v-model="newApartment.tenant_email" label="Tenant Email" />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="createDialog = false">Cancel</v-btn>
          <v-btn color="primary" @click="addApartment">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Edit Apartment Modal -->
    <v-dialog v-model="editDialog" max-width="500px">
      <v-card>
        <v-card-title>Edit Apartment</v-card-title>
        <v-card-text>
          <v-text-field v-model="editedApartment.name" label="Apartment Name" />
          <v-text-field v-model="editedApartment.location" label="Location" />
          <v-text-field v-model="editedApartment.tenant_name" label="Tenant Name" />
          <v-text-field v-model="editedApartment.tenant_phone" label="Tenant Phone" />
          <v-text-field v-model="editedApartment.tenant_email" label="Tenant Email" />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="editDialog = false">Cancel</v-btn>
          <v-btn color="primary" @click="updateApartment(editingApartmentId)">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-container>
</template>

<script>
import axios from 'axios';
import { useToast } from 'vue-toastification'

const toast = useToast();

export default {
  name: 'ApartmentsList',
  data() {
    return {
      apartments: [],
      createDialog: false,
      editDialog: false,
      editingApartmentId: null,
      loading: false,
      newApartment: {
        name: '',
        location: '',
        tenant_name: '',
        tenant_phone: '',
        tenant_email: '',
      },
      editedApartment: {
        name: '',
        location: '',
        tenant_name: '',
        tenant_phone: '',
        tenant_email: '',
      },
      headers: [
        { title: 'Name', value: 'name' },
        { title: 'Location', value: 'location' },
        { title: 'Tenant Name', value: 'tenant_name' },
        { title: 'Tenant Phone', value: 'tenant_phone' },
        { title: 'Actions', value: 'actions', sortable: false },
      ],
    };
  },
  mounted() {
    this.fetchApartments();
  },
  methods: {
    async fetchApartments() {
      this.loading = true;
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/apartments');
        this.apartments = response.data;
      } catch (error) {
        console.error('Error fetching apartments:', error);
      } finally {
        this.loading = false;
      }
    },
    async addApartment() {
      try {
        await axios.post('http://127.0.0.1:8000/api/apartments', this.newApartment);
        this.newApartment = { name: '', location: '', tenant_name: '', tenant_phone: '', tenant_email: '' };
        this.createDialog = false;
        this.fetchApartments();
        toast.success('Apartment added successfully!');
      } catch (error) {
        console.error('Error adding apartment:', error);
        toast.error('Failed to add apartment.');
      }
    },
    startEdit(apartment) {
      this.editingApartmentId = apartment.id;
      this.editedApartment = { ...apartment };
      this.editDialog = true;
    },
    async updateApartment(id) {
      try {
        await axios.put(`http://127.0.0.1:8000/api/apartments/${id}`, this.editedApartment);
        this.editDialog = false;
        this.fetchApartments();
        toast.success('Apartment updated successfully!');
      } catch (error) {
        console.error('Error updating apartment:', error);
        toast.error('Failed to update apartment.');
      }
    },
    async deleteApartment(id) {
      if (!confirm('Are you sure you want to delete this apartment?')) {
        return;
      }
      try {
        await axios.delete(`http://127.0.0.1:8000/api/apartments/${id}`);
        this.fetchApartments();
        toast.success('Apartment deleted successfully!');
      } catch (error) {
        console.error('Error deleting apartment:', error);
        toast.error('Failed to delete apartment.');
      }
    },
  }
};
</script>
