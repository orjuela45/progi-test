<?php

/**
 * Miguel Angel Orjuela Riaño
 * This is an algorithm to calculate which is the maximum value to push for a car auction after fees according to one budget.
 * Note: This is the value of the budget, you can put different budgest as an array or only a number. for example:
 *      $budgetValues = [1000.00, 670.00, 670.01, 110.00, 111.00, 116.02, 1000000.00];
 */

$budgetValues = 1500;

// Constant variables of fees
$basicFeePercentage  = 0.1;
$maxBasicFee         = 50;
$minBasicFee         = 10;
$sellerFeePercentage = 0.02;
$storageFeeValue     = 100;

foreach ((array)$budgetValues as $budgetValue) {
  
  // If the budget is lower than the minium push with fees, the program ends
  $minValueForBucket = $storageFeeValue + $minBasicFee;
  if ($budgetValue < $minValueForBucket){
    print_r("The bucket must be greater or equal to {$minValueForBucket}");
    die();
  }
 
  // Variable to loop
  $previousVehicleAmount  = 0;
  $jumps                  = floor($budgetValue/$minValueForBucket);
  $currentVehicleAmount   = $budgetValue/$jumps;
  $incrementVehicleAmount = $minValueForBucket*$jumps;
  $canFinishLoop          = false;
  $resultTotalCost        = [];

  do {
    $resultTotalCost = calculateTotalCost($currentVehicleAmount);
    if ($resultTotalCost["total_cost"] > $budgetValue){
      $differenceCostVsButget = ($resultTotalCost["total_cost"] - $budgetValue) * 100;
      if ($differenceCostVsButget > 0.01){
        $incrementVehicleAmount = $incrementVehicleAmount/2;
        $currentVehicleAmount= $previousVehicleAmount;
      } else {
        $currentVehicleAmount = round($currentVehicleAmount,2);
        $resultTotalCost = calculateTotalCost($currentVehicleAmount);
        $canFinishLoop = true; 
      }
    } else {
      $previousVehicleAmount = $currentVehicleAmount;
      $currentVehicleAmount += $incrementVehicleAmount;
    }

  } while (!$canFinishLoop);

  echo "-----------------------------------------\n";
  echo "Budget: ".                  round($budgetValue,2) ."\n";
  echo "Maximum vehicle amount: ".  round($currentVehicleAmount,2) ."\n";
  echo "Basic Fee: ".               round($resultTotalCost['basic_fee'],2)."\n";
  echo "Special Fee: ".             round($resultTotalCost['special_fee'],2)."\n";
  echo "Association Fee: ".         round($resultTotalCost['association_fee'],2)."\n";
  echo "Storage Fee: ".             round($resultTotalCost['storage_fee_value'],2)."\n";
  echo "-----------------------------------------\n";
}

/**
 * This function calculate the fees according to an amount and return all the fees with the sum of these 
 */
function calculateTotalCost($amount = 0){

  global $basicFeePercentage, $maxBasicFee, $minBasicFee, $sellerFeePercentage, $storageFeeValue;

  $basicFee   = max($minBasicFee, min($amount * $basicFeePercentage, $maxBasicFee));
  $specialFee = $amount * $sellerFeePercentage;
  switch (true) {
    case 1 <= $amount && $amount <= 500:
      $associationFee = 5;
      break;
    case 500 < $amount && $amount <= 1000:
      $associationFee = 10;
      break;
    case 1000 < $amount && $amount <= 3000:
      $associationFee = 15;
      break;
    case 3000 < $amount:
      $associationFee = 20;
      break;
    default:
      $associationFee = 0;
      break;
  }
  
  return [
    "total_cost" => $amount + $basicFee + $storageFeeValue + $specialFee + $associationFee,
    "basic_fee" => $amount == 0 ? 0 : $basicFee,
    "special_fee" => $amount == 0 ? 0 : $specialFee,
    "association_fee" => $amount == 0 ? 0 : $associationFee,
    "storage_fee_value" => $amount == 0 ? 0 : $storageFeeValue,
  ];
}