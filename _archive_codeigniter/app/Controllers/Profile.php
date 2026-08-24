<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Profile extends Controller
{
    public function upload_photo()
    {
        $session = session();

        $file = $this->request->getFile('photo');

        if ($file && $file->isValid() && !$file->hasMoved()) {

            // Optionnel : valide le type et la taille du fichier
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return redirect()->back()->with('error', 'Format de fichier non supporté.');
            }

            // Renomme le fichier pour éviter conflit
            $newName = $file->getRandomName();

            // Déplace vers dossier uploads (crée le dossier si nécessaire)
            $uploadPath = WRITEPATH . 'uploads';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $file->move($uploadPath, $newName);

            // Met à jour la base de données utilisateur
            $userId = $session->get('user_id'); // adapte selon ta session
            $userModel = new \App\Models\UserModel();
            $userModel->update($userId, ['photo' => $newName]);

            // Met à jour la session pour refléter la nouvelle photo
            $session->set('photo', $newName);

            return redirect()->back()->with('success', 'Photo de profil mise à jour avec succès.');
        }

        return redirect()->back()->with('error', 'Erreur lors du téléchargement de la photo.');
    }
}
