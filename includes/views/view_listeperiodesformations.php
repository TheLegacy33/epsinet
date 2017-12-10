<section id="content_body" class="row">
	<header class="text-center text-info">Liste des périodes de formations</header>
	<div class="row">
		<span class="col-sm-offset-3 col-sm-3 text-center"><label style="margin-right: 5px">Ecole : </label>
			<?php
				if ($promo == null){
					print('Toutes');
				}else{
					print($promo->getEcole()->getNom());
				}
			?>
		</span>
		<span class="col-sm-3 text-center"><label style="margin-right: 5px">Promotion : </label>
			<?php
				if ($promo == null){
					print('Toutes');
				}else {
					print($promo->getLibelle());
				}
			?>
		</span>
	</div>
	<table>
		<tr>
			<?php
				if ($promo == null){
					print('<th colspan="2">Formation</th>');
				}
			?>
			<th>Date Début</th>
			<th>Date Fin</th>
			<th>Effectif</th>
			<th>Nb Modules</th>
			<th>Resp. Peda.</th>
			<th colspan="2">Actions</th>
		</tr>
		<?php
			$script = '';
			if (count($listePf) == 0){
				$script .= '<tr><td colspan="6">Aucune donnée disponible !</td></tr>';
			}else{
				foreach ($listePf as $pf){
					$script .= '<tr>';
					if ($promo == null){
						$script .= '<td>'.$pf->getPromo()->getEcole()->getNom().'</td>';
						$script .= '<td>'.$pf->getPromo()->getLibelle().'</td>';
					}
					$script .= '<td>'.$pf->getDateDebut().'</td>';
					$script .= '<td>'.$pf->getDateFin().'</td>';
					$script .= '<td>'.$pf->getEffectif().'</td>';
					$script .= '<td>'.$pf->getNbModules().'</td>';
					$script .= '<td>'.$pf->getResponsable().'</td>';
					$script .= '<td><a href="index.php?p=periodesformation&a=listeetudiants&idpf='.$pf->getId().'" title="Liste des étudiants"><span class="glyphicon glyphicon-user"></span></a></td>';
					$script .= '<td><a href="index.php?p=periodesformation&a=listemodules&idpf='.$pf->getId().'" title="Liste des modules"><span class="glyphicon glyphicon-list"></td>';
					$script .= '</tr>';
				}
			}
			print($script);
		?>
	</table>
</section>