<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table
                    :default-object="selectedObject"
                    :defaultFilter="false"
                    :table-heading="tableHeading"
                    table-title="Assign Fees"
                >
                    <template v-slot:page_top>
                        <page-top
                            :default-add-button="can('fees_master_view')"
                            :default-object="{
                                session_year_id: '',
                                class_id: '',
                                section_id: '',
                                department_id: '',
                                configs: [
                                    {
                                        type: '',
                                        amount: '',
                                    },
                                ],
                            }"
                            topPageTitle="Assign Fees"
                            page-modal-title="Fees Add/Edit"
                        >
                        </page-top>
                    </template>

                    <template v-slot:filter>
                        <div class="col-md-2">
                            <select
                                type="text"
                                class="form-control"
                                v-model="formFilter.class_id"
                                name="class_id"
                            >
                                <option value="">Select Class</option>
                                <template
                                    v-for="(
                                        eachData, index
                                    ) in requiredData.class"
                                >
                                    <option :value="eachData.id">
                                        {{ eachData.name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select
                                type="text"
                                class="form-control"
                                v-model="formFilter.session_year_id"
                                name="session_year_id"
                            >
                                <option value="">Select Session</option>
                                <template
                                    v-for="(
                                        eachData, index
                                    ) in requiredData.session"
                                >
                                    <option :value="eachData.id">
                                        {{ eachData.title }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select
                                type="text"
                                class="form-control"
                                v-model="formFilter.section_id"
                                name="section_id"
                            >
                                <option value="" selected>Select Section</option>
                                <template
                                    v-for="(
                                        eachData, index
                                    ) in requiredData.section"
                                >
                                    <option :value="eachData.id">
                                        {{ eachData.name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select
                                type="text"
                                class="form-control"
                                v-model="formFilter.department_id"
                                name="department_id"
                            >
                                <option value="">Select Department</option>
                                <template
                                    v-for="(
                                        eachData, index
                                    ) in requiredData.departments"
                                >
                                    <option :value="eachData.id">
                                        {{ eachData.department_name }}
                                    </option>
                                </template>
                            </select>
                        </div>
                    </template>

                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">
                                {{ parseInt(dataList.from) + index }}
                            </td>
                            <td style="text-align: center">{{ data.date }}</td>
                            <td style="text-align: center">
                                {{ showData(data.class_name, "name") }}
                            </td>
                            <td style="text-align: center">
                                {{ showData(data.session, "title") }}
                            </td>
                            <td style="text-align: center">
                                {{ showData(data.section, "name") }}
                            </td>
                            <td style="text-align: center">
                                {{
                                    showData(data.department, "department_name")
                                }}
                            </td>
                            <td style="text-align: center">
                                {{ showData(data.types, "name") }}
                            </td>
                            <td style="text-align: center">
                                BDT. {{ data.amount }}/-
                            </td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a
                                        class="link-success"
                                        @click="getDetails(data)"
                                        ><i class="fa fa-eye"></i
                                    ></a>
                                    <a
                                        class="link-primary"
                                        @click="
                                            editDataInformation(
                                                data,
                                                data.class_id
                                            )
                                        "
                                        ><i class="fa fa-edit"></i
                                    ></a>
                                    <a
                                        class="link-danger"
                                        @click="
                                            deleteInformation(index, data.id)
                                        "
                                        ><i class="fa fa-trash"></i
                                    ></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal
            modalSize="modal-xs"
            @submit="submitForm(formObject, 'formModal')"
        >
            <div class="row">
                <div class="col-md-6">
                    <label class="col-form-label">Class:</label>
                    <select
                        type="text"
                        class="form-control"
                        v-model="formObject.class_id"
                        v-validate="'required'"
                        name="class_id"
                    >
                        <option value="">Select Class</option>
                        <template
                            v-for="(eachData, index) in requiredData.class"
                        >
                            <option :value="eachData.id">
                                {{ eachData.name }}
                            </option>
                        </template>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="col-form-label">Session:</label>
                    <select
                        type="text"
                        class="form-control"
                        v-model="formObject.session_year_id"
                        v-validate="'required'"
                        name="session_year_id"
                    >
                        <option value="">Select Session</option>
                        <template
                            v-for="(eachData, index) in requiredData.session"
                        >
                            <option :value="eachData.id">
                                {{ eachData.title }}
                            </option>
                        </template>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="col-form-label">Section:</label>
                    <select
                        type="text"
                        class="form-control"
                        v-model="formObject.section_id"
                        name="section_id"
                    >
                        <option value="">Select Section</option>
                        <template
                            v-for="(eachData, index) in requiredData.section"
                        >
                            <option :value="eachData.id">
                                {{ eachData.name }}
                            </option>
                        </template>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="col-form-label">Department:</label>
                    <select
                        type="text"
                        class="form-control"
                        v-model="formObject.department_id"
                        name="department_id"
                    >
                        <option value="">Select Department</option>
                        <template
                            v-for="(
                                eachData, index
                            ) in requiredData.departments"
                        >
                            <option :value="eachData.id">
                                {{ eachData.department_name }}
                            </option>
                        </template>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="col-form-label">Exam Date:</label>
                    <datepicker
                        class="form-control"
                        validate="required"
                        v-model="formObject.date"
                        name="date" placeholder="Select a date"
                    ></datepicker>
                </div>

                <div v-for="(itemm, index) in formObject.configs" :key="index">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="col-form-label">Fees Types:</label>
                            <select
                                type="text"
                                class="form-control"
                                v-model="itemm.type"
                                :name="'type' + index"
                            >
                                <option value="">Select Fees Type</option>
                                <template
                                    v-for="(
                                        eachData, index
                                    ) in requiredData.fees_typee"
                                >
                                    <option :value="eachData.id">
                                        {{ eachData.name }}
                                    </option>
                                </template>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="col-form-label">Amount:</label>
                            <input
                                type="number"
                                v-model="itemm.amount"
                                v-validate="'required'"
                                placeholder="Amuont"
                                :name="'amount' + index"
                                class="form-control"
                            />
                        </div>

                        <div class="col-md-3 gap-3 fs-15">
                            <label class="col-form-label">Add More:</label>
                            <a @click="addField" class="btn btn-primary btn-sm"
                                ><i class="fa fa-plus"></i
                            ></a>
                            <button
                                v-if="formObject.configs.length > 1"
                                @click="removeField(index)"
                                class="btn btn-danger btn-sm"
                            >
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </formModal>

        <getModal
            modal-id="detailsModal"
            @search="getAllDetails()"
            :defaultSearchButton="false"
            :isEnablePrint="false"
        >
            <template v-slot:body>
                <table
                    class="table data_table table-bordered align-middle table-nowrap mb-0"
                    id="printDiv"
                >
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Class</th>
                            <th>Session</th>
                            <th>Section</th>
                            <th>Department</th>
                            <th>Fees Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(data, index) in feesDetails.data">
                            <tr>
                                <td>{{ index + 1 }}</td>
                                <td>{{ data.date }}</td>
                                <td>{{ showData(data.class_name, "name") }}</td>
                                <td>{{ showData(data.session, "title") }}</td>
                                <td>{{ showData(data.section, "name") }}</td>
                                <td>
                                    {{
                                        showData(
                                            data.department,
                                            "department_name"
                                        )
                                    }}
                                </td>
                                <td>{{ showData(data.types, "name") }}</td>
                                <td>BDT. {{ data.amount }}/-</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </getModal>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import getModal from "../../components/getModal";
import Editor from "@tinymce/tinymce-vue";
import { computed } from "vue";

export default {
    name: "FeesMaster",
    components: { PageTop, Pagination, DataTable, formModal, getModal, Editor },
    data() {
        return {
            tableHeading: [
                "Sl",
                "Date",
                "Class",
                "Session",
                "Section",
                "Department",
                "Fees Type",
                "Amount",
                "Action",
            ],
            formModalId: "formModal",
            feesDetails: {},
            detailsData: {},
            selectedObject: {
                class_id: "",
                session_year_id: "",
                section_id: "",
                department_id: "",
            },
        };
    },

    methods: {
        addField() {
            const _this = this;
            _this.formObject.configs.push({
                type: "",
                amount: "",
            });
        },
        removeField(index) {
            if (
                Array.isArray(this.formObject.configs) &&
                index >= 0 &&
                index < this.formObject.configs.length
            ) {
                this.formObject.configs.splice(index, 1);
            } else {
                console.error(
                    "Invalid index or formObject.configs is not an array"
                );
            }
        },

        getDetails: function (data) {
            const _this = this;
            var studentUrl = `${_this.urlGenerate()}/${
                data.class_id
            }?session_year_id=${data.session_year_id}`;
            this.getData(
                studentUrl,
                "get",
                this.formObject,
                {},
                function (retData) {
                    _this.feesDetails = retData;
                    _this.openModal("detailsModal", false);
                }
            );
        },

        getAllDetails: function () {
            const _this = this;
           
            if (_this.feesDetails) {
                _this.detailsData = _this.feesDetails;
            }
            var studentUrl = `${_this.urlGenerate()}/${
                _this.detailsData.class_id
            }?session_year_id=${_this.detailsData.session_year_id}`;
            _this.getData(
                studentUrl,
                "get",
                _this.formObject,
                _this.formFilter.session_year_id,
                function (retData) {
                    _this.feesDetails = retData;
                    _this.openModal("detailsModal", false);
                }
            );
        },

        editDataInformation: function (data, id) {
            const _this = this;

            _this.formObject.configs = [];

            _this.editData(data, id, "formModal", function () {
                var editUrl = `${_this.urlGenerate()}/${id}/edit`;
                _this.getData(editUrl, "get", {}, {}, function (retData) {
                    const uniqueConfigs = new Map();
                    retData.data.forEach((item) => {
                        if (!uniqueConfigs.has(item.types.id)) {
                            uniqueConfigs.set(item.types.id, {
                                type: item.types.id,
                                amount: item.amount,
                            });
                        }
                    });

                    _this.formObject.configs = Array.from(
                        uniqueConfigs.values()
                    );
                });
            });
        },
    },

    mounted() {
        this.getDataList();
        const _this = this;
        this.getGeneralData([
            "fees_groups",
            "fees_typee",
            "session",
            "class",
            "section",
            "departments",
        ]);
        _this.$set(this.formObject,'date','')
        _this.$set(_this.formObject, "configs", [
            {
                type: "",
                amount: "",
            },
        ]);
    },
};
</script>

<style scoped></style>
