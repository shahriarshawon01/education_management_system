<template>
    <div class="row">
        <form @submit.prevent="submitForm(formObject, false, false, false, false, '', 'profile')">
            <div class="row gutters">
                <div class="col-xl-3 col-lg-3 col-md-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="account-settings">
                                <div class="user-profile">
                                    <h5>Click to Update Profile</h5>
                                    <div class="user-avatar">
                                        <div @click="clickImageInput('image')" class="mb-2">
                                            <div class="form-group image_upload"
                                                :style="{ backgroundImage: 'url(' + getImage(null, 'images/upload.png') + ')', }">
                                                <img v-if="formObject.image" :src="getImage(formObject.image)" />
                                                <input name="thumbnail"
                                                    :v-validate="formType === 1 ? 'required' : 'sometimes'"
                                                    style="display: none" id="image" type="file"
                                                    @change="UploadImage($event, formObject, 'image')" />
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="user-name text-align:center;">{{ formObject.username }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-9 col-md-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="row gutters">
                                <div class="col-md-12">
                                    <h4 class="mb-2 text-primary">Basic Information</h4>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">School Name</label>
                                        <input type="text" class="form-control" name="title"
                                            :value="formObject.school?.title" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email </label> <span class="text-muted"> (that you are logged in)</span>
                                        <input type="email" class="form-control" name="email"
                                            v-model="formObject.email">
                                    </div>
                                </div>
                            </div>
                            <div class="row gutters">
                                <div class="col-md-12">
                                    <h4 class="mt-3 mb-2 text-primary">Change Password</h4>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Old Password</label><span class="text-muted"> (Your Current Password Type
                                            here)</span>
                                        <input type="password" class="form-control" name="password"
                                            placeholder="Old Password" v-model="formObject.old_password">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <input type="password" class="form-control" name="newPassword"
                                            placeholder="New Password" v-model="formObject.password">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row gutters">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Layouts</label>
                                        <select class="form-select" type="text" v-model="formObject.layout"
                                            name="layouts">
                                            <option value="0">--Select-- </option>
                                            <option value="vertical">vertical</option>
                                            <option value="horizontal">horizontal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row gutters mt-5">
                                <div class="col-md-12">
                                    <div class="text-right">
                                        <button type="submit" name="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
export default {
    name: "profile",
    components: {},
    methods: {
        getUserProfile() {
            const _this = this;
            var URL = `${_this.urlGenerate('api/profile')}`;
            _this.getData(URL, 'get', {}, {}, function (retData) {
                _this.$store.commit('formObject', retData.data[0]);
            });
        },

    },
    mounted() {
        const _this = this;
        _this.getUserProfile();
    },

}
</script>
<style scoped>
.card {
    height: 100% !important;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: row;
}

h5,
h5 {

    text-align: center;
}
</style>
