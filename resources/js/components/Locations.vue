<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Location List</h3>

                <div class="card-tools">
                  
                  <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                      <i class="fa fa-plus-square"></i>
                      Add New
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Address</th>
                      <th>City</th>
                      <th>State</th>
                      <th>Zip</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="location in locations.data" :key="location.id">
                        <td>{{location.id}}</td>
                        <td>{{location.address}}</td>
                        <td>{{location.city}}</td>
                        <td>{{location.state}}</td>
                        <td>{{location.zip}}</td>
                        <td>
                          <a href="#" @click="editModal(location)">
                            <i class="fa fa-edit blue"></i>
                          </a>
                          /
                          <a href="#" @click="deleteLocation(location.id)">
                            <i class="fa fa-trash red"></i>
                          </a>
                        </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="Object.assign({}, locations)" @pagination-change-page="loadLocations"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New Location</h5>
                    <h5 class="modal-title" v-show="editmode">Edit Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateLocation() : createLocation()">
                    <div class="modal-body">
                        <div class="form-group">
                          <label>Address</label>
                          <input v-model="form.address" type="text" name="address"
                              class="form-control" :class="{ 'is-invalid': errors.address }">
                          <span class="error" v-if="errors.address">{{errors.address}}</span>
                        </div>
                        <div class="form-group">
                          <label>City</label>
                          <input v-model="form.city" type="text" name="city"
                              class="form-control" :class="{ 'is-invalid': errors.city }">
                          <span class="error" v-if="errors.city">{{errors.city}}</span>
                        </div>
                        <div class="form-group">
                          <label>State</label>
                          <input v-model="form.state" type="text" name="state"
                              class="form-control" :class="{ 'is-invalid': errors.state }">
                          <span class="error" v-if="errors.state">{{errors.state}}</span>
                        </div>
                        <div class="form-group">
                          <label>Zip</label>
                          <input v-model="form.zip" type="text" name="zip"
                              class="form-control" :class="{ 'is-invalid': errors.zip }">
                          <span class="error" v-if="errors.zip">{{errors.zip}}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
  </section>
</template>

<script>
  export default {
    components: {
    },
    data () {
      return {
        editmode: false,
        locations : [],
        form: new Form({
          id: '',
          address: '',
          city: '',
          state: '',
          zip: ''
        }),
        errors: {}
      }
    },
    methods: {
      loadLocations(page) {
        let url = '';
        if (page) {
          url = `api/location?page=${page}`
        } else {
          url = 'api/location';
        }
        this.$Progress.start();
        axios.get(url).then(response => {
          if (response && response.data && response.data.data) {
            this.locations = response.data.data;
          }
          this.$Progress.finish();
        }).catch(error => {
          console.log(error);
          this.locations = [];
          this.$Progress.finish();
        });
      },
      editModal(location) {
        this.editmode = true;
        this.form.reset();
        this.errors = {};
        $('#addNew').modal('show');
        this.form.fill(location);
      },
      newModal() {
        this.editmode = false;
        this.form.reset();
        this.errors = {};
        $('#addNew').modal('show');
      },
      createLocation() {
        this.$Progress.start();
        this.form.post('api/location')
        .then(response => {
          $('#addNew').modal('hide');
          Toast.fire({
            icon: 'success',
            title: response.data.message
          });
          this.$Progress.finish();
          this.loadLocations();
        })
        .catch(error => {
          this.errors = error.response.data.data;
          Toast.fire({
            icon: 'error',
            title: 'Some error occured! Please try again'
          });
          this.$Progress.finish();
        });
      },
      updateLocation() {
        this.$Progress.start();
        this.form.put('api/location/' + this.form.id)
        .then(response => {
          $('#addNew').modal('hide');
          Toast.fire({
            icon: 'success',
            title: response.data.message
          });
          this.$Progress.finish();
          this.loadLocations();
        })
        .catch(error => {
          this.errors = error.response.data.data;
          Toast.fire({
            icon: 'error',
            title: 'Some error occured! Please try again'
          });
          this.$Progress.finish();
        });
      },
      deleteLocation(id) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!'
        }).then(result => {
          if (result.value) {
            this.form.delete('api/location/' + id).then(()=>{
              Swal.fire(
                'Deleted!',
                'Your file has been deleted.',
                'success'
              );
              this.loadLocations();
            }).catch(error => {
              Swal.fire("Failed!", error.message, "warning");
            });
          }
        })
      },
    },
    mounted() {
      this.$Progress.start();
      this.loadLocations();
      this.$Progress.finish();
    },
  }
</script>
