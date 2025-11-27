<template>
    <div class="report-header">
        <!-- <img v-if="schoolInfo.school_logo" class="school-logo" :src="getLogoUrl(schoolInfo.school_logo)" alt="Logo" /> -->
        <img v-if="schoolInfo.school_logo" :src="getImage('uploads/logotpsc.png')" class="school-logo" alt="Logo" />
        <h3 class="school-name">{{ schoolInfo.name }}</h3>
        <p class="school-address">{{ schoolInfo.address }}</p>
        <h4 class="report-title">{{ title }}</h4>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        title: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            schoolInfo: {
                name: "Loading...",
                address: "Loading...",
                school_logo: null,
            },
        };
    },
    mounted() {
        this.fetchSchoolInfo();
    },
    methods: {
        fetchSchoolInfo() {
            axios.get("/api/reports/school_info")
                .then((response) => {
                    this.schoolInfo = response.data;
                })
                .catch((error) => {
                    console.error("Error fetching school info:", error);
                    this.schoolInfo = {
                        name: "TMSS Public School & College",
                        address: "Joypur Para, Bogura",
                        school_logo: null,
                    };
                });
        },
    },
};
</script>

<style scoped>
.report-header {
    text-align: center;
    margin-bottom: 6px;
}

.school-logo {
    width: 70px;
    height: 70px;
    margin-bottom: 6px;
}

.school-name {
    font-size: 20px;
    font-weight: bold;
    color: #2c3e50;
    margin: 0;
}

.school-address {
    font-size: 16px;
    color: #7f8c8d;
    margin: 2px 0;
}

.report-title {
    font-size: 18px;
    font-weight: 600;
    color: #34495e;
    text-transform: uppercase;
    border-top: 1px solid #bdc3c7;
    border-bottom: 1px solid #bdc3c7;
    padding: 6px 0;
    display: inline-block;
    margin-top: 0px;
}
</style>
