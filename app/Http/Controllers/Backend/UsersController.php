<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Purify\Facades\Purify;
use Intervention\Image\Facades\Image;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class UsersController extends Controller
{

    public function __construct()
    {
        if(\auth()->check()){
            $this->middleware('auth');
        }else{
            return view('backend.auth.login');
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
                /* if(!\auth()->user()->ability('admin','manage_users,show_users')){
                            return redirect('admin/index');'
                        }
                class EntrustAbility
*/
                        if (!\auth()->user()->ability('admin','manage_post,show_users')) {
                            return redirect('admin/index');
                        }
        $keyword = (isset(\request()->keyword) && \request()->keyword != '') ? \request()->keyword : null;
        $status = (isset(\request()->status) && \request()->status != '') ? \request()->status : null;
        $sort_by = (isset(\request()->sort_by) && \request()->sort_by != '') ? \request()->sort_by : 'id';
        $order_by = (isset(\request()->order_by) && \request()->order_by != '') ? \request()->order_by : 'desc';
        $limit_by = (isset(\request()->limit_by) && \request()->limit_by != '') ? \request()->limit_by : '10';


        $users = User::whereHas('roles',function($query){
            $query->where('name','user');
        });
        if($keyword != null){
           $users=$users->search($keyword);
        }

        if($status != null){
           $users=$users->whereStatus($status);
        }


      $users=$users->orderBy($sort_by,$order_by);
      $users=$users->paginate($limit_by);








        return view('backend.users.index',compact('users'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
            if(!\auth()->user()->ability('admin','create_users')){
                return redirect('admin/index');
            }
        return view('backend.users.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
              if(!\auth()->user()->ability('admin','create_users')){
                    return redirect('admin/index');
                }
            $validate = Validator::make($request->all(),[
                'name'                 =>'required',
                'username'             => 'required|max:20|unique:users',
                'email'                =>  'required|email|unique:users',
                'mobile'               => 'required|numeric|unique:users',
                'status'               => 'required',
                'password'             =>'required|min:8',
            ]);
        if($validate->fails()){
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $data['name']              = $request->name;
        $data['username']          = $request->username;
        $data['email']             = $request->email;
        $data['email_verified_at'] = Carbon::now();
        $data['mobile']            = $request->mobile;
        $data['password']          = bcrypt($request->password);
        $data['status']            = $request->status;
        $data['bio']               = $request->bio;
        $data['receive_email']     = $request->receive_email;


        if($user_image = $request->file('user_image') ){

                $filename = Str::slug($request->username). '.' .$user_image->getClientOriginalExtension();

                $path = public_path('assets/users/'.$filename);

                Image::make($user_image->getRealPath())->resize(300,300,function($constraint){
                    $constraint->aspectRatio();
                })->save($path,100);
                $data['user_image']     = $filename ;

        }

        $user = User::create($data);
        $user->attachRole(Role::whereName('user')->first()->id);

        return redirect()->route('admin.users.index')->with([
            'message'  => 'users created Successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
              if(!\auth()->user()->ability('admin','display_users')){
                    return redirect('admin/index');
                }
                $user = User::whereId($id)->withCount('posts')->first();
                if($user){
                    return view('backend.users.show',compact('user'));
                }
                return redirect()->route('admin.users.index')->with([
                    'message'  => ' something was wrong',
                    'alert-type' => 'danger',
                 ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         if(!\auth()->user()->ability('admin','update_users')){
                return redirect('admin/index');
            }

        $user = User::whereId($id)->first();
        if($user){
            return view('backend.users.edit',compact('user'));
        }
        return redirect()->route('admin.users.index')->with([
            'message'  => ' something was wrong',
            'alert-type' => 'danger',
         ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!\auth()->user()->ability('admin','update_users')){
                return redirect('admin/index');
            }
            $validate = Validator::make($request->all(),[
                'name'                 =>'required',
                'username'             => 'required|max:20|unique:users,username,'.$id,
                'email'                =>  'required|email|unique:users,email,'.$id,
                'mobile'               => 'required|numeric|unique:users,mobile,'.$id,
                'status'               => 'required',
                'password'             =>'nullable|min:8',
            ]);
        if($validate->fails()){
            return redirect()->back()->withErrors($validate)->withInput();
        }

        $user =User::whereId($id)->first();
        if($user){
        $data['name']              = $request->name;
        $data['username']          = $request->username;
        $data['email']             = $request->email;
        $data['mobile']            = $request->mobile;
        if(trim($request->password) != ''){
            $data['password']          = bcrypt($request->password);

        }
        $data['status']            = $request->status;
        $data['bio']               = $request->bio;
        $data['receive_email']     = $request->receive_email;


        if($user_image = $request->file('user_image') ){

            if($user->user_image != ''){
                if(File::exists('assets/users/' .$user->user_image )){
                    unlink('assets/users/' . $user->user_image);
                    }
            }

                $filename = Str::slug($request->username). '.' .$user_image->getClientOriginalExtension();
                $path = public_path('assets/users/'.$filename);

                Image::make($user_image->getRealPath())->resize(300,300,function($constraint){
                    $constraint->aspectRatio();
                })->save($path,100);
                $data['user_image']     = $filename ;

        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with([
            'message'  => 'users created Successfully',
            'alert-type' => 'success',
        ]);
        }
        return redirect()->route('admin.users.index')->with([
            'message'  => ' something was wrong',
            'alert-type' => 'danger',
         ]);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
             if(!\auth()->user()->ability('admin','delete_users')){
                    return redirect('admin/index');
                }

        $user =User::whereId($id)->first();
        if($user){

                if(File::exists('assets/users/' . $user->user_image )){
                    unlink('assets/users/' . $user->user_image);
                    }


             $user->delete();
             return redirect()->route('admin.users.index')->with([
                'message'  => 'users deleted Successfully',
                'alert-type' => 'success',
             ]);
         }
         return redirect()->route('admin.users.index')->with([
            'message'  => ' something was wrong',
            'alert-type' => 'danger',
         ]);
    }

    public function removeImage(Request $request){

              if(!\auth()->user()->ability('admin','delete_users')){
                    return redirect('admin/index');
                }
        $user =User::whereId($request->user_id)->first();
        if($user){
            if($user->user_image != ''){
            if(File::exists('assets/users/' .$user->user_image )){
                unlink('assets/users/' .$user->user_image);
            }
        }
            $user->user_image = null;
            $user->save();
            return 'true';
        }

        return 'false';
    }

}
