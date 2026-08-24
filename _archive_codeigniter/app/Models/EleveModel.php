<?php

namespace App\Models;

use CodeIgniter\Model;

class EleveModel extends Model
{
    protected $table = 'eleve';
    protected $primaryKey = 'id';
    protected $allowedFields = ['lastname', 'firstname', 'email', 'password', 'photo', 'role'];
    protected $useTimestamps = false;
}
