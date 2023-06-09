<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Mail\ApprovedUser;
use App\Mail\DeniedUser;
use App\Models\Company;
use App\Models\PendingCompany;
use App\Models\PendingUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:user list', ['only' => ['index', 'show']]);
        $this->middleware('can:user create', ['only' => ['create', 'store']]);
        $this->middleware('can:user edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:user delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = (new User)->newQuery();
        $users->latest();
        $users = $users->paginate(10)->onEachSide(2)->appends(request()->query());
        $pendingUsers = (new PendingUser)->newQuery();
        $pendingUsers->latest();
        $pendingUsers->where('approved','pending');
        $pendingUsers = $pendingUsers->paginate(10)->onEachSide(2)->appends(request()->query());

        return Inertia::render('Superadmin/User/Index', [
            'users' => $users,
            'pendingUsers' => $pendingUsers,
            'can' => [
                'create' => Auth::user()->can('user create'),
                'edit' => Auth::user()->can('user edit'),
                'delete' => Auth::user()->can('user delete'),
            ]
        ]);
    }

    public function approve($id){
        
        $pendingUser = PendingUser::find($id);
        $pendingCompany = PendingCompany::where('user_id', $pendingUser->id)->first();
        $logo = explode('/',$pendingCompany->logo);
        $user = User::create([
            'name' => $pendingUser->name,
            'email' => $pendingUser->email,
            'password' => $pendingUser->password,
        ]);

        $role = Role::where('name', 'admin')->first();
        $user->assignRole($role);

        $company = Company::create([
            'name' => $pendingCompany->name,
            'user_id' => $user->id,
            'address' => $pendingCompany->address,
            'phone' => $pendingCompany->phone,
            'description' => $pendingCompany->description,
            'location_id' => $pendingCompany->location_id,
            'logo' => $logo[4],
            'instagram' => $pendingCompany->instagram,
            'facebook' => $pendingCompany->facebook,
            'website' => $pendingCompany->website,
        ]);
        $pendingUser->approved = 'true';
        $pendingCompany->approved = 'true';
        $pendingUser->save();
        $pendingCompany->save();

        Mail::to($pendingUser->email)->send(new ApprovedUser());

        return redirect()->route('user.index');


    }

    public function deny($id){
        $pendingUser = PendingUser::find($id);
        $pendingCompany = PendingCompany::where('user_id', $pendingUser->id)->first();
        $pendingUser->approved = 'false';
        $pendingCompany->approved = 'false';

        $pendingUser->save();
        $pendingCompany->save();

        Mail::to($pendingUser->email)->send(new DeniedUser());
        return redirect()->route('user.index');


    }

    public function viewCompany($id){
        $company = Company::with((['location']))->where('user_id', $id)->first();

        if(empty($company)){
            $company = PendingCompany::with((['location']))->where('user_id', $id)->first();
        }
        return Inertia::render('Superadmin/User/Company', [
            'company' => $company,
        ]);
    }
}