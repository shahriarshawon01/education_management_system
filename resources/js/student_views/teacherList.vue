<template>
  <data-table :table-heading="tableHeading" table-title="Teachers List" :defaultFilter="false"
    :defaultSearchButton="false">
    <template v-slot:page_top>
      <page-top topPageTitle="Teachers List" page-modal-title="Teachers Add/Edit"
        :default-add-button="false"></page-top>
    </template>
    <template v-slot:data>
      <tr v-for="(data, index) in dataList.data">
        <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
        <td>{{ data.name ?? "" }}</td>
        <td>{{ data.email ?? "" }}</td>
        <td>{{ data.phone ?? "" }}</td>
        <td>{{ data.department_name ?? "" }}</td>
        <td>{{ data.designation_name ?? "" }}</td>
      </tr>
    </template>
  </data-table>
</template>

<script>
import DataTable from "../components/dataTable";
import PageTop from "../components/pageTop";
export default {
  name: "teacherList",
  components: { PageTop, DataTable },
  data() {
    return {
      tableHeading: ["Sl", "Name", "Email", "Phone", "Department", "Designation"],
    };
  },
  mounted() {
    this.getDataList();
  },
};
</script>

<style scoped></style>
