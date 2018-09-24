<?php

namespace App\Http\Controllers;

use App\Services\RoleScopeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * @var RoleScopeService
     */
    protected $roleScopeService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->roleScopeService = new RoleScopeService();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // set the user on the role scope server
        $this->roleScopeService->user = Auth::user();

        // determine if user is verified
        $verified = Auth::user()->verified;

        return view('home')->with('verified', $verified);
    }

    /**
     * route for an invalid invite
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function invalid()
    {
        return view('invalid-invite');
    }
}
