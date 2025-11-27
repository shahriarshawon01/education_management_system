<template>
    <div class="employee-details-container p-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-white p-3">
                <h3 class="card-title fw-bold m-0 text-primary">Employee Details</h3>
                <div class="action-buttons">
                    <router-link to="/admin/employee" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Back
                    </router-link>
                    <button @click="printData('printDiv')" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm" id="printDiv">
            <div class="card-body p-4">
                <div class="row align-items-center mb-4 text-center text-md-start">
                    <div class="col-md-4 order-md-2 d-flex justify-content-center justify-content-md-end">
                        <div
                            class="profile-photo-container rounded-circle overflow-hidden border border-2 border-secondary p-1">
                            <img v-if="details && details.photo" :src="getImage(details.photo)"
                                class="img-fluid rounded-circle" alt="Employee Photo" />
                            <div v-else class="text-muted d-flex align-items-center justify-content-center h-100">
                                <i class="fas fa-user-circle fa-4x text-light-grey"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 order-md-1">
                        <h2 class="fw-bold mb-0 text-primary">{{ details.school ? details.school.title : 'N/A' }}</h2>
                        <h5 class="text-muted">{{ details.school ? details.school.address : 'N/A' }}</h5>
                        <h6 class="text-muted">Institute Code: {{ details.school ? details.school.institute_code : 'N/A'
                        }}</h6>
                    </div>
                </div>

                <hr class="my-2" />

                <div class="section-card mb-4">
                    <h4 class="fw-bold text-dark mb-3">Basic Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Name:</span>
                                <span class="detail-value col-8 text-dark">{{ details.name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Employee ID:</span>
                                <span class="detail-value col-8 text-dark">{{ details.employee_id }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Father Name:</span>
                                <span class="detail-value col-8 text-dark">{{ details.father_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Mother Name:</span>
                                <span class="detail-value col-8 text-dark">{{ details.mother_name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="details.user && details.user.email">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Email:</span>
                                <span class="detail-value col-8 text-dark">{{ details.user.email }}</span>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="details.profile && details.profile.phone">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Phone:</span>
                                <span class="detail-value col-8 text-dark">{{ details.profile.phone }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Date of Birth:</span>
                                <span class="detail-value col-8 text-dark">{{ details ?
                                    details.date_of_birth : 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Designation:</span>
                                <span class="detail-value col-8 text-dark" v-if="details.designation">{{
                                    details.designation.name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Department:</span>
                                <span class="detail-value col-8 text-dark" v-if="details.department">{{
                                    details.department.name }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Gender:</span>
                                <span class="detail-value col-8 text-dark">{{ parseInt(details.gender) === 1 ? 'Male' :
                                    (parseInt(details.gender) === 2 ? 'Female' : 'N/A') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Marital Status:</span>
                                <span class="detail-value col-8 text-dark">{{ parseInt(details.marital_status) === 1 ?
                                    'Married' : (parseInt(details.marital_status) === 2 ? 'Unmarried' : 'N/A') }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Religion:</span>
                                <span class="detail-value col-8 text-dark">
                                    <span v-if="parseInt(details.religion) === 1">Muslim</span>
                                    <span v-else-if="parseInt(details.religion) === 2">Hindus</span>
                                    <span v-else-if="parseInt(details.religion) === 3">Christian</span>
                                    <span v-else-if="parseInt(details.religion) === 4">Buddhist</span>
                                    <span v-else-if="parseInt(details.religion) === 5">Others</span>
                                    <span v-else>N/A</span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="details.status !== undefined">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Job Status:</span>
                                <span class="detail-value col-8">
                                    <span class="badge" :class="'bg-' + statusBadge.color">{{ statusBadge.text }}</span>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                <hr class="my-2" />

                <div class="section-card mb-4">
                    <h4 class="fw-bold text-dark mb-3">Address</h4>
                    <div class="address-grid row row-cols-md-2 g-3">
                        <div v-for="(address, index) in details.address" :key="index" class="col">
                            <div class="address-item p-3 rounded bg-light">
                                <h6 class="fw-bold text-primary">{{ parseInt(address.type) === 1 ? 'Permanent Address' :
                                    'Present Address' }}</h6>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <span class="detail-label">Division:</span>
                                        <span class="detail-value ms-2">{{ address.division }}</span>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <span class="detail-label">District:</span>
                                        <span class="detail-value ms-2">{{ address.district }}</span>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <span class="detail-label">Upazilla:</span>
                                        <span class="detail-value ms-2">{{ address.upazilla }}</span>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <span class="detail-label">Union:</span>
                                        <span class="detail-value ms-2">{{ address.union_name }}</span>
                                    </div>
                                    <div class="col-12 mb-2">
                                        <span class="detail-label">Post Office:</span>
                                        <span class="detail-value ms-2">{{ address.post_office }}</span>
                                    </div>
                                    <div class="col-12">
                                        <span class="detail-label">Village:</span>
                                        <span class="detail-value ms-2">{{ address.village }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-2" />

                <div class="section-card">
                    <h4 class="fw-bold text-dark mb-3">Education Qualification</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light text-primary">
                                <tr>
                                    <th class="text-center" style="width: 20px;">Degree Name</th>
                                    <th class="text-center">Board Name</th>
                                    <th class="text-center">Passing Year</th>
                                    <th class="text-center">Department Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(qulific, gIndex) in details.employee_qualifications" :key="gIndex">
                                    <td>{{ qulific.degree_name }}</td>
                                    <td>{{ qulific.board_name }}</td>
                                    <td>{{ qulific.passing_year }}</td>
                                    <td>{{ qulific.dept_name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";

export default {
    name: "employeeDetails",
    components: { PageTop, Pagination, DataTable },
    data() {
        return {
            details: {},
        };
    },
    methods: {
        getDetails: function () {
            let _this = this;
            var url = `${this.urlGenerate()}/${this.$route.params.id}`;
            this.getData(url, "get", {}, {}, function (retData) {
                _this.details = retData;
            });
        },
        printData(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        },
    },

    computed: {
        statusBadge() {
            if (!this.details || this.details.status === undefined || this.details.status === null) {
                return { text: 'N/A', color: 'secondary' };
            }

            switch (parseInt(this.details.status)) {
                case 1:
                    return { text: 'Active', color: 'success' };
                case 0:
                    return { text: 'Inactive', color: 'secondary' };
                case 2:
                    return { text: 'Resigned', color: 'warning' };
                case 3:
                    return { text: 'Terminated', color: 'danger' };
                default:
                    return { text: 'N/A', color: 'secondary' };
            }
        }
    },

    mounted() {
        this.getDetails();
        this.getGeneralData(["district", "upazila", "union"]);
    },
};
</script>

<style scoped>
table.table tr th:first-child,
table.table tr td:first-child {
    width: initial !important;
}

.employee-details-container {
    font-family: 'Segoe UI', 'Roboto', sans-serif;
    color: #333;
}

.card-header {
    border-bottom: 1px solid #e9ecef;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

.profile-photo-container {
    width: 150px;
    height: 150px;
    min-width: 150px;
    min-height: 150px;
}

.profile-photo-container img {
    object-fit: cover;
    width: 100%;
    height: 100%;
}

.detail-label {
    font-weight: 500;
    color: #6c757d;
}

.detail-value {
    font-weight: 400;
}

.section-card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1.5rem;
    background-color: #fff;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

h4 {
    border-left: 4px solid #007bff;
    padding-left: 10px;
}

.table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
}

.table th,
.table td {
    vertical-align: middle;
    padding: 1rem;
}

.table-bordered th,
.table-bordered td {
    border-color: #dee2e6;
}

.table-hover tbody tr:hover {
    background-color: #f1f1f1;
}

.badge {
    padding: 0.4em 0.8em;
    font-size: 0.9rem;
}

/* @media (max-width: 767.98px) {
    .detail-item {
        flex-direction: column;
    }
}

.text-light-grey {
    color: #ced4da;
}

.address-item {
    border: 1px solid #dee2e6;
} */




</style>