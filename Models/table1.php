<?php

namespace App\Models;

class table1 extends Model{

    public function MIGRATE() //миграции 
    {

        $this->TABLE = [
        'id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'name' => 'char(255) NOT NULL',
        'text' => 'text(1080) NOT NULL',
        'PRIMARY KEY'=>'id',
        'CHARSET'=>'utf8'];
    }


}





