<?php
namespace App\Models;
use CodeIgniter\Model;

class InscriptionModel extends Model{
    protected $table='etudiant';
    protected $primaryKey='id';
    protected $allowedFields=['nom','prenom','email','password'];
    protected $useTimestamps=false;


}