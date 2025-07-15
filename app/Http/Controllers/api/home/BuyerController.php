<?php

namespace App\Http\Controllers\api\home;

use App\Http\Controllers\api\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BuyerController extends BaseController
{
    public function show()
    {
        return response()->json(['success' => true, 'data' => auth()->user()]);
    }
    public function update(Request $r)
    {
        $u = auth()->user();
        $r->validate([
            'fname' => 'required',
            'lname' => 'required',
            'phone' => 'required',
            'gender' => 'required|in:Male,Female,Other'
        ]);
        $u->update([
            'name' => trim("$r->fname $r->lname"),
            'phone' => $r->phone,
            'gender' => $r->gender,
        ]);
        return response()->json(['success' => true, 'data' => $u]);
    }
    public function changePassword(Request $r)
    {
        $r->validate([
            'current' => ['required'],
            'new' => ['required', 'min:6'],
        ]);

        $user = $r->user();

        /* eski şifre doğru mu? */
        if (!Hash::check($r->current, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mevcut şifre hatalı'
            ], 422);
        }

        /* aynı şifre mi? */
        if (Hash::check($r->new, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Yeni şifre eski şifreyle aynı olamaz'
            ], 422);
        }

        $user->update(['password' => bcrypt($r->new)]);

        return response()->json(['success' => true]);
    }
}
