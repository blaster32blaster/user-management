<template>
    <div>
        <!-- Edit Client Modal -->
        <div class="modal fade" id="clientModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                                Invite New User
                        </h4>

                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>

                    <div class="modal-body">
                        <!--@todo : 2Sep18 - things are defo shaping up here, but there is still some work to do.-->
                        <!--@todo : 1. check data responsiveness-->
                        <!--@todo : 2. ensure that users are being displayed properly-->
                        <!--@todo : 3. make sure role system is still working ok-->
                        <!--@todo : 4. fix up janky styling-->
                        <!--@todo : 5. fix up toast notif-->
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
                        <div v-for="(role, index) in users" style="padding:1rem;">
                            <role
                                    v-on:persistRoles="updateRoles()"
                                    v-on:deletedRole="updateRoles()"
                                    :role="role"
                                    :index="index"
                                    :clientId="client.id"
                            />
                        </div>

                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            }
        },
        data () {
            return {
                newEmail: '',
                client: this.theClient,
                users: []
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
                        this.$toasted.show('Successfully invited User')
                    });
            },
            manageUsers(client) {
                this.makeRequest(client.id)
            },
            updateRoles() {
                this.manageUsers(this.client, this.index)
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
                $('#clientModal').modal('show');
                },
                hide () {
                }
        },
        mounted () {
          this.makeRequest(this.client.id)
          this.show()
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