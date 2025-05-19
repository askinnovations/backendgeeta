<?php
namespace App\Http\Controllers\Frontend\Api;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;


class AuthController extends Controller
{
   public function register(Request $request)
{
    try {
        $request->validate([
            'name' => 'required',
            'contact' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'mobile_number' => $request->contact,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user,
        ]);
    } catch (ValidationException $e) {
        $errors = $e->errors();

        if (isset($errors['email'])) {
            return response()->json([
                'status' => false,
                'message' => 'Email already registered',
            ], 422);
        }

        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $errors,
        ], 422);
    }
}
 public function login(Request $request)
    {
     
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

         if (! $user) {
        return response()->json([
            'status' => false,
            'message' => 'Email does not exist in the database.',
        ], 401);
    }
     if (! Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'The provided password is incorrect.',
        ], 401);
    }
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }


public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out',
        ]);
    }
   

public function orderDetail()
{
    $user = Auth::user();

    $orders = Order::with('user')
                ->where('user_id', $user->id)
                ->get();

    if ($orders->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No orders found',
        ]);
    }

    return response()->json([
        'status' => true,
        'orders' => $orders
    ]);
}
 public function profile()
    {
       
        $user = Auth()->user();
       if(!$user){
         return response()->json([
            'status' => false,
            'message' => 'User Not found!',
    ]);
       }
        return response()->json([
            'status' => true,
            'user' => $user
    ]);
    }

    public function updateProfile(Request $request)
   {   
        $user = auth()->user();
 if(!$user){
         return response()->json([
            'status' => false,
            'message' => 'User Not found!',
    ]);
}
        $user->update([
            'name' => $request->name,
            'pan_number' => $request->pan_number,
            'tan_number' => $request->tan_number,
            'address' => json_encode($request->address),
        ]);

      
         return response()->json([
            'status' => true,
            'message' => 'User Profile updated Succefully!',
    ]);
   }
   

}