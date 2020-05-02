<?php

namespace App\Http\Controllers;

use App\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Friend;

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


    /**
     * Show Home page after successfull login 
     */
    public function shoutHome()
    {
        $user_id = Auth::id();
        if(Friend::where('user_id', $user_id)->where('friend_id', $user_id)->count() == 0){
            $friend = new Friend();
            $friend->user_id = $user_id;
            $friend->friend_id = $user_id;
            $friend->save();
        }
        $status = Auth::user()->friendsStatus;
        $avatar = (Auth::user()->avatar) ? Auth::user()->avatar : asset("public/images/avatar.jpg");
        return view('shouthome', ['status' => $status, 'avatar' => $avatar]);
    }


    /**
     * Show specific user's status
     */
    public function shoutPublic($nickname)
    {
        $user = User::where('nickname', $nickname)->first();

        if ($user) {
            $status = Status::where('user_id', $user->id)->orderBy('id', 'desc')->get();
            $avatar = ($user->avatar) ? $user->avatar : asset("public/images/avatar.jpg");
            $name = $user->name;

            $displayFriendship = false;
            if (Auth::check() && (Auth::user()->id != $user->id)) {
                $displayFriendship = true;
            }

            $hasFriendship = 0;
            if (Friend::where('user_id', Auth::user()->id)->where('friend_id', $user->id)->count() > 0) {
                $hasFriendship = 1;
            } else {
                $hasFriendship = 0;
            }
            return view('shoutpublic', [
                'status' => $status,
                'avatar' => $avatar,
                'name' => $name,
                'displayFriendship' => $displayFriendship,
                'hasFriendship' => $hasFriendship,
                'friendId' => $user->id
            ]);
        } else {
            return redirect('/');
        }
    }


    /**
     * Add new status
     */
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

    /**
     * Show own profile with edit option
     */
    public function profile()
    {
        return view('profile');
    }


    /**
     * Update users profile
     */
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

    /**
     * Make a specific user as friend
     */
    public function makeFriend($friendId)
    {
        $userId = Auth::user()->id;

        if (Friend::where('user_id', $userId)->where('friend_id', $friendId)->count() == 0) {
            $friendShip = new Friend();
            $friendShip->user_id = $userId;
            $friendShip->friend_id = $friendId;
            $friendShip->save();
        }
        if (Friend::where('friend_id', $userId)->where('user_id', $friendId)->count() == 0) {
            $friendShip = new Friend();
            $friendShip->user_id = $friendId;
            $friendShip->friend_id = $userId;
            $friendShip->save();
        }

        return redirect()->route('shout');
    }


    /**
     * Unfriend a specific user
     */
    public function unFriend($friendId)
    {
        $userId = Auth::user()->id;
        Friend::where('user_id', $userId)->where('friend_id', $friendId)->delete();
        Friend::where('user_id', $friendId)->where('friend_id', $userId)->delete();
        return redirect()->route('shout');
    }
}
