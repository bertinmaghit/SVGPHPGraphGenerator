<?php
class graph
{
	protected $largeur, $hauteur, $data = array(), $basZero;

	public function __construct($largeur, $hauteur, $data, $basZero = false)
	{
		$this->largeur = $largeur;
		$this->hauteur = $hauteur;
		$this->data = $data;
		$this->basZero = $basZero;
	}

	public function graphLignes()
	{
		$verifErreur = $this->verifErreur();
		if($verifErreur != 'ok'){ return $verifErreur; }

		$margeGauche = 70;
		$margeBas = 50;
		$maxY = $this->calculValeurYMax();
		$minY = $this->calculValeurYMin();
		$nombreLignesHorizontal = 10;
		$ecartColonnes = ($this->largeur-$margeGauche)/(count($this->data)-1);

		$result = '<svg style="width:'.$this->largeur.'; height:'.$this->hauteur.';">';

		//Rectangle :
		$result .= '<rect x="'.$margeGauche.'" y="0" width="'.($this->largeur-$margeGauche).'" height="'.($this->hauteur-$margeBas).'" class="rectangle"/>';

		//Lignes horizontales :
		for ($i = 1; $i < $nombreLignesHorizontal; $i++) { 
			if(!$this->basZero){ $val = ($i*($maxY-$minY))/$nombreLignesHorizontal; $val += $minY; }
			else{ $val = ($i*$maxY)/$nombreLignesHorizontal; }
			$val = round($val);
			$result .= '<text x="'.($margeGauche-5).'" y="'.($this->calculPositionY($val, $margeBas)+12).'" text-anchor="end" class="text">'.$val.'</text>';
			$result .= '<line x1="'.$margeGauche.'" x2="'.$this->largeur.'" y1="'.($this->calculPositionY($val, $margeBas)).'" y2="'.$this->calculPositionY($val, $margeBas).'" class="ligneFond"/>';
		}

		//Texte :
		$result .= '<text x="'.($margeGauche-5).'" y="12" text-anchor="end" class="text">'.$maxY.'</text>';
		$result .= '<text x="'.($this->largeur).'" y="'.($this->hauteur-$margeBas+30).'" text-anchor="end" class="text" style="background-color:white;" id="tooltip"></text>';

		//- Texte bas :
		$i = 0;
		foreach ($this->data as $key => $value) {
			$textanchor = "middle";
			if($i == count($this->data)-1){ $textanchor = "end"; }
			$result .= '<text x="'.(($ecartColonnes*$i)+$margeGauche).'" y="'.($this->hauteur-$margeBas+15).'" text-anchor="'.$textanchor.'" class="text">'.$key.'</text>'; $i++;
		}

		//Chemin :
		$chemin = '';
		$cercles = '';
		$i = 0;
		foreach ($this->data as $key => $value) {
			if($i == 0){ $chemin .= 'M '; }
			else{ $chemin .= 'L '; }

			$x = (($ecartColonnes*$i)+$margeGauche);
			$y = $this->hauteur - $margeBas - ((($this->hauteur - $margeBas)*$value)/$maxY); //Bas = 0
			$y = $this->calculPositionY($value, $margeBas);

			//Décalage premier et dernier points :
			if($i == 0){ $x += 6; }
			if($i == count($this->data)-1){ $x -= 6; }

			$chemin .= $x.' '.$y.' ';

			$cercles .= '<circle cx="'.$x.'" cy="'.$y.'" r="4" title="'.$key.' : '.$value.'" class="cercle"/>';

			$i++;
		}
		$result .= '<path d="'.$chemin.'" class="chemin"/>';
		//$result .= '<path d="'.$chemin.' L '.$this->largeur.' '.($this->hauteur-$margeBas).' L '.$margeGauche.' '.($this->hauteur-$margeBas).'" class="cheminFond"/>';
		$result .= $cercles;

		//Résultat :
		$result .= '</svg>';

		return $result;
	}

	private function verifErreur()
	{
		$erreur = false;
		$msg = '';
		if(count($this->data) == 0){ $msg .= "Vos donnees sont vides<br/>"; $erreur = true; }
		if($this->verifErreurNumerique()){ $msg .= "Vos donnees ne sont pas toutes numeriques : <br/>"; $erreur = true; }
		if(!is_numeric($this->largeur) || !is_numeric($this->hauteur)){ $msg .= "La largeur ou la hauteur de votre graphique ne sont pas numerique<br/>"; $erreur = true; }
	
		if(!$erreur){ return "ok"; }
		else{ return json_encode(array('erreur' => true, 'msg' => $msg, 'data' => $this->data)); }
	}

	private function calculPositionY($valeur, $margeBas)
	{
		//Début axe y à zéro ou à la valeur min des données
		if($this->basZero){ return $this->hauteur - $margeBas - ((($this->hauteur - $margeBas)*$valeur)/$this->calculValeurYMax()); }
		else{ return $this->hauteur - $margeBas - (($valeur-$this->calculValeurYMin())*($this->hauteur - $margeBas))/($this->calculValeurYMax()-$this->calculValeurYMin()); }
	}

	private function calculValeurYMax()
	{
		$max = 0;

		foreach ($this->data as $key => $value) {
			if($max < $value){ $max = $value; }
		}

		return $max;
	}

	private function calculValeurYMin()
	{
		$min = 999999999999999999;

		foreach ($this->data as $key => $value) {
			if($min > $value){ $min = $value; }
		}

		return $min;
	}

	private function verifErreurNumerique()
	{
		foreach ($this->data as $key => $value) {
			if(!is_numeric($value)){ return true; }
		}

		return false;
	}
}
?>