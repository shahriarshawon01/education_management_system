<template :key="$route.meta.name">
    <div class="container">
        <div class="row justify-content-center" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Details Of User</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 user_image">
                                <i class="fa fa-user mb-2"></i>
                                <p><strong>Name : </strong> {{details.name}}</p>
                            </div>
                            <div class="col-md-8">
                                <template>
                                    <p><strong>Contact Number : </strong> {{details.contact_number}}</p>
                                    <p><strong>Email : </strong> {{details.email}}</p>
                                    <p><strong>NID : </strong> {{details.nid}}</p>
                                    <p><strong>Company Name : </strong> {{details.company_name}}</p>
                                    <p><strong>Company Address : </strong> {{details.address}}</p>
                                    <p><strong>User Name : </strong> {{details.username}}</p>
                                    <p><strong>status : </strong> <span v-html="showStatus(details.status)"></span></p>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import axios from "axios";
    export default {
        name: "userDetails",
        data() {
            return {
                details:[]
            }
        },
        methods:{
            showDetailsData() {
                axios.get(`/api/users/${this.$route.params.id}`)
                    .then((response) => {
                        this.details = response.data.result.data[0];
                    })
                    .catch((error) => {
                        console.error("Failed to fetch ticket details", error);
                    });
            },
        },
        mounted(){
            this.showDetailsData();
        },

    }
</script>

<style scoped>
    .user_image i {
        font-size: 110px;
        color: rgb(255 255 255);
        background: #8d8d8d;
        padding: 16px;
        border-radius: 11px;
    }
</style>
