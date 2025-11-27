<template>
    <div class="row">
        <div class="row mb-4">
            <div class="col-md-12 mb-2">
                <div class="row">
                    <label class="col-md-3">Parent Name</label>
                    <div class="col-md-9">
                        <input type="text" v-model="parentFormObject.name" v-validate="'required'" name="name"
                            class="form-control" placeholder="Enter Parent Name">
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-2">
                <div class="row">
                    <label class="col-md-3">Mobile</label>
                    <div class="col-md-9">
                        <input type="text" v-model="parentFormObject.phone" v-validate="'required'" name="phone"
                            class="form-control" placeholder="Enter Mobile No">
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-2">
                <div class="row">
                    <label class="col-md-3">Gender</label>
                    <div class="col-md-9">
                        <select name="gender" class="form-control" v-model="parentFormObject.gender">
                            <option value="">Select Gender</option>
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                            <option value="3">Others</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-2">
                <div class="row">
                    <label class="col-md-3">Profession</label>
                    <div class="col-md-9">
                        <input type="text" v-model="parentFormObject.profession" v-validate="'required'"
                            name="profession" class="form-control" placeholder="Enter Parent Profession">
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-2">
                <div class="row">
                    <label class="col-md-3">Monthly Salary</label>
                    <div class="col-md-9">
                        <input type="text" v-model="parentFormObject.income" v-validate="'required'" name="income"
                            class="form-control" placeholder="Enter Parent Monthly Salary">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h4>Address</h4>
                    <hr>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-10">
                    <div class="row mb-2" v-for="(address, index) in parentFormObject.address">
                        <label class="col-md-2" v-if="parseInt(address.type) === 1">Permanent</label>
                        <label class="col-md-2" v-else>Present</label>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-2">
                                    <select v-model="address.division" v-validate="'required'"
                                        @change="getGeneralData({ district: { division_id: address.division, objectName: `${address.type}_district` } })"
                                        name="district" class="form-control">
                                        <option value="">Division</option>
                                        <template v-for="(eachData, index) in requiredData.division">
                                            <option :value="eachData.id">{{ eachData.name }}</option>
                                        </template>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select v-model="address.district" v-validate="'required'"
                                        @change="getGeneralData({ upazila: { district_id: address.district, objectName: `${address.type}_upazilla` } })"
                                        name="district" class="form-control">
                                        <option value="">District</option>
                                        <template v-for="(eachData, index) in requiredData[`${address.type}_district`]">
                                            <option :value="eachData.id">{{ eachData.name }}</option>
                                        </template>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select v-model="address.upazila" v-validate="'required'"
                                        @change="getGeneralData({ union: { upazilla_id: address.upazila, objectName: `${address.type}_union` } })"
                                        name="upazila" class="form-control">
                                        <option value="">Upazila</option>
                                        <template v-for="(eachData, index) in requiredData[`${address.type}_upazilla`]">
                                            <option :value="eachData.id">{{ eachData.name }}</option>
                                        </template>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select v-model="address.union" name="union" class="form-control"
                                        v-validate="'required'">
                                        <option value="">Union</option>
                                        <template v-for="(eachData, index) in requiredData[`${address.type}_union`]">
                                            <option :value="eachData.id">{{ eachData.name }}</option>
                                        </template>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <input type="text" placeholder="Post Office" v-model="address.post_office"
                                        v-validate="'required'" name="post_office" class="form-control">
                                </div>
                                <div class="col-2">
                                    <input type="text" placeholder="Village" v-model="address.village"
                                        v-validate="'required'" name="village" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label>Same As Permanent <input type="checkbox" @click="checkSameData($event)"
                            v-model="parentFormObject.as_same"></label>
                </div>
            </div>

            <label>Show Login Information
                <input type="checkbox" @click="toggleLoginInfo($event)" v-model="parentFormObject.as_login">
            </label>

            <div class="row" v-if="showLoginInfo">
                <div class="col-md-12">
                    <h4>Parent's Photo & Login Information</h4>
                    <hr>

                </div>
                <div class="col-md-6">
                    <div class="col-md-12 mb-2">
                        <div class="row">
                            <label class="col-md-3">User Name</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" v-validate="'required'"
                                    v-model="parentFormObject.name" placeholder="User Name" name="name" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="row">
                            <label class="col-md-3">Email address</label>
                            <div class="col-md-9">
                                <input type="email" class="form-control" v-validate="'required'"
                                    v-model="parentFormObject.email" placeholder="Enter Valid Email" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <div class="row">
                            <label class="col-md-3">Password</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control"
                                    v-validate="formType == 1 ? 'required|min:6' : ''"
                                    v-model="parentFormObject.password" placeholder="Enter Strong Password"
                                    name="password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div @click="clickImageInput('image')" class="mb-2 text-center">
                        <div class="form-group image_upload"
                            :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')' }"
                            style="background-size: 350px !important">
                            <img v-if="parentFormObject.image" :src="getImage(parentFormObject.image)">
                            <input name="thumbnail" v-validate="formType == 1 ? 'required' : ''" style="display: none;"
                                id="image" type="file" @change="uploadFile($event, parentFormObject, 'image')">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>

export default {
    name: "parentForm",
    props: {
        parentFormObject: {
            type: [Object],
            default() {
                return {};
            }
        },
    },
    data() {
        return {
            showLoginInfo: false,
            parentFormObject: {}
        };
    },
    methods: {
        getEditData(id) {
            const _this = this;
            var url = `${_this.urlGenerate()}/${id}/edit`;
            _this.getData(url, 'get', {}, {}, function (retData) {

                _this.$store.commit('formObject', retData);
                _this.$store.commit('updateId', id);
                _this.$store.commit('formType', 2);

                email: retData.user ? retData.user.email : null,

                    $.each(retData.address, function (index, value) {
                        _this.getGeneralData({
                            district: { division_id: value.division, objectName: `${value.type}_district` },
                            upazila: { district_id: value.district, objectName: `${value.type}_upazilla` },
                            union: { upazilla_id: value.upazila, objectName: `${value.type}_union` }
                        }, function (ret) {
                            _this.$store.commit('formObject', retData);
                        });
                    });
            });
        },

        checkSameData(event) {
            const _this = this;
            if (event.target.checked) {
                let presentAddressIndex = _this.parentFormObject.address.findIndex(item => item.type == 1);
                let permanentAddressIndex = _this.parentFormObject.address.findIndex(item => item.type == 2);

                if (presentAddressIndex !== -1) {
                    let presentAddress = JSON.parse(JSON.stringify(_this.parentFormObject.address[presentAddressIndex]));
                    presentAddress.type = 2;

                    if (permanentAddressIndex !== -1) {
                        _this.$set(_this.parentFormObject.address, permanentAddressIndex, presentAddress);
                    } else {
                        _this.parentFormObject.address.push(presentAddress);
                    }

                    let keys = ['district', 'upazilla', 'union'];
                    $.each(keys, function (ind, value) {
                        let keyOne = `1_${value}`;
                        let keyTwo = `2_${value}`;
                        if (_this.requiredData[keyOne] !== undefined) {
                            _this.$set(_this.requiredData, keyTwo, _this.requiredData[keyOne]);
                        }
                    });
                }
            } else {
                let permanentAddressIndex = _this.parentFormObject.address.findIndex(item => item.type == 2);
                if (permanentAddressIndex !== -1) {
                    _this.$set(_this.parentFormObject.address, permanentAddressIndex, { district: '', upazila: '', union: '', post_office: '', village: '', type: 2 });
                }
            }
        },

        toggleLoginInfo(event) {
            const _this = this;
            if (event.target.checked) {
                _this.showLoginInfo = true;
            } else {
                _this.showLoginInfo = false;
                _this.$set(_this.parentFormObject, 'email', '');
                _this.$set(_this.parentFormObject, 'password', '');
            }
        },

        assign() {
            const _this = this;
            _this.$set(_this.parentFormObject, 'gender', '');
            _this.$set(_this.parentFormObject, 'address', [
                { division: '', district: '', upazila: '', union: '', post_office: '', village: '', type: 1 },
                { division: '', district: '', upazila: '', union: '', post_office: '', village: '', type: 2 }
            ]);
        },
    },
    mounted() {
        this.assign();
        this.getGeneralData(['division', 'district',]);
    },

}
</script>

<style scoped></style>
