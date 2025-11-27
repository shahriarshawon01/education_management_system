<template>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0  me-2">Profile Activity</h4>
                    </div>
                    <div class="card-body">

                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->
    </div>
</template>

<script>
    export default {
        name: "dashboardComponent",
        data(){
            return{
                Data : {},
            }
        },
        methods : {
            getDashboardData : function () {
                const _this = this;
                _this.getData(_this.urlGenerate(), "get", {}, {}, function (retData) {
                   _this.Data = retData;
                });
            }
        },
        mounted() {
            this.getDashboardData();
        }
    }
</script>

<style scoped>

</style>
