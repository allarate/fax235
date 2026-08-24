<?php

namespace App\Controllers;

use App\Models\EleveModel;   // On importe le modèle EleveModel pour interagir avec la base de données
use CodeIgniter\Controller;  // On hérite des fonctionnalités de base du contrôleur de CodeIgniter

class Auth extends Controller
{   
    // Méthode qui gère l'inscription
    public function register()
    {
        helper(['form', 'url']); // Charge les helpers nécessaires pour gérer les formulaires et les URLs

        // Vérifie si le formulaire est soumis en POST
        if ($this->request->getMethod() == 'post') {

            // Définition des règles de validation du formulaire
            $rules = [
                'lastname'         => 'required|min_length[2]|max_length[50]',        // Nom obligatoire, entre 2 et 50 caractères
                'firstname'        => 'required|min_length[2]|max_length[50]',        // Prénom obligatoire, entre 2 et 50 caractères
                'email'            => 'required|valid_email|is_unique[eleve.email]',  // Email obligatoire, valide, et unique dans la table eleve
                'password'         => 'required|min_length[6]',                       // Mot de passe obligatoire, minimum 6 caractères
                'confirm_password' => 'required|matches[password]'                    // Confirmation obligatoire, doit correspondre au mot de passe
            ];

            // Si les données respectent les règles de validation
            if ($this->validate($rules)) {
                
                $model = new \App\Models\EleveModel(); // Crée une instance du modèle EleveModel

                // Préparation des données à insérer dans la base
                $data = [
                    'lastname'  => $this->request->getPost('lastname'), // Récupère le nom
                    'firstname' => $this->request->getPost('firstname'), // Récupère le prénom
                    'email'     => $this->request->getPost('email'),     // Récupère l'email
                    'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT) // Hash le mot de passe
                ];

                $model->save($data); // Enregistre les données dans la base

                // Crée un message flash temporaire de félicitations
                session()->setFlashdata('success', 'Félicitations ! Votre inscription est réussie. Veuillez vous connecter ci-dessous.');

                // Redirige vers la page de connexion
                return redirect()->to(base_url('auth/login'));

            } else {
                // Si validation échoue, on prépare les erreurs à afficher dans la vue
                $data['validation'] = $this->validator;
            }
        }

        // Affiche la vue 'register' avec les données (ou tableau vide si aucune)
        echo view('register', isset($data) ? $data : []);
    }

    // Méthode qui gère la connexion
    public function login()
    {
        helper(['form', 'url']); // Charge les helpers form et url

        // Si le formulaire est soumis en POST
        if ($this->request->getMethod() == 'post') {
            
            $model = new EleveModel(); // Instance du modèle EleveModel
            $email = $this->request->getPost('email');      // Récupère l'email soumis
            $password = $this->request->getPost('password'); // Récupère le mot de passe soumis

            // Recherche l'utilisateur correspondant à l'email
            $user = $model->where('email', $email)->first();

            // Si l'utilisateur existe et que le mot de passe est correct
            if ($user && password_verify($password, $user['password'])) {
                
                // Préparation des données à enregistrer dans la session
                $sessionData = [
                    'user_id'   => $user['id'],
                    'firstname' => $user['firstname'],
                    'lastname'  => $user['lastname'],
                    'email'     => $user['email'],
                    'logged_in' => true // Indique que l'utilisateur est connecté
                ];

                session()->set($sessionData); // Stocke les informations dans la session

                return redirect()->to('/dashboard'); // Redirige vers le tableau de bord

            } else {
                // Si authentification échoue, on prépare un message d'erreur
                $data['error'] = 'Email ou mot de passe incorrect';
            }
        }

        // Affiche la vue 'login' avec les erreurs si présentes
        echo view('login', isset($data) ? $data : []);
    }

    // Méthode pour se déconnecter
    public function logout()
    {
        session()->destroy(); // Détruit la session (déconnexion)
        return redirect()->to('auth/login'); // Redirige vers la page de connexion
    }
}
