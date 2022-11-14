<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Config;

class ProfileController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // As it's common for every user we are not going to add any permission check
        // $this->middleware('permission:list-profile');
        // $this->middleware('permission:edit-profile', ['only' => ['edit','update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $user = User::where(['id' => Auth::id()])->firstOrFail();
        $statusArr = Config::get('params.status');
        $genders = Config::get('params.gender');

        return view('pages.profile.index', compact('user', 'genders', 'statusArr'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProfileUpdateRequest $request
     * @param User $user
     * @return Response
     */
    public function update(ProfileUpdateRequest $request, User $user)
    {

        $data = $request->validated();

        $user = User::where(['id' => Auth::id()])->firstOrFail();

        if ($request->hasFile('file')) {


            $data['avatar'] = Storage::disk('avatar')->put('', $request->file);

            if ($user->avatar != '') {
                Storage::disk('avatar')->delete($user->avatar);
            }
        }


        $user->update($data);

        return redirect()->route('profile.index')->with('success', trans('admin.message.updated', ['module' => trans('admin.label.profile')]));
    }
}
