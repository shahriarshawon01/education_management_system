<template>
    <data-table :table-heading="tableHeading" table-title="Library List" :defaultFilter="false" :defaultSearchButton="false">
        <template v-slot:page_top>
            <page-top topPageTitle="Library List" page-modal-title="Library Add/Edit" :default-add-button="false"></page-top>
        </template>
        <template v-slot:data>
            <tr v-for="(data, index) in dataList.data">
                <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
                <td>{{ data.book_name }}</td>
                <td>{{ data.issue_date }}</td>
                <td>{{ data.return_date }}</td>
                <td>
                    <template v-if="data.status === 1">
                        <span class="badge badge-soft-success">Issued</span>
                    </template>
                    <template v-if="data.status === 2">
                        <span class="badge badge-soft-warning">Returned</span>
                    </template>
                </td>
            </tr>
        </template>
    </data-table>
</template>

<script>
    import DataTable from "../components/dataTable";
    import PageTop from "../components/pageTop";

    export default {
        name: "libraryList",
        components: {PageTop, DataTable},
        data() {
            return {
                tableHeading: ["Sl", "Article Name", "Issue Date", "Return Date", "Status"],
            };
        },
        mounted() {
            this.getDataList();
        },
    };
</script>

<style scoped></style>
