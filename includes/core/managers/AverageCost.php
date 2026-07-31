<?php

declare(strict_types=1);

final class AverageCost
{
    public function calculate(float $currentQuantity, float $currentAverage, float $incomingQuantity, float $incomingCost): float
    {
        if ($incomingQuantity <= 0 || $incomingCost < 0 || $currentQuantity < 0 || $currentAverage < 0) {
            throw new InvalidArgumentException('Invalid average-cost values.');
        }
        $newQuantity = $currentQuantity + $incomingQuantity;
        return $newQuantity > 0 ? round((($currentQuantity * $currentAverage) + ($incomingQuantity * $incomingCost)) / $newQuantity, 4) : 0.0;
    }
}
