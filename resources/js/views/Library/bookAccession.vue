<template :key="$route.meta.name">
    <div class="main_component">
        <div class="row">
            <div class="col">
                <data-table :table-heading="tableHeading" table-title="Book Accession">
                    <template v-slot:page_top>
                        <page-top topPageTitle="Book Accession" page-modal-title="Add/Edit" v-if="can('book_accession_add')"></page-top>
                    </template>
                    <template v-slot:data>
                        <tr v-for="(data, index) in dataList.data">
                            <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                            <td>{{data.book_title}}</td>
                            <!-- <td>
                                <template v-if="data.soft_copy">
                                    <img style="height: 40px !important;cursor: pointer" :src="getImage(null, 'images/pdf.png')" @click="openFile(getImage(data.soft_copy))" alt="File">
                                </template>
                            </td> -->

                            <td>
                                <a v-if="data.soft_copy" @click="openFile(getImage(data.soft_copy))">
                                    <i class="fa fa-download"> File</i>
                                </a>
                                <p v-else>-</p>
                            </td>
                            <td>{{data.author_name}}</td>
                            <td>{{data.books_publishers}}</td>
                            <td>{{data.books_edition}}</td>
                            <td>{{data.books_language}}</td>
                            <td>{{data.cell_number}}</td>
                            <td>{{data.isbn}}</td>
                            <td>
                                <a @click="changeStatus(data)" v-html="showStatus(data.status)"></a>
                            </td>
                            <td>
                                <div class="hstack gap-3 fs-15">
                                    <a class="link-primary" v-if="can('book_accession_update')" @click="editData(data, data.id)"><i class="fa fa-edit"></i></a>
                                    <a class="link-danger" v-if="can('book_accession_delete')" @click="deleteInformation(index, data.id)"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    </template>
                </data-table>
            </div>
        </div>
        <formModal modalSize="modal-lg" @submit="submitForm(formObject, 'formModal')">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Book Title</label>
                    <input type="text" class="form-control" placeholder="Book Title" v-model="formObject.book_title" v-validate="'required'" name="book_title">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    <select class="form-control" v-model="formObject.book_type" v-validate="'required'" name="book_type">
                        <option value="">Select Type</option>
                        <option value="1">Academic</option>
                        <option value="2">Non Academic</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Author</label>
                    <select v-select2 class="form-control" v-model="formObject.author" v-validate="'required'" name="author">
                        <option value="">Search Author</option>
                        <template v-for="(data, index) in requiredData.books_author">
                            <option :value="data.id">{{data.name}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Publisher</label>
                    <select v-select2 class="form-control" v-model="formObject.publisher" v-validate="'required'" name="publisher">
                        <option value="">Search Publisher</option>
                        <template v-for="(data, index) in requiredData.books_publishers">
                            <option :value="data.id">{{data.name}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Edition</label>
                    <select v-select2 class="form-control" v-model="formObject.edition" v-validate="'required'" name="edition">
                        <option value="">Search Edition</option>
                        <template v-for="(data, index) in requiredData.books_edition">
                            <option :value="data.id">{{data.name}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Language</label>
                    <select v-select2 class="form-control" v-model="formObject.language" v-validate="'required'" name="edition">
                        <option value="">Search Edition</option>
                        <template v-for="(data, index) in requiredData.books_language">
                            <option :value="data.id">{{data.name}}</option>
                        </template>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cell Number</label>
                    <input type="text" class="form-control" placeholder="Enter Cell Number" v-model="formObject.cell_number" v-validate="'required'" name="cell_number">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ISBN</label>
                    <input type="text" class="form-control" placeholder="Enter ISBN" v-model="formObject.isbn" name="isbn">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Soft Copy</label>
                    <div class="col-md-12">
                    <div @click="clickImageInput('soft_copy')" class="mb-2">
                        <div class="form-group image_upload" :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }">
                            <img v-if="formObject.soft_copy" :src="getImage(formObject.soft_copy)">
                            <input name="soft_copy" style="display: none;" id="soft_copy" type="file" @change="uploadFile($event, formObject, 'soft_copy')">
                        </div>
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
    name: "bookAccession",
    components: {PageTop, Pagination, DataTable, formModal},
    data() {
        return {
            tableHeading: ['Sl','Book Name','Soft Copy','Author','Publisher','Edition','Language','Cell No','ISBN No','Status','Action'],
            formModalId : 'formModal',
        }
    },
    mounted(){
        const _this = this;
        _this.$set(_this.formObject,'author','');
        _this.$set(_this.formObject,'publisher','');
        _this.$set(_this.formObject,'edition','');
        _this.$set(_this.formObject,'language','');
        _this.$set(_this.formObject,'book_type','');
        _this.getDataList();
        _this.getGeneralData(['books_author','books_edition','books_language','books_publishers'])
    }
}
</script>

<style scoped>

</style>
