<template>
    <div class="row">
        <div class="col">
            <data-table :table-heading="tableHeading" table-title="Exam Routine" :defaultFilter="false" :defaultSearchButton="false">
                <template v-slot:page_top>
                    <page-top :default-add-button="false" topPageTitle="Exam Routine" page-modal-title="Exam Routine Add/Edit"></page-top>
                </template>
                <template v-slot:data>
                    <tr v-for="(data, index) in dataList.data">
                        <td class="fw-medium">{{parseInt(dataList.from)+index}}</td>
                        <td>{{ data.name }}</td>
                        <td>{{ showData(data.class,'name') }}</td>
                        <td>{{ showData(data.session,'title') }}</td>
                        <td>{{ data.start_date }} To {{ data.end_date }}</td>
                        <td>
                            <a v-if="data.file" @click="openFile(getImage(data.file))">
                                <i class="fa fa-download"> File</i>
                            </a>
                            <p v-else>-</p>
                        </td>
                    </tr>
                </template>
            </data-table>
        </div>
    </div>
</template>

<script>
    import DataTable from "../components/dataTable";
    import PageTop from "../components/pageTop";
    export default {
        name: "examRoutine",
        components: {PageTop, DataTable},
        data() {
            return {
                tableHeading: ['Sl','Exam Name','Class','Session','Date','Routine File'],
            }
        },
        mounted() {
            this.getDataList();
        }
    }
</script>

<style scoped>

</style>


