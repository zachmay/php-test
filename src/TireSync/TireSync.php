<?php

namespace Uparts\TireSync;

interface TireSync
{
    public function fetchAvailableYears();
    public function fetchAvailableMakes($year);
    public function fetchAvailableModels($year, $make);
    public function fetchAvailableOptions($year, $make, $model);
    public function fetchVehicleFitments($year, $make, $model, $option);
    public function fetchStandardFitment($year, $make, $model, $option);
}
