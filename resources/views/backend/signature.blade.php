{{-- <div class="signature_box" style="position: absolute; bottom: 1px; width: 100%; margin-top: 100px;">
    <div class="row align-items-end">
        <div class="col-md-3">
            <div class="box text-center">
                <hr style="border: 0.5px solid; width: 100px; margin: 5px auto;" class="mb-1">
                <p style="font-weight: 600; color: #555; margin-top: -3px;">Guardians</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="box text-center">
                <hr style="border: 0.5px solid; width: 100px; margin: 5px auto;" class="mb-1">
                <p style="font-weight: 600; color: #555; margin-top: -3px;">Class Teacher</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="box text-center">
                <img src="{{asset('storage/app/public/uploads/vicePrincipalSignature.png')}}" 
                     alt="vice_principal_signature" 
                     style="height: 40px; width: 80px; margin-bottom: -10px;">
                <hr style="border: 0.5px solid; width: 100px; margin: 5px auto;" class="mb-1">
                <p style="font-weight: 600; color: #555; margin-top: -3px;">Vice-Principal</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="box text-center">
                <img src="{{asset('storage/app/public/uploads/principalSignature.png')}}" 
                     alt="principal_signature" 
                     style="height: 40px; width: 80px; margin-bottom: -10px;">
                <hr style="border: 0.5px solid; width: 100px; margin: 5px auto;" class="mb-1">
                <p style="font-weight: 600; color: #555; margin-top: -3px;">Principal</p>
            </div>
        </div>
    </div>
</div> --}}

@php
    // Set default values (true = display, false = hide)
    $guardianSignatureNeeded = isset($guardianSignatureNeeded) ? $guardianSignatureNeeded : true;
    $classTeacherSignatureNeeded = isset($classTeacherSignatureNeeded) ? $classTeacherSignatureNeeded : true;
    $vicePrincipalSignatureNeeded = isset($vicePrincipalSignatureNeeded) ? $vicePrincipalSignatureNeeded : true;
    $principalSignatureNeeded = isset($principalSignatureNeeded) ? $principalSignatureNeeded : true;
@endphp

<div class="signature_box" style="position: absolute; bottom: 1px; width: 100%; margin-top: 100px;">
    <div class="row align-items-end">

        @if ($guardianSignatureNeeded)
            <div class="col-md-3">
                <div class="box text-center">
                    <hr style="border: 0.5px solid; width: 100px; margin: 5px auto;" class="mb-1">
                    <p style="font-weight: 600; color: #555; margin-top: -3px;">Guardians</p>
                </div>
            </div>
        @endif

        @if ($classTeacherSignatureNeeded)
            <div class="col-md-3">
                <div class="box text-center">
                    <hr style="border: 0.5px solid; width: 100px; margin: 5px auto;" class="mb-1">
                    <p style="font-weight: 600; color: #555; margin-top: -3px;">Class Teacher</p>
                </div>
            </div>
        @endif

        @if ($vicePrincipalSignatureNeeded)
            <div class="col-md-3">
                <div class="box text-center">
                    <img src="{{ asset('storage/app/public/uploads/vicePrincipalSignature.png') }}"
                        alt="vice_principal_signature" style="height: 40px; width: 80px; margin-bottom: -10px;">
                    <hr style="border: 0.5px solid; width: 100px; margin: 5px auto;" class="mb-1">
                    <p style="font-weight: 600; color: #555; margin-top: -3px;">Vice-Principal</p>
                </div>
            </div>
        @endif

        @if ($principalSignatureNeeded)
            <div class="col-md-3">
                <div class="box text-center">
                    <img src="{{ asset('storage/app/public/uploads/principalSignature.png') }}"
                        alt="principal_signature" style="height: 40px; width: 80px; margin-bottom: -10px;">
                    <hr style="border: 0.5px solid; width: 100px; margin: 5px auto;" class="mb-1">
                    <p style="font-weight: 600; color: #555; margin-top: -3px;">Principal</p>
                </div>
            </div>
        @endif

    </div>
</div>
