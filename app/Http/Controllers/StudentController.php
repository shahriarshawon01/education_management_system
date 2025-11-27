<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use App\Helpers\Helper;
use App\Models\Address;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Guardian;
use App\Models\SessionYear;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StudentQualification;
use Illuminate\Support\Facades\Hash;
use App\Models\Dormitory\AssignDormitory;

class StudentController extends Controller
{
    use Helper;

    public function __construct()
    {
        $this->model = new Student();
    }

    public function index()
    {
        $user = auth()->user();
        if (!can('student_view')) {
            return $this->notPermitted();
        }
        try {
            $keyword = request()->input('keyword');
            $class = request()->input('class_id');
            $section = request()->input('section_id');
            $activityStatus = request()->input('status');

            $data = $this->model->filterBySchoolId()
                ->with(
                    'guardian',
                    'sessions:id,title',
                    'classes:id,name',
                    'departments:id,department_name',
                    'sections:id,name',
                    'user:id,username,email',
                    'optionalSubject:id,name'
                )
                ->when($keyword, function ($query) use ($keyword) {
                    $query->where('student_name_en', 'like', "%$keyword%")
                        ->orWhere('student_roll', 'like', "%$keyword%")
                        ->orWhere('merit_roll', 'like', "%$keyword%");
                })
                ->when($section, function ($query) use ($section) {
                    $query->where(function ($query) use ($section) {
                        $query->where('section_id', '=', $section)
                            ->orWhereNull('section_id');
                    });
                })
                ->when($class, function ($query) use ($class, $keyword) {
                    $query->where(function ($query) use ($class) {
                        $query->where('class_id', '=', $class)
                            ->orWhereNull('class_id');
                    });

                    if (!is_null($keyword)) {
                        $query->where('merit_roll', $keyword);
                    }
                    $query->orderBy('merit_roll', 'asc');
                })
                ->when($activityStatus !== null && $activityStatus !== '', function ($query) use ($activityStatus) {
                $query->where('students.status', '=', (int) $activityStatus);
            })
                ->orderby('id', 'desc')
                ->paginate(input('perPage'));
            return returnData(2000, $data);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!can('student_add')) {
            return $this->notPermitted();
        }
        try {
            DB::beginTransaction();
            $input = $request->all();
            $qualifications = $request->input('qualification', []);
            $addresses = $request->input('address', []);
            $guardians = $request->input('guardians', []);
            $schoolId = auth()->user()->school_id;
            $createdById = auth()->user()->id;

            // Validate input
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors());
            }

            $userData = new User();
            if (User::where('email', $request->student_roll)->exists()) {
                return returnData(5000, null, 'Email Already Exists');
            }

            // User entry
            $userName = strtolower($request->student_name_en);
            $userData->username = $userName;
            $userData->email = $request->student_roll;
            $userData->password = Hash::make($request->password);
            $userData->role_id = 5;
            $userData->type = 5;
            $userData->school_id = $schoolId;
            $userData->created_by = $createdById;
            $userData->save();

            $userId = $userData->id;

            // Student entry
            $picturePath = $request->image ?? null;

            $this->model->user_id = $userId;
            $this->model->student_name_bn = $request->input('student_name_bn');
            $this->model->student_name_en = $request->input('student_name_en');
            $this->model->merit_roll = $request->input('merit_roll');
            $this->model->student_roll = $request->input('student_roll');
            $this->model->reg_number = $request->input('reg_number');
            $this->model->father_name_bn = $request->input('father_name_bn');
            $this->model->father_name_en = $request->input('father_name_en');
            $this->model->father_nid = $request->input('father_nid');
            $this->model->father_phone = $request->input('father_phone');
            $this->model->father_profession = $request->input('father_profession');
            $this->model->mother_name_bn = $request->input('mother_name_bn');
            $this->model->mother_name_en = $request->input('mother_name_en');
            $this->model->mother_nid = $request->input('mother_nid');
            $this->model->mother_phone = $request->input('mother_phone');
            $this->model->mother_profession = $request->input('mother_profession');
            $this->model->school_id = $schoolId;
            $this->model->class_id = $request->input('class_id');
            $this->model->responsible_teacher_id = $request->input('responsible_teacher_id');
            $this->model->section_id = $request->input('section_id');
            $this->model->department_id = $request->input('department_id');
            $this->model->session_year_id = $request->input('session_year_id');
            $this->model->parent_id = $request->input('parent_id');
            $this->model->nationality = $request->input('nationality');
            $this->model->religion = $request->input('religion');
            $this->model->admission_date = $request->input('admission_date');
            $this->model->blood_group = $request->input('blood_group');
            $this->model->gender = $request->input('gender');
            $this->model->reference_name = $request->input('reference_name');
            $this->model->reference_mobile = $request->input('reference_mobile');
            $this->model->reference_address = $request->input('reference_address');
            $this->model->height = $request->input('height');
            $this->model->weight = $request->input('weight');
            $this->model->relation = $request->input('relation');
            $this->model->yearly_income = $request->input('yearly_income');
            $this->model->status = 1;
            $this->model->print_status = 0;
            $this->model->photo = $picturePath;
            $this->model->date_of_birth = $request->input('date_of_birth');
            $this->model->birth_certificate_no = $request->input('birth_certificate_no');
            $this->model->student_phone = $request->input('student_phone');
            $this->model->save();

            $studentId = $this->model->id;

            // Qualification entry
            foreach ($qualifications as $qualification) {
                $guardianData = new StudentQualification();
                $guardianData->fill($qualification);
                $guardianData->school_id = $schoolId;
                $guardianData->student_id = $studentId;
                $guardianData->save();
            }
            // Address entry
            foreach ($addresses as $address) {
                $addressData = new Address();
                $addressData->fill($address);
                $addressData->addressable_id = $studentId;
                $addressData->addressable_type = 1;
                $addressData->school_id = $schoolId;
                $addressData->save();
            }
            // Guardian entry
            foreach ($guardians as $guardian) {
                $guardianData = new Guardian();
                $guardianData->fill($guardian);
                $guardianData->student_id = $studentId;
                $guardianData->school_id = $schoolId;
                $guardianData->save();
            }
            DB::commit();
            return returnData(2000, null, 'Successfully Inserted');
        } catch (\Exception $exception) {
            DB::rollBack();
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function show($id)
    {
        if (!can('student_view')) {
            return $this->notPermitted();
        }
        $data = $this->model::with('guardian', 'user', 'classes', 'sessions', 'sections', 'departments')->where('id', $id)
            ->first();

        $address = Address::selectRaw("*, divisions.name as division, districts.name as district, upazilas.name as upazilla,unions.name as union_name")
            ->join('divisions', 'addresses.division', '=', 'divisions.id')
            ->join('districts', 'addresses.district', '=', 'districts.id')
            ->join('upazilas', 'addresses.upazila', '=', 'upazilas.id')
            ->join('unions', 'addresses.union', '=', 'unions.id')
            ->where('addressable_id', $id)
            ->where('addressable_type', 1)
            ->get();

        $student_qualifications = StudentQualification::where('student_id', $id)->get();

        $school = School::first();
        if ($school) {
            $data['school'] = collect($school->toArray());
        } else {
            $data['school'] = null;
        }

        $data->{'address'} = $address;
        $data['student_qualifications'] = $student_qualifications->toArray();
        return returnData(2000, $data);
    }

    public function edit($id)
    {
        try {
            $data = $this->model->where('id', $id)
                ->with(['user'])
                ->first();

            if (!$data) {
                return returnData(5000, null, 'Student not found');
            }
            $guardians = Guardian::where('student_id', $id)->get();
            $address = Address::where('addressable_id', $id)->where('addressable_type', 1)->get();
            $qualification = StudentQualification::where('student_id', $id)->get();

            $resultData = $data->toArray();
            $resultData['guardians'] = $guardians->toArray();
            $resultData['address'] = $address->toArray();
            $resultData['qualification'] = $qualification->toArray();

            return returnData(2000, $resultData);
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        if (!can('student_update')) {
            return $this->notPermitted();
        }
        try {
            DB::beginTransaction();
            $input = $request->except(['user', 'profile', 'guardians', 'address', 'qualification', 'as_same']);
            $qualifications = $request->input('qualification', []);
            $addresses = $request->input('address', []);
            $guardians = $request->input('guardians', []);
            $schoolId = auth()->user()->school_id;
            $createdById = auth()->user()->id;

            $student = $this->model->find($id);
            if (!$student) {
                return returnData(5000, null, 'Student not found');
            }

            // Validate input
            $validate = $this->model->validate($input);
            if ($validate->fails()) {
                return returnData(5000, $validate->errors());
            }

            // Update User data
            $userData = User::find($student->user_id);

            if (!$userData) {
                return returnData(5000, null, 'User not found');
            }
            $userData->username = strtolower($request->student_name_en);
            $userData->email = $request->student_roll;
            if ($request->has('password')) {
                $userData->password = Hash::make($request->password);
            }
            $userData->school_id = $schoolId;
            $userData->created_by = $createdById;
            $userData->status = 1;
            $userData->save();

            $input = $request->except([
                'user',
                'profile',
                'guardians',
                'address',
                'qualification',
                'image',
                '1_district',
                '1_upazilla',
                '1_union',
                '2_district',
                '2_upazilla',
                '2_union',
                'password',
                'as_same'
            ]);

            // Update Student data
            $picturePath = $request->image ?? null;

            $student->student_name_bn = $request->input('student_name_bn');
            $student->student_name_en = $request->input('student_name_en');
            $student->merit_roll = $request->input('merit_roll');
            // $student->student_roll = $request->input('student_roll');
            $student->reg_number = $request->input('reg_number');
            $student->father_name_bn = $request->input('father_name_bn');
            $student->father_name_en = $request->input('father_name_en');
            $student->father_nid = $request->input('father_nid');
            $student->father_phone = $request->input('father_phone');
            $student->father_profession = $request->input('father_profession');
            $student->mother_name_bn = $request->input('mother_name_bn');
            $student->mother_name_en = $request->input('mother_name_en');
            $student->mother_nid = $request->input('mother_nid');
            $student->mother_phone = $request->input('mother_phone');
            $student->mother_profession = $request->input('mother_profession');
            $student->school_id = $schoolId;
            // $student->class_id = $request->input('class_id');
            $student->responsible_teacher_id = $request->input('responsible_teacher_id');
            $student->section_id = $request->input('section_id');
            $student->department_id = $request->input('department_id');
            // $student->session_year_id = $request->input('session_year_id');
            $student->parent_id = $request->input('parent_id');
            $student->nationality = $request->input('nationality');
            $student->religion = $request->input('religion');
            $student->admission_date = $request->input('admission_date');
            $student->blood_group = $request->input('blood_group');
            $student->gender = $request->input('gender');
            $student->reference_name = $request->input('reference_name');
            $student->reference_mobile = $request->input('reference_mobile');
            $student->reference_address = $request->input('reference_address');
            $student->height = $request->input('height');
            $student->weight = $request->input('weight');
            $student->relation = $request->input('relation');
            $student->yearly_income = $request->input('yearly_income');
            $student->status = 1;
            $student->print_status = 0;
            $student->photo = $picturePath;
            $student->date_of_birth = $request->input('date_of_birth');
            $student->birth_certificate_no = $request->input('birth_certificate_no');
            $student->student_phone = $request->input('student_phone');

            $student->save();

            // Update Qualification data
            foreach ($qualifications as $qualification) {
                if (isset($qualification['id'])) {
                    $existingQualification = StudentQualification::find($qualification['id']);
                    if ($existingQualification) {
                        $existingQualification->fill($qualification);
                        $existingQualification->school_id = $schoolId;
                        $existingQualification->save();
                    }
                } else {
                    $newQualification = new StudentQualification();
                    $newQualification->fill($qualification);
                    $newQualification->student_id = $student->id;
                    $newQualification->school_id = $schoolId;
                    $newQualification->save();
                }
            }

            // Update Address data

            // ddA($addresses);
            // foreach ($addresses as $address) {
            //     // ddA(isset($address['id']));
            //     if (isset($address['id'])) {
            //         $existingAddress = Address::find($address['id']);
            //         if ($existingAddress) {
            //             $existingAddress->fill($address);
            //             $existingAddress->school_id = $schoolId;
            //             $existingAddress->save();
            //         }
            //     } else {
            //         $newAddress = new Address();
            //         $newAddress->fill($address);
            //         $newAddress->student_id = $student->id;
            //         $newAddress->school_id = $schoolId;
            //         $newAddress->save();
            //     }
            // }

            foreach ($addresses as $address) {
                if (isset($address['id'])) {
                    $type1Address = Address::where('addressable_id', $student->id)
                        ->where('type', 1)
                        ->first();

                    if ($type1Address) {
                        $type1Address->fill($address);
                        $type1Address->type = 1;
                        $type1Address->school_id = $schoolId;
                        $type1Address->save();
                    } else {
                        $newType1Address = new Address();
                        $newType1Address->fill($address);
                        $newType1Address->addressable_id = $student->id;
                        $newType1Address->school_id = $schoolId;
                        $newType1Address->type = 1;
                        $newType1Address->save();
                    }

                    $type2Address = Address::where('addressable_id', $student->id)
                        ->where('type', 2)
                        ->first();

                    if ($type2Address) {
                        $type2Address->fill($address);
                        $type2Address->type = 2;
                        $type2Address->school_id = $schoolId;
                        $type2Address->save();
                    } else {
                        $newType2Address = new Address();
                        $newType2Address->fill($address);
                        $newType2Address->addressable_id = $student->id;
                        $newType2Address->school_id = $schoolId;
                        $newType2Address->type = 2;
                        $newType2Address->save();
                    }
                } else {
                    $newAddress = new Address();
                    $newAddress->fill($address);
                    $newAddress->addressable_id = $student->id;
                    $newAddress->school_id = $schoolId;
                    $newAddress->save();
                }
            }

            // Update Guardian data
            foreach ($guardians as $guardian) {
                if (isset($guardian['id'])) {
                    $existingGuardian = Guardian::find($guardian['id']);
                    if ($existingGuardian) {
                        $existingGuardian->fill($guardian);
                        $existingGuardian->school_id = $schoolId;
                        $existingGuardian->save();
                    }
                } else {
                    $newGuardian = new Guardian();
                    $newGuardian->fill($guardian);
                    $newGuardian->student_id = $student->id;
                    $newGuardian->school_id = $schoolId;
                    $newGuardian->save();
                }
            }
            DB::commit();
            return returnData(2000, null, 'Successfully Updated');
        } catch (\Exception $exception) {
            return returnData(5000, $exception->getMessage(), 'Whoops, Something Went Wrong..!!');
        }
    }

    public function destroy($id)
    {
        if (!can('student_delete')) {
            return $this->notPermitted();
        }
        $data = $this->model->where('id', $id)->first();

        if ($data) {
            $studentId = $data->id;

            Address::where('addressable_id', $studentId)->delete();
            Guardian::where('student_id', $studentId)->delete();
            StudentQualification::where('student_id', $studentId)->delete();
            AssignDormitory::where('dormitory_user_type', $studentId)->delete();

            $hasInvoice = DB::table('invoices')->where('student_id', $studentId)->exists();
            if ($hasInvoice) {
                return returnData(5000, null, 'Cannot delete student with existing invoices.');
            }

            $data->delete();
            User::where('id', $data->user_id)->delete();

            return returnData(2000, null, 'Successfully Deleted');
        }
        return returnData(2000, [], 'Record not found');
    }

    // optional subject update
    public function updateOptionalSubject(Request $request, $id)
    {
        try {
            $student = $this->model->findOrFail($id);
            $student->optional_subject_id = $request->optional_subject_id;
            $student->save();

            return returnData(2000, ['message' => 'Optional subject updated successfully']);
        } catch (\Exception $e) {
            return returnData(5000, $e->getMessage(), 'Something went wrong');
        }
    }

    public function getOptionalSubjects(Request $request)
    {
        $classId = $request->input('class_id');
        $subjects = Subject::where('class_id', $classId)
            ->where('is_optional', 2)
            ->where('status', 1)
            ->select('id', 'name')
            ->get();

        return returnData(2000, ['optional_subjects' => $subjects]);
    }

    public function studentStatus(Request $request, $id)
    {
        if (!can('student_add')) {
            return $this->notPermitted();
        }
        $userId = Student::where('id', $id)->value('user_id');

        try {
            $dropoutDate = $request->dropout_date;
            $studentStatus = $request->status;

            if ($studentStatus == 1 || $studentStatus == 0) {
                $dropoutDate = null;
            }

            Student::where('id', $id)->update([
                'status' => $studentStatus,
                'comments' => $request->comments,
                'dropout_date' => $dropoutDate,
            ]);

            if ($userId) {
                $userStatus = ($studentStatus == 1) ? 1 : 0;
                User::where('id', $userId)->update([
                    'status' => $userStatus,
                ]);
            }

            return returnData(2000, null, 'Student status updated successfully');
        } catch (\Exception $exception) {

            return returnData(5000, null, $exception->getMessage());
        }
    }

    public function updatePrintStatus(Request $request, $id)
    {
        $student = Student::find($id);
        if ($student) {
            $student->print_status = $request->input('print_status');
            $student->save();
            return returnData(2000, null, 'Print status updated successfully.');
        } else {
            return returnData(5000, null, 'Student not found.');
        }
    }

    public function generateStudentRoll(Request $request)
    {
        $sessionYear = SessionYear::find($request->session_year_id);
        $class = StudentClass::find($request->class_id);

        if (!$sessionYear || !$class) {
            return returnData(5000, null, 'Invalid data provided');
        }

        $sessionParts = explode('-', $sessionYear->title);
        if (count($sessionParts) > 1) {
            $rollPrefix = substr($sessionParts[0], 2, 2) . substr($sessionParts[1], 2, 2) . $class->numeric_name;
        } else {
            $rollPrefix = substr($sessionYear->title, 2, 2) . $class->numeric_name;
        }

        $studentRoll = DB::transaction(function () use ($request, $rollPrefix) {

            $lastStudent = Student::where('session_year_id', $request->session_year_id)
                ->where('class_id', $request->class_id)
                ->where('student_roll', 'LIKE', $rollPrefix . '%')
                ->orderByRaw('CAST(SUBSTRING(student_roll, ' . (strlen($rollPrefix) + 1) . ') AS UNSIGNED) DESC')
                ->lockForUpdate()
                ->first();

            $nextRollNumber = $lastStudent
                ? (int) substr($lastStudent->student_roll, strlen($rollPrefix)) + 1
                : 1;

            $maxNumberLength = $lastStudent
                ? strlen((string) ((int) substr($lastStudent->student_roll, strlen($rollPrefix))))
                : 1;

            $minPadding = max(3, $maxNumberLength);

            $nextRollNumber = str_pad($nextRollNumber, $minPadding, '0', STR_PAD_LEFT);

            $studentRoll = $rollPrefix . $nextRollNumber;

            if (Student::where('student_roll', $studentRoll)->exists()) {
                // throw new \Exception('Student roll conflict detected. Please try again.');
                return returnData(5000, null, 'Student roll conflict detected. Please try again');
            }

            return $studentRoll;
        });

        return response()->json(['student_roll' => $studentRoll], 200);
    }
}
