<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response; 
use App\Models\User;
use App\Traits\ApiResponser;
use App\Models\UserJob;


Class UserController extends Controller {
use ApiResponser;
private $request;
public function __construct(Request $request){
$this->request = $request;
}

public function loginPage(){
    return view('login');
}

public function getUser($id){
    $user = app('db')->select("SELECT * FROM tbluser WHERE id=".$id);
    if($user == null) return response()->json('No user found in the database');
    return response()->json($user,200);
}

public function getUsers(){
    $users = app('db')->select("SELECT * FROM tbluser");
    return response()->json($users,200);
    }

    public function validateUser(){
            
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = app('db')->select("SELECT * FROM tbluser WHERE username='$username' and password='$password'");

        if(empty($user)){
            return 'Doesnt Exists in the Database or Incorrect Credentials.';
        }else{
            return redirect()->route('dashboard');
        }
        
    }
    
    public function dashboard(){
        return view('dashboard');
    }

    public function addUsers(Request $request){

        $rules = [
            'username' => 'required|max:20',
            'password' => 'required:max:20',
            'jobid' => 'required|numeric|min:1|not_in:0',
        ];

        $this->validate($request, $rules);

        //validate if jobid is found in the tbluserjob
        $userjob=UserJob::findOrFail($request->jobid);
        $users = User::create($request->all());

        return $this->successResponse($users, Response::HTTP_CREATED);

        // $users = new User;

        // $users->username = $this->request->username;
        // $users->password = $this->request->password;

        // $users->save(); 
        // return response()->json($users,200);
    }


    // Update User
    public function updateUser(Request $request, $id){

        $rules = [
            'jobid' => 'required|numeric|min:1|not_in:0',
        ];
        $this->validate($request,$rules); 
        $userjob = UserJob::findOrFail($request->jobid);
  
        $users = User::find($id);

        
        if($request->input('password') == null){
            $users->username = $request->input('username');
            $request->input = $users->password;
            $users->jobid= $request->jobid;
        }else if($request->input('username') == null ){
            $users->password = $request->input('password');
            $request->username = $users->username;
            $users->jobid= $request->jobid;
        }else{
            $users->username = $request->input('username');
            $users->password = $request->input('password');
            $users->jobid= $request->jobid;
        }
        $users->save();

        return $this->successResponse('User Updated Successfully',Response::HTTP_OK);
    }
    
    public function deleteUser($id){
        // $user = User::findOrFail($id);
        // $user->delete();
        // return $this->errorResponse('User ID Does Not Exists', Response::HTTP_NOT_FOUND);
        $user = User::find($id);

            if($user == null) return response()->json('Doesnt exist in the database',404);

            $user->delete();

            return response()->json('The ID including the user has been deleted',200);
    
        // $user = app('db')->select("SELECT * FROM tbluser WHERE id=".$id);

        // if($user == null) return response()->json('No User Found in the Database');

        // $user->delete();
        // $user = app('db')->select ("DELETE * FROM tbluser WHERE id=".$id);
        // return response()->json('User Deleted',200);
    }
}
  
//eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyIiwianRpIjoiZWQ2N2MyMmFlYmJhZDViZTgxZmEyYTZiMWE5MDI5N2IxNzlkZGZkN2VlZjg3MWIzMGIxNzM4YTE2MTFiNWIwM2U1MjI3ZmUwM2U4ZWVlY2IiLCJpYXQiOjE2MDc0OTQ3NzIsIm5iZiI6MTYwNzQ5NDc3MiwiZXhwIjoxNjM5MDMwNzcyLCJzdWIiOiIiLCJzY29wZXMiOltdfQ.kRcGpJOQFSURwyxXirLXcFOmXgtr-8Muwm8yinlEIzcvyBlWb8QwuukhYs6XCFkDuhWgh22o2EOSbBqx3YgsK8fcTUbYYLFlIYhzmNG4R3Nwoawqat5xQ0GFA492N3dPUZjmJiJoQPY8qF_Fbf0QT_6xE4q34lpm04T4PkGXLI0m_98RSbYGroJ4KVLKyNdLcSBrbHKo6S_2r54ngkIgSBU3JFaYG-qXw19aZT63_NIAFs2Cw1s-0sBLB6-HqHZrl0vLA5ViU39SdxMBUZraAO-keT2QXuvn1o5F24LuO4AztvvCMx2ukL5yS_vt-VPqcglV2hc1PVW1MeekQYXkdMel0l8u558UxUvPNJvVDleE0iDFIIM1esCxpA0bsCoKYeDwaZvGXTS3k13QXLi0UEHOZjq4_leAPAonkkBkMPQlzxkkXm7kjaNVwhRZrlX5utJG4VxxTWdcDOY7pHOYEblSJSkkC9sltFsR7VR46oawZfxsKDzpCHGHsrK2pLAVT5FejvOVX_APlhqlbHwvMoAzDdTFbQ3C0k9A8ZcwlXSmVH7Ggflwnf4jroMO6_9k1Eq_arbQrGaDtdatRMHLmgHMCe03xlrTjaPk8p5svtawLIdWtmCnjSV9hkhRfJzQSJ5WmUcSL6ZecBQktr6f9uTU4mSVmTROVipwthWu46c
