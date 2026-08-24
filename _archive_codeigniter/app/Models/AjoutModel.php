<?php

namespace App\Models;

use CodeIgniter\Model;

class AjoutModel extends Model
{
    protected $table = 'filiere';
    protected $primaryKey = 'id';
    protected $allowedFields = ['faculte','nom', 'universite', 'bac'];
    protected $useTimestamps = false;
}

