<?php
namespace App\Models;
use CodeIgniter\Model;

class SujetModel extends Model
{
    protected $table = 'sujets';
    protected $allowedFields = [
        'titre',
        'fichier_sujet', 'auteur_sujet', 'statut_sujet',
        'fichier_corrige', 'auteur_corrige', 'statut_corrige'
    ];
    public function search($searchTerm)
    {
        $this->like('column_to_search', $searchTerm); // Remplacez par la colonne de recherche
        return $this->findAll();
    }
}
