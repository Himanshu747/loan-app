<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;

class usersController extends Controller
	{
		function createUser( Request $request )
			{
				$validated = $request->validate( [ 'username' =>'required|unique:users' ] );

				if ( ! empty( $validated ) )
					{
						// To store data in DB Syntax ( $modal_object->db_columns_name = $request->field_name )
						$user = new User();

						$user->status = 1;
						$user->username = $request->username;
						$user->password = bcrypt( $request->password );
						$user->secret_key = hash_hmac( 'sha256' , '2<1CglY[1Sm\2/MyN}e)"n,Y91K_7',  bin2hex( random_bytes( 32 ) ) );

						$result = $user->save();

						if ( $result )
							{
								return [ $this->finalResponse( 'success', 'User created!', 'Pleae login to continue.' ) ];
							}
						else
							{
								return [ $this->finalResponse( 'error', '', '' ) ];
							}
					}
				else
					{
						return [ $this->finalResponse( 'info', 'User already exists!', 'Please choose a different username.' ) ];
					}
			}

		function checkUser( Request $request )
			{
				if ( Auth::attempt( [ 'username'=>$request->username,'password'=>$request->password ] ) )
					{
						// $result = Users::where( 'username', '=', Input::get( 'email' ) )->first();
					// $result = Users::select( 'secret_key' )->where( [ 'username' => $request->username, 'password' => bcrypt( $request->password ) ] )->first();

					// if ( $result )
					// 	{
							return [ $this->finalResponse( 'success', 'User authenticated!', 'Welcome to loan application.', [ 'secret' => Auth::User()->secret_key ] ) ];
					// 	}
					// else
					// 	{
					// 		return [ $this->finalResponse( 'error', 'Unable to login!', 'Please enter correct credentials.' ) ];
					// 	}
					}
				else
					{
						return [ $this->finalResponse( 'error', 'Unable to login!', 'Please enter correct credentials.' ) ];
					}
			}
	}
