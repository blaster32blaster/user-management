<template>
    <div style="border-bottom: .5rem ridge gainsboro; margin-bottom: 2rem">
        <div class="flex-container">
            <div class="row client-header">
                <div class="col-sm-6 col-md-2">Client Id</div>
                <div class="col-sm-6 col-md-2">Name</div>
                <div class="col-sm-6 col-md-2">Secret</div>
                <div class="col-sm-6 col-md-2">Address</div>
                <div class="col-sm-6 col-md-2">Password Client</div>
            </div>
        </div>
        <div  class="flex-container row">
        <!-- ID -->
            <div class="col-xs-6 col-md-2" style="vertical-align: middle;">
                {{ client.id }}
            </div>

            <!-- Name -->
            <div class="col-sm-6 col-md-2" style="vertical-align: middle;">
                {{ client.name }}
            </div>

            <!-- Secret -->
            <div class="col-sm-6 col-md-2" style="vertical-align: middle;">
                <code>{{ client.secret }}</code>
            </div>

            <div class="col-sm-6 col-md-2" style="vertical-align: middle;">
                <code>{{ client.redirect}}</code>
            </div>

            <div class="col-sm-6 col-md-2" style="vertical-align: middle;">
                <code>{{ client.password_client }}</code>
            </div>

        </div>
        <div  class="flex-container row">

            <!--manage users-->
            <div class="col-sm-3" :class="{ active :editUsers[index]}" v-if="client.admin" style="vertical-align: middle;">
                <a class="action-link" tabindex="-1" @click="manageUsers(client, index)">
                    Manage Users
                </a>
            </div>

            <!--check logs-->
            <div class="col-sm-3" v-if="client.admin" style="vertical-align: middle;">
                <a class="action-link" tabindex="-1" @click="checkLogs(client)">
                    View Client Logs
                </a>
            </div>

            <!-- Edit Button -->
            <div class="col-sm-3" style="vertical-align: middle;">
                <a class="action-link" tabindex="-1" @click="edit(client)">
                    Edit
                </a>
            </div>

            <!-- Delete Button -->
            <div class="col-sm-3" style="vertical-align: middle;">
                <a class="action-link text-danger" @click="destroy(client)">
                    Delete
                </a>
            </div>
        </div>
            <div class="active" v-if="editUsers[index]">
                <div class="flex-container row" style="border-bottom: .5rem ridge gainsboro; padding: 2rem">
                    <div class="col-xs-4 col-md-4" style="vertical-align: middle;">
                        Invite New User
                    </div>
                    <div class="col-xs-6 col-md-6" style="vertical-align: middle;">
                        <input type="text" v-model="newEmail" placeholder="Email Address"/>
                    </div>
                    <div class="col-xs-2 col-md-2" style="vertical-align: middle;">
                        <button @click="inviteUser(client)" type="button">Send Invite</button>
                    </div>
                </div>
                <div class="flex-container row" style="padding:1rem;">
                    <div class="col-xs-6 col-md-3" style="vertical-align: middle;">
                        Name
                    </div>
                    <div class="col-xs-6 col-md-3" style="vertical-align: middle;">
                        Email
                    </div>
                    <div class="col-xs-6 col-md-offset-3" style="vertical-align: middle;">
                        Role
                    </div>
                </div>
                <div v-for="(role, index) in client.roles" style="padding:1rem;">
                    <role
                            v-on:persistRoles="updateRoles()"
                            v-on:deletedRole="updateRoles()"
                            :role="role"
                            :index="index"
                            :clientId="client.id"
                    />
                </div>
            </div>

        <!-- Edit Client Modal -->
        <div class="modal fade" :id="modalId" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            Edit Client
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!-- Form Errors -->
                        <div class="alert alert-danger" v-if="editForm.errors.length > 0">
                            <p class="mb-0"><strong>Whoops!</strong> Something went wrong!</p>
                            <br>
                            <ul>
                                <li v-for="error in editForm.errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <!-- Edit Client Form -->
                        <form role="form">
                            <!-- Name -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Name</label>

                                <div class="col-md-9">
                                    <input id="edit-client-name" type="text" class="form-control"
                                           @keyup.enter="update" v-model="editForm.name">

                                    <span class="form-text text-muted">
                                        Something your users will recognize and trust.
                                    </span>
                                </div>
                            </div>

                            <!-- Redirect URL -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Redirect URL</label>

                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="redirect"
                                           @keyup.enter="update" v-model="editForm.redirect">

                                    <span class="form-text text-muted">
                                        Your application's authorization callback URL.
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                        <button type="button" class="btn btn-primary" @click="update">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
          client: {

          },
          index: {

          }
        },
        /*
        * The component's data.
        */
        data() {
            return {
                createForm: {
                    errors: [],
                    name: '',
                    redirect: ''
                },

                editForm: {
                    errors: [],
                    name: '',
                    redirect: ''
                },

                modalId: this.index + '-modal-edit-client',
                editUsers: [],
                roles: {},
                newEmail: '',
            };
        },
        /**
         * Prepare the component (Vue 1.x).
         */
        ready() {
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
        },

        methods: {
            /**
             * Edit the given client.
             */
            edit(client) {
                this.editForm.id = client.id;
                this.editForm.name = client.name;
                this.editForm.redirect = client.redirect;
                let clientWithHash = '#'+ this.modalId
                console.log(clientWithHash)

                $(clientWithHash).modal('show');
            },

            updateRoles() {
                this.manageUsers(this.client, this.index)
            },

            /**
             * Update the client being edited.
             */
            update() {
                let clientWithHash = '#'+ this.modalId
                this.persistClient(
                    'put', '/oauth/clients/' + this.editForm.id,
                    this.editForm, clientWithHash
                );
            },
            //
            /**
             * Persist the client to storage using the given form.
             */
            persistClient(method, uri, form, modal) {
                form.errors = [];

                axios[method](uri, form)
                    .then(response => {
                        this.$emit('persistEvent')

                        form.name = '';
                        form.redirect = '';
                        form.errors = [];

                        $(modal).modal('hide');
                    })
                    .catch(error => {
                        if (typeof error.response.data === 'object') {
                            form.errors = _.flatten(_.toArray(error.response.data.errors));
                        } else {
                            form.errors = ['Something went wrong. Please try again.'];
                        }
                    });
            },

            /**
             * Destroy the given client.
             */
            destroy(client) {
                axios.delete('/oauth/clients/' + client.id)
                    .then(response => {
                        this.$emit('deletedEvent')
                    });
            },
            inviteUser(client) {
                axios.post('/api/oauth-proxy/client/users/invite/' + client.id, {
                        'email': this.newEmail
                    })
                    .then(response => {
                        this.$emit('invitationEvent')
                    });
            },
            manageUsers(client, key) {
                if (!this.editUsers[key]) {
                    Vue.set(this.editUsers, key, false);
                    this.makeRequest(client.id, key)
                }
                if (this.editUsers[key]) {
                    Vue.set(this.editUsers, key, false);
                }
            },
            checkLogs(client) {

            },
            makeRequest(client, key) {
                axios.get('/api/oauth-proxy/client/users/' + client
                )
                    .then(response => {
                        this.client.roles = response.data
                        Vue.set(this.editUsers, key, true);
                    })
            },
        }
    }
</script>

<style>
    .flex-container {
        display: flex;

    }
    .client-header {
        justify-content: left;
        width: 100%;
    }
    .row {
        flex-direction: row;
        margin-bottom: .2rem;
        justify-content: left;
    }
    .active {
        background-color: #007bff;
        color: #ffffff;
    }
    .active a {
        padding-left: 1rem;
        padding-right: 1rem;
        color: #ffffff;
    }
</style>
