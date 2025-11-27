<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Appraisal Template" >
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Appraisal Template" :default-add-button="can('appraisal_template_add')" ></page-top>
                    </template>
                    <template v-slot:filter>

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.apprisal_for }}</td>
                            <td class="table-center">{{ data.wheightage_total }}</td>
                            <td class="table-center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="action-buttons">
                                <div class="hstack gap-3 fs-15">
                                    <a v-if="can('appraisal_template_update')" class="link-primary" @click="editDocumentData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('appraisal_template_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">

            <div class="mb-4">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Apprisal For:</label>
                    </div>
                    <div class="col-md-8">
                        <input v-select2 type="text" class="form-control" placeholder="Apprisal For" v-model="formObject.apprisal_for" name="apprisal_for" v-validate="'required'">
                    </div>
                </div>
            </div>
            <h4>Configure KRA & Weightage (Must be 100%)</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 40%; !important;">KRA</th>
                        <th style="width: 40%;">Weightage</th>
                        <th style="width: 20%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, index) in formObject.apprisal" :key="index">
                        <td>
                            <input type="text" v-model="row.kra"  name="kra" class="form-control" placeholder="KRA">
                        </td>
                        <td>
                            <input type="text" v-model="row.wheightage" name="wheightage" placeholder="wheightage" class="form-control" @input="checkWeightage(index)">
                        </td>
                        <td class="text-center">
                            <a class="btn btn-outline-success" @click="addRow(formObject.apprisal,{kra:'',wheightage:''})">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-outline-danger" v-if="formObject.apprisal.length > 1" @click="deleteRow(formObject.apprisal, index)">
                                <i class="fa fa-close" aria-hidden="true"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </formModal>
    </div>
</template>
<script>
    import DataTable from "../../components/dataTable";
    import Pagination from "../../plugins/pagination/pagination";
    import PageTop from "../../components/pageTop";
    import formModal from "../../components/formModal";
    export default {
        name: "ApprisalTemplate",
        components: { PageTop, Pagination, DataTable, formModal },
        data() {
            return {
                tableHeading: ["Sl","Apprisal For","Total Wheightage", "Status","Action"],
                formModalId: "formModal",
                apprisal:[{
                    kra:'',
                    wheightage:'',
                }]
            };
        },
        methods:{
            // editDocumentData : function (data, id) {
            //     const _this = this;
            //     _this.editData(data, id, 'formModal', function (retData) {
            //         console.log(retData);
            //         _this.$set(_this.formObject, 'apprisal', [{
            //             kra:retData.kra,
            //             wheightage:retData.wheightage,
            //         }]);
            //     });
            // },
            checkWeightage(index) {
                const totalWeightage = this.formObject.apprisal.reduce((acc, row) => acc + parseFloat(row.wheightage || 0), 0);
                if (totalWeightage > 100) {
                    alert("Total Weightage cannot exceed 100!");

                    this.formObject.apprisal[index].wheightage = '';
                }
            },
            editDocumentData: function (data, id) {
                    const _this = this;
                    const URL = `${_this.urlGenerate()}/${id}/edit`;
                    
                    _this.$store.commit('updateId', id);
                    _this.$store.commit('formType', 2);

                    _this.getData(URL, "get", {}, {}, function (retData) {
                        _this.openModal('formModal', 'Edit Template');

                        const kraArray = Array.isArray(retData.kra) ? retData.kra : [retData.kra];
                        const wheightageArray = Array.isArray(retData.wheightage) ? retData.wheightage : [retData.wheightage];

                        const apprisal = kraArray.map((kra, index) => ({
                            kra: kra || '',
                            wheightage: wheightageArray[index] || '',
                        }));

                        _this.$set(_this.formObject, 'apprisal', apprisal);

                        _this.$set(_this.formObject, 'apprisal_for', retData.apprisal_for);
                    });
                },

        },
        mounted() {
            const _this = this;
            _this.getDataList();
            _this.$set(_this.formObject, 'apprisal', [{ kra:'',wheightage:'' }]);
        },
    };
</script>

<style scoped>
.table-center {
      text-align: center;
  }

.action-buttons {
      display: flex;
      justify-content: center;
      gap: 10px;
  }
</style>

