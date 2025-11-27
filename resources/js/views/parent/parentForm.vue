<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3>Parent Add</h3>
                </div>
                <div class="col-md-6 text-end">
                    <router-link to="/admin/parents" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Back
                    </router-link>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form @submit.prevent="submitForm(formObject, false, null, true, false, {}, '/admin/parents')">
                <formComponent :parentFormObject="formObject"></formComponent>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-start">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import FormModal from "../../components/formModal";
import PageTop from "../../components/pageTop";
import GeneralModal from "../../components/generalModal";
import formComponent from "../parent/formComponent";

export default {
    name: "parentForm",
    components: { GeneralModal, PageTop, FormModal, formComponent },
    data() {
        return {
            parentFormObject: {}
        };
    },
    methods: {
        getEditData(id) {
            const _this = this;
            var url = `${_this.urlGenerate()}/${id}/edit`;

            _this.getData(url, 'get', {}, {}, function (retData) {
                if (!_this.parentFormObject) {
                    _this.$set(_this, 'parentFormObject', {});
                }
                if (retData && retData.user && retData.user.email) {
                    _this.$set(_this.parentFormObject, 'email', retData.user.email);
                    _this.$store.commit('formObject', {
                        ..._this.$store.state.formObject,
                        email: retData.user.email
                    });
                } else {
                    _this.$set(_this.parentFormObject, 'email', null);
                }
                $.each(retData.address, function (index, value) {
                    _this.getGeneralData({
                        district: { division_id: value.division, objectName: `${value.type}_district` },
                        upazila: { district_id: value.district, objectName: `${value.type}_upazilla` },
                        union: { upazilla_id: value.upazila, objectName: `${value.type}_union` }
                    }, function (ret) {
                        _this.$store.commit('formObject', retData);
                    });
                });
            });
        },



        initializeData() {
            const _this = this;
            _this.getGeneralData(['division', 'district']);
            if (_this.$route.params.id !== undefined) {
                _this.getEditData(_this.$route.params.id);
            }
        }
    },
    mounted() {
        this.initializeData();
    },
};
</script>

<style scoped>
/* Add your styles here */
</style>
