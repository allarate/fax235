<?php
namespace App\Models;
use CodeIgniter\Model;

class SujetModel extends Model
{
    protected $table = 'sujets';
    protected $allowedFields = [
        'titre',
        'fichier',
        'type',
        'serie',
        'matiere',
        'annee',
        'statut',
        'eleve_id'
    ];
}
