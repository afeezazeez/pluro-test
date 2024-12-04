<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test',function (){
   $user =  \App\Models\User::where('id',1)->firstorfail();
   dd($user);
});
