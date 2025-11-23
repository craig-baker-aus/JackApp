<?php
class Date extends DateTimeImmutable {
    /*  A Date class that only stores date information (no time).
        It extends DateTimeImmutable but always sets the time to 00:00.
    */
            
    public function __construct($dateTime = null, $timezone = null) {
    $timeString = "00:00";
    parent::__construct("$dateTime $timeString", $timezone);
    }
    public function __toString() {
        return $this->format("Y-m-d");
    }

    public static function createFromInterface($dateTime = null): Date {
        return new Date($dateTime->format("Y-m-d"));
    }
    public function modify(string $modifier): Date { 
        return $this->createFromInterface(parent::modify($modifier));
    }
}
