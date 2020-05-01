<?php

namespace App\Http\Controllers;

use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }


    public function shoutHome()
    {
        $user_id = Auth::id();
        $status = Status::where('user_id', $user_id)->orderBy('id', 'desc')->get();
        $avatar = (Auth::user()->avatar) ? Auth::user()->avatar : asset("public/images/avatar.jpg");
        return view('shouthome', ['status' => $status, 'avatar' => $avatar]);
    }


    public function shoutPublic($nickname)
    {
        $user = User::where('nickname', $nickname)->first();
        if ($user) {
            $status = Status::where('user_id', $user->id)->orderBy('id', 'desc')->get();
            $avatar = ($user->avatar) ? $user->avatar : asset("public/images/avatar.jpg");
            $name = $user->name;
            return view('shoutpublic', ['status' => $status, 'avatar' => $avatar, 'name' => $name]);
        }
    }


    public function saveStatus(Request $request)
    {
        if (Auth::check()) {

            $status = $request->post('status');
            $user_id = Auth::id();

            $statusModel = new Status();
            $statusModel->status = $status;
            $statusModel->user_id = $user_id;
            $statusModel->save();

            return redirect()->route('shout');
        }
    }

    public function profile()
    {
        return view('profile');
    }

    public function saveProfile(Request $request)
    {
        if (Auth::check()) {
            $current_user_model = Auth::user();
            $current_user_model->name = $request->name;
            $current_user_model->email = $request->email;
            $current_user_model->nickname = join("-", explode(" ", $request->nickname));

            if ($request->image) {
                $image_name = "user_" . $current_user_model->id . "." . $request->image->extension();
                $request->image->move(public_path("images"), $image_name);
                $current_user_model->avatar = asset("public/images/{$image_name}");
            }

            $current_user_model->save();

            return redirect()->route('shout.profile');
        }
    }
}
