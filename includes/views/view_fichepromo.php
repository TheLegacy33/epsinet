<nav class="navinterne">
	<a href="index.php?p=promotions&a=listepromotions&idecole=<?php print((isset($idEcole)) ? $idEcole : 0); ?>" title="Retour à la liste des promotions"><< Retour</a>
</nav>
<section id="content_body" class="container">
	<form action="" method="post" id="frmSaisie" enctype="multipart/form-data">
		<div class="card text-justify">
			<div class="card-header text-uppercase">Informations de la promotion</div>
			<div class="card-body">
				<div><label for="ttNom">Nom : </label><input type="text" name="ttNom" id="ttNom" value="<?php print($promo->getLibelle()); ?>" /></div>
			</div>
			<div class="card-footer formbtn">
				<button type="submit" class="btn btn-success mr-3">Enregistrer<span class="fa fa-save"></span></button>
				<button type="reset" class="btn btn-secondary">Annuler<span class="fa fa-ban"></span></button>
			</div>
		</div>
	</form>
</section>