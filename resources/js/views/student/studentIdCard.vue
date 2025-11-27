<template>
    <div class="card-container">
        <div class="print_preview_wrapper front">
            <div id="print-preview" class="print-preview">
                <div class="print-format" :style="idCardFontStyles">
                    <div style="width: 100%; text-align: left;">
                        <img class="mouse-action" :src="getImage('uploads/logotpsc.png', null)"
                            style="width:60px; height:60px;border: 2px white solid;border-radius: 50%;margin-top: 10px; margin-left: 35px;">
                    </div>
                    <p
                        style="margin-bottom: -2px;color: #ffffff;font-size: 42px;text-align: right;margin-top: -58px;margin-right: 24px;font-family: none; font-weight: bold">
                        TPSC</p>

                    <div style="width: 100%; margin-top: 10px;">
                        <img :src="getImage(studentData.photo)" class="img-responsive"
                            style="height:120px;border: 2px black solid;width:100px;display: block; margin-top: 0px;float: right;margin-right: 20px;" />
                    </div>

                    <div style="width: 100%;display: block;font-size: 18px;">
                        <p
                            style="margin:150px 10px 0px 10px;font-weight: bold;border: 2px black solid; border-radius: 15px; text-align: center; background-color: white; text-transform: capitalize;">
                            {{ studentData.student_name }}</p>
                    </div>
                    <div class="" style="width: 100%;display: block;font-size:18px;">
                        <p style="margin:0px 15px 4px 6px;font-weight: bold; text-align: center;">
                            <span style="padding-right: 8px;"><b>Class</b></span> <span><b style="margin-left: -8px;">
                                    :</b></span>
                            <span style="padding-right: 0px;">{{ studentData.class }}</span>
                        </p>
                    </div>

                    <div style="width: 100%; display: block; font-size: 14px;">
                        <p
                            style="margin: 2px 20px 1px 6px; font-weight: bold; display: flex; justify-content: space-between;">
                            <span><b>Sec:</b> {{ studentData.section }}</span>
                            <span><b>Roll:</b> {{ studentData.merit_roll }}</span>
                        </p>
                    </div>

                    <div style="width: 100%;display: block;font-size: 14px;line-height: 1;padding-left: 0px;">
                        <p style="margin:7px 0px 4px 6px;font-weight: bold; text-transform: capitalize;"><b>Father :
                            </b> {{
                                studentData.father_name
                            }}</p>
                    </div>

                    <div class="" style="width: 100%;display: block;font-size: 14px;line-height:1;padding-left: 0px;">
                        <p style="margin: 7px 0px 4px 6px; font-weight: bold; text-transform: capitalize;">
                            <b>Mother : </b> {{ studentData.mother_name }}
                        </p>
                    </div>

                    <div style="width: 100%;display: block;font-size: 14px;padding-left: 0px;">
                        <p style="margin:5px 0px 4px 6px;font-weight: bold;"><b>Blood Group :</b> {{
                            studentData.blood_group }}</p>
                    </div>
                    <div class="personal_details_wrapper">
                        <div
                            style="display: block;font-size: 15px;position: absolute;left:0px;writing-mode: vertical-rl;background-color: green;border-bottom-left-radius: 10px;border-top-left-radius: 10px;color: white;top: 19.7%;padding: 3px 5px;width: 24.9px;line-height: 1.5;font-weight: bold; transform: rotate(180deg)">
                            <span style="rotate: 180deg"><b>Reg.No :</b> {{ studentData.student_roll }}</span>
                        </div>

                        <div
                            style="width:28%;text-align: center;margin-top: 5px;display: block;position: absolute;right: 0;bottom: 5px; font-size:12px;margin-right: 10px">
                            <div class="" style="width: 100%;text-align: center;">
                                <img class="mouse-action" :src="getImage('uploads/principalSignature.png', null)"
                                    style="height:35px;margin-bottom: -8px;">
                            </div>
                            <hr style="width:90%;margin-top: 0px;margin-bottom: 0;padding: 0;">
                            <span><b style="font-size: 15px;">Principal</b></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="print-tool-bar">
                <div class="btn-group tool" style="text-align:center !important; display:block">
                    <a @click="printDiv('front')" class="btn-print-print btn-sm btn btn-default print-button mt-3"
                        data-side="front">
                        <strong style="font-size: 14px">Print</strong>
                    </a>
                </div>
            </div>
        </div>

        <div class="print-preview_wrapper back">
            <div id="print-preview-back" class="print-preview-back">
                <div class="print-format" :style="idCardBackStyles">
                    <div style="width: 100%;margin-top: 15%;margin-left:25px;">
                        <p
                            style="font-size: 18px;width: 100%;text-align: left;margin-top: 12px ; margin-bottom: 0px;font-weight: bold; margin-left: 10px;">
                            Student Address :</p>
                        <hr
                            style="width: 63%;margin-bottom: 0px;margin-top:-5px; margin-left:10px;border-bottom: 2px solid black;">

                        <p v-if="primaryAddress && primaryAddress.village && primaryAddress.district"
                            style="font-size: 18px; margin: 7px; margin-left: 10px; font-weight: normal;">
                            {{ primaryAddress.village || 'N/A' }}, {{ primaryAddress.district || 'N/A' }}
                        </p>
                        <p v-else
                            style="font-size: 18px; margin: 7px; margin-left: 10px; font-weight: normal; color: red;">
                            Address not available.
                        </p>
                    </div>

                    <div class="" style="width: 100%;margin-top: 25%; margin-left:34px;">
                        <p
                            style="font-size: 18px;width: 100%;text-align: left;margin-top: 9px; margin-bottom: 0px;font-weight: bold;">
                            Contact Number :</p>
                        <hr
                            style="width: 63%;margin-bottom: 0px;margin-top:-5px;margin-left:0px;border-bottom: 2px solid black;">

                        <p
                            style="font-size: 18px;margin-left:0px;font-weight: normal;text-align: left;margin-top: 8px;">
                            {{ studentData.father_phone }}</p>
                    </div>

                    <div style="width: 100%;margin-top: 45%;text-align: left; margin-left: 35px;">
                        <p
                            style="font-size: 18px;width: 100%; margin-top: -20px; margin-bottom: 0px;font-weight: bold;">
                            If found please return to</p>
                        <hr
                            style="width: 85%;margin-bottom: 1px;margin-top:-2px;margin-left:0px;border-bottom: 2px solid black;">

                        <h1 style="font-size: 18px;margin-top:3%;margin-bottom: 0;font-weight: bold;">
                            {{ studentData.school ? studentData.school.title : 'School Title Unavailable' }}
                        </h1>

                        <p style="font-size: 18px;font-weight: semi-bold;margin-top: 0px;">
                            {{ studentData.school ? studentData.school.address : 'Address Unavailable' }}
                        </p>
                        <p style="font-size: 18px;font-weight: semi-bold;margin-top: -15px;">
                            Phone: {{ studentData.school ? studentData.school.phone : 'Phone Unavailable' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="print-tool-bar">
                <div class="btn-group tool" style="text-align:center !important; display:block">
                    <a @click="printBack" class="btn-print-print btn-sm btn btn-default print-button mt-3">
                        <strong style="font-size: 14px">Print</strong>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import PageTop from "../../components/pageTop";
import formModal from "../../components/formModal";
import axios from 'axios';

export default {
    name: "studentIdCard",
    components: { PageTop, formModal },
    data() {
        return {
            studentData: {
                address: [],
            },
            letterHead: true,
            printedSide: "",
        };
    },
    computed: {
        primaryAddress() {
            if (!this.studentData || !this.studentData.address) {
                return {};
            }
            const presentAddress = this.studentData.address;
            return {
                village: presentAddress.village || 'N/A',
                post_office: presentAddress.post_office || 'N/A',
                district: presentAddress.district || 'N/A'
            };
        },

        idCardFontStyles() {
            return {
                width: '237.6px',
                height: '378.32px',
                position: 'relative',
                padding: '5px',
                float: 'left',
                marginLeft: '-7px',
                marginTop: '-8px',
                backgroundSize: 'cover',
                backgroundRepeat: 'no-repeat',
                backgroundImage: `url(${this.getImage('uploads/id-card-bg-image.png', null)})`,
            };
        },

        idCardBackStyles() {
            return {
                width: '237.6px',
                height: '378.32px',
                position: 'relative',
                padding: '5px',
                float: 'left',
                marginLeft: '-5px',
                marginTop: '-8px',
            };
        },
    },
    methods: {
        fetchStudentData() {
            const studentId = this.$route.params.id;

            if (studentId) {
                axios.post(`/api/student_id_card/${studentId}`)
                    .then(response => {

                        if (response.data) {
                            this.studentData = response.data;
                            console.log("student ID data : ", this.studentData);
                            
                        } else {
                            console.error('Unexpected response format:', response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching student data:', error);
                    });
            } else {
                console.error('Student ID not provided in route parameters');
            }
        },

        printDiv(side) {
            const divToPrint = side === "front" ? document.getElementById('print-preview') : document.getElementById('print-preview-back');
            const newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write(`
        <html>
            <head><style>body{font-family: "Helvetica Neue", Helvetica, Arial, "Open Sans", sans-serif;}</style></head>
            <body onload="window.print()">${divToPrint.innerHTML}</body>
        </html>`);
            newWin.document.close();
            this.printedSide = side;
            this.updatePrintStatus(side);
        },

        printBack() {
            const divToPrint = document.getElementById('print-preview-back');
            const newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write(`
        <html>
            <head><style>body{font-family: "Helvetica Neue", Helvetica, Arial, "Open Sans", sans-serif;}</style></head>
            <body onload="window.print()">${divToPrint.innerHTML}</body>
        </html>`);
            newWin.document.close();
            this.updatePrintStatus('back');
        },

        updatePrintStatus(side) {
            let printStatus = side === 'front' ? 1 : 2;
            let successMessage = side === 'front' ? 'Front side print successfully' : 'Back side print successfully';
            axios.post(`/api/update_print_status/${this.studentData.id}`, { print_status: printStatus })
                .then(response => {
                    this.$toastr('success', successMessage, "Success");
                })
                .catch(error => {
                    this.$toastr('error', 'Failed to update print status', "Error");
                    console.error('Error updating print status:', error);
                });
        },

        updateButtonColor() {
            const printButtons = document.querySelectorAll(".print-button");
            for (let button of printButtons) {
                button.style.backgroundColor = this.printedSide === button.dataset.side ? (this.printedSide === 'front' ? 'yellow' : 'gray') : '#007bff';
            }
        }
    },
    mounted() {
        this.fetchStudentData();
    },
};
</script>

<style scoped>
.card-container {
    display: flex;
    gap: 70px;
    justify-content: center;
    align-items: unset;
    min-height: 100vh;
    padding: 20px;
}

.section-title {
    font-weight: bold !important;
}

.section-divider {
    width: 90%;
    margin-bottom: 5px;
    margin-top: -2px;
    margin-left: 0px;
    border: 1.5px solid rgba(48, 48, 48);
}

.cards-wrapper {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.print-format {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 15px;
    overflow: hidden;
}

.print-button {
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.print-button:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

.print-button:active {
    background-color: #004085;
    transform: translateY(0);
}

.print-button:focus {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
}
</style>
