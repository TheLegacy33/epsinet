<?php
	include_once ROOTMODELS.'DAO.php';
	include_once ROOTMODELS.'model_etudiant.php';
	include_once ROOTMODELS.'model_module.php';

	class StatutRattrapage{
		private $statr_id, $statr_libelle;

		public function __construct($id = 0, $libelle = ''){
			$this->statr_id = $id;
			$this->statr_libelle = $libelle;
		}

		public function getId(){
			return $this->statr_id;
		}

		public function getLibelle(){
			return $this->statr_libelle;
		}

		public static function getById($id){
			$SQLStmt = DAO::getInstance()->prepare("SELECT * FROM statutrattrapage WHERE statr_id = :idstatut");
			$SQLStmt->bindValue(':idstatut', $id);
			$SQLStmt->execute();
			$SQLRow = $SQLStmt->fetchObject();
			$newStatut = new StatutRattrapage($SQLRow->statr_id, $SQLRow->statr_libelle);
			$SQLStmt->closeCursor();
			return $newStatut;
		}

        public static function getByLibelle($libelle){
            $SQLStmt = DAO::getInstance()->prepare("SELECT * FROM statutrattrapage WHERE statr_libelle = :libstatut");
            $SQLStmt->bindValue(':libstatut', $libelle);
            $SQLStmt->execute();
            $SQLRow = $SQLStmt->fetchObject();
            $newStatut = new StatutRattrapage($SQLRow->statr_id, $SQLRow->statr_libelle);
            $SQLStmt->closeCursor();
            return $newStatut;
        }

		public static function getListe(){
			$SQLStmt = DAO::getInstance()->prepare("SELECT * FROM statutrattrapage ORDER BY statr_libelle");
			$SQLStmt->execute();
			$retVal = array();
			while ($SQLRow = $SQLStmt->fetchObject()){
				$newStatut = new StatutRattrapage($SQLRow->statr_id, $SQLRow->statr_libelle);
				$retVal[] = $newStatut;
			}
			$SQLStmt->closeCursor();
			return $retVal;
		}
	}

	class DelaiRattrapage{
		private $valeur, $unite, $valInterval;

		public function __construct($val = 1, $unit = 'h'){
			$this->valeur = $val;
			$this->unite = $unit;
			switch ($unit){
				case 'h':{
					$this->valInterval = 'PT'.$this->valeur.'H';
					break;
				}
				case 'd':{
					$this->valInterval = 'P'.$this->valeur.'D';
					break;
				}
				case 'm':{
					$this->valInterval = 'P'.$this->valeur.'M';
					break;
				}
				case 'y':{
					$this->valInterval = 'P'.$this->valeur.'Y';
					break;
				}
				default: {
					$this->valInterval = 'PT'.$this->valeur.'H';
					break;
				}
			}
		}

		public function __toString(){
			return $this->valeur.' '.$this->unite;
		}

		public function setValeur($valeur){
			$this->valeur = $valeur;
		}

		public function setUnite($unite){
			$this->unite = $unite;
		}

		public function getValeur(){
			return $this->valeur;
		}

		public function getUnite(){
			return $this->unite;
		}

		public function getInterval(){
			return $this->valInterval;
		}
	}

	class Rattrapage {
		private $rat_id, $ficsujet, $daterecup, $ficretour, $dateretour, $md5sujet, $md5docretour;
		private $etudiant, $module, $statut, $delai;

		public function __construct($rat_id = 0, $daterecupsujet = null, $dateretouretudiant = null, $fichiersujet = null, $md5ficsujet = null, $fichierretouretudiant = null, $md5retetudiant = null, $statutId = 1){
			$this->rat_id = $rat_id;
			$this->ficsujet = $fichiersujet;
			$this->daterecup = $daterecupsujet;
			$this->dateretour = $dateretouretudiant;
			$this->ficretour = $fichierretouretudiant;
			$this->md5sujet = $md5ficsujet;
			$this->md5docretour = $md5retetudiant;
			$this->etudiant = null;
			$this->module = null;
			$this->delai = new DelaiRattrapage();
			$this->statut = StatutRattrapage::getById($statutId);
		}

		public function getId(){
			return $this->rat_id;
		}

		public function getEtudiant(){
			return $this->etudiant;
		}

		public function getModule(){
			return $this->module;
		}

		public function getStatut(){
			return $this->statut;
		}

		public function getDateRecup(){
			return $this->daterecup;
		}

		public function getDateRetour(){
			return $this->dateretour;
		}

		public function getFicSujet(){
			return $this->ficsujet;
		}

		public function getFicRetour(){
			return $this->ficretour;
		}

		public function getDelai(){
			return $this->delai;
		}

		public function setEtudiant($etudiant){
			$this->etudiant = $etudiant;
		}

		public function setModule($module){
			$this->module = $module;
		}

		public function setStatut($statut){
			$this->statut = $statut;
		}

		public function setDelai($delai){
			$this->delai = $delai;
		}

		public function setDateRecup($datetime){
			$this->daterecup = $datetime;
		}

		public function downloaded(){
			return !is_null($this->daterecup);
		}

		public function uploaded(){
			return !is_null($this->dateretour);
		}

		public function expired(){
			$DTNow = new DateTime('now');
			$DTRecup = new DateTime($this->daterecup);
			$DTRenduAttendue = $DTRecup->add(new DateInterval($this->delai->getInterval()))->add(new DateInterval('PT1M'));
            return($DTNow > $DTRenduAttendue);
		}

		public static function getListeForEtudiant($idEtudiant = 0){
			if ($idEtudiant == 0){
				return null;
			}
			$SQLQuery = 'SELECT * FROM rattrapage INNER JOIN statutrattrapage ON rattrapage.statr_id = statutrattrapage.statr_id  ';
			$SQLQuery .= 'WHERE etu_id = :idetudiant ';
			$SQLQuery .= 'ORDER BY rattrapage.statr_id, rat_daterecupsujet';
			$SQLStmt = DAO::getInstance()->prepare($SQLQuery);
			$SQLStmt->bindValue(':idetudiant', $idEtudiant);
			$SQLStmt->execute();

			$retVal = array();
			while ($SQLRow = $SQLStmt->fetchObject()){
				$newRattrapage = new Rattrapage($SQLRow->rat_id, $SQLRow->rat_daterecupsujet, $SQLRow->rat_dateretouretudiant, $SQLRow->rat_fichiersujet, $SQLRow->rat_md5ficsujet, $SQLRow->rat_fichierretouretudiant, $SQLRow->rat_md5retetudiant, $SQLRow->statr_id);
				$newRattrapage->setEtudiant(Etudiant::getById($SQLRow->etu_id));
				$newRattrapage->setModule(Module::getById($SQLRow->mod_id));
				$newRattrapage->setDelai(new DelaiRattrapage($SQLRow->rat_valdelai, $SQLRow->rat_unitdelai));
				$retVal[] = $newRattrapage;
			}
			$SQLStmt->closeCursor();
			return $retVal;
		}

		public static function getById($id){
			if ($id == 0){
				return null;
			}
			$SQLQuery = 'SELECT * FROM rattrapage INNER JOIN statutrattrapage ON rattrapage.statr_id = statutrattrapage.statr_id  ';
			$SQLQuery .= 'WHERE rat_id = :idrattrapage';
			$SQLStmt = DAO::getInstance()->prepare($SQLQuery);
			$SQLStmt->bindValue(':idrattrapage', $id);
			$SQLStmt->execute();

			$SQLRow = $SQLStmt->fetchObject();
			$newRattrapage = new Rattrapage($SQLRow->rat_id, $SQLRow->rat_daterecupsujet, $SQLRow->rat_dateretouretudiant, $SQLRow->rat_fichiersujet, $SQLRow->rat_md5ficsujet, $SQLRow->rat_fichierretouretudiant, $SQLRow->rat_md5retetudiant);
			$newRattrapage->setEtudiant(Etudiant::getById($SQLRow->etu_id));
			$newRattrapage->setModule(Module::getById($SQLRow->mod_id));
			$newRattrapage->setDelai(new DelaiRattrapage($SQLRow->rat_valdelai, $SQLRow->rat_unitdelai));
			$SQLStmt->closeCursor();
			return $newRattrapage;
		}

		public static function update($rattrapage){
			//Je commence par récupérer à nouveau les données du rattrapage
			$ratToUpdate = Rattrapage::getById($rattrapage->getId());

			$SQLQuery = 'UPDATE rattrapage SET rat_id = rat_id, ';

			if ($ratToUpdate->getFicSujet() != $rattrapage->getFicSujet()){
				$SQLQuery .= 'rat_fichiersujet = :fichiersujet, ';
			}
			if ($ratToUpdate->getDateRecup() != $rattrapage->getDateRecup()){
				$SQLQuery .= 'rat_daterecupsujet = :daterecupsujet, ';
			}
			if ($ratToUpdate->getDateRetour() != $rattrapage->getDateRetour()){
				$SQLQuery .= 'rat_dateretouretudiant = :dateretouretudiant, ';
			}
			if ($ratToUpdate->getFicRetour() != $rattrapage->getFicRetour()){
				$SQLQuery .= 'rat_fichierretouretudiant = :fichierretouretudiant, ';
			}
			if ($ratToUpdate->getStatut()->getId() != $rattrapage->getStatut()->getId()){
				$SQLQuery .= 'statr_id = :idstatut, ';
			}
			if ($ratToUpdate->getDelai()->getValeur() != $rattrapage->getDelai()->getValeur()){
				$SQLQuery .= 'rat_valdelai = :valdelai, ';
			}
			if ($ratToUpdate->getDelai()->getUnite() != $rattrapage->getDelai()->getUnite()){
				$SQLQuery .= 'rat_unitdelai = :unitdelai, ';
			}

			$SQLQuery = substr($SQLQuery, 0 ,strlen($SQLQuery) - 2).' ';
			$SQLQuery .= 'WHERE rat_id = :idrattrapage';

			$stmt = DAO::getInstance()->prepare($SQLQuery);
			if ($ratToUpdate->getFicSujet() != $rattrapage->getFicSujet()){
				$stmt->bindValue(':fichiersujet', $rattrapage->getFicSujet());
			}
			if ($ratToUpdate->getDateRecup() != $rattrapage->getDateRecup()){
				$stmt->bindValue(':daterecupsujet', $rattrapage->getDateRecup());
			}
			if ($ratToUpdate->getDateRetour() != $rattrapage->getDateRetour()){
				$stmt->bindValue(':dateretouretudiant', $rattrapage->getDateRetour());
			}
			if ($ratToUpdate->getFicRetour() != $rattrapage->getFicRetour()){
				$stmt->bindValue(':fichierretouretudiant', $rattrapage->getFicRetour());
			}
			if ($ratToUpdate->getStatut()->getId() != $rattrapage->getStatut()->getId()){
				$stmt->bindValue(':idstatut', $rattrapage->getStatut()->getId());
			}
			if ($ratToUpdate->getDelai()->getValeur() != $rattrapage->getDelai()->getValeur()){
				$stmt->bindValue(':valdelai', $rattrapage->getDelai()->getValeur());
			}
			if ($ratToUpdate->getDelai()->getUnite() != $rattrapage->getDelai()->getUnite()){
				$stmt->bindValue(':unitdelai', $rattrapage->getDelai()->getUnite());
			}
			$stmt->bindValue(':idrattrapage', $rattrapage->getId());

			$stmt->execute();
		}
	}