<template>
    <div class="modal fade" :id="modalId" :class="modalSize" :data-bs-backdrop="backDrop">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                    <div class="modal-header">
        <div class="col-12">
            <form @submit.prevent="search()">
                <div class="row mb-2 filter_desktop">
                    <!-- <div :class="defaultFilterWidth" v-if="defaultFilter">
                        <input type="text" v-model="formFilter.keyword" class="form-control" placeholder="Name"/>
                    </div> -->
                    <slot name="filter"></slot>

                    <div class="col-md-1" >
                        <slot name="buttom"></slot>
                        <button v-if="defaultSearchButton" class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
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
                    <div class="col-2" >
                        <slot name="buttom"></slot>
                        <button v-if="defaultSearchButton" @click="search()" class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

            <h5 class="modal-title" id="exampleModalFullscreenSmLabel">
                    <slot name="title"></slot>
                    <!-- {{modalTitle}} -->
                </h5>
                </div>
                <div class="modal-body" id="printDivModal">
                    <slot name="body"></slot>
                </div>
                <div class="modal-footer">
                    <div v-if="isEnablePrint" @click="printData('printDivModal')">
                            <button class="form-control"><i class="fa-sharp fa-solid fa-print"></i> Print</button>
                        </div>
                    <a @click="closeModal(modalId)" class="btn btn btn-outline-secondary fw-medium">
                        <i class="fa fa-close"></i> Close
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "getModal",
        props: {
            modalId: {
                type: [String],
                default: 'formModal'
            },
            modalSize: {
                type: [String],
                default: 'modal-xl'
            },
            isEnableFooter: {
                type: [Boolean],
                default: true
            },
            isEnablePrint: {
                type: [Boolean],
                default: true
            },
            backDrop : {
                type: [String],
                default: 'static'
            },
            defaultFilter: {
                type: [Boolean],
                default: true,
            },
            defaultFilterWidth: {
                type: [String],
                default: "col-md-2 mb-1",
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
            }
        },
        methods : {
            submit : function () {
                this.$emit('submit');
            },
            search: function () {
                 this.$emit("search");
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
    }
</script>

<style scoped>

</style>