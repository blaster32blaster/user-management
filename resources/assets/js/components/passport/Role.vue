<template>
    <div>
        <div class="row" style="padding:1rem;">
            <div class="col-xs-6 col-md-3" style="vertical-align: middle;">
                {{ role.user.name }}
            </div>
            <div class="col-xs-6 col-md-3" style="vertical-align: middle;">
                {{ role.user.email }}
            </div>
            <div class="col-xs-6 col-md-3" style="vertical-align: middle;">
                {{ role.role.name}}
            </div>
            <div class="col-xs-6 col-md-3" style="vertical-align: middle;">
                <a @click="edit(role)">
                    Change Role
                </a>
                <a @click="destroy(role)" style="color: orangered">
                    Delete
                </a>
            </div>
        </div>

        <!-- Edit Role Modal -->
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
                                    <select id="edit-user-role" class="form-control" @keyup.enter="update" v-model="editForm.name">
                                        <option v-for="role in possibleRoles" v-bind:value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <span class="form-text text-muted">
                                        Change {{ editForm.user }}'s role.
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
            role: {

            },
            index: {

            },
            clientId: {

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
                    id: '',
                    errors: [],
                    name: '',
                    redirect: ''
                },

                modalId: this.index + '-modal-edit-role',
                editUsers: [],
                theRole: this.role,
                possibleRoles: []
            };
        },

        /**
         * Prepare the component (Vue 2.x).
         */
        mounted() {
            this.getRoles()
        },

        methods: {
            getRoles() {
                axios.get('/api/oauth-proxy/client/roles/' + this.clientId
                )
                    .then(response => {
                        this.possibleRoles = response.data
                    })
            },
            /**
             * Edit the given client.
             */
            edit(role) {
                this.editForm.id = role.id;
                this.editForm.user = role.user.name;
                this.editForm.name = role.role.name;
                let roleWithHash = '#'+ this.modalId

                $(roleWithHash).modal('show');
            },

            /**
             * Update the client being edited.
             */
            update() {
                let roleWithHash = '#'+ this.modalId
                this.persistClient(
                    'put', '/api/oauth-proxy/client/users/' + this.editForm.id,
                    this.editForm, roleWithHash
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
                        this.$emit('persistRoles')

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
             * Destroy the given role.
             */
            destroy(role) {
                axios.delete('/api/oauth-proxy/client/users/' + role.id)
                    .then(response => {
                        this.$emit('deletedRole')
                    });
            },
            // manageUsers(client, key) {
            //     if (!this.editUsers[key]) {
            //         Vue.set(this.editUsers, key, false);
            //         this.makeRequest(client.id, key)
            //     }
            //     if (this.editUsers[key]) {
            //         Vue.set(this.editUsers, key, false);
            //     }
            // },
            // checkLogs(client) {
            //
            // },
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
    }
    .active a {
        padding-left: 1rem;
        padding-right: 1rem;
    }
</style>
