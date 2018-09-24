<template>

    <div style="margin-bottom: 2rem" class="client-root">
        <div class="row client-spacing-header"></div>
        <div class="row client-header">
            <div class="col-sm-10 client-name" style="vertical-align: middle;">
                {{ client.name }}
            </div>
            <div v-if="client.admin"  class="col-sm-1 col-md-1 client-options" >
                <a class="action-link" tabindex="-1" @click="edit(client)">
                    <i class="fas fa-pen" ></i>
                </a>
            </div>
            <div v-if="client.admin"  class="col-sm-1 col-md-1 client-options">
                <a class="action-link text-danger" @click="destroy(client)">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>
        <div class="row client-body">
            <div class="col-sm-12 col-md-6">
                <div class="client-inset">
                    <!-- Secret -->
                    <div class="col-sm-12" style="vertical-align: middle;">
                        <div class="client-label">Client Secret</div>
                        <div class="client-value">{{ client.secret }}</div>
                    </div>

                    <div class="col-sm-12" style="vertical-align: middle;">
                        <div class="client-label">Client Url</div>
                        <div class="client-value">{{ client.redirect }}</div>
                    </div>

                    <div class="col-sm-12" style="vertical-align: middle;">
                        <div class="client-label">Password Access Allowed ?</div>
                        <div class="client-value">{{ client.password_client}}</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6" >
                <div class="client-button-inset">
                    <!--manage users-->
                    <button type="button" class="client-button" tabindex="-1" @click="manageUsers" v-if="client.admin">
                        Manage Users
                    </button>
                    <button type="button" class="client-button" tabindex="-1" @click="checkLogs(client)">
                        View Client Logs
                    </button>
                </div>
            </div>
        </div>

    <!--start whats being replaced-->
    <!--<div style="border-bottom: .5rem ridge gainsboro; margin-bottom: 2rem">-->
        <!--<div class="flex-container">-->
            <!--<div class="row client-header">-->
                <!--<div class="col-sm-6 col-md-2">Client Id</div>-->
                <!--<div class="col-sm-6 col-md-2">Name</div>-->
                <!--<div class="col-sm-6 col-md-2">Secret</div>-->
                <!--<div class="col-sm-6 col-md-2">Address</div>-->
                <!--<div class="col-sm-6 col-md-2">Password Client</div>-->
            <!--</div>-->
        <!--</div>-->
        <!--<div  class="flex-container row">-->
        <!--&lt;!&ndash; ID &ndash;&gt;-->
            <!--<div class="col-xs-6 col-md-2" style="vertical-align: middle;">-->
                <!--{{ client.id }}-->
            <!--</div>-->

            <!--&lt;!&ndash; Name &ndash;&gt;-->
            <!--<div class="col-sm-6 col-md-2" style="vertical-align: middle;">-->
                <!--{{ client.name }}-->
            <!--</div>-->

            <!--&lt;!&ndash; Secret &ndash;&gt;-->
            <!--<div class="col-sm-6 col-md-2" style="vertical-align: middle;">-->
                <!--<code>{{ client.secret }}</code>-->
            <!--</div>-->

            <!--<div class="col-sm-6 col-md-2" style="vertical-align: middle;">-->
                <!--<code>{{ client.redirect}}</code>-->
            <!--</div>-->

            <!--<div class="col-sm-6 col-md-2" style="vertical-align: middle;">-->
                <!--<code>{{ client.password_client }}</code>-->
            <!--</div>-->

        <!--</div>-->
        <!--<div  class="flex-container row">-->

            <!--&lt;!&ndash;manage users&ndash;&gt;-->
            <!--<div class="col-sm-3" :class="{ active :editUsers[index]}" v-if="client.admin" style="vertical-align: middle;">-->
                <!--&lt;!&ndash;<a class="action-link" tabindex="-1" @click="manageUsers(client, index)">&ndash;&gt;-->
                <!--<a class="action-link" tabindex="-1" @click="manageUsers">-->
                    <!--Manage Users-->
                <!--</a>-->
            <!--</div>-->

            <!--&lt;!&ndash;check logs&ndash;&gt;-->
            <!--<div class="col-sm-3" v-if="client.admin" style="vertical-align: middle;">-->
                <!--<a class="action-link" tabindex="-1" @click="checkLogs(client)">-->
                    <!--View Client Logs-->
                <!--</a>-->
            <!--</div>-->

            <!--&lt;!&ndash; Edit Button &ndash;&gt;-->
            <!--<div class="col-sm-3" v-if="client.admin" style="vertical-align: middle;">-->
                <!--<a class="action-link" tabindex="-1" @click="edit(client)">-->
                    <!--Edit-->
                <!--</a>-->
            <!--</div>-->

            <!--&lt;!&ndash; Delete Button &ndash;&gt;-->
            <!--<div class="col-sm-3" v-if="client.admin" style="vertical-align: middle;">-->
                <!--<a class="action-link text-danger" @click="destroy(client)">-->
                    <!--Delete-->
                <!--</a>-->
            <!--</div>-->
        <!--</div>-->
        <!--end whats being replaced-->


            <!--<div class="active" v-if="editUsers[index]">-->
        <div class="active" v-if="editUsers">
                <invitation-modal
                        :theClient="client"
                        :index="index"
                        @invitationEvent="emitInvitation"
                        @deletedRole="updateRoles()"
                        @closingEvent="childClosedHandler"
                />
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

                            <!-- Password Client -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Allow Password Auth</label>

                                <div class="col-md-9">
                                    <select v-model="editForm.pass">
                                        <option v-for="option in passwordClientOptions" v-bind:value="option">
                                            {{ option }}
                                        </option>
                                    </select>
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
                    redirect: '',
                    pass: false
                },

                modalId: this.index + '-modal-edit-client',
                editUsers: false,
                roles: {},
                passwordClientOptions: [true, false],
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
                this.editForm.pass = client.password_client;
                let clientWithHash = '#'+ this.modalId

                $(clientWithHash).modal('show');
            },

            /**
             * Update the client being edited.
             */
            update() {
                let clientWithHash = '#'+ this.modalId
                this.persistClient(
                    // 'put', '/oauth/clients/' + this.editForm.id,
                    'put', '/oauth-proxy/client/' + this.editForm.id,
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
                        form.pass = false;
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
            manageUsers() {
              this.editUsers = !this.editUsers
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
            emitInvitation() {
                this.$emit('invitationEvent')
            },
            childClosedHandler () {
                this.manageUsers()
            }
        }
    }
</script>

<style scoped>
    .flex-container {
        display: flex;

    }

    .row {
        flex-direction: row;
        margin-bottom: .2rem;
        /*justify-content: left;*/
    }
    .active {
        /*background-color: #007bff;*/
        /*color: #ffffff;*/
    }
    .active a {
        padding-left: 1rem;
        padding-right: 1rem;
        /*color: #ffffff;*/
    }
</style>
