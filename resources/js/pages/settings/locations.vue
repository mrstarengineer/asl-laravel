<template>
  <div>
      <b-button v-b-modal.modal-1>New location</b-button>

      <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#exampleModal">

      </button>
      <table class="table">
          <thead>
          <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Status</th>
              <th scope="col">Action</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(location, index) in locations">
              <th scope="row">{{ ++index }}</th>
              <td>{{ location.name }}</td>
              <td>{{ location.status }}</td>
              <td>
                  <button class="btn btn-danger btn-sm" @click="deleteData(location.id)">Delete</button>
                  <button class="btn btn-info btn-sm" @click="editData(location.id)">Edit</button>
              </td>
          </tr>
          </tbody>
      </table>

      <div class="modal show" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Add new location</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <div class="modal-body">
                      <form>
                          <div class="form-group">
                              <label class="col-form-label">Location Name:</label>
                              <input type="text" class="form-control" v-model="form.name">
                          </div>
                          <div class="form-group">
                              <label class="col-form-label">Status:</label>
                              <input type="checkbox" value="1" v-model="form.status">
                          </div>
                      </form>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary" @click.prevent="saveLocation">Save</button>
                  </div>
              </div>
          </div>
      </div>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  name: 'Locations',

  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('locations') }
  },

  data: () => ({
    locations: [],
    loading: false,
    form: {
      name: '',
      status: 1
    }
  }),

  created () {
      this.loadData();
  },

  methods: {
      loadData() {
          this.loading = true;
          axios.get('/api/v1/settings/locations').then(resp => {
              this.loading = false;
              this.locations = resp.data.data;
          }).catch(err => {
              this.loading = false;
          });
      },

      saveLocation() {
          axios.post('/api/v1/settings/locations', this.form).then(resp => {
              this.loading = false;
              this.loadData();
          }).catch(err => {
              this.loading = false;
          });
      },

      editData(id) {
          axios.get('/api/v1/settings/locations/' + id).then(resp => {
              this.loading = false;
              this.form = {
                  name: resp.data.name,
                  status: resp.data.status,
              }
          }).catch(err => {
              this.loading = false;
          });
      },

      deleteData(id) {
          axios.delete('/api/v1/settings/locations/' + id).then(resp => {
              this.loading = false;
              this.loadData();
          }).catch(err => {
              this.loading = false;
          });
      },

      update(id) {
          axios.put('/api/v1/settings/locations/' + id, this.form).then(resp => {
              this.loading = false;
              this.loadData();
          }).catch(err => {
              this.loading = false;
          });
      }
  }
}
</script>
