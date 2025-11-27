<template>
    <div class="align-items-center d-flex">
        <h4 class="card-title mb-0 flex-grow-1">
            <template v-if="showData($route.meta, 'isFormPage', true) === true">
                <span>{{topPageTitle}}</span>
            </template>
            <template v-else>
                <select @change="getDataList()" class="pointer btn btn-default perpage" v-model="formFilter.perPage">
                    <template v-for="page in perPages">
                        <option :value="page">{{topPageTitle}} {{page}}</option>
                    </template>
                </select>
            </template>
        </h4>
        <div class="flex-shrink-0">
            <div class="form-check form-switch form-switch-right form-switch-md table_header">
                <slot></slot>
                <button v-if="defaultAddButton" @click="openModal('formModal', pageModalTitle, false, false, defaultObject),preMethod()" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Add New
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "pageTop",
        props: {
            topPageTitle: {
                type: [String],
                default: 'Data List'
            },
            defaultAddButton: {
                type: [Boolean],
                default: true
            },
            pageModalTitle: {
                type: [String],
                default: 'Add/edit modal'
            },
            defaultObject : {
                type: [Array, Object],
                default(){
                    return [];
                }
            },

        },
        methods : {
            preMethod : function () {
                this.$emit('preMethod');
            }
        },
        mounted() {
            console.log(this.defaultObject);
        }
    }
</script>

<style scoped>

</style>
