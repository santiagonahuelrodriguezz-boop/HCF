<?php

namespace hcf\handler;

use hcf\handler\kit\KitManager;
use hcf\handler\kit\pay\KitManagerPay;
use hcf\handler\reclaim\ReclaimManager;
use hcf\handler\vkit\vKitManager;
use hcf\handler\kit\specials\KitManagerSpecials;

class HandlerManager {

    public KitManager $kitManager;
    public KitManagerPay $kitManagerPay;
    public KitManagerSpecials $kitManagerSpecials;

    public function __construct(){
        $this->kitManager = new KitManager;
        $this->kitManagerPay = new KitManagerPay;
        $this->kitManagerSpecials = new KitManagerSpecials;
    }

    public function getKitManager(): KitManager {
        return $this->kitManager;
    }
    
    public function getKitSpecialsManager(): KitManagerSpecials {
        return $this->kitManagerSpecials;
    }

    public function getKitPayManager(): KitManagerPay {
        return $this->kitManagerPay;
    }
}