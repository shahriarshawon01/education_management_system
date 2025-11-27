<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Notice Board" >
                    <template v-slot:page_top>
                        <page-top :default-object="{notice_to:''}" topPageTitle="Notice Board" :default-add-button="can('noticeboard_add')" ></page-top>
                    </template>
                    <template v-slot:filter>

                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                            <td>{{ data.title }}</td>
                            <td>{{ data.subject }}</td>
                            <td>{{ data.notice }}</td>
                             <td>{{ data.author }}</td>
                            <td style="text-align: center">
                                <a v-if="data.file" @click="openFile(getImage(data.file))">
                                    <i class="fa fa-download"> File</i>
                                </a>
                                <p v-else>-</p>
                            </td>
                            <td style="text-align: center">
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td style="text-align: center">
                                <div class="hstack gap-3 fs-15">
                                    <a v-if="can('noticeboard_update')" class="link-primary" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a v-if="can('noticeboard_delete')" class="link-danger" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">

            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Notice Title :</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" v-model="formObject.title" v-validate="'required'" name="title" class="form-control" placeholder="Notice Title">
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Notice Subject :</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" v-model="formObject.subject" v-validate="'required'" name="subject" class="form-control" placeholder="Notice Subject">
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Author :</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" v-model="formObject.author" v-validate="'required'" name="author" class="form-control" placeholder="Author">
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Notice To :</label>
                    </div>
                    <div class="col-md-8">
                        <select v-model="formObject.notice_to" v-validate="'required'" name="notice_to" class="form-control">
                            <option value="" selected>select</option>
                            <option value="All">All</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Student">Student</option>
                            <option value="Class">Class</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label">Wirte Notice :</label>
                <textarea name="notice" v-model="formObject.notice" v-validate="'required'" class="form-control" rows="3" placeholder="Wirte Notice"></textarea>
            </div>
            <div class="row mb-2">
                <label class="form-label">File :</label>
                <div class="col-md-12">
                    <div @click="clickImageInput('image')" class="mb-2 mt-3">
                        <div class="form-group image_upload" :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }" style="background-size:360px !important">
                            <img v-if="formObject.file" :src="getImage(formObject.file)">
                            <input name="thumbnail" :v-validate="formType === 1 ? 'required' : 'sometimes'" style="display: none;" id="image" type="file" @change="uploadFile($event, formObject, 'file')">
                        </div>
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
        name: "NoticeBoardList",
        components: { PageTop, Pagination, DataTable, formModal },
        data() {
            return {
                tableHeading: ["Sl","Notice Title","Notice Subject","Notice","Author","File","Status", "Action"],
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

</style>

