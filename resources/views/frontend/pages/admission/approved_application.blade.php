@extends('frontend.layouts.master')

@section('title','Contact')

@push('css')
    <style> body { margin: 0; padding: 0; z-index: 1; } ::-webkit-scrollbar { width: 10px; background-color: #fff; border-radius: 5px; } ::-webkit-scrollbar-thumb { background: linear-gradient(transparent, #4A8BDF); border-radius: 5px; } ::-webkit-scrollbar-thumb:hover { background: linear-gradient(transparent, #4A8BDF); } .useful-link li { width: 100% !important; } .toolbar-share-icon-active { border-bottom: 2px solid #FFFFFF; } </style>
    <style> .page-header { background-image: url(front/images/bg/page-header-bg.jpg); background-size: cover; background-position: center; margin: 0px; padding: 0px; border: none; } .section-notch:before { content: ""; position: absolute; background-repeat: repeat-x; display: block; top: 0; width: 100%; height: 7px; z-index: 2; } .page-header .overlay { padding: 25px 0px; background-color: rgba(125, 172, 230, 0.8); text-align: center; } .notice-section { background-image: url(https://diu.ac/front/images/bg/dot-grid.png) !important; background-attachment: fixed; background-position: center top; padding: 70px 0 50px !important}
        .pagination .page-item > * {
            width: 35px !important;
            height: 35px !important;
            line-height: 35px !important;
            font-size: 16px !important;
            color: #4A8BDF !important;
        }
       
        .active{
            color: #1a22ac !important;
        }

    </style>
@endpush

@section('content')
    <section class="page-header section-notch">
        <div class="overlay">
            <div class="container">
                <div class="col-md-12 text-center">
                    <h1 class="page-title">Welcome to Dashboard</h1>
                    <ul>
                        <li><a class="active" href="{{url('/')}}">Home</a> / <span style="color: #FFFFFF!important;"> Dashboard</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <div id="latest_news" class="latest_news notice-section">
        <div class="container">
            <div class="row">
                @include('frontend.layouts.applicant_dashboard_nav')
                <div class="col-sm-9">
                    <div id="all_application">
                        <h4 style="margin: 0!important; margin-top: -7px; margin-bottom: 5px">Applicant Admit Card</h4>
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Application ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Image</th>
                                <th>Admid Card</th>
                            </tr>
                            </thead>
                            <tbody>
                            
                            <tr v-for="(admission, index) in admission_data" id="admission_data_status">
                                <td v-text="index+1"></td>
                                <td v-text="admission.applicant_id"></td>
                                <td v-text="admission.applicant_name_en"></td>
                                <td v-text="admission.applicant_phone"></td>
                                <td>
                                    <img :src="getImageUrl(admission.profile_photo)"
                                    style="width: 40px; height: 40px; object-fit:cover;border-radius: 4px "
                                    alt="Profile Picture">
                                </td>
                                <td>

                                    <a :href="'/view_admit_card'+'/'+admission.id" target="_blank">
                                        <button class="btn btn-sm btn-light"
                                        >
                                          Print admit  <i class="fa fa-print"></i>
                                        </button>
                                    </a>

                                </td>
                                
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script type="text/javascript" src="{{ asset('frontend_assets/vue/vue.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('frontend_assets/vue/axios.min.js') }}"></script>
<script src="{{ asset('frontend_assets/vue/vee-validate.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        window.appUrl = '{{ url('/') }}';
    </script>
    <script>
        Vue.use(VeeValidate);
        new Vue({
            el: '#all_application',
            data: {
                appUrl: window.appUrl,
                admission_data: [],
            },
            methods: {
                getImageUrl(imagePath) {
                    if (!imagePath) {
                        return '/default-image.jpg'; // Return a default image if no image is available
                    }
                    return `/storage/admission/${imagePath}`;
                },
                all_applicant() {
                    const _this = this;
                    axios.get('/api/applicant/all_application')
                        .then((response) => {
                            _this.admission_data = response.data.result;

                        })
                        .catch((erorr) => {
                            console.log('invalid try');
                            console.log(erorr);
                        })
                },
                application_details($application_id) {
                    const _this = this;
                    axios.get('/api/applicant/application_details/' + $application_id)
                        .then((response) => {
                            console.log(response.data.result);
                            _this.admission_data = response.data.result;

                            $('#application_details').modal('show');

                        })
                        .catch((erorr) => {
                            console.log('invalid try');
                            console.log(erorr);
                        })
                },

            },

            created: function () {
                this.all_applicant();
            }
        });
    </script>
@endpush