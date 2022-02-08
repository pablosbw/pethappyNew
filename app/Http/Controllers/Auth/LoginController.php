<?php

namespace App\Http\Controllers\Auth;

use App\BranchOffice;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/fichas';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @Override Illuminate\Foundation\Auth\AuthenticatesUsers::showLoginForm
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return "gola";
        // $branchOffices = BranchOffice::all();

        //return view('auth.login')->with('branchOffices', $branchOffices);
    }

    /**
     * @Override Illuminate\Foundation\Auth\AuthenticatesUsers::login
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
            'branch' => 'required'
        ]);

        $fieldType = 'username';
        if(auth()->attempt(array($fieldType => $input['username'], 'password' => $input['password'])))
        {
            $this->updateBranchOffice($input['branch']);
            $userInfo = null;
            // TODO: Develop and use helper instead
            switch (Auth::user()->role_id){
                case 1:
                    $userInfo = Auth::user()->admin;
                    return redirect('sucursales');
                // Vendedor
                case 2:
                    $userInfo = Auth::user()->seller;
                    return redirect('fichas');
                // Veterinario
                case 3:
                    $userInfo = Auth::user()->veterinary;
                    return redirect('fichas');
                // ABA
                case 6:
                case 7:
                    $userInfo = Auth::user()->schedule_user;
                    return redirect('editar');
                // Peluquero
                case 8:
                    $userInfo = Auth::user()->pet_groomer;
                    return redirect('fichas');
            }
            
        }else{
            return redirect('/')
                ->with('error','Email-Address And Password Are Wrong.');
        }
    }

    /**
     * @param int $id
     */
    private function updateBranchOffice(int $id)
    {
        $userInfo = null;
        // TODO: Develop and use helper instead
        switch (Auth::user()->role_id){
            // Vendedor
            case 2:
                $userInfo = Auth::user()->seller;
                break;
            // Veterinario
            case 3:
                $userInfo = Auth::user()->veterinary;
                break;
            // ABA
            case 6:
            case 7:
                $userInfo = Auth::user()->schedule_user;
                break;
            // Peluquero
            case 8:
                $userInfo = Auth::user()->pet_groomer;
                break;
        }
        if($userInfo){
            $userInfo->branch_office_id = $id;
            $userInfo->save();
        }
    }
}