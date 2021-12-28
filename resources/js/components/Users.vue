<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header">
                <h3 class="card-title">User List</h3>

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
                      <th>Type</th>
                      <th>First Name</th>
                      <th>Last Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Email Verified?</th>
                      <th>Created</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="user in users.data" :key="user.id">

                      <td>{{user.id}}</td>
                      <td class="text-capitalize">{{user.type}}</td>
                      <td class="text-capitalize">{{user.first_name}}</td>
                      <td class="text-capitalize">{{user.last_name}}</td>
                      <td>{{user.email}}</td>
                      <td>{{user.phone}}</td>
                      <td :inner-html.prop="user.email_verified_at | yesno"></td>
                      <td>{{user.created_at}}</td>

                      <td>

                        <a href="#" @click="editModal(user)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteUser(user.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="users" @pagination-change-page="loadUsers"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Create New User</h5>
                    <h5 class="modal-title" v-show="editmode">Update User's Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? updateUser() : createUser()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>First Name</label>
                            <input v-model="form.first_name" type="text" name="first_name"
                                class="form-control" :class="{ 'is-invalid': errors.first_name }">
                            <span class="error" v-if="errors.first_name">{{errors.first_name}}</span>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input v-model="form.last_name" type="text" name="last_name"
                                class="form-control" :class="{ 'is-invalid': errors.last_name }">
                            <span class="error" v-if="errors.last_name">{{errors.last_name}}</span>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input v-model="form.email" type="text" name="email"
                                class="form-control" :class="{ 'is-invalid': errors.email }">
                            <span class="error" v-if="errors.email">{{errors.email}}</span>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input v-model="form.phone" type="text" name="phone"
                                class="form-control" :class="{ 'is-invalid': errors.phone }">
                            <span class="error" v-if="errors.phone">{{errors.phone}}</span>
                        </div>
                    
                        <div class="form-group">
                            <label>Password</label>
                            <input v-model="form.password" type="password" name="password"
                                class="form-control" :class="{ 'is-invalid': errors.password }" autocomplete="false">
                            <span class="error" v-if="errors.password">{{errors.password}}</span>
                        </div>
                    
                        <div class="form-group">
                            <label>User Role</label>
                            <select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': errors.type }">
                                <option value="">Select User Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">Standard User</option>
                            </select>
                            <span class="error" v-if="errors.type">{{errors.type}}</span>
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
        data () {
            return {
                editmode: false,
                users : {},
                form: new Form({
                    id : '',
                    type : '',
                    first_name: '',
                    last_name: '',
                    email: '',
                    phone: '',
                    password: '',
                    email_verified_at: '',
                }),
                errors: {}
            }
        },
        methods: {
            loadUsers(page) {
                let url = '';
                if (page) {
                    url = `api/location?user=${page}`
                } else {
                    url = 'api/user';
                }
                if(this.$gate.isAdmin()) {
                    this.$Progress.start();
                    axios.get(url).then(response => {
                        if (response && response.data && response.data.data) {
                            this.users = response.data.data;
                        }
                        this.$Progress.finish();
                    }).catch(error => {
                        console.log(error);
                        this.users = [];
                        this.$Progress.finish();
                    });
                }
            },
            updateUser() {
                this.$Progress.start();
                this.form.put('api/user/' + this.form.id)
                .then(response => {
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                    this.loadUsers();
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
            editModal(user) {
                this.editmode = true;
                this.form.reset();
                this.errors = {};
                $('#addNew').modal('show');
                this.form.fill(user);
            },
            newModal() {
                this.editmode = false;
                this.form.reset();
                this.errors = {};
                $('#addNew').modal('show');
            },
            deleteUser(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then(result => {
                    if (result.value) {
                        this.form.delete('api/user/' + id).then(() => {
                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );
                            this.loadUsers();
                        }).catch(error => {
                            Swal.fire("Failed!", error.message, "warning");
                        });
                    }
                });
            },
            createUser() {
                this.$Progress.start();
                this.form.post('api/user')
                .then(response => {
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: response.data.message
                    });
                    this.$Progress.finish();
                    this.loadUsers();
                })
                .catch(error => {
                    this.errors = error.response.data.data;
                    Toast.fire({
                        icon: 'error',
                        title: 'Some error occured! Please try again'
                    });
                    this.$Progress.finish();
                });
            }
        },
        mounted() {
            console.log('User Component mounted.');
            this.$Progress.start();
            this.loadUsers();
            this.$Progress.finish();
        },
    }
</script>
