<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loan;
use App\Payment;
use App\PaymentMeta;

class loansController extends Controller
	{
		private function checkLoanExistence( $id )
			{
				if ( ! empty( $id ) )
					{
						$existence_result = Loan::find( $id );

						if ( $existence_result )
							{
								return $existence_result ;
							}
						else
							{
								$this->finalResponse( 'error', 'Unable to find loan records!', 'Are you sure you have selected correct loan for Enquiry?' );
							}
					}
				else
					{
						$this->finalResponse();
					}
			}

		private function calculateEMIAmount( $amount, $tenor )
			{
				if ( ! empty( $amount ) && ! empty( $tenor ) )
					{
						return ceil( $amount / $tenor );
					}
				else
					{
						$this->finalResponse();
					}
			}

		function applyforLoan( Request $request )
			{
				if ( ! $this->doesUserExist( $request->username, $request->secret ) )
					{
						return;
					}

				$loans = new Loan();

				// To store data in DB Syntax ( $modal_object->db_columns_name = $request->field_name )
				$loans->status = 0;
				$loans->user_id = 1;
				$loans->loan_amt = $request->amount;
				$loans->loan_tenor = $request->tenor;

				try
					{
						$result = $loans->save();

						if ( $result )
							{
								return [ $this->finalResponse( 'success', 'Loan applicaton successful!', 'Your loan has been sent for approval, you will receive a confirmation post approval.' ) ];
							}
						else
							{
								return [ $this->finalResponse( 'error', 'Unable to apply for loan!', '' ) ];
							}
					}
				catch ( Exception $e )
					{
						$this->finalResponse();
					}
			}

		function approveLoan( Request $request )
			{
				if ( $this->doesUserExist( $request->username, $request->secret ) )
					{
						$response_result = $this->checkLoanExistence( $request->loan_id );

						$paymentsMeta = new PaymentMeta();

						$paymentsMeta->status = 1;
						$paymentsMeta->loan_id = $request->loan_id;
						$paymentsMeta->repayment_type_id = $request->type;
						$paymentsMeta->last_repayment_date = NULL;
						$paymentsMeta->next_repayment_date = date( 'Y-m-d H:i:s', strtotime( '+7 day' ) );
						$paymentsMeta->installment_amount = $this->calculateEMIAmount( $response_result->loan_amt, $response_result->loan_tenor );
						$paymentsMeta->pending_amount = $response_result->loan_amt;

						$result = $paymentsMeta->save();

						if ( $result )
							{
								$loan_result = Loan::where( [ 'id' => $request->loan_id ] )->update( [ 'status' => 1 ] );

								if ( $loan_result )
									{
										return [ $this->finalResponse( 'success', 'Loan applicaton approved!', 'User can start paying EMI now. You can set an email ( Using mailgun or any other ) / SMS notification ( If DLT Registration done ) to inform the user for further process.' ) ];
									}
								else
									{
										$this->finalResponse();
									}
							}
						else
							{
								return [ $this->finalResponse( 'error', 'Loan approval unsuccessful!', '' ) ];
							}
					}
			}

		function payLoanEMI( Request $request )
			{
				if ( $this->doesUserExist( $request->username, $request->secret ) )
					{
						$meta_result = PaymentMeta::find( $request->meta_id );

						if ( $meta_result )
							{
								if ( $meta_result->pending_amount <= 0 )
									{
										$this->finalResponse( 'success', 'EMIs Completed!', 'All EMIs were paid were you, no EMIs pending' );
									}

								if ( $request->amount < $meta_result->installment_amount )
									{
										$this->finalResponse( 'error', 'EMI amount mismatched!', 'You have to pay min ' . $meta_result->installment_amount . ' every EMI cycle.' );
									}

								if ( $request->amount > $meta_result->installment_amount )
									{
										$this->finalResponse( 'error', 'EMI amount mismatched!', 'You can only pay max ' . $meta_result->installment_amount . ' every EMI cycle.' );
									}

								// If you want you can put a date condition to make sure that evey Week only 1 EMI is paid, you may put a condition below
								// if ( $meta_result->next_repayment_date < $current_date )
								// 	{

								// 	}

								$payment = new Payment();

								$payment->payment_status = 1;
								$payment->payment_meta_id = $request->meta_id;
								$payment->amount_received = $request->amount;

								$result = $payment->save();

								if ( $result )
									{
										$pending_amount = ( $meta_result->pending_amount - $meta_result->installment_amount );
										$prev_payment_date = date( 'Y-m-d H:i:s', strtotime( '+7 day' ) );
										$next_payment_date = date( 'Y-m-d H:i:s', strtotime( '+7 day', strtotime( $prev_payment_date ) ) );

										$meta_update_result = PaymentMeta::where( [ 'id' => $request->meta_id ] )->update( [ 'status' => 1, 'last_repayment_date' => $prev_payment_date, 'next_repayment_date' => $next_payment_date, 'pending_amount' => $pending_amount ] );

										if ( $meta_update_result )
											{
												return [ $this->finalResponse( 'success', 'EMI Paid successfully!', 'Next EMI Payment on or before ' . $next_payment_date ) ];
											}
										else
											{
												$this->finalResponse();
											}
									}
								else
									{
										return [ $this->finalResponse( 'error', 'Unable to Pay EMI!', '' ) ];
									}
							}
						else
							{
								$this->finalResponse( 'error', 'Unable to load payment infromation!', 'Are you sure, you are making a correct request?' );
							}
					}
			}
	}
