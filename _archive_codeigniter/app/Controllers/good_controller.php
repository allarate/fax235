<?php

namespace App\Controllers;

use App\Models\EleveModel;
use CodeIgniter\Controller;

class Auth extends Controller
{

     public function index(): string
    {
        return view('welcome_message');
    }

    public function register()
    {
        helper(['form', 'url']);

        if ($this->request->getPost()) {
            $rules = [
                'lastname'         => 'required|min_length[2]|max_length[50]',
                'firstname'        => 'required|min_length[2]|max_length[50]',
                'email'            => 'required|valid_email|is_unique[eleve.email]',
                'password'         => 'required|min_length[6]',
                'confirm_password' => 'required|matches[password]',
            ];

            if ($this->validate($rules)) {
                $model = new EleveModel();
                $data = [
                    'lastname'  => $this->request->getPost('lastname'),
                    'firstname' => $this->request->getPost('firstname'),
                    'email'     => $this->request->getPost('email'),
                    'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'photo'     => null, // photo par défaut sera utilisée si null
                ];

                if ($model->save($data)) {
                    return redirect()->to(site_url('auth/login'));
                } else {
                    session()->setFlashdata('error', 'Erreur lors de l\'inscription.');
                }
            } else {
                $data['validation'] = $this->validator;
            }
        }

        return view('register', isset($data) ? $data : []);
    }

    public function login()
    {
        helper(['form', 'url']);
        if ($this->request->getPost()) {
            $model = new EleveModel();
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $user = $model->where('email', $email)->first();

            if ($user && password_verify($password, $user['password'])) {
                $sessionData = [
                    'user_id'   => $user['id'],
                    'firstname' => $user['firstname'],
                    'lastname'  => $user['lastname'],
                    'email'     => $user['email'],
                    'photo'     => $user['photo'], // nom fichier photo en session
                    'logged_in' => true,
                ];

                session()->set($sessionData);
                return redirect()->to('auth/index');
            } else {
                $data['error'] = 'Email ou mot de passe incorrect';
            }
        }

        return view('login');
    }

    public function logout()
    {
        return redirect()->to('auth/login');
        session()->destroy();
    }

    public function upload_photo()
{
    helper(['form', 'url']);
    $session = session();
    $userId = $session->get('user_id');

    if (!$userId) {
        return redirect()->to('auth/login');
    }

    $file = $this->request->getFile('photo');

    if ($file && $file->isValid() && !$file->hasMoved()) {
        // Sécuriser le type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return redirect()->back()->with('error', 'Format d\'image non autorisé.');
        }

        $newName = $file->getRandomName();
        $file->move(ROOTPATH . 'public/uploads', $newName);

        $model = new EleveModel();
        $model->update($userId, ['photo' => $newName]);

        // Mise à jour de la session
        $session->set('photo', $newName);

        return redirect()->back()->with('success', 'Photo mise à jour avec succès.');
    } else {
        return redirect()->back()->with('error', 'Aucun fichier sélectionné ou erreur.');
    }
}
    // Bac controller
    public function bac(){
            return view('bac');
        }
    

}
