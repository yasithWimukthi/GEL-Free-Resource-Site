<?php

namespace App\Http\Controllers;

use App\Models\AwardingBody;
use App\Models\Course;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDOException;
use Symfony\Component\Console\Input\Input;

class CourseController extends Controller
{

    public function index(){
        $course = Course::all();
        $awardingBody = AwardingBody::all();
        return view('course',['courses'=>$course,'awardingBodies' => $awardingBody]);
    }

    public function store(Request $request){
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'awarding_body' => 'required',
            'image' => 'mimes:jpeg,png,jpg',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect('/course')
                ->withInput()
                ->withErrors($validator);
        }else{
            $data = $request->input();
            try{
                $inputs = [];
                if(request('image')){
                    $inputs['image'] = request('image')->store('course images');
                    request('image')->store('course images');
                }

                $course = new Course();
                $course->name = $data['name'];
                $course->description = $data['description'];
                $course->image = $inputs['image'];
                $course->awarding_body_id = $data['awarding_body'];
                //$data['image']->store('course images');
                $course->save();
            }catch(Exception $e){
                return redirect('/exam')->with('failed',"operation failed");
            }
        }

        if(request('image')){
            $inputs['image'] = request('image')->store('course images');
        }

//        $course = new Course();
//        $course->name = $request->name;
//        $course->description = $request->description;
//        $course->awarding_body = $request->awarding_body;
//        $course->image = $inputs['image'];
//
//        $course->save();

        return back();
    }

    function getCoursesByAwardingId(Request $request){
       $data = DB::table('courses')->whereIn('awarding_body_id',[$request->id])->get();
        echo json_encode($data);
        exit;

    }
}
