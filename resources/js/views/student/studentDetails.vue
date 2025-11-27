<template>
    <div class="employee-details-container p-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-white p-3">
                <h3 class="card-title fw-bold m-0 text-primary">Student Details</h3>
                <div class="action-buttons">
                    <router-link to="/admin/student" class="btn btn-outline-secondary me-2">
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
                        <h2 class="fw-bold mb-0 text-primary">{{ details.school ? details.school.title : '' }}</h2>
                        <h5 class="text-muted">{{ details.school ? details.school.address : '' }}</h5>
                        <h6 class="text-muted">Institute Code: {{ details.school ? details.school.institute_code : '' }}
                        </h6>
                    </div>
                </div>

                <hr class="my-2" />

                <div class="section-card mb-4">
                    <h4 class="fw-bold text-dark mb-3">Basic Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Student Name (en):</span>
                                <span class="detail-value col-8 text-dark">{{ details.student_name_en }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Student Name (bn):</span>
                                <span class="detail-value col-8 text-dark">{{ details.student_name_bn || 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Merit Roll:</span>
                                <span class="detail-value col-8 text-dark">{{ details.merit_roll }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Student ID:</span>
                                <span class="detail-value col-8 text-dark">{{ details.student_roll }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Registration No:</span>
                                <span class="detail-value col-8 text-dark">{{ details.reg_number || 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="details.user && details.user.email">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Email:</span>
                                <span class="detail-value col-8 text-dark">{{ details.user.email }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Phone:</span>
                                <span class="detail-value col-8 text-dark">{{ details.student_phone || 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Date Of Birth</span>
                                <span class="detail-value col-8 text-dark">{{ details.date_of_birth || 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Nationality</span>
                                <span class="detail-value col-8 text-dark">{{ details.nationality }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Blood Group</span>
                                <span class="detail-value col-8 text-dark">{{ details.blood_group || 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Religion</span>
                                <span class="detail-value col-8 text-dark"
                                    v-if="parseInt(details.religion) === 1">Muslim</span>
                                <span class="detail-value col-8 text-dark"
                                    v-else-if="parseInt(details.religion) === 2">Hindus</span>
                                <span class="detail-value col-8 text-dark"
                                    v-else-if="parseInt(details.religion) === 3">Christian</span>
                                <span class="detail-value col-8 text-dark"
                                    v-else-if="parseInt(details.religion) === 4">Buddhist</span>
                                <span class="detail-value col-8 text-dark"
                                    v-else-if="parseInt(details.religion) === 4">Others</span>
                            </div>
                        </div>
                        <div class="col-md-6" v-if="details.status !== undefined">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Status:</span>
                                <span class="detail-value col-8">
                                    <span class="badge" :class="'bg-' + statusBadge.color">{{ statusBadge.text }}</span>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
                <hr class="my-2" />

                <div class="section-card mb-4">
                    <h4 class="fw-bold text-dark mb-3">Academic Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Class</span>
                                <span class="detail-value col-8 text-dark">{{ details.classes ? details.classes.name :
                                    '' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Session</span>
                                <span class="detail-value col-8 text-dark">{{ details.sessions ? details.sessions.title
                                    : '' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Section</span>
                                <span class="detail-value col-8 text-dark">{{ details.sections ? details.sections.name :
                                    '' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex detail-item">
                                <span class="detail-label col-4">Department</span>
                                <span class="detail-value col-8 text-dark">{{ details.departments ?
                                    details.departments.department_name : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section-card mb-4">
                    <h4 class="fw-bold text-dark mb-3">Guardian Information</h4>

                    <div class="row">
                        <hr>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h4>Father Information</h4>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Father Name (en)</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.father_name_en) || 'N/A' }}</label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Father Name (bn)</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7"> 
                                        <label>{{ (details && details.father_name_bn) || 'N/A' }}</label>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Father Phone</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.father_phone) || 'N/A' }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Father Nid</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.father_nid) || 'N/A' }}</label>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Father Profession</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.father_profession) || 'N/A' }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Father Yearly Income</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.yearly_income) || 'N/A' }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <h4>Mother Information</h4>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Mother Name (en)</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.mother_name_en) || 'N/A' }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Mother Name (bn)</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.mother_name_bn) || 'N/A' }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Mother Phone</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.mother_phone) || 'N/A' }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Mother Nid</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.mother_nid) || 'N/A' }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="row">
                                    <label class="col-md-4">Mother Mother Profession</label>
                                    <label class="col-md-1">:</label>
                                    <div class="col-md-7">
                                        <label>{{ (details && details.mother_profession) || 'N/A' }}</label>
                                    </div>
                                </div>
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
                    <div class="row mb-3">
                        <table class="table table-bordered mt-4">
                            <thead class="data_table">
                                <tr>
                                    <th style="width: 15%;">Exam Name</th>
                                    <th style="width: 15%;">Board Name/University</th>
                                    <th style="width: 15%;">Registration No</th>
                                    <th style="width: 15%;">Roll No</th>
                                    <th style="width: 15%;">Group/Subject</th>
                                    <th style="width: 15%;">Passing Year</th>
                                    <th style="width: 15%;">GPA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(qulific, gIndex) in details.student_qualifications" :key="gIndex">
                                    <td>{{ qulific.exam_name }}</td>
                                    <td>{{ qulific.board_name }}</td>
                                    <td>{{ qulific.reg_number }}</td>
                                    <td>{{ qulific.roll_number }}</td>
                                    <td>{{ qulific.group }}</td>
                                    <td>{{ qulific.passing_year }}</td>
                                    <td>{{ qulific.gpa }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="my-2" />

                <!-- <div class="section-card">
                    <h4 class="fw-bold text-dark mb-3">Local Guardian Information</h4>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-4">
                                            <strong>Guardian Name</strong>
                                        </div>
                                        <div class="col-4">
                                            <strong>Guardian Mobile</strong>
                                        </div>
                                        <div class="col-4">
                                            <strong>Guardian Relation</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>

                        <div class="col-md-12">
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-4">
                                            <label>{{ guardian.guardian_name }}</label>
                                        </div>
                                        <div class="col-4">
                                            <label>{{ guardian.guardian_phone }}</label>
                                        </div>
                                        <div class="col-4">
                                            <label>{{ guardian.relation }}</label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

            </div>
        </div>
    </div>
</template>

<script>
import DataTable from "../../components/dataTable";
import Pagination from "../../plugins/pagination/pagination";
import PageTop from "../../components/pageTop";

export default {
    name: "studentDetails",
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
                _this.address = retData.address;
                _this.guardian = retData.guardian.length ? retData.guardian[0] : {};

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

@media (max-width: 767.98px) {
    .detail-item {
        flex-direction: column;
    }
}

.text-light-grey {
    color: #ced4da;
}

.address-item {
    border: 1px solid #dee2e6;
}
</style>