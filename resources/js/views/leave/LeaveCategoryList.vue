<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Leave Category List" >
                    <template v-slot:page_top>
                        <page-top :default-object="{}" topPageTitle="Leave Category List" :default-add-button="can('leave_category_add')" ></page-top>
                    </template>
                    <template v-slot:filter>

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.title }}</td>
                            <td>{{ data.note }}</td>
                            <td class="center">{{ data.total_leave }}</td>
                            <td class="center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td class="action-buttons">
                                <div class="hstack gap-3 fs-15">
                                    <a v-if="can('leave_category_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('leave_category_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-md" @submit="submitForm(formObject, 'formModal')">

            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Category :</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" v-model="formObject.title" v-validate="'required'" name="title" class="form-control" placeholder="Category" />
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Note :</label>
                    </div>
                    <div class="col-md-8">
                        <textarea rows="2" v-model="formObject.note" name="note" class="form-control" placeholder="Note" ></textarea>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="col-form-label">Anoual Total Leave :</label>
                    </div>
                    <div class="col-md-8">
                        <input type="number" v-model="formObject.total_leave" v-validate="'required'" name="total_leave" class="form-control" placeholder="Anoual Total Leave" />
                    </div>
                </div>
            </div>
        </formModal>
    </div>
</template>
<script>
    import DataTable from "../../components/dataTable";
    import Pagination from "../../plugins/pagination/pagination";
    import PageTop from "../../components/pageTop";
    import formModal from "../../components/formModal";
    export default {
        name: "LeaveCategoryList",
        components: { PageTop, Pagination, DataTable, formModal },
        data() {
            return {
                tableHeading: ["Sl","Title","Note","Anoual Leave","Status", "Action"],
                formModalId: "formModal",
            };
        },
        mounted() {
            const _this = this;
            _this.getDataList();
        },
    };
</script>

<style scoped>
.center {
        text-align: center;
    }

   .action-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
</style>

