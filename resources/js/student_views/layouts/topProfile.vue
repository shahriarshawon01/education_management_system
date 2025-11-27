<template>
    <div style="margin-top: 50px;">
        <div class="profile-foreground position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg"></div>
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4">
            <div class="row g-4">
                <div class="col-auto">
                    <div class="avatar-lg">
                        <template v-if="studentData.student !== null">
                            <!-- Student image if exists -->
                            <img v-if="studentImage" :src="studentImage"
                                style="height: 120px;width: 115px;border: 5px solid #fff;border-radius: 15px" />

                            <!-- Fallback: initials -->
                            <div v-else
                                class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                                style="height: 120px; width: 115px; font-weight: 600; font-size: 36px; border: 5px solid #fff; border-radius: 15px;">
                                {{ getInitials(showData(studentData.student, 'student_name_en')) }}
                            </div>
                        </template>
                    </div>
                </div>
                <div class="col">
                    <div class="p-2">
                        <h3 class="text-white mb-1">{{ showData(studentData.student, 'student_name_en') }}</h3>
                        <p class="text-white-75">{{ showData(Config.user, 'email') }}</p>
                        <div class="hstack text-white-50 gap-1">
                            <div class="me-2" v-for="(data, index) in studentData.student?.address" :key="index">
                                <i class="ri-map-pin-user-line me-1 text-white-75 fs-16 align-middle"></i>
                                {{ data.village ?? '' }}, {{ data.post_office ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
export default {
    name: "topProfile",
    data() {
        return {
            studentData: {
                student: null,
            },
        }
    },

    computed: {
        studentImage() {
            const img = this.showData(this.studentData.student, 'photo');
            if (!img || typeof img !== 'string' || img.trim() === '' || img === '-' || img.toLowerCase() === 'null' || img.toLowerCase() === 'NULL') {
                return null;
            }
            return this.getImage(img);
        },
    },

    methods: {
        getDashboardData: function () {
            const _this = this;
            _this.getData(_this.urlGenerate('dashboard'), "get", {}, {}, function (retData) {
                _this.studentData = retData;
            });
        },

        getInitials(name) {
            if (!name || typeof name !== 'string' || !name.trim()) return '';
            const parts = name.trim().split(' ');
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        },
    },
    mounted() {
        this.getDashboardData();
    }
}
</script>

<style scoped></style>
