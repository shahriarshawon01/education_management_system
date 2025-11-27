<template>
  <data-table :table-heading="tableHeading" table-title="Notice List" :defaultFilter="false" :defaultSearchButton="false">
    <template v-slot:page_top>
      <page-top
        topPageTitle="Notice List"
        page-modal-title="Notice Add/Edit"
        :default-add-button="false"
      ></page-top>
    </template>
    <template v-slot:data>
      <tr v-for="(data, index) in dataList.data">
        <td class="fw-medium">{{ parseInt(dataList.from) + index }}</td>
        <td>{{ data.title }}</td>
        <td>{{ data.subject }}</td>
        <td>{{ data.author }}</td>
        <td>
            <a v-if="data.file" @click="openFile(getImage(data.file))">
                <i class="fa fa-download"> File</i>
            </a>
            <p v-else>-</p>
        </td>
      </tr>
    </template>
  </data-table>
</template>

<script>
import DataTable from "../components/dataTable";
import PageTop from "../components/pageTop";
export default {
  name: "noticeList",
  components: { PageTop, DataTable },
  data() {
    return {
      tableHeading: ["Sl", "Notice Title", "Subject", "Author","File"],
    };
  },
  mounted() {
    this.getDataList();
  },
};
</script>

<style scoped></style>
