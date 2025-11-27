<template>
    <div class="modal fade" :id="modalId" :class="modalSize" :data-bs-backdrop="backDrop">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <form @submit.prevent="search()">
                <div class="modal-content">
                    <div class="modal-header">
                    <div class="col-12">
                        <div style="text-align: right;">
                            <a @click="onCloseButton" class="btn btn-sm btn-light"><i class="fa fa-close"></i> Close</a>
                        </div>
                    </div>
                    <h5 class="modal-title" id="exampleModalFullscreenSmLabel">
                        <slot name="title"></slot>
                            <!-- {{modalTitle}} -->
                        </h5>
                        </div>
                        <div class="modal-body">
                            <slot name="filter"></slot>
                        </div>
                        <div class="modal-footer">
                            <div>
                                <slot name="buttom"></slot>
                                <button v-if="defaultSearchButton" class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i> Apply</button>
                                <button v-if="defaultSearchButton" class="btn btn-outline-primary" @click.prevent="$emit('clear')"><i class="fa fa-trash"></i> Clear</button>
                            </div>

                        </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            modalId: {
                type: [String],
                default: 'formModal'
            },
            modalSize: {
                type: [String],
                default: 'modal-lg'
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
            },
            disableCloseButtonAction: {
                type: Boolean,
                default: false
            },
        },
        methods : {
            submit : function () {
                this.$emit('submit');
            },
            search: function () {
                if (this.defaultSearchMethod) {
                    this.getDataList();
                } else {
                    this.$emit("search");
                }
            },
            onCloseButton() {
                if(this.disableCloseButtonAction) {
                    this.$emit('close');
                    $('#' + this.modalId).modal('hide');
                } else {
                    this.closeModal(modalId);
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
    }
</script>

<style scoped>

</style>