<template>
    <div class="modal fade" :id="modalId" :class="modalSize" :data-bs-backdrop="backDrop">
        <div class="modal-dialog modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalFullscreenSmLabel">{{ modalTitle }}</h5>
                    <button type="button" class="btn-close" @click="closeModal(modalId)"></button>
                </div>
                <div class="modal-body">
                    <template v-for="(value, index) in $store.state.globalDetailsData">
                        <div class="row" v-if="!hiddenField.includes(index)">
                            <template v-if="typeof value !== 'object'">
                                <div class="col-4">
                                    <strong>{{ capitalize(replaceString(index, '_')) }} : </strong>
                                </div>
                                <div v-if="index == 'status'" class="col-8">{{ parseInt(value) === 1 ? 'Active' :
                                    'InActive'}}</div>
                                <div v-else class="col-8">{{ value }}</div>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="modal-footer">
                    <a v-if="isEnablePrint" @click="closeModal(modalId)"
                        class="btn btn btn-outline-secondary fw-medium">
                        <i class="fa fa-close"></i> Close
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "detailsModal",
    data() {
        return {
            hiddenField: ['id', 'created_at', 'updated_at'],
        }
    },
    props: {
        resetFormData: {
            type: [Boolean],
            default: true
        },
        modalId: {
            type: [String],
            default: 'detailsModal'
        },
        modalSize: {
            type: [String],
            default: 'modal-md'
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
        backDrop: {
            type: [String],
            default: 'static'
        }
    }
}
</script>

<style scoped></style>