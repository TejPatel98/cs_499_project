<?php

namespace App\Http\Controllers;

use App\Exceptions\FSSNotFoundException;
use App\Exceptions\InvalidDateFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class stocks extends Controller
{
	public function submitSelection(Request $request)
	{
		// Setup variables
		$invAmount = $request->input('principle') * ($request->input('investmentPercent') * 0.01);
		$maxTrades = $request->input('maxTradesPerDay');
		$startDate = $request->input('startDate');
		$endDate = $request->input('endDate');
		$maxTradeLength = $request->input('maxTradeLength');
		$minTradeLength = $request->input('minTradeLength');		

		$dateResults = DB::connection('ovs')->select("SELECT * FROM `ovscalendar` where calType = '2' and date_ BETWEEN ? and ? order by date_ asc", [$startDate, $endDate]);

		// Format all the date results to YYYY-MM-DD ex 2000-01-01
		foreach($dateResults as $result)
		{
			$formattedDates[] = (new \DateTime($result->date_))->format('Y-m-d');
		}
		$balance = $request->input('principle');
		$data = array();
		foreach($formattedDates as $date)
		{

<<<<<<< HEAD
			$values = array(
				'balance' => intval($balance),
				'invAmount' => $balance * ($request->input('investmentPercent') * 0.01),
				'amountForEachStock' => $balance * ($request->input('investmentPercent') * 0.01) * pow($maxTrades, -1),
				'information' => array()
				// $this->fillOptionData($date, $amountPerStock, $maxTrades, $maxTradeLength, $minTradeLength, $balance, $startDate, $endDate)
			);
=======
		$balance = $request->input('principle');
		$data = array();
		foreach($formattedDates as $date)
		{
			$amountPerStock = $balance * ($request->input('investmentPercent') * 0.01) * pow($maxTrades, -1);
>>>>>>> 1144be9a0bb3ac017a034a51fa30af9298c5d49e

			$amountSpentOnThisDay = 0;

			// Get the scan results for the first day
			$stockResults = \FSSCLE::VolitilityHVIVDifference($date);

			// Get the options list for the first day
			$optionList = $this->getOptionsList($stockResults, $date, $amountPerStock, $maxTrades);
<<<<<<< HEAD
			
			// Choose the options near the strike price
			$chosenOptions = $this->optionSelect($optionList, $maxTradeLength, $minTradeLength, $balance);
			
			// Adds values for each day
			for ($i = 0; $i < count($chosenOptions); $i++)
			{
				$balance -= intval($chosenOptions[$i]->optAsk*100 * floor($amountPerStock * pow($chosenOptions[$i]->optAsk*100, -1)));
				$newOption = array(
					'optionId' => $chosenOptions[$i]->optId,
					'purchaseDate' => $date,
					'expDate' => $chosenOptions[$i]->expDate,
					'strike' => $chosenOptions[$i]->strike,
					'pricePerOption' => $chosenOptions[$i]->optAsk*100,
					'numberOfOptions' => floor($amountPerStock * pow($chosenOptions[$i]->optAsk*100, -1)),
					'amountSpent' => $chosenOptions[$i]->optAsk*100 * floor($amountPerStock * pow($chosenOptions[$i]->optAsk*100, -1)),
					'priceHistory' => $this->getPriceHistory($chosenOptions[$i], $date, $endDate)[$chosenOptions[$i]->optId]
				);
				$values['information'][] = $newOption;
			}

			// $amountSpentOnThisDay = 0;
			// foreach($values['information'] as $val)
			// {
			// 	echo $val;
			// }
			// $balance -= $amountSpentOnThisDay;

			$data[$date] = $values;
		}	

		$viewData = [
			'results' => $data,
		];

        return view('results/results', $viewData);
	}

    private function optionSelect($list, $maxLength, $minLength, $invAmt)
    {
        $theChosenOnes = array();
        $max_option_hold_days = 50;
		$amountPerStock = $invAmt * 0.2;
		
        for ($i =0; $i < count($list); $i++){
            $theChosenOne;
            $closest_to_max_option_hold_days = False;
            $correctOptionFound = False;
            $optionBuyPrice = -1.00;
            $finalOptionBuyPrice = -1;
            $daysToExpire = -1;
            $counter = 0;
            for ($counter = 0; $counter < count($list[$i]); $counter++){
                //echo $counter." ";    
                if ($counter == 0){
                    
        //            echo intval($list[$i][$counter]-> daysToExp)." ";
                    $theChosenOne = $list[$i][$counter];
                    $optionBuyPrice = floatval($list[$i][$counter]->optAsk)*100;
                    //dd(substr(explode("+\"strike\": \"", $list[$i][$counter])[1], 0, 5));
                }
                else{

//                    echo intval($list[$i][$counter]-> daysToExp)." ";


/*
*
* Ask Don if we want to remove the min length and also work with only a mxaimum than having both
*
*/

                    if (intval($list[$i][$counter]-> daysToExp) <= $maxLength and intval($list[$i][$counter]-> daysToExp) >= $minLength){
                        if ((floatval($list[$i][$counter]->optAsk)*100) <= $amountPerStock){
                                $theChosenOne = $list[$i][$counter]; 
                            }
                            else{
                                break;
                            }

                    }
                }   

                $counter += 1;

            }

            array_push($theChosenOnes, $theChosenOne);

        } 
=======

			// Choose the options near the strike price
			$chosenOptions = $this->optionSelect($optionList, $maxTradeLength, $minTradeLength, $amountPerStock);

			$values = array(
				'balance' => intval($balance),
				'invAmount' => $balance * ($request->input('investmentPercent') * 0.01),
				'amountForEachStock' => $amountPerStock,
				'information' => array()
			);

			foreach($chosenOptions as $key=> $value)
			{
				$balance -= intval($value->optAsk*100 * floor($amountPerStock * pow($value->optAsk*100, -1)));
				$newOption = array(
					'name' => $key,
					'optionId' => $value->optId,
					'purchaseDate' => $date,
					'expDate' => (new \DateTime($value->expDate))->format('Y-m-d'),
					'pricePerOption' => $value->optAsk*100,
					'numberOfOptions' => floor($amountPerStock * pow($value->optAsk*100, -1)),
					'amountSpent' => $value->optAsk*100 * floor($amountPerStock * pow($value->optAsk*100, -1)),
					'priceHistory' => $this->getPriceHistory($value, $date, $endDate)[$value->optId]
				);
				$values['information'][] = $newOption;
			}


			$amountSpentOnThisDay = 0;
			$balance -= $amountSpentOnThisDay;

			$data[$date] = $values;

		}	

		$portfolioValue = $this->getPortfolioValue($data);

		foreach($formattedDates as $date)
		{
			$valForToday = $this->getSpecificDayValue($portfolioValue, $date);
			$data[$date]["portfolioValue"] = $valForToday;
		}

		$viewData = [
			'results' => $data,
		];

		return view('results/results', $viewData);
	}


	/*
	 *
	 * Ask Don if we want to remove the min length and also work with only a mxaimum than having both
	 * We either need to remove the minimum trade length or the maximum trade length
	 * portfolio price based on Ask or Bid 
	 * need to add the sell functionality and hten compute portoflio value 
	 *
	 */
	private function getPortfolioValue ($data)
	{
		$chosenOptionsThroughout = array();
		foreach($data as $key=> $value)
		{
			foreach($value['information'] as $temp)
			{
				$chosenOptionsThroughout[] = $temp;
			}
		}
		return $chosenOptionsThroughout;
	}

	private function getSpecificDayValue($portfolioVal, $date)
	{
		$portfolioValueForToday = 0;
		foreach ($portfolioVal as $optionVals)
		{
			if($optionVals['expDate'] > $date)
				foreach($optionVals['priceHistory'] as $priceHistoryVar)
				{
					$optPriceHistoryDate = (new \DateTime($priceHistoryVar->date_))->format('Y-m-d');
					if ($optPriceHistoryDate == $date)
						$portfolioValueForToday += floatval($priceHistoryVar->bid*100*$optionVals['numberOfOptions']);
				}

		}
		return $portfolioValueForToday;
	}



	private function optionSelect($list, $maxLength, $minLength, $amtForEachStock)
	{
		$theChosenOnes = array();

		foreach($list as $key=> $val)
		{
			$theChosenOne;
			$closest_to_max_option_hold_days = False;
			$correctOptionFound = False;
			$optionBuyPrice = -1.00;
			$finalOptionBuyPrice = -1;
			$daysToExpire = -1;
			$counter = 0;

			for ($counter = 0; $counter < count($val); $counter++)
			{
				if ($counter == 0){

					$theChosenOne = $val[$counter];
					$optionBuyPrice = floatval($val[$counter]->optAsk)*100;
				}
				else{
					if (intval($val[$counter]-> daysToExp) <= $maxLength and intval($val[$counter]-> daysToExp) >= $minLength){
						if ((floatval($val[$counter]->optAsk)*100) <= $amtForEachStock){
							$theChosenOne = $val[$counter]; 
						}
						else{
							break;
						}

					}
				} 
				$counter++;
			}
			$theChosenOnes[$key] = $theChosenOne;

		}
		return $theChosenOnes;
>>>>>>> 1144be9a0bb3ac017a034a51fa30af9298c5d49e

	}

	// This takes in a list of options and gets the price history
	// from a start date to an end date.
	private function getPriceHistory($option, $startDate, $endDate)
	{
<<<<<<< HEAD
		// foreach($chosenOptions as $option)
		// {
			$arguments = [
				'optId' => $option->optId,
				'startDate' => $startDate,
				'endDate' => $endDate,
			];
=======
		$arguments = [
			'optId' => $option->optId,
			'startDate' => $startDate,
			'endDate' => $endDate,
		];
>>>>>>> 1144be9a0bb3ac017a034a51fa30af9298c5d49e

		$query = "
				SELECT *
				FROM `optprice`
				WHERE optId=:optId and date_ BETWEEN :startDate and :endDate
			";

		$result = DB::connection('ovs')->select($query, $arguments);

		$priceHistory[$option->optId] = $result;

<<<<<<< HEAD
			$priceHistory[$option->optId] = $result;
		// }
		
=======
>>>>>>> 1144be9a0bb3ac017a034a51fa30af9298c5d49e

		return $priceHistory;
	}

	private function getOptionsList($stocks, $date, $amountPerStock, $maxTrade)
	{
		$optionsFound = 0;
		$counter = 0;
<<<<<<< HEAD

=======
		$callResults = array();
>>>>>>> 1144be9a0bb3ac017a034a51fa30af9298c5d49e
		while($optionsFound < $maxTrade && $counter < count($stocks)) 
		{
			// You cannot use named bindings multiple times in the same query.
			// There must be an individual named binding for each spot.
			if($stocks[$counter]["price"] < $amountPerStock)
			{
				$arguments = [
					'date1' => $date,
					'date2' => $date,
					'date3' => $date,
					'days_to_exp' => 5,
					'eq_id' => $stocks[$counter]['id'],
					'p_c' => 'C',
				];

				$query = '
					SELECT 
					oc.eqId,
					DATEDIFF(oc.expDate, :date1) AS daysToExp, 
					DATEDIFF(oc.expDate, oc.startDate) AS optLength,
					oc.optId,
					oc.expDate,
					op.date_,
					oc.putCall,
					oc.strike,
					op.ask AS optAsk,
					op.bid AS optBid,
					eqp.ask,
					eqp.bid,
					ivl.ivAsk,
					ivl.ivBid
					FROM optcontract AS oc
					INNER JOIN optprice AS op ON op.optId=oc.optId AND op.ask > 0 AND op.bid > 0 AND op.date_=:date2
					INNER JOIN eqprice AS eqp ON eqp.eqId = oc.eqId AND eqp.date_ = op.date_
					INNER JOIN ivlisted AS ivl ON ivl.optId = oc.optId AND ivl.date_ = op.date_
					WHERE DATEDIFF(oc.expDate, :date3) >= :days_to_exp AND oc.eqId=:eq_id AND oc.putCall=:p_c AND oc.strike > eqp.ask

					ORDER by oc.strike ASC, oc.expDate ASC
				';

				$callResult = DB::connection('ovs')->select($query, $arguments);

				$callResults[$stocks[$counter]["name"]] = $callResult;
				$optionsFound++;
			}	

			$counter++;
		}

		return $callResults;
	}

	// private function updatedInvAmount($optionData, $investment)
	// {
	// 	foreach($optionData as $opt)
	// 	{
	// 		$investment -= $opt->optAsk*100
	// 	}
	// }
}

<<<<<<< HEAD
// class optionDetails {
// 	public $name;
// 	public $optionId;
// 	public $purchaseDate;
// 	public $expDate;
// 	public $symbol;
// 	public $pricePerOption;
// 	public $numberOfOptions;
// 	public $amountSpent;
// 	public $priceHistory;
// }
=======
>>>>>>> 1144be9a0bb3ac017a034a51fa30af9298c5d49e
