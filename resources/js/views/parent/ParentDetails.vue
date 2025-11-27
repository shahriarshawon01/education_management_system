<template :key="$route.meta.name">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3>Teacher Details</h3>
                </div>
                <div class="col-md-6 text-end">
                    <router-link to="/admin/parents" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Back
                    </router-link>
                    <button  @click="printData('printDiv')" class="btn btn-outline-primary"><i class="fa-sharp fa-solid fa-print"></i> Print</button>
                </div>
            </div>
        </div>
        <div class="card-body" id="printDiv">
            <div class="row mb-4">
                <div class="col-md-12">
                    <h4>Basic Information</h4><hr>
                </div>
                <div class="col-md-12 mb-2">
                    <div class="row">
                        <label class="col-md-2">Image :</label>
                        <div class="col-md-4">
                            <img style="height: 75px;width: 65px;" :src="getImage(details.image)">
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-2">
                    <div class="row">
                        <label class="col-md-3">Name :</label>
                        <div class="col-md-9">
                            <label>{{ details.name }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-2">
                    <div class="row">
                        <label class="col-md-3">Phone :</label>
                        <div class="col-md-9">
                            <label>{{ details.phone }}</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-2">
                    <div class="row">
                        <label class="col-md-3">Gender :</label>
                        <div class="col-md-9">
                            <label v-if="parseInt(details.gender) === 1">Male</label>
                            <label v-else-if="parseInt(details.gender) === 2">Female</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-2">
                    <div class="row">
                        <label class="col-md-3">Profession :</label>
                        <div class="col-md-9">
                            <label>{{ details.profession }}</label>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row mb-3">
                <div class="col-md-12">
                    <h4>Address</h4>
                </div>
                <div class="col-md-12">
                    <div class="row mb-2">
                        <label class="col-md-2"></label>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-2">
                                    <strong>Division</strong>
                                </div>
                                <div class="col-2">
                                    <strong>District</strong>
                                </div>
                                <div class="col-2">
                                    <strong>Upazilla</strong>
                                </div>
                                <div class="col-2">
                                    <strong>Union</strong>
                                </div>
                                <div class="col-2">
                                    <strong>Post Office</strong>
                                </div>
                                <div class="col-2">
                                    <strong>Village</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="col-md-12">
                    <div class="row mb-2" v-for="(address, index) in details.address">
                        <label class="col-md-2" v-if="parseInt(address.type) === 1">Permanent</label>
                        <label class="col-md-2" v-else>Present</label>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-2">
                                    <label>{{ address.division }}</label>
                                </div>
                                <div class="col-2">
                                    <label>{{ address.district }}</label>
                                </div>
                                <div class="col-2">
                                    <label>{{ address.upazilla }}</label>
                                </div>
                                <div class="col-2">
                                    <label>{{ address.union_name }}</label>
                                </div>
                                <div class="col-2">
                                    <label>{{ address.post_office }}</label>
                                </div>
                                <div class="col-2">
                                    <label>{{ address.village }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
export default {
    name: "parentDetails",
    components: {PageTop, Pagination, DataTable},
    data() {
        return {
            details:{},
        }
    },
    methods:{
        getDetails: function () {
            let _this = this;
            var url = `${this.urlGenerate()}/${this.$route.params.id}`;
            this.getData(url, 'get', {}, {}, function (retData) {
                _this.details = retData;
            });
        }

    },
    mounted(){
        this.getDetails();
        this.getGeneralData(['district', 'upazila', 'union']);

    }
}
</script>

<style scoped>

</style>