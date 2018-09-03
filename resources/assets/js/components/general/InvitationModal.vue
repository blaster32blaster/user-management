<template>
    <div>
        <!-- Edit Client Modal -->
        <div class="modal fade"
            :id="modalId"
            :ref="modalId"
             tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                                Invite New User
                        </h4>

                        <button type="button" class="close" @click="closeEvent" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="flex-container row" style="border-bottom: .5rem ridge gainsboro; padding: 2rem">

                            <div class="col-xs-6 col-md-6" style="vertical-align: middle;">
                                <input type="text" v-model="newEmail" placeholder="Email Address"/>
                            </div>
                            <div class="col-xs-6 col-md-6" style="vertical-align: middle;">
                                <button @click="inviteUser(client)" type="button">Send Invite</button>
                            </div>
                        </div>
                        <div class="flex-container row" style="padding:1rem;">
                            <h4>
                                Existing Users
                            </h4>
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
                        <div v-for="(role, ind) in users" style="padding:1rem;">
                            <role
                                    v-on:persistRoles="updateRoles"
                                    v-on:deletedRole="deleteRole"
                                    :role="role"
                                    :index="ind"
                                    :parentIndex="index"
                                    :clientId="client.id"
                            />
                        </div>

                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeEvent" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import Toasted from 'vue-toasted';
    Vue.use(Toasted)

    export default {
        props: {
            theClient: {
                Type: Object,
                default: () => {}
            },
            index: {
                Type: Number,
                default: 0
            }
        },
        data () {
            return {
                newEmail: '',
                client: this.theClient,
                users: [],
                modalId: 'invitation-modal' + this.index,
            }
        },
        methods: {
            inviteUser(client) {
                axios.post('/api/oauth-proxy/client/users/invite/' + client.id, {
                    'email': this.newEmail
                })
                    .then(response => {
                        this.$emit('invitationEvent')
                        this.newEmail = ''
                        this.$toasted.success('Successfully invited User')
                            .goAway(1500)
                    })
                    .catch(error => {
                        this.$toasted.error('Failed to Invite User')
                            .goAway(1500)
                    });
            },
            manageUsers(client) {
                this.makeRequest(client.id)
            },
            deleteRole () {
                this.manageUsers(this.client, this.index)
            },
            updateRoles() {
                this.manageUsers(this.client, this.index)
                this.hide()
            },
            makeRequest(client) {
                axios.get('/api/oauth-proxy/client/users/' + client
                )
                    .then(response => {
                        this.users = response.data
                    })
            },
            getUserList () {

            },
            show () {
                let invitationModalWithHash = '#'+ this.modalId
                $(invitationModalWithHash).modal({backdrop: 'static'}, 'show');
            },
            hide () {
                    let invitationModalWithHash = '#'+ this.modalId
                    $(invitationModalWithHash).modal('hide');
                    this.closeEvent()
            },
            closeEvent () {
                this.$emit('closingEvent')
            }
        },
        mounted () {
          this.makeRequest(this.client.id)
          this.show()

          // $(this.$refs.modalId).on("hidden.bs.modal",  this.$emit('closingEvent'))
        },
    }
</script>
<style>
    .flex-container {
        display: flex;

    }
    .modal-dialog {
        max-width: 800px;
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
        /*background-color: #007bff;*/
        /*color: #ffffff;*/
    }
    .active a {
        padding-left: 1rem;
        padding-right: 1rem;
        /*color: #ffffff;*/
    }
</style>