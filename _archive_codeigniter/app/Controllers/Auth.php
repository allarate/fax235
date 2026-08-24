<?php

namespace App\Controllers;

use App\Models\EleveModel;
use App\Models\SujetModel;
use App\Models\ChatModel;
use CodeIgniter\Controller;
use App\Models\AjoutModel;

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
                    'photo'     => null,
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
                session()->set([
                    'user_id'   => $user['id'],
                    'firstname' => $user['firstname'],
                    'lastname'  => $user['lastname'],
                    'email'     => $user['email'],
                    'photo'     => $user['photo'],
                    'logged_in' => true,
                    'role'      => $user['role'] ?? 'eleve' //
                ]);
                return redirect()->to('auth/index');
            } else {
                $data['error'] = 'Email ou mot de passe incorrect';
            }
        }
        return view('login', isset($data) ? $data : []);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login');
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
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return redirect()->back()->with('error', 'Format d\'image non autorisé.');
            }
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads', $newName);

            $model = new EleveModel();
            $model->update($userId, ['photo' => $newName]);
            $session->set('photo', $newName);

            return redirect()->back()->with('success', 'Photo mise à jour avec succès.');
        }
        return redirect()->back()->with('error', 'Aucun fichier sélectionné ou erreur.');
    }

    // PAGE BAC AVEC LISTE DES SUJETS + CHAT
        public function bac()
{$sujetModel = new \App\Models\SujetModel();
    $chatModel = new \App\Models\ChatModel();

    $data['sujets'] = $sujetModel->orderBy('id', 'DESC')->findAll();
    $data['messages'] = $chatModel->orderBy('id', 'DESC')->findAll();

    return view('bac', $data);
}


    // DEPOT D'UN SUJET PAR UN ETUDIANT
  public function depot()
{
    helper(['form', 'url']);
    
    // Vérifie que l'utilisateur est connecté
    if (!session()->get('logged_in')) {
        return redirect()->to('auth/login');
    }

    if ($this->request->getPost()) {
        // Règles de validation
        $validationRules = [
            'titre'   => 'required|min_length[3]|max_length[255]',
            'serie'   => 'required|in_list[A,C,D]',
            'matiere' => 'required',
            'type'    => 'required|in_list[sujet,corrige]',
            'annee'   => 'required|numeric|greater_than_equal_to[2000]|less_than_equal_to[2099]',
            'fichier' => 'uploaded[fichier]|max_size[fichier,10240]|ext_in[fichier,pdf,jpg,png]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('fichier');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Renomme et déplace le fichier
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads', $newName);

            // Prépare les données à insérer
            $data = [
                'titre'     => $this->request->getPost('titre'),
                'serie'     => $this->request->getPost('serie'),
                'matiere'   => $this->request->getPost('matiere'),
                'type'      => $this->request->getPost('type'),
                'annee'     => $this->request->getPost('annee'),
                'fichier'   => $newName,
                'statut'    => 'attente',
                'eleve_id'  => session()->get('user_id'),
            ];

            $sujetModel = new \App\Models\SujetModel();
            $sujetModel->save($data);

            return redirect()->back()->with('success', 'Document soumis avec succès. En attente de validation.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'upload du fichier.');
        }
    }

    return view('depot');
}




    // ENVOI D'UN MESSAGE DE DISCUSSION
public function envoyer_message()
{
    helper(['form', 'url']);
    $chatModel = new \App\Models\ChatModel();

    if ($this->request->getPOST()) {
        if (!session()->get('logged_in')) {
            return redirect()->to('auth/login');
        }

        $sujetId = $this->request->getPost('sujet_id');
        $message = trim($this->request->getPost('message'));
        $query   = $this->request->getPost('query') ?? '';

        if ($sujetId && $message !== '') {
            $chatModel->save([
                'auteur'   => session()->get('firstname'),
                'message'  => $message,
                'sujet_id' => $sujetId,
            ]);
        }

        // Redirection vers la page de recherche avec la même requête et ancre sujet
        return redirect()->to(site_url('auth/rechercher?q=' . urlencode($query) . '#sujet-' . $sujetId));
    }

    // Fallback : redirection vers la page consultation
    return redirect()->to('auth/page_consulter');
}




public function page_consultation_acceuil()
{
    // Sinon, afficher le formulaire de proposition
    return view('consultation');
    }

public function page_consulter()
{
    $sujetModel = new \App\Models\SujetModel();
    $chatModel = new \App\Models\ChatModel();

    $resultats = $sujetModel->orderBy('id', 'DESC')->findAll();

    // Ajouter les discussions à chaque sujet
    foreach ($resultats as &$sujet) {
        $sujet['messages'] = $chatModel->where('sujet_id', $sujet['id'])->orderBy('id', 'ASC')->findAll();
    }

    return view('consultation', [
        'resultats' => $resultats,
        'query'     => ''
    ]);
}


public function rechercher()
{
    $input = $this->request->getGet('q');
    $query = strtolower(trim($input));

    $sujetModel = new \App\Models\SujetModel();
    $chatModel = new \App\Models\ChatModel();
    $resultats = [];

    if (!empty($query)) {
        // 🔁 Synonymes à remplacer
        $synonymes = [
            'baccalauréat' => 'bac',
            'baccalaureat' => 'bac',
            'baccalareat' => 'bac',
            'bacc' => 'bac',
            'bac d' => 'D',
            'bac a' => 'A',
            'bac c' => 'C',
            'serie d' => 'D',
            'serie a' => 'A',
            'serie c' => 'C',
            'd' => 'D',
            'a' => 'A',
            'c' => 'C',
            'sujets bac' => 'bac',
            'sujet bac' => 'bac',
        ];

        // Remplacer les synonymes dans la requête
        foreach ($synonymes as $mot_cle => $remplacement) {
            $query = str_ireplace($mot_cle, $remplacement, $query);
        }

        // Nettoyage des caractères
        $query = preg_replace('/[^a-z0-9àâçéèêëîïôûùüÿñæœ ]/i', '', $query);
        $mots = explode(' ', $query);

        $mots_vides = ['je', 'veux', 'veut', 'chercher', 'cherche', 'donnez', 'moi',
            'le', 'la', 'les', 'de', 'du', 'des', 'un', 'une',
            'sujet', 'corrigé', 'corrige', 'document', 'fichier', 'trouver'];

        $builder = $sujetModel->builder();
        $builder->where('statut', 'valide');

        foreach ($mots as $mot) {
            $mot = trim($mot);
            if (strlen($mot) < 2 || in_array($mot, $mots_vides)) {
                continue;
            }

            $builder->groupStart()
                ->like('LOWER(titre)', $mot)
                ->orLike('LOWER(matiere)', $mot)
                ->orLike('LOWER(type)', $mot)
                ->orLike('LOWER(serie)', $mot)
                ->orLike('LOWER(annee)', $mot)
                ->groupEnd();
        }

        $resultats = $builder->get()->getResultArray();

        foreach ($resultats as &$sujet) {
            $sujet['messages'] = $chatModel->where('sujet_id', $sujet['id'])->orderBy('id', 'ASC')->findAll();
        }
    }

    return view('consultation', ['resultats' => $resultats, 'query' => $input]);
}

public function liste_sujets_admin()
{
    if (session()->get('role') !== 'admin') {
        return redirect()->to('auth/login');
    }

    $sujetModel = new \App\Models\SujetModel();
    $data['sujets'] = $sujetModel->orderBy('created_at', 'DESC')->findAll();

    return view('liste_sujets', $data);
}

public function valider_sujet($id)
{
    if (session()->get('role') !== 'admin') {
        return redirect()->to('auth/login');
    }

    $sujetModel = new \App\Models\SujetModel();
    $sujet = $sujetModel->find($id);

    if ($sujet) {
        $sujetModel->update($id, ['statut' => 'valide']);
    }

    return redirect()->to(site_url('auth/liste_sujets_admin'));
}

public function envoyer_message_ajax()
{
    $json = $this->request->getJSON(true); // true = array

    if (!session()->get('logged_in')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Utilisateur non connecté.'
        ]);
    }

    $sujetId = $json['sujet_id'] ?? null;
    $message = $json['message'] ?? '';

    if (!$sujetId || !$message) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Sujet ou message manquant.'
        ]);
    }

    $chatModel = new \App\Models\ChatModel();
    $chatModel->save([
        'auteur'    => session()->get('firstname'),
        'message'   => $message,
        'sujet_id'  => $sujetId
    ]);

    // 🔄 Récupérer les messages mis à jour
    $messages = $chatModel->where('sujet_id', $sujetId)->orderBy('id', 'ASC')->findAll();

    // Générer le HTML à retourner (juste la zone discussion)
    $html = '';
    foreach ($messages as $msg) {
        $html .= '<p><strong>' . esc($msg['auteur']) . ' :</strong> ' . esc($msg['message']) . '</p>';
    }

    return $this->response->setJSON([
        'success' => true,
        'html'    => $html
    ]);
}

public function orientation(){
    return view('orientation');
}


public function Ajouter_Filiere()
    {
        return view('ajouterfiliere');
    }
public function EnregistrerFiliere()
{
    helper(['form', 'url']);

    if ($this->request->getPOST()) {
        $rules = [
            'faculte'    => 'required|min_length[2]|max_length[50]',
            'nom'        => 'required|min_length[2]|max_length[50]',
            'universite' => 'required|min_length[2]|max_length[100]',
            'bac'        => 'required|min_length[2]|max_length[50]',
        ];

        if ($this->validate($rules)) {
            // Récupération des données
            $faculte    = $this->request->getPost('faculte');
            $nom        = $this->request->getPost('nom');
            $universite = $this->request->getPost('universite');
            $bac        = $this->request->getPost('bac');

            $model = new \App\Models\AjoutModel();

            // Vérifier si cette combinaison existe déjà
            $exists = $model->where([
                'faculte'    => $faculte,
                'nom'        => $nom,
                'universite' => $universite,
                'bac'        => $bac,
            ])->first();

            if ($exists) {
                // Déjà existante : on refuse
                session()->setFlashdata('error', '❌ Cette filière existe déjà avec cette faculté, université et ce bac.');
                return redirect()->back()->withInput();
            }

            // Nouvelle insertion
            $data = [
                'faculte'    => $faculte,
                'nom'        => $nom,
                'universite' => $universite,
                'bac'        => $bac,
            ];

            if ($model->save($data)) {
                session()->setFlashdata('success', '✅ Filière ajoutée avec succès.');
                return redirect()->to(site_url('auth/Ajouter_Filiere'));
            } else {
                session()->setFlashdata('error', '❌ Une erreur s’est produite lors de l’enregistrement.');
                return redirect()->back()->withInput();
            }
        } else {
            // Erreurs de validation
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }

    // Si accès direct sans POST
    return redirect()->to(site_url('auth/Ajouter_Filiere'));
}


public function rechercher_univ_moundou()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', 'Université de Moundou')->findAll();

        return view('moundou', ['filieres' => $filieres]);
    }

public function rechercher_univ_doba()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', 'Université de Doba')->findAll();

        return view('doba', ['filieres' => $filieres]);
    }
public function rechercher_univ_ndjamena()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', 'Université de Ndjamena')->findAll();

        return view('ndjamena', ['filieres' => $filieres]);
    }
public function rechercher_univ_sarh()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', 'Université de Sarh')->findAll();

        return view('sarh', ['filieres' => $filieres]);
    }
public function rechercher_univ_pala()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', 'Université de Pala')->findAll();

        return view('pala', ['filieres' => $filieres]);
    }
public function rechercher_univ_bongor()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', 'Université de Bongor')->findAll();

        return view('bongor', ['filieres' => $filieres]);
    }
public function rechercher_univ_mongo()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', 'Université de Mongo')->findAll();

        return view('mongo', ['filieres' => $filieres]);
    }
public function rechercher_univ_faya()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', 'Université de Faya')->findAll();

        return view('faya', ['filieres' => $filieres]);
    }
public function rechercher_univ_bol()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', 'Université de Bol')->findAll();

        return view('bol', ['filieres' => $filieres]);
    }

public function rechercher_univ_abeche()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', "Université d'Abeché")->findAll();

        return view('abeche', ['filieres' => $filieres]);
    }
public function rechercher_univ_ati()
    {
        $model = new AjoutModel();
        $filieres = $model->where('universite', "Université d'Ati")->findAll();

        return view('ati', ['filieres' => $filieres]);
    }



}
