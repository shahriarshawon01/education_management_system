
<template>
    <div class="card" :class="tableClass">
        <div class="card-header">
            <slot name="page_top"></slot>
        </div>
        <div class="card-body">
            <form @submit.prevent="search()">
                <div class="row mb-2 filter_desktop">
                    <!-- <div :class="defaultFilterWidth" v-if="defaultFilter">
                        <input type="text" v-model="formFilter.keyword" class="form-control" placeholder="Name"/>
                    </div> -->
                    <slot name="filter"></slot>

                    <div class="col-md-1" v-if="defaultSearchButton">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                    <slot name="sorting"></slot>
                </div>
                <div class="row mb-2 filter_mobile">
                    <div class="col-10">
                        <div class="row">
                            <!-- <div :class="defaultFilterWidth" v-if="defaultFilter">
                                <input type="text" v-model="formFilter.keyword" class="form-control" placeholder="Name"/>
                            </div> -->
                            <slot name="filter"></slot>
                        </div>
                    </div>
                    <div class="col-2" v-if="defaultSearchButton">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
            <!-- <slot name="reportHeader"></slot> -->
            <div class="live-preview">
                <div   id="printDiv">
                    <table class="table data_table table-bordered align-middle table-nowrap mb-0">
                        <thead class="data_table prevent-select" v-if="tableHeading.length > 0">
                        <tr>
                            <template v-for="(heading, index) in tableHeading">
                                <th scope="col">{{ heading }}</th>
                            </template>
                        </tr>
                        </thead>
                        <slot name="thead"></slot>
                        <tbody>
                        <slot name="data"></slot>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: "resultTable",
        props: {
            tableHeading: {
                type: [Array, Object],
                default: [],
            },
            tableTitle: {
                type: [String],
                default: "Data List",
            },
            defaultAddButton: {
                type: [Boolean],
                default: true,
            },
            defaultFilter: {
                type: [Boolean],
                default: true,
            },
            defaultFilterWidth: {
                type: [String],
                default: "col-md-2 mb-1",
            },
            tableClass: {
                type: [String],
                default: "data_table",
            },
            defaultSearchMethod: {
                type: [Boolean],
                default: true,
            },
            defaultSearchButton: {
                type: [Boolean],
                default: true,
            },
            defaultObject: {
                type: [Array, Object],
                default() {
                    return [];
                },
            },
        },
        methods: {
            search: function () {
                if (this.defaultSearchMethod) {
                    this.getDataList();
                } else {
                    this.$emit("search");
                }
            },

            assignDefaultFIlter: function () {
                const _this = this;

                $.each(_this.defaultObject, function (index, value) {
                    _this.$set(_this.formFilter, index, value);
                });
            },
        },
        mounted() {
            this.assignDefaultFIlter();
        },
    };
</script>

<style scoped>
    .table_header .btn {
        margin-right: 3px !important;
    }

    .prevent-select {
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>