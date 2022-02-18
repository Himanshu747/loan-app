<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\User;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function doesUserExist( $username, $secret )
		{
			$result = User::where( [ 'username' => $username, 'secret_key' => $secret ] )->first();

			if ( $result )
				{
					return true;
				}
			else
				{
					return [ $this->finalResponse( 'error', 'Invalid / Unauthorized access!', 'Are you sure you are making correct request?' ) ];
				}
		}

	public function finalResponse( string $status = NULL, string $heading = NULL, string $message = NULL, array $data = NULL )
		{
			$status = ( empty ( $status ) ) ? 'error' : $status;
			$heading = ( empty( $heading ) ) ? 'Something went wrong!' : $heading;
			$message = ( empty( $message ) ) ? 'Please try again after few moments.' : $message;

			// the $data variable is used because let's say we want to  response some more data in future, instead of altering this functions again and again we just pass array to the data variable. In this way our functions is flexible to send any amount of response to an API call
			if ( ! empty( $data ) )
				{
					echo json_encode(
													[
														'status' => $status,
														'heading' => $heading,
														'message' => $message,
														'data' => $data
													]
												);
				}
			else
				{
					echo json_encode(
													[
														'status' => $status,
														'heading' => $heading,
														'message' => $message
													]
												);
				}

			exit();
		}
}
