<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            Status::insert([
                [
                    'title' => 'To Do',
                    'slug' => 'to_do',
                    'order' => 1,
                    'user_id' => $user->id
                ],
                [
                    'title' => 'In Progress',
                    'slug' => 'in_progress',
                    'order' => 2,
                    'user_id' => $user->id
                ],
                [
                    'title' => 'Done',
                    'slug' => 'done',
                    'order' => 3,
                    'user_id' => $user->id
                ],
            ]);
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


        event(new Registered($user));

        Auth::login($user);



        return redirect(RouteServiceProvider::HOME);
    }
}
