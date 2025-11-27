<template>
    <div class="autocomplete">
        <div class="autocomplete_input row">
            <div class="col-12">
                <div class="input-group">
                    <input class="form-control" v-model="inputValue" :placeholder="placeholder" :readonly="readonly"
                        :disabled="disabled" :name="name" :maxlength="maxlength" :data-vv-as="validation_name"
                        v-validate="validate" autocomplete="off" @input="onInput" @keyup.down="onArrowDown"
                        @keyup.up="onArrowUp" @keyup.enter="onEnter" @blur="$emit('blur')" />
                </div>
            </div>
        </div>
        <ul id="autocomplete-results" class="autocomplete-results" v-if="results.length > 0">
            <li v-for="(result, index) in results" :key="index" @click="setResult(result)"
                :class="['autocomplete-result', { 'highlighted': index === arrowCounter }]">
                <div class="each_member">
                    <template v-if="result.product_name && result.product_code">
                        <p class="auto-complete">
                            <strong>{{ result.product_name }} ({{ result.product_code }})</strong>
                        </p>
                        <small v-if="result.category_name"><strong>Category:</strong> {{ result.category_name }}</small>
                    </template>

                    <template v-else>
                        <div v-if="result.student_name_en && result.student_roll" class="member-card">
                            <p class="member-name">
                                {{ result.student_name_en }}
                                <span v-if="result.student_roll" class="roll">({{ result.student_roll }})</span>
                            </p>
                            <p class="member-meta">
                                <span v-if="result.class_name"><strong>Class:</strong> {{ result.class_name }}</span>
                            </p>
                            <p class="member-meta">
                                <span v-if="result.session_name"><strong>Session:</strong> {{ result.session_name
                                }}</span>
                            </p>
                        </div>
                        <div v-else-if="result.name && result.employee_id" class="member-card">
                            <p class="member-name">
                                {{ result.name }}
                                <span v-if="result.employee_id" class="emp-id">({{ result.employee_id }})</span>
                            </p>
                            <p class="member-meta">
                                <span v-if="result.department"><strong>Department</strong> {{
                                    result.department }}</span>
                            </p>
                            <p class="member-meta">
                                <span v-if="result.designation"><strong>Designation:</strong> {{
                                    result.designation }}</span>
                            </p>
                        </div>
                        <div v-else class="member-card">
                            <p class="member-name">{{ result.name || 'Unknown' }}</p>
                        </div>
                    </template>


                </div>
            </li>
        </ul>

    </div>
</template>

<script>
export default {
    name: "autocomplete",
    props: {
        maxlength: String,
        minlength: String,
        placeholder: String,
        validate: String,
        value: String,
        name: String,
        readonly: {
            type: Boolean,
            default: false,
        },
        validation_name: String,
        apiUrl: {
            type: String,
            required: true,
        },
        disabled: {
            type: Boolean,
            default: false
        },

    },
    data() {
        return {
            isOpen: false,
            results: [],
            inputValue: this.value || "",
            isLoading: false,
            arrowCounter: -1,
        };
    },
    methods: {
        onInput(e) {
            this.inputValue = e.target.value;
            this.$emit("input", this.inputValue);

            if (this.inputValue.length >= 2) {
                this.filterResults(this.inputValue);
            } else {
                this.results = [];
            }
        },

        filterResults(query) {
            this.isLoading = true;

            let url = this.apiUrl;

            if (url.includes("get_product")) {
                url = `${this.apiUrl}?query=${query}`;
            } else if (url.includes("get-employee")) {
                url = `${this.apiUrl}?employee_id=${query}`;
            } else {
                url = `${this.apiUrl}?student_roll=${query}`;
            }

            this.axios
                .get(url)
                .then((res) => {
                    this.isLoading = false;
                    if (res.data.result && Array.isArray(res.data.result)) {
                        this.results = res.data.result;
                    } else {
                        this.results = [];
                    }
                })
                .catch(() => {
                    this.isLoading = false;
                    this.results = [];
                });
        },

        setResult(result) {
            if (result.product_name && result.product_code) {
                this.inputValue = result.product_code;
                this.$emit('select', {
                    type: 'product',
                    id: result.id,
                    product_code: result.product_code,
                    product_name: result.product_name,
                    category: result.category_name || result.category,
                    price: result.price,
                    stock_quantity: result.stock_quantity,
                });
            }
            // Student selected
            else if (result.student_name_en && result.student_roll) {
                this.inputValue = result.student_roll;
                this.$emit('select', result);
            }
            // Employee selected
            else if (result.name && result.employee_id) {
                this.inputValue = result.employee_id;
                this.$emit('select', result);
            }
            // fallback
            else {
                this.inputValue = result.name || '';
                this.$emit('select', result);
            }

            // reset dropdown
            this.results = [];
            this.arrowCounter = -1;
        },

        onArrowDown() {
            if (this.arrowCounter < this.results.length - 1) {
                this.arrowCounter++;
            }
        },

        onArrowUp() {
            if (this.arrowCounter > 0) {
                this.arrowCounter--;
            }
        },

        onEnter() {
            if (this.arrowCounter >= 0) {
                this.setResult(this.results[this.arrowCounter]);
            }
        },
    },
    watch: {
        value(newVal) {
            this.inputValue = newVal;
        },
    },
};
</script>

<style scoped>
.each_member {
    padding: 2px 4px;
    border-radius: 10px;
    transition: all 0.2s ease-in-out;
}

.autocomplete-result:hover,
.highlighted {
    background: linear-gradient(90deg, #f0f7ff 0%, #ffffff 100%);
    border-left: 4px solid #3b82f6;
    transform: translateX(2px);
}

.member-card {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.member-name {
    font-weight: 700;
    font-size: 13px;
    margin: 0;
    color: #1e40af;
}

.member-card.student .member-name {
    color: #2563eb;
}

.member-card.employee .member-name {
    color: #047857;
}

.member-meta {
    font-size: 12px;
    color: #6b7280;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.member-meta span strong {
    color: #374151;
    font-weight: 600;
}

.roll,
.emp-id {
    color: #64748b;
    font-weight: 500;
    font-size: 12px;
    margin-left: 2px;
}

input.form-control {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 8px 10px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

input.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    outline: none;
}

input[disabled] {
    background-color: #f9fafb;
    cursor: not-allowed;
    color: #9ca3af;
}

.autocomplete {
    position: relative;
}

.autocomplete-results {
    position: absolute;
    top: 100%;
    width: 100%;
    max-height: 240px;
    overflow-y: auto;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    z-index: 999;
    padding: 6px 0;
    margin-top: 4px;
}

.autocomplete-result {
    cursor: pointer;
    padding: 1px 2px;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
}

.autocomplete-result:last-child {
    border-bottom: none;
}
</style>
