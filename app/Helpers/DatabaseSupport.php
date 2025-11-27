<?php
namespace App\Helpers;
class DatabaseSupport{

    public function count_class_or_depertment($request,$data){


       $data_count= $data->when($request->class_id !=null && $request->group_id !=null, function ($query) use($request) {
            $query->where('class_id', '=', $request->class_id);
            $query->where('group_id', '=', $request->group_id ?? 0);
            $query->where('session_id', '=', $request->session_id );
        })
        ->when(($request->depertment_id !=null), function ($query) use($request) {
            $query->where('department_id', '=', $request->depertment_id);
            $query->where('session_id', '=', $request->session_id );
        })->count();
        return $data_count;
    }

    public function select_exam($request,$data){

        $data_count= $data->when($request->class_id !=null && $request->group_id !=null, function ($query) use($request) {
            $query->where('class_id', '=', $request->class_id);
            $query->where('group_id', '=', $request->group_id ?? 0);
            $query->where('session_id', '=', $request->session_id );
        })
        ->when(($request->depertment_id !=null), function ($query) use($request) {
            $query->where('department_id', '=', $request->depertment_id);
            $query->where('session_id', '=', $request->session_id );
        })->first();
        return $data_count;
}
}