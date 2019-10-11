<?php

namespace Singleton;

class Database extends Singleton {

    protected $label;


    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }
}


$database = Database::instance();
$database->setLabel('Angel');
echo $database->getLabel();

$other_db = Database::instance();
echo $other_db->getLabel() . PHP_EOL;

$other_db->setLabel('Emil');
echo $database->getLabel() . PHP_EOL;
echo $other_db->getLabel() . PHP_EOL;