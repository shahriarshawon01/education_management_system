<template>
    <div class="modal fade" :id="modalId" :class="modalSize" :data-bs-backdrop="backDrop">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <form @submit.prevent="submit()">
                <div :class="`modal-content ${colorClass}`">
                    <div :class="`modal-header ${colorClass}`">
                        <h5 class="modal-title" id="exampleModalFullscreenSmLabel">{{modalTitle}}</h5>
                        <button type="button" class="btn-close" @click="closeModal(modalId, resetFormDataOnCloseIcon)"></button>
                    </div>
                    <div class="modal-body">
                        <slot></slot>
                    </div>
                    <div class="modal-footer">
                        <a v-if="isEnablePrint" @click="closeModal(modalId, resetFormData)" class="btn btn btn-outline-secondary fw-medium">
                            <i class="fa fa-close"></i> Close
                        </a>
                        <button v-if="isEnablePrintSubmit" type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "formModal",
        props: {
            resetFormData: {
                type: [Boolean],
                default: true
            },
            resetFormDataOnCloseIcon: {
                type: [Boolean],
                default: true
            },
            modalId: {
                type: [String],
                default: 'formModal'
            },
            colorClass: {
                type: [String],
                default: ''
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
            isEnablePrintSubmit: {
                type: [Boolean],
                default: true
            },
            backDrop : {
                type: [String],
                default: 'static'
            }
        },
        methods : {
            submit : function () {
                this.$emit('submit');
            }
        }
    }
</script>

<style scoped>

</style>
