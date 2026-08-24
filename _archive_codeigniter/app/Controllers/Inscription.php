<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\InscriptionModel;

class Inscription extends Controller {

    public function afficher_inscription() {
        return view('inscription');
    }

    public function inscriptionstudent() {
        helper(['form', 'url']);
        if ($this->request->getPost()) {
            $rules = [
                'nom'              => 'required|min_length[2]|max_length[50]',
                'prenom'           => 'required|min_length[2]|max_length[50]',
                'email' => 'required|valid_email|is_unique[etudiant.email]',
                'password'         => 'required|min_length[1]',
                'confirm_password' => 'required|matches[password]',
            ];

            if ($this->validate($rules)) {
                $model = new InscriptionModel();
                $data = [
                    'nom'     => $this->request->getPost('nom'),
                    'prenom'  => $this->request->getPost('prenom'),
                    'email'   => $this->request->getPost('email'),
                    'password'=> password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                ];
                $model->save($data);
                echo 'Inscription réussie';
            } else {
                echo 'error de validation';
            }
        } else {
            echo 'papa';
        }
    }

 public function afficher_inscription_page() {
        return view('Afficher_inscription');
    }

public function afficherInscriptionConsulation() {
    helper(['form', 'url']);

    if ($this->request->getPOST()) {
        $nom = $this->request->getPost('nom');
        $email = $this->request->getPost('email');

        $model = new InscriptionModel();

        $etudiant = $model->where('nom', $nom)->where('email', $email)->first();

        if ($etudiant) {
            return view('Afficher_inscription', ['etudiant' => $etudiant]);
        } else {
            return view('Afficher_inscription', ['message' => 'Aucun étudiant trouvé.']);
        }
    }

    return view('Afficher_inscription');
}




}
