<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthAdminRequest;
use App\Models\History;
use App\Models\Negative;
use App\Models\Plan;
use App\Models\Positive;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Admin dashboard 
     */
    public function index()
    {
        $negatives = Negative::all();
        $positives = Positive::all();
        $products = Product::all();
        $histories = History::all();
        $users = User::all();
        $plans = Plan::all();
        $subscriptions = Subscription::all();

        return view('admin.dashboard')->with([
            "negatives" => $negatives,
            "positives" => $positives,
            "products" => $products,
            "histories" => $histories,
            "users" => $users,
            "plans" => $plans,
            "subscriptions" => $subscriptions,
        ]);
    }

    /**
     * Display the login form for the admin
     */
    public function login()
    {
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.index');
        }

        return view('admin.login');
    }

    /**
     * Login admin
     */
    public function auth(AuthAdminRequest $request)
    {
        if (auth()->guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            echo "email: ".$request->email;
            $request->session()->regenerate();

            return redirect()->route('admin.index');
        } else {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.'
            ]);
        }
    }

    /**
     * Logout admin
     */
    public function logout()
    {
        auth()->guard('admin')->logout();

        return redirect()->route('admin.login');
    }
}
