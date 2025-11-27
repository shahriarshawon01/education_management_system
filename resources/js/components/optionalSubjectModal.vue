<template>
    <transition name="modal-fade">
        <div v-if="isOpen" class="modal-backdrop" @click.self="close">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title">Update Optional Subject</h5>
                            <p class="modal-subtitle">Choose an optional subject for the student</p>
                        </div>
                        <button type="button" class="btn-close" @click="close" aria-label="Close">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="subject-list">
                            <pre>{{ "Sub : ". selectedSubjectId }}</pre>
                            <label v-for="subject in subjects" :key="subject.id" class="subject-option"
                                :class="{ 'selected': selectedSubjectId === subject.id }">
                                <input type="radio" class="subject-radio" :value="subject.id"
                                    v-model="selectedSubjectId" name="optional_subject" />
                                <span class="subject-checkmark"></span>
                                <span class="subject-name">{{ subject.name }}</span>
                            </label>
                            <label class="subject-option" :class="{ 'selected': selectedSubjectId === null }">
                                <input type="radio" class="subject-radio" :value="null" v-model="selectedSubjectId"
                                    name="optional_subject" />
                                <span class="subject-checkmark"></span>
                                <span class="subject-name">None (No Optional Subject)</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="close">
                            Cancel
                        </button>
                        <button class="btn btn-primary" @click="submit">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="margin-right: 6px;">
                                <path d="M13.5 4L6 11.5L2.5 8" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Update Subject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: "optionalSubjectModal",
    props: {
        subjects: { type: Array, default: () => [] },
        studentId: { type: [Number, null], default: null },
        modelValue: { type: [Number, null], default: null }
    },
    emits: ["update:modelValue", "submit", "close"],
    data() {
        return {
            isOpen: false,
            selectedSubjectId: this.modelValue ?? null
        };
    },
    watch: {
        modelValue(newVal) {
            this.selectedSubjectId = newVal ?? null;
        }
    },
    methods: {
        open() {
            this.selectedSubjectId = this.modelValue ?? null;
            this.isOpen = true;
        },
        close() {
            this.isOpen = false;
            this.$emit("close");
        },
        submit() {
            this.$emit("update:modelValue", this.selectedSubjectId);
            this.$emit("submit", {
                studentId: this.studentId,
                optionalSubjectId: this.selectedSubjectId
            });
            this.close();
        }
    }
};
</script>



<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-fade-enter-active .modal-dialog,
.modal-fade-leave-active .modal-dialog {
    transition: transform 0.3s ease;
}

.modal-fade-enter-from .modal-dialog,
.modal-fade-leave-to .modal-dialog {
    transform: scale(0.95) translateY(-20px);
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
    padding: 10px;
}

.modal-dialog {
    width: 100%;
    max-width: 480px;
    animation: slideUp 0.3s ease;
}

.modal-content {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.modal-header {
    padding: 6px 10px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.modal-title {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    line-height: 1.3;
}

.modal-subtitle {
    margin: 4px 0 0 0;
    font-size: 14px;
    color: #6b7280;
    font-weight: 400;
}

.btn-close {
    background: transparent;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.2s ease;
    flex-shrink: 0;
    margin-left: 16px;
}

.btn-close:hover {
    background: #f3f4f6;
    color: #1f2937;
}

.modal-body {
    padding: 16px;
}

.subject-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.subject-option {
    display: flex;
    align-items: center;
    padding: 2px 4px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
    position: relative;
}

.subject-option:hover {
    border-color: #d1d5db;
    background: #f9fafb;
    transform: translateX(2px);
}

.subject-option.selected {
    border-color: #3b6e67;
    background: #eff6ff;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.subject-radio {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.subject-checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    margin-right: 12px;
    flex-shrink: 0;
    position: relative;
    transition: all 0.2s ease;
    background: #ffffff;
}

.subject-option:hover .subject-checkmark {
    border-color: #9ca3af;
}

.subject-option.selected .subject-checkmark {
    border-color: #3b6e67;
    background: #3b6e67;
}

.subject-option.selected .subject-checkmark::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: #ffffff;
    border-radius: 50%;
}

.subject-name {
    font-size: 15px;
    color: #374151;
    font-weight: 500;
    transition: color 0.2s ease;
}

.subject-option.selected .subject-name {
    color: #3b6e67;
}

.modal-footer {
    padding: 20px 28px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #f9fafb;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    letter-spacing: -0.01em;
}

.btn-secondary {
    background: #ffffff;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-secondary:active {
    transform: scale(0.98);
}

.btn-primary {
    background: linear-gradient(135deg, #3b6e67 0%, #3b6e67 100%);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #3b6e67 0%, #3b6e67 100%);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    transform: translateY(-1px);
}

.btn-primary:active {
    transform: translateY(0);
}

@media (max-width: 576px) {
    .modal-dialog {
        max-width: 100%;
        margin: 0;
    }

    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 20px;
    }

    .modal-title {
        font-size: 18px;
    }

    .subject-option {
        padding: 12px 14px;
    }
}
</style>



<!-- another think if any student have a optional subject that one be selected other wise selected this 
 <label class="form-check-label mb-2">
                            <input type="radio" class="form-check-input" :value="null" v-model="selectedSubjectId"
                                name="optional_subject" />
                            None (No Optional Subject)
                        </label> -->