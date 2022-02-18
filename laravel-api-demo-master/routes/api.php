<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\loansController;
use App\Http\Controllers\usersController;

Route::middleware( 'auth:api' )->get( '/user', function ( Request $request )
	{
		return $request->user();
	} );

// Pre login APIs
Route::post( 'register', [ usersController::class, 'createUser' ] );
Route::post( 'login', [ usersController::class, 'checkUser' ] );

// Post Login APIs
Route::post( 'applyloan', [ loansController::class, 'applyforLoan' ] );
Route::post( 'approveloan', [ loansController::class, 'approveLoan' ] );
Route::post( 'payemi', [ loansController::class, 'payLoanEMI' ] );
