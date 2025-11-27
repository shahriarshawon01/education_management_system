<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>admit Card</title>
    <style>
        txt-center {
            text-align: center;
        }

        .container {
            width: 48% !important;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
            margin-bottom: 50px;
        }

        .border- {
            border: 1px solid #5a5a5a !important;
        }

        .padding {
            padding: 15px;
        }

        .mar-bot {
            margin-bottom: 7px;
        }

        .admit-card {
            border: 1px solid #5a5a5a;
            padding: 7px;
            margin: 20px 0;
        }

        .BoxA h5,
        .BoxA p {
            margin: 0;
        }

        h5 {
            text-transform: uppercase;
        }

        table img {
            width: 100%;
            margin: 0 auto;
        }

        .table-bordered td,
        .table-bordered th,
        .table thead th {
            border: 1px solid #5a5a5a !important;
            font-size: 15px;
        }

        h6 {
            font-size: 15px;
        }

        .hb {
            !important
        }

        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <section>
        <div class="container">
            <div class="admit-card">
                <div class="BoxA border- padding">
                    <div class="row">
                        <div class="col-12" align="center">
                            <h4>{{ $data['school']->title }}</h4>
                            {{-- <h5>টিএমএসএস ইন্সিটিউট অফ সায়েন্স অ্যান্ড আইসিটি</h5> --}}
                            <h6>{{ $data['school']->address }}</h6>
                            <p>Emis code : {{$data['school']->emis_code}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12" style="margin: 5px 0">
                    <h6 class="text-center mb-0" style="text-align: center; font-weight:600; font-size: 18px">Admit Card</h6>
                </div>
                <div class="BoxC border- padding mar-bot">
                    <div class="row">
                        <div class="col-sm-6 col-lg-6">
                            <h6 class="text-left"><b>Applicant No :</b> {{ $admission_requests->applicant_id }}</h6>
                        </div>
                        <div class="col-sm-6 col-lg-6">
                            @if ($admission_requests->admission_payment_status == 1)
                                <h6 class="text-right"><b>Payment Status</b> : <span class="text-success"
                                        style="font-size: 16px">Paid</span></h6>
                            @else
                                <h6 class="text-right"><b>Payment Status</b> : <span class="text-danger"
                                        style="font-size: 16px">UnPaid</span></h6>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <div class="row">
                            <div class="col-sm-9">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><b>Applicant Name :</b>
                                                {{ $admission_requests->applicant_name_en ?? '' }}</td>
                                            <td><b>Class : </b>{{ @$admission_requests->class_info->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Session : </b>{{ @$admission_requests->session->title }}</td>
                                            @if ($admission_requests->section)
                                                <td><b>section : </b>{{ @$admission_requests->section->name }}</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            @if ($admission_requests->section)
                                                <td><b>Department :
                                                    </b>{{ @$admission_requests->department->department_name }}</td>
                                            @endif
                                            <td><b>Phone : </b>{{ $admission_requests->applicant_phone ?? '' }}</td>
                                        </tr>
                                
                                        <tr>
                                           
                                            <td><b>Date Of Birth :
                                                </b>{{ date('j F Y', strtotime($admission_requests->birthday)) }}
                                                <br /><b> Age : </b><span id="currentAge"></span></td>
                                                <td colspan="2">
                                                    <b>Time :</b>
                                                   {{ date('g:i A', strtotime(@$exam_shedule->starting_time)) }} -
                                                    {{ date('g:i A', strtotime(@$exam_shedule->ending_time)) }}
                                                </td>
                                        </tr>
                                        {{--                            {{$admission_requests->selection_mapping}} --}}
                                        <tr>
                                            <td colspan="2"><b>Examination Venue :
                                                </b>{{ @$admission_requests->exam->venue }}<br /><b>Exam Date:</b>
                                                {{ date('l , j F Y', strtotime(@$admission_requests->exam->exam_date)) }}
                                                ,
                                            </td>
                                        </tr>
                                        <tr>
                                           
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-3">
                                <table align="right">
                                    <tbody class="table table-bordered">
                                        <tr>
                                            <td>
                                                <div>
                                                    <img src="{{ asset('storage/admission/' . $admission_requests->profile_photo) }}"
                                                        style="width: 100%; height: auto; object-fit:cover;border-radius: 4px "
                                                        alt="Profile Picture">
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="BoxC border- padding mar-bot">
                        <div class="row">
                            <div class="col-sm-12">
                                <span
                                    style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000">1.
                                    Please print bring this admit card during admission test and preserve it for further
                                    admission formalities.</span><br />
                                <span
                                    style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000">2.
                                    Candidate will carry transparent black ink ball point pen so that refill can be seen
                                    easily. Carrying mobile phone, calculator, watch and any electronic devices are
                                    strictly prohibited in examination hall.</span><br />
                                <span
                                    style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000">3.
                                    Candidate will report to the respective centre of examination at least one &amp;
                                    half hour prior to start time of written examination.</span><br />
                                <span
                                    style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Helvetica;color:#000000"><b>4.
                                        Examinees must enter the exam hall half an hour before the
                                        exam.</b></span></span><br />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div align="right">
                <button class="btn btn-secondary btn-print" onclick="window.print()"
                    style="border-radius: 2px !important; padding: 2px 22px !important; font-size: small; text-transform:uppercase;font-weight: 500  ">Print</button>
            </div>
    </section>

    <script>
        var dob = new Date("{{ $admission_requests->date_of_birth }}");

        function calculateAge(dob) {
            var today = new Date();
            var diff = Math.abs(today - dob);
            var years = Math.floor(diff / (1000 * 60 * 60 * 24 * 365));
            var months = Math.floor((diff % (1000 * 60 * 60 * 24 * 365)) / (1000 * 60 * 60 * 24 * 30.436875));
            var days = Math.floor((diff % (1000 * 60 * 60 * 24 * 30.436875)) / (1000 * 60 * 60 * 24));
            return {
                years: years,
                months: months,
                days: days
            };
        }

        var age = calculateAge(dob);
        document.getElementById("currentAge").innerText = age.years + " Years, " + age.months + " Months, " + age.days +
            " Days";
    </script>

</body>

</html>

