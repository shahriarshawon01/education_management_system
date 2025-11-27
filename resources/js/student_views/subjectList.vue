<template>
    <data-table :table-heading="tableHeading" table-title="Subject List" :defaultFilter="false"
        :defaultSearchButton="false">
        <template v-slot:page_top>
            <page-top topPageTitle="Subject List" page-modal-title="Subject Add/Edit"
                :default-add-button="false"></page-top>
        </template>
        <template v-slot:data>
            <tr v-for="(data, index) in dataList.data">
                <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                <td>{{ showData(data.teachers, "name") }}</td>
                <td>{{ data.name }}</td>
                <td>{{ data.subject_code }}</td>
                <td>{{ data.subject_mark }}</td>
            </tr>
        </template>
    </data-table>
</template>

<script>
import DataTable from "../components/dataTable";
import PageTop from "../components/pageTop";

export default {
    name: "subjectList",
    components: { PageTop, DataTable },
    data() {
        return {
            tableHeading: ["Sl", "Teacher", "Subject Name", "Subject Code", "Subject Mark"],
        };
    },
    mounted() {
        this.getDataList();
    },
};
</script>

<style scoped></style>
