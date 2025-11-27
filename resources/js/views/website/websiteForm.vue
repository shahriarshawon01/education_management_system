<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3>Website Setup Form</h3>
                </div>
            </div>
        </div>
        <form @submit.prevent="submitForm(formObject, false, null, true, false, {}, '/admin/website')">
            <div class="card-body">
                <div class="col-md-12">
                    <template v-for="(input, inIndex) in formObject">
                        <div class="row">
                            <div class="form-group row mt-2">
                                <div class="col-md-3">
                                    <label>{{ input.display_name }}</label>
                                </div>
                                <div class="col-md-9">
                                    <template v-if="input.type == 'text'">
                                        <input class="form-control form-control" v-validate="'required'"
                                            data-vv-as="Value" v-model="input.value" name="value" type="text">
                                    </template>
                                    <template v-if="input.type == 'textarea'">
                                        <textarea rowspan="2" class="form-control form-control" v-validate="'required'"
                                            data-vv-as="Value" v-model="input.value" style="width: 100%;"></textarea>
                                    </template>
                                    <template v-if="input.type == 'tinmyce'">
                                        <textarea class="form-control form-control" v-validate="'required'"
                                            data-vv-as="Value" v-model="input.value" style="width: 100%;"></textarea>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="text-end card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</template>

<script>
import Editor from '@tinymce/tinymce-vue';
export default {
    name: "websiteForm",
    components: { Editor },
    data() {
        return {
            keys: {
                software_title: 'Application name',
                footer_title: 'Footer name',
                logo: 'Logo',
            },
            requiredDataObj: {},
        }
    },
    methods: {
        showInformation: function () {
            const _this = this;
            var editUrl = '/api/website';
            _this.getData(editUrl, "get", {}, {}, function (retData) {
                _this.$store.commit("formObject", retData);
            });
        },
        onChangeFile: function ($event, image, path) {
            const _this = this;
            _this.UploadImage($event, image, null, function (retData) {
                _this.formObject.files.push({ path: '', name: '', extension: '', size: '', });
            })
        },
        defaultSelectedObject: function () {
            const _this = this;
            _this.$set(_this.formObject, 'applicant_status', '');
            _this.$set(_this.formObject, 'value', '');
            _this.$set(_this.formObject, 'key', '');
            _this.$set(_this.formObject, 'type', 'text');
            _this.$set(_this.formObject, 'type', 'textarea');
        }
    },
    computed: {
        dataSource: function (key) {
            const _this = this;
            if (key === 'applicant_status') {
                return _this.requiredData.applicant_status;
            }
            if (key === 'employee_document_type') {
                return _this.requiredData.categories;
            }
        }
    },
    mounted() {
        const _this = this;
        _this.defaultSelectedObject();
        _this.showInformation();
        _this.getGeneralData({
            applicant_status: { objName: 'applicant_status' },
            categories: { objName: 'employee_document_type' },
            role: { objName: 'general_stuff_role' },
        }, function (retData) {
            $.each(retData, function (value, index) {
                _this.$set(_this.requiredDataObj, index, value);
            });
        });
        _this.$store.commit('formObject', []);
    }
}
</script>
<style scoped></style>
