<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
  <div class="main_component">
    <div class="row">
      <div class="col">
        <data-table :defaultObject="{}" :table-heading="tableHeading" table-title="Class Routine"
          table-class="" :defaultFilter="false" :defaultSearchButton="false">
          <template v-slot:page_top>
            <page-top
              topPageTitle="Class Routine"
              page-modal-title="Class Routine Add/Edit Modal"
              :default-add-button="false"
            ></page-top>
          </template>
          <template v-slot:header>
            <button @click="openModal(formModalId)" class="btn btn-outline-secondary">
              <i class="fa fa-print"></i> Print
            </button>
          </template>
          <template v-slot:thead>
            <thead class="data_table">
                <tr>
                    <th scope="col">Day/Period</th>
                    <th v-for="data in dataList.period">
                    <span>Period - {{data}}</span>
                    </th>
                </tr>
            </thead>
          </template>
          <template v-slot:data>
            <tr v-for="(data, index) in dataList.routines">
                <td>{{index}}</td>
                <template v-for="(routine, rIndex) in data">
                    <td>
                        <strong>
                            Class :
                        </strong>
                        {{showData(routine.class, 'name')}}<br>
                        <strong>
                            Subject :
                        </strong>
                        {{showData(routine.subject, 'name')}} <br>
                        <strong>
                            Session :
                        </strong>
                        {{showData(routine.sessions, 'title')}}<br>
                        <strong>
                            Section :
                        </strong>
                        {{showData(routine.sections, 'name')}}<br>
                        <strong>
                            Building :
                        </strong>
                        {{showData(routine.building, 'name')}}<br>
                        <strong>
                            Class Room :
                        </strong>
                        {{showData(routine.class_room, 'name')}}<br>
                        <strong>
                            Class Teacher :
                        </strong>
                        {{showData(routine.teacher,'name')}}<br>
                        <strong>Time : </strong>
                        ({{formatTime(routine.starting_hour)}}-{{formatTime(routine.end_hour)}})

                    </td>
                </template>
            </tr>
          </template>
        </data-table>
      </div>
    </div>
  </div>
</template>

<script>
import DataTable from "../components/dataTable";
import PageTop from "../components/pageTop";
export default {
  name: "routinList",
  components: { PageTop, DataTable },
  data() {
    return {
      tableHeading: [],
    };
  },
  methods:{
        formatTime(time) {
        if (!time) return '-';
        const [hours, minutes] = time.split(':');

        let formattedHours = hours % 12;
        formattedHours = formattedHours ? formattedHours : 12;

        const formattedTime = `${formattedHours}:${minutes}`;

        const period = hours < 12 ? 'am' : 'pm';

        return `${formattedTime} ${period}`;
        }
    },
  mounted() {
    this.getDataList();
  },
};
</script>

<style scoped></style>
