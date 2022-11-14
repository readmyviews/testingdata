<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{

    public function __construct()
    {
        // As it's common for every user we are not going to add any permission check
        //$this->middleware('permission:change-password-profile', ['only' => ['index','store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('pages.changepassword.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ChangePasswordRequest $request
     * @return Response
     */
    public function store(ChangePasswordRequest $request)
    {
        User::where('id', auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);
        return redirect()->route('change.password')->with('success', trans('admin.message.updated', ['module' => trans('admin.label.password')]));
    }

}
