import {printPage} from '../plugins/print_div';

export default {
    data() {
        return {
            baseUrl: baseUrl,
            sortData: [],
            sortable: false,
            perPages: [5, 10, 15, 20, 30, 40, 50, 100, 200],
            singleNumber: '',
            extra_plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor colorpicker textpattern", "emoticons template paste textcolor colorpicker textpattern code directionality table", "insertdatetime media nonbreaking save table contextmenu directionality",
            ],
            toolbars2: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol | code",

            tinyMCESetup: {
                height: 200,
                menubar: false,
                path_absolute: "/",

                statusbar: false,
                allow_script_urls: true,
                relative_urls: false,
                remove_script_host: false,
                convert_urls: true,
                paste_data_images: false,
                paste_as_text: true,
                paste_enable_default_filters: true,
                images_upload_url: '/api/image',
                images_upload_handler: function (blobInfo, success, failure) {
                    var xhr, formData;

                    xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '/api/image/');

                    xhr.onload = function () {
                        var json;

                        if (xhr.status != 200) {
                            console.log('HTTP Error: ' + xhr.status);
                            return;
                        }

                        json = JSON.parse(xhr.responseText);

                        if (!json || typeof json.location != 'string') {
                            console.log('Invalid JSON: ' + xhr.responseText);
                            return;
                        }

                        success(json.location);
                    };

                    formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    // append CSRF token in the form data
                    formData.append('_token', $('meta[name="csrf_token"]').attr('content'));

                    xhr.send(formData);
                },
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount image'
                ],
                toolbar: 'insertfile undo redo | formatselect | bold italic backcolor | \
                    alignleft aligncenter alignright alignjustify | \
                    bullist numlist outdent indent | removeformat | help | link image media',
                    forced_root_block: false,
            },
            numberInputValidation: [
                {classes: 'text-warning', rule: (tag => tag.text.length !== 11 || tag.text.length !== 13)},
                {classes: 'text-success', rule: tag => tag.text.length === 11},
                {classes: 'text-success', rule: tag => tag.text.length === 13},
            ],
            contentType : [
                {name : 'English', value : 1},
                // {name : 'English Flash', value : 2},
                {name : 'Unicode(Bangla)', value : 3},
                // {name : 'Unicode Flash (Bangla)', value : 4},
            ],

            maxCharacters: 0,
            charactersPerPart: 0,
            totalLimit: 0,
        }
    },
    watch: {
        'errors': {
            handler: function (value) {
                const _this = this;
                var is_invalid = $('.is-invalid');
                $(is_invalid).removeAttr("title", '');
                $(is_invalid).removeClass('is-invalid');
                $('.error_message').remove();
                if (value.items.length > 0) {
                    value.items.forEach(function (val) {
                        var target = $("[name='" + val.field + "']");
                        if ($('.is-invalid').length == 0) {
                            $(target).parent().remove(`#${val.field}_message`);
                        }
                        var message = _this.replaceString(val.msg, val.field);
                        if ($(`#${val.field}_message`).length == 0) {
                            $(target).parent().append(`<span style="color:red" class="error_message" id="${val.field}_message">${message}</span>`);
                        }
                        $(target).addClass('is-invalid');
                        $(target).attr("title", message.replace(val.field, ""));
                    });
                }
            },
            deep: true
        },
        '$store.getters.httpRequest': function () {
            if (this.httpRequest) {
                $('.main-content button').attr('disabled', 'disabled');
                $('.main-content input').attr('disabled', 'disabled');
            } else {
                $('.main-content button').removeAttr('disabled');
                $('.main-content input').removeAttr('disabled');
            }
        },
        '$route.path': function () {
            this.assignCurrentAccess();
        }
    },
    computed: {
        detailsData: function () {
            return this.$store.getters.detailsData;
        },
        formType: function () {
            return this.$store.getters.formType;
        },
        formObject: function () {
            return this.$store.getters.formObject;
        },
        formFilter: function () {
            return this.$store.getters.formFilter;
        },
        dataList: function () {
            return this.$store.getters.dataList;
        },
        updateId: function () {
            return this.$store.getters.updateId;
        },
        httpRequest: function () {
            return this.$store.getters.httpRequest;
        },
        requiredData: function () {
            return this.$store.getters.requiredData;
        },
        modalTitle: function () {
            return this.$store.getters.modalTitle;
        },
        Config: function () {
            return this.$store.getters.Config;
        },
        allMenus: function () {
            return this.$store.getters.allMenus;
        },
        Permissions: function () {
            if (this.$store.getters.Config.permissions !== undefined
                && this.$store.getters.Config.permissions != null) {
                return this.$store.getters.Config.permissions;
            }
            return [];
        },
        currentPage: function () {
            return this.$store.getters.currentPage;
        },
        currentPagination: function () {
            return this.$store.getters.currentPagination;
        },
        uploadProgress: function () {
            return this.$store.getters.uploadProgress;
        },
        globalDetailsData: function () {
            return this.$store.getters.globalDetailsData;
        },

    },
    methods: {
        resetFileInput: function (event) {
            const element = event.target;
            element.value = '';
        },

        printData: function (div_id = 'printDiv') {
            console.log(div_id);
            printPage("#" + div_id);
        },
        routePush: function (path) {
            this.$router.push({path: path})
        },
        assignCurrentAccess: function () {
            // const _this = this;
            // var path = this.$route.path;
            // var currentPage = {};
            //
            // $.each(_this.Config.menus, function (index, each) {
            //     if (path == each.link) {
            //         currentPage = each;
            //     } else {
            //         $.each(each.submenus, function (index, eachSub) {
            //             if (path == eachSub.link) {
            //                 currentPage = eachSub;
            //             }
            //         });
            //     }
            // });
            // _this.$store.commit('currentPage', currentPage);

        },

        can: function (role) {
            const _this = this;
            if ((_this.Permissions !== null && _this.Permissions.length > 0)
                && _this.Permissions.includes(role)) {
                return true;
            }
            return false;
        },
        showData(dataArray, fieldName, retBoolean = false) {
            if ((dataArray !== null && dataArray !== undefined)
                && (dataArray[fieldName] !== undefined && dataArray[fieldName] !== null)) {
                return dataArray[fieldName];
            } else {
                return retBoolean ? false : '-';
            }
        },
        getConfig: function (Obj, name) {
            if ((Obj !== undefined && Obj !== null !== null) &&
                (Obj[name] !== undefined && Obj[name] !== null)) {
                return Obj[name];
            } else {
                return '';
            }
        },
        cloneData: function (data, exceptObject = [], model = 'formModal', formObject = false, callback = false) {
            const _this = this;

            $.each(data, function (index, value) {
                if (!exceptObject.includes(index)) {
                    if (formObject) {
                        _this.$set(formObject, index, value);
                    } else {
                        _this.$set(_this.formObject, index, value);
                    }
                }
            });
            _this.$store.commit('formType', 1);

            if (model) {
                _this.openModal(model);
            }
            if (typeof callback == 'function') {
                callback(data);
            }
        },
        editData: function (data, updateId, model = 'formModal', callback = false) {
            const _this = this;
            _this.$store.commit('formObject', data);
            _this.$store.commit('updateId', updateId);
            _this.$store.commit('formType', 2);

            if (model) {
                _this.openModal(model);
            }
            if (typeof callback == 'function') {
                callback(data);
            }
            setTimeout(() => {
                $("select").trigger("change.select2");
            }, 1000);
        },

        openModal: function (modalName = 'formModal', title = false, callback = false, resetValidation = true, defaultObject = {}, formObject = false) {
            const _this = this;

            if (title) {
                this.$store.commit('modalTitle', title);
            }
            $('#' + modalName).modal('show');

            if (resetValidation) {
                this.$validator.reset();
            }

            $.each(defaultObject, function (index, value) {
                if (formObject) {
                    _this.$set(formObject, index, value);
                } else {
                    _this.$set(_this.formObject, index, value);
                }
            });

            _this.$set(_this.formObject, 'school_id', _this.getCookie('school_id'));
            _this.$set(_this.formObject, 'session_id', _this.Config.session_id);

            if (typeof callback === 'function') {
                callback(true);
            }
        },
        closeModal: function (modalName = 'createModal', resetForm = true, resetFormType = true, formObject = false) {
            const _this = this;
            this.$validator.reset();
            $('#' + modalName).modal('hide');
            this.$store.commit('formType', 1);
            $('.error_message').remove();
            $('.is-invalid').removeClass('is-invalid');
            if (resetForm) {
                if (formObject) {
                    formObject = {};
                } else {
                    this.$store.commit('formObject', {});
                }

            }
            if (resetFormType) {
                _this.$store.state.formType = 1;
            }
        },
        getUrl: function () {
            if (this.$route.meta.dataUrl !== undefined) {
                return this.$route.meta.dataUrl;
            }
            return '';
        },
        urlGenerate: function (customUrl = false) {
            var url = '';
            if (customUrl) {
                url = `${baseUrl}/${customUrl}`;
            } else {
                url = `${baseUrl}/${this.getUrl()}`;
            }
            return url;
        },
        assignValidationError: function (errors) {
            const _this = this;
            $.each(errors, function (index, errorValue) {
                _this.$validator.errors.add({
                    id: index,
                    field: index,
                    name: index,
                    msg: errorValue[0],
                });
            })
        },
        resetForm: function (formData) {
            if (typeof formData == 'object') {
                Object.keys(formData).forEach(function (key) {
                    formData[key] = '';
                });
                return formData;
            }
        },
        pageTitle: function () {
            return this.$route.meta.pageTitle;
        },
        resetFilter: function (parameter = []) {
            this.$store.commit('resetFilter', parameter);
            this.getDataList();
        },
        clickImageInput: function (ID) {
            $('#' + ID).click();
        },
        getImage: function (imagePath = null, alternative = false) {
            if (imagePath !== undefined && imagePath !== '' && imagePath !== null) {
                return `${UploadPath}/${imagePath}`;
            }
            if (alternative) {
                return `${publicPath}/${alternative}`;
            }
        },
        getUpload: function (filePath) {
            if (filePath !== undefined && filePath !== '') {
                return `${UploadPath}/${filePath}`;
            }
        },
        indexToLabel: function (string) {
            var removed_space = '';
            if (typeof string === 'string') {
                removed_space = string.replace(/_/g, ' ');
                if (typeof removed_space !== 'string') {
                    return index;
                }
                return removed_space.charAt(0).toUpperCase() + removed_space.slice(1)
            }
            return '';
        },
        addRow: function (object, pushEr) {
            if (typeof object === 'object') {
                object.push(pushEr);
            }
        },
        deleteRow: function (object, index) {
            object.splice(index, 1);
        },
        arrLength: function (array) {
            if (array.length !== undefined) {
                return array.length;
            }
            return 0;
        },
        openFile: function (url, filename) {
            var newwindow;
            var height = parseInt(window.innerHeight) - 100;
            var width = parseInt(window.innerWidth) - 250;
            newwindow = window.open(url, filename, `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=${width},height=${height},left=100,top=100`);

            if (window.focus) {
                newwindow.focus()
            }
            return false;
        },

        playSound: function (url) {
            var audio = document.createElement('audio');
            audio.style.display = "none";
            audio.src = url;
            audio.autoplay = true;
            audio.onended = function () {
                audio.remove()
            };
            document.body.appendChild(audio);
        },
        showStatus: function (status, activeText = "Active", inactiveText = "InActive") {
            if (parseInt(status) === 1) {
                return `<span class="badge badge-soft-success">${activeText}</span>`;
            }

            return `<span class="badge badge-soft-danger">${inactiveText}</span>`;
        },
        startSortable: function () {
            const _this = this;
            if (_this.sortable) {
                _this.sortable = false;
                $("#sortable").sortable("disable");
                _this.$toastr('warning', 'Sorting DeActivated', 'Information');
                return;
            }
            _this.sortable = true;
            _this.$toastr('warning', 'Sorting Activated, now you can Change The list', 'Information');

            $("#sortable").sortable({
                update: function (event, ui) {
                    _this.sortData = [];
                    let product_list = $(".sort_each");
                    $.each(product_list, function (index, value) {
                        _this.sortData.push({
                            id: $(value).attr("data-id"),
                            sort_id: index + 1
                        });
                    });
                    _this.changeStatus({
                        sorts: _this.sortData,
                        type: 'sort',
                    }, false)
                }
            });
        },
        updateSortable: function (trigger) {
            let data = [];
            NewArray = [];
            let product_list = $(".sort_product-each");
            product_list.each(function (index, value) {
                let rv = {
                    id: $(value).attr("data_product_id"),
                    sort_id: (index + 1)
                };
                NewArray.push(rv)
            });
        },

        replaceString: function (string, name) {
            if (string !== undefined && string !== null) {
                return string.replace(name, ' ');
            }

            return '';
        },
        confirmBox: function (callback = false, title = null, message = null) {
            const _this = this;
            var TiTle = title ? title : 'Are you sure..??';
            var MessAge = message ? message : 'Data will be delete Permanently??';
            this.$swal({
                title: TiTle,
                text: MessAge,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-check"></i>',
                cancelButtonText: '<i class="fa fa-close"></i>',
                showCloseButton: true,
            }).then((result) => {
                if (typeof callback == 'function') {
                    callback(result.value);
                }
            });
        },
        onTimeCreate: function (url, objectName = false, closeModal = false) {
            const _this = this;
            _this.submitForm(_this.formObject, false, function (retData) {
                if (objectName) {
                    _this.getGeneralData([objectName]);
                }

                if (closeModal) {
                    _this.closeModal('createModal', false);
                }
            }, false, url)
        },
        setCharactersLimit: function () {
            const _this = this;
            if (_this.requiredData.characters !== undefined) {
                if (parseInt(_this.formObject.content_type) === 2) {
                    _this.maxCharacters = parseInt(_this.requiredData.characters.unicode.count);
                    _this.totalLimit = parseInt(_this.requiredData.characters.unicode.limit);
                } else {
                    _this.maxCharacters = parseInt(_this.requiredData.characters.english.count);
                    _this.totalLimit = parseInt(_this.requiredData.characters.english.limit);
                }
            } else {
                _this.maxCharacters = 160;
                _this.totalLimit = 1000;
            }
        },
        smsCost : function (data){
            if(data.message !== undefined && data.message !== null){
                return `${(parseFloat(data.rate)*parseFloat(data.message.sms_count))} BDT`;
            }
            return  0;
        },
        globalDetails : function (data, modalTitle = null, modalId = 'detailsModal') {
            const _this = this;
            _this.$store.commit('globalDetailsData', data);
            _this.openModal(modalId, modalTitle);
        },
        capitalize: function(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },
        formatDate(date) {
            const options = {year: 'numeric', month: '2-digit', day: '2-digit'};
            return date.toLocaleDateString('en-US', options);
        },

        SetSchoolId: function (schoolId) {
            const _this = this;
            _this.setCookie('school_id', schoolId, 30);
            location.reload();
        },

        setCookie: function (name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        },
        getCookie: function (name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        },

        checkAll : function (event, object, column, arrayObject = false, pussColumn = 'id') {
            const _this = this;

            $.each(object, function (index, value) {
                if (event.target.checked){
                    _this.$set(value, column, 1);
                    if (arrayObject){
                        arrayObject.push(value[pussColumn]);
                    }
                }else{
                    _this.$set(value, column, 0);
                    if (arrayObject){
                        const itemIndex = arrayObject.indexOf(value[pussColumn]);
                        arrayObject.splice(itemIndex, 1);
                    }
                }
            })
        },

        singleCheck : function (event, value, column, arrayObject = false, pussColumn = 'id') {
            const _this = this;
            if (event.target.checked){
                _this.$set(value, column, 1);
                if (arrayObject){
                    arrayObject.push(value[pussColumn]);
                }
            }else{
                _this.$set(value, column, 0);
                if (arrayObject){
                    const itemIndex = arrayObject.indexOf(value[pussColumn]);
                    arrayObject.splice(itemIndex, 1);
                }
            }
        }
    },
}
