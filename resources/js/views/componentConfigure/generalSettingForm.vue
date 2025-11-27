<template>
    <div class="content py-1">
        <div class="panel panel-default shadow rounded-2 bg-light p-1 border">
            <form @submit.prevent="submitForm(formObject, false, null, true, false, {}, '/admin/general_setting')">
                <div v-for="(eachSettingType, stIndex) in formObject" :key="stIndex"
                     class="mb-4 rounded shadow-lg overflow-hidden">
                    <div class="panel-heading bg-gradient text-white p-3 rounded-top shadow-sm">
                        <h5 class="m-0">
                            <i class="fa fa-cogs me-2"></i> {{ stIndex }}
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="panel-body bg-white p-4 rounded-bottom shadow-sm border border-top-0">
                            <div v-for="(input, inIndex) in eachSettingType" :key="input.key || inIndex"
                                 class="row mb-2 align-items-center p-3 rounded shadow-sm"
                                 style="background-color: #f8f9fa; border: 1px solid #eee;">
                                <div class="col-md-3 fw-semibold text-primary">
                                    {{ input.display_name }}
                                </div>
                                <div class="col-md-6">
                                    <template v-if="input.type === 'text'">
                                        <input class="form-control border-primary shadow-sm" v-validate="'required'"
                                               data-vv-as="Value" :name="`${input.key}`" v-model="input.value" type="text"
                                               :placeholder="'Enter ' + input.display_name" />
                                    </template>
                                    <template v-if="input.type === 'file'">
                                        <div @click="clickImageInput(input.key)" class="mb-2">
                                            <div class="form-group image_upload shadow-sm border rounded p-2 text-center"
                                                 :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }"
                                                 style="background-size: cover; height: 120px; cursor: pointer;">
                                                <img v-if="input.value" :src="getImage(input.value)"
                                                     class="img-thumbnail shadow-sm"
                                                     style="max-height: 100px; border-radius: 10px;" />
                                                <input name="thumbnail" style="display: none;" :id="input.key"
                                                       type="file" @change="UploadImage($event, input, 'value')" />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-if="input.type === 'textarea'">
                                        <textarea class="form-control border-success shadow-sm" v-validate="'required'"
                                                  v-model="input.value" :name="`${input.key}`" rows="3"
                                                  :placeholder="'Enter ' + input.display_name"></textarea>
                                    </template>
                                    <template v-if="input.type === 'tinmyce'">
                                        <textarea class="form-control border-info shadow-sm" v-validate="'required'"
                                                  data-vv-as="Value" v-model="input.value" :name="`${input.key}`"
                                                  rows="5"></textarea>
                                    </template>
                                    <template v-if="input.type === 'select'">
                                        <select v-validate="'required'" v-model="input.value" :name="`${input.key}`"
                                                class="form-select border-warning shadow-sm">
                                            <option value="">Select</option>
                                            <option v-for="(data, index) in requiredData[input.key]"
                                                    :key="data.id || index" :value="data.id">
                                                {{ data.title }}
                                            </option>
                                        </select>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-lg rounded-pill">
                        <i class="fa fa-save me-2"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import Editor from '@tinymce/tinymce-vue';
    export default {
        name: "generalSettingForm",
        components: { Editor },
        data() {
            return {
                requiredDataObj: {}
            }
        },
        methods: {

            showInformation() {
                const _this = this;
                var editUrl = '/api/general_setting';
                _this.getData(editUrl, "get", {}, {}, function (retData) {

                    _this.$store.commit("formObject", retData);
                });
            },
            onChangeFile($event, image, path) {
                const _this = this;
                _this.UploadImage($event, image, null, function (retData) {
                    _this.formObject.files.push({ path: '', name: '', extension: '', size: '', });
                })
            },
            defaultSelectedObject() {
                const _this = this;
                _this.$set(_this.formObject, 'applicant_status', '');
                _this.$set(_this.formObject, 'value', '');
                _this.$set(_this.formObject, 'key', '');
                _this.$set(_this.formObject, 'type', 'text');
                _this.$set(_this.formObject, 'type', 'textarea');
            }
        },
        mounted() {
            const _this = this;
            _this.defaultSelectedObject();
            _this.showInformation();
            _this.getGeneralData({
                canteenInstitutes: { objName: 'canteen_manage_by' },
                dormitoryInstitutes: { objName: 'dormitory_manage_by' },
                transportInstitutes: { objName: 'transport_manage_by' },
                cashPaymentInstitutes: { objName: 'cash_payment_manage_by' },
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

<style scoped>
    .content {
        background-color: #f5f7fa;
        min-height: 100vh;
    }

    .panel-heading h5 {
        font-weight: bold;
        letter-spacing: 0.5px;
    }

    .panel-body {
        background-color: #fff;
    }

    .form-group.image_upload {
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 10px;
    }
</style>
