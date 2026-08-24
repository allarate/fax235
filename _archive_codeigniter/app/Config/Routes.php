<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');

$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register', 'Auth::register');

$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');

$routes->get('auth/logout', 'Auth::logout');

$routes->get('auth/index', 'Auth::index');

$routes->get('auth/bac', 'Auth::bac');
$routes->post('auth/bac', 'Auth::bac');


$routes->get('auth/depot', 'Auth::depot');
$routes->post('auth/depot', 'Auth::depot');

$routes->get('auth/proposer_sujet', 'Auth::proposer_sujet');
$routes->post('auth/proposer_sujet', 'Auth::proposer_sujet');

$routes->get('auth/envoyer_message', 'Auth::envoyer_message');
$routes->post('auth/envoyer_message', 'Auth::envoyer_message');

$routes->post('profile/upload_photo', 'Profile::upload_photo');

$routes->get('auth/profile_photo', 'Auth::profile_photo');
$routes->get('auth/upload_photo_form', 'Auth::upload_photo_form');
$routes->post('auth/upload_photo', 'Auth::upload_photo');

$routes->get('auth/page_consulter', 'Auth::page_consulter');
$routes->post('auth/page_consulter', 'Auth::page_consulter');

$routes->get('auth/rechercher', 'Auth::rechercher');
$routes->post('auth/rechercher', 'Auth::rechercher');

$routes->get('auth/page_consultation_acceuil', 'Auth::page_consultation_acceuil');
$routes->post('auth/page_consultation_acceuil', 'Auth::page_consultation_acceuil');


$routes->get('auth/liste_sujets_admin', 'Auth::liste_sujets_admin');
$routes->post('auth/liste_sujets_admin', 'Auth::liste_sujets_admin');

$routes->get('auth/valider_sujet', 'Auth::valider_sujet');
$routes->post('auth/valider_sujet', 'Auth::valider_sujet');
$routes->post('auth/valider_sujet/(:num)', 'Auth::valider_sujet/$1');


$routes->get('auth/envoyer_message_ajax', 'Auth::envoyer_message_ajax');
$routes->post('auth/envoyer_message_ajax', 'Auth::envoyer_message_ajax');


$routes->get('auth/orientation', 'Auth::orientation');
$routes->post('auth/orientation', 'Auth::orientation');


$routes->get('auth/Ajouter_Filiere', 'Auth::Ajouter_Filiere');
$routes->post('auth/Ajouter_Filiere', 'Auth::Ajouter_Filiere');

$routes->get('auth/EnregistrerFiliere', 'Auth::EnregistrerFiliere');
$routes->post('auth/EnregistrerFiliere', 'Auth::EnregistrerFiliere');

$routes->get('auth/rechercher_univ_moundou', 'Auth::rechercher_univ_moundou');
$routes->post('auth/rechercher_univ_moundou', 'Auth::rechercher_univ_moundou');

$routes->get('auth/rechercher_univ_doba', 'Auth::rechercher_univ_doba');
$routes->post('auth/rechercher_univ_doba', 'Auth::rechercher_univ_doba');

$routes->get('auth/rechercher_univ_sarh', 'Auth::rechercher_univ_sarh');
$routes->post('auth/rechercher_univ_sarh', 'Auth::rechercher_univ_sarh');

$routes->get('auth/rechercher_univ_pala', 'Auth::rechercher_univ_pala');
$routes->post('auth/rechercher_univ_pala', 'Auth::rechercher_univ_pala');

$routes->get('auth/rechercher_univ_bongor', 'Auth::rechercher_univ_bongor');
$routes->post('auth/rechercher_univ_bongor', 'Auth::rechercher_univ_bongor');

$routes->get('auth/rechercher_univ_abeche', 'Auth::rechercher_univ_abeche');
$routes->post('auth/rechercher_univ_abeche', 'Auth::rechercher_univ_abeche');

$routes->get('auth/rechercher_univ_faya', 'Auth::rechercher_univ_faya');
$routes->post('auth/rechercher_univ_faya', 'Auth::rechercher_univ_faya');

$routes->get('auth/rechercher_univ_bol', 'Auth::rechercher_univ_bol');
$routes->post('auth/rechercher_univ_bol', 'Auth::rechercher_univ_bol');

$routes->get('auth/rechercher_univ_mongo', 'Auth::rechercher_univ_mongo');
$routes->post('auth/rechercher_univ_mongo', 'Auth::rechercher_univ_mongo');

$routes->get('auth/rechercher_univ_ati', 'Auth::rechercher_univ_ati');
$routes->post('auth/rechercher_univ_ati', 'Auth::rechercher_univ_ati');

$routes->get('auth/rechercher_univ_ndjamena', 'Auth::rechercher_univ_ndjamena');
$routes->post('auth/rechercher_univ_ndjamena', 'Auth::rechercher_univ_ndjamena');







$routes->get('inscription/afficher_inscription', 'Inscription::afficher_inscription');
$routes->post('inscription/afficher_inscription', 'Inscription::afficher_inscription');


$routes->get('inscription/inscriptionstudent', 'Inscription::inscriptionstudent');
$routes->post('inscription/inscriptionstudent', 'Inscription::inscriptionstudent');

$routes->get('inscription/afficher_inscription_page', 'Inscription::afficher_inscription_page');
$routes->post('inscription/afficher_inscription_page', 'Inscription::afficher_inscription_page');

$routes->get('inscription/afficherInscriptionConsulation', 'Inscription::afficherInscriptionConsulation');
$routes->post('inscription/afficherInscriptionConsulation', 'Inscription::afficherInscriptionConsulation');


