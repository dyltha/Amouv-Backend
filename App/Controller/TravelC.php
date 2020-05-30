<?php

/**
 *  @title : TravelC.php
 *  
 *  @author : Théo MOUMDJIAN
 *  @author : Guillaume RISCH
 *  @refractor : Matthis HOULES
 *  
 *  @brief : travel pages controller
 */

// imports
require_once(__DIR__.'/../Model/User.php');
require_once(__DIR__.'/../Model/Travel.php');
require_once(__DIR__.'/../../Core/PopUp.php');
session_start();


class TravelC {



    /**
     * 
     *  @name : __construct
     * 
     *  @param void
     * 
     *  @return void
     * 
     *  @brief : middleware TravelC (check if user is connected)
     */
    function __construct() {
        if (!isset($_SESSION['user'])) {

            $_SESSION['popup'] = new PopUp('error', 'vous devez être connecté pour pouvoir accéder à cette page');
            header('location: /AMOUV/connexion');
        }

    } // function __construct()



    /**
     * 
     *  @name : createTravel
     * 
     *  @param void
     *  
     *  @return void
     * 
     *  @brief : create Travel page
     * 
     */
    public function createTravel() {

        if (isset($_POST['submit'])) {

            // Cities of departure & arrival
            if (empty($_POST['cityStart']) || empty($_POST['cityEnd'])) {
                $_SESSION['popup'] = new PopUp('error', 'Vous devez renseigner une ville de départ et une ville d\'arrivée');
                header('location: /amouv/voyage/creation');
                exit();
            }

            // Day & time of departure
            if (empty($_POST['dayDeparture']) || empty($_POST['timeDeparture'])) {
                $_SESSION['popup'] = new PopUp('error', 'Le jour et l\'heure de départ ');
                header('location: /amouv/voyage/creation');
                exit();
            }

            $dateDeparture = strtotime($_POST['dayDeparture'] . $_POST['timeDeparture']);

            if ($dateDeparture <= time()) {
                $_SESSION['popup'] = new PopUp('error', 'Votre départ ne peut pas être dans le passé');
                header('location: /amouv/voyage/creation');
                exit();
            }


            // Car
            $carChoose = Car::carBelongUser($_SESSION['user']->getId(), $_POST['car']);
            if (empty($_POST['car']) || !$carChoose) {
                $_SESSION['popup'] =  new PopUp('error', 'Vous devez choisir une voiture valide');
                header('location: /amouv/voyage/creation');
                exit();
            }


            if (empty($_POST['nbseat']) || !ctype_digit($_POST['nbseat']) || $_POST['nbseat'] >= $carChoose->getCar_seat()) {
                $_SESSION['popup'] =  new PopUp('error', 'Votre nombre de siège est incorrect (soit vide soit supérieur au nombre de places de votre voiture.)');
                header('location: /amouv/voyage/creation');
                exit();
            }

            if (empty($_POST['nbLugage']) ||  !ctype_digit($_POST['nbLugage']) || $_POST['nbLugage'] < 0) {
                $_SESSION['popup'] =  new PopUp('error', 'Nombre de bagages par personne incorrect');
                header('location: /amouv/voyage/creation');
                exit(); 
            }

            //    public static function insertNewTravel($id_car, $departure, $arrival, $date_dep, $seats, $smoking, $lugage) {
        

            Travel::insertNewTravel($_POST['car'],
                                    $_POST['cityStart'],
                                    $_POST['cityEnd'],
                                    $dateDeparture,
                                    (int)$_POST['nbseat'],
                                    false,
                                    (int)$_POST['nbLugage']
                                    );
            
            $_SESSION['popup'] =  new PopUp('success', 'Votre voyage a bien été créé !');
            header('location: /amouv/');
            exit(); 
                    
                
        }

        View::render('Travel/createTravel', ['slt' => 'bonjour']);

    } // public function createTravel()


}

