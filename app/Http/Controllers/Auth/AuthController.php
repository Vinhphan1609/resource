<?php
  
namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
  
class AuthController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function __construct(User $User){
        $this->User = $User;
    }
    public function index()
    {
        return view('auth.login');
    }  
      
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registration()
    {
        return view('auth.registration');
    }
      
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function doLogin(Request $request)
    {
        if ($this->User->is_email($request->name)) {
            $credentials = [ 'email'=> $request['name'], 'password' => $request['password'] ];
        } else {
            $credentials = [ 'name'=> $request['name'], 'password' => $request['password'] ];
        }
        // $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            if(Auth::user()->can('edit-users')){
            return redirect()->intended('dashboard')
                        ->withSuccess('You have Successfully loggedin');
            }
            return redirect()->intended('home');
        }
  
        return redirect()->back()->with('error','Invalid username/email or password, try again');
    }
      
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postRegistration(Request $request)
    {  
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
           
        $data = $request->all();
        $check = $this->create($data);
         
        return redirect("dashboard")->withSuccess('Great! You have Successfully loggedin');
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function dashboard()
    {
        if(Auth::check()){
            return view('dashboard');
        }
  
        return redirect("login")->withSuccess('Opps! You do not have access');
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout() {
        Session::flush();
        Auth::logout();
  
        return Redirect('login');
    }
}