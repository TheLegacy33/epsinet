<?php
include_once ROOTMODELS.'DAO.php';
include_once ROOTMODELS.'model_etudiant.php';
include_once ROOTMODELS.'model_module.php';
include_once ROOTMODELS.'model_responsablepedago.php';


class Periodeformation {
	private $id, $datedebut, $datefin, $promo, $responsable;
	private $etudiants, $modules;

	public function __construct($id = 0, $datedebut, $datefin, $promo){
		$this->id = $id;
		$this->datedebut = $datedebut;
		$this->datefin = $datefin;
		$this->promo = $promo;
		$this->etudiants = array();
		$this->modules = array();
	}
	public function getId(){
		return $this->id;
	}

	public function getDateDebut(){
		return $this->datedebut;
	}

	public function getDateFin(){
		return $this->datefin;
	}

	public function getPromo(){
		return $this->promo;
	}

	public function getModules(){
	    return $this->modules;
    }

	public function getEffectif(){
		return count($this->etudiants);
	}

	public function getNbModules(){
		return count($this->modules);
	}

	public function getResponsable(){
		return $this->responsable;
	}

	public function fillStudents($listeEtudiants){
		$this->etudiants = $listeEtudiants;
	}

	public function fillModules($listeModules){
		$this->modules = $listeModules;
	}

	public function setResponsable($responsable){
		$this->responsable = $responsable;
	}

	public static function getListe($idPf = 0){
		$SQLQuery = 'SELECT * ';
		$SQLQuery .= 'FROM periodeformation INNER JOIN promotion ON periodeformation.promo_id = promotion.promo_id ';
		if ($idPf != 0){
			$SQLQuery .= 'WHERE periodeformation.pf_id = :idpf ';
		}
		$SQLQuery .= 'ORDER BY promotion.promo_libelle, pf_datedebut DESC, pf_datefin DESC';
		$SQLStmt = DAO::getInstance()->prepare($SQLQuery);
		if ($idPf != 0){
			$SQLStmt->bindValue(':idpf', $idPf);
		}
		$SQLStmt->execute();
		$retVal = array();
		while ($SQLRow = $SQLStmt->fetchObject()){
			$newPf = new Periodeformation($SQLRow->pf_id, $SQLRow->pf_datedebut, $SQLRow->pf_datefin, Promotion::getById($SQLRow->promo_id));
			$newPf->fillStudents(Etudiant::getListeFromPf($SQLRow->pf_id));
			$newPf->fillModules(Module::getListeFromPf($SQLRow->pf_id));
			$newPf->setResponsable(ResponsablePedago::getById($SQLRow->resp_id));
			$retVal[] = $newPf;
		}
		$SQLStmt->closeCursor();
		return $retVal;
	}

	public static function getListeFromPromo($idPromo = 0){
		$SQLQuery = 'SELECT * ';
		$SQLQuery .= 'FROM periodeformation INNER JOIN promotion ON periodeformation.promo_id = promotion.promo_id ';
		$SQLQuery .= 'WHERE promotion.promo_id = :idpromo ';
		$SQLQuery .= 'ORDER BY promotion.promo_libelle, pf_datedebut DESC, pf_datefin DESC';
		$SQLStmt = DAO::getInstance()->prepare($SQLQuery);
		$SQLStmt->bindValue(':idpromo', $idPromo);
		$SQLStmt->execute();
		$retVal = array();
		while ($SQLRow = $SQLStmt->fetchObject()){
			$newPf = new Periodeformation($SQLRow->pf_id, $SQLRow->pf_datedebut, $SQLRow->pf_datefin, Promotion::getById($SQLRow->promo_id));
			$newPf->fillStudents(Etudiant::getListeFromPf($SQLRow->pf_id));
			$newPf->fillModules(Module::getListeFromPf($SQLRow->pf_id));
			$newPf->setResponsable(ResponsablePedago::getById($SQLRow->resp_id));
			$retVal[] = $newPf;
		}
		$SQLStmt->closeCursor();
		return $retVal;
	}

	public static function getById($id){
		$SQLStmt = DAO::getInstance()->prepare("SELECT * FROM periodeformation WHERE pf_id = :idpf");
		$SQLStmt->bindValue(':idpf', $id);
		$SQLStmt->execute();
		$SQLRow = $SQLStmt->fetchObject();
		$newPf = new Periodeformation($SQLRow->pf_id, $SQLRow->pf_datedebut, $SQLRow->pf_datefin, Promotion::getById($SQLRow->promo_id));
		$newPf->fillStudents(Etudiant::getListeFromPf($SQLRow->pf_id));
        $newPf->fillModules(Module::getListeFromPf($SQLRow->pf_id));
		$newPf->setResponsable(ResponsablePedago::getById($SQLRow->resp_id));
		$SQLStmt->closeCursor();
		return $newPf;
	}
}