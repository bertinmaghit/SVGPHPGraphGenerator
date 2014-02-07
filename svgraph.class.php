<?php
class graph
{
	protected $largeur, $hauteur, $data = array(), $basZero, $couleursChemins;
	protected $margeBas, $margeGauche, $ecartColonnes, $maxY, $minY;

	public function __construct($largeur, $hauteur, $data, $basZero = false, $couleursChemins = array('blue'))
	{
		$this->largeur = $largeur;
		$this->hauteur = $hauteur;
		$this->data = $data;
		$this->basZero = $basZero;
		$this->margeBas = 50;
		$this->margeGauche = 70;
		$this->couleursChemins = $couleursChemins;
	}

	public function graphLignes()
	{
		$verifErreur = $this->verifErreur();
		if($verifErreur != 'ok'){ return $verifErreur; }

		$this->maxY = $this->calculValeurYMax();
		$this->minY = $this->calculValeurYMin();
		$nombreLignesHorizontal = 10;
		$this->ecartColonnes = ($this->largeur-$this->margeGauche)/(count($this->data)-1);

		$result = '<svg style="width:'.$this->largeur.'; height:'.$this->hauteur.';">';

		//Rectangle :
		$result .= '<rect x="'.$this->margeGauche.'" y="0" width="'.($this->largeur-$this->margeGauche).'" height="'.($this->hauteur-$this->margeBas).'" class="rectangle"/>';

		//Lignes horizontales :
		for ($i = 1; $i < $nombreLignesHorizontal; $i++) { 
			if(!$this->basZero){ $val = ($i*($this->maxY-$this->minY))/$nombreLignesHorizontal; $val += $this->minY; }
			else{ $val = ($i*$this->maxY)/$nombreLignesHorizontal; }
			$val = round($val);
			$result .= '<text x="'.($this->margeGauche-5).'" y="'.($this->calculPositionY($val, $this->margeBas)+12).'" text-anchor="end" class="text">'.$val.'</text>';
			$result .= '<line x1="'.$this->margeGauche.'" x2="'.$this->largeur.'" y1="'.($this->calculPositionY($val, $this->margeBas)).'" y2="'.$this->calculPositionY($val, $this->margeBas).'" class="ligneFond"/>';
		}

		//Texte :
		$result .= '<text x="'.($this->margeGauche-5).'" y="12" text-anchor="end" class="text">'.$this->maxY.'</text>';
		$result .= '<text x="'.($this->largeur).'" y="'.($this->hauteur-$this->margeBas+30).'" text-anchor="end" class="text" style="background-color:white;" id="tooltip"></text>';

		//- Texte bas :
		$i = 0;
		foreach ($this->data as $key => $value) {
			$textanchor = "middle";
			if($i == count($this->data)-1){ $textanchor = "end"; }
			$result .= '<text x="'.(($this->ecartColonnes*$i)+$this->margeGauche).'" y="'.($this->hauteur-$this->margeBas+15).'" text-anchor="'.$textanchor.'" class="text">'.$key.'</text>'; $i++;
		}

		//Chemin :
		$result .= $this->traceChemin();

		//Résultat :
		$result .= '</svg>';

		return $result;
	}

	private function traceChemin()
	{
		$chemin = array();
		$cercles = array();
		$result = '';
		$i = 0;
		foreach ($this->data as $key => $value) {

			if(!is_array($value)){ $value = array($value); }

			//Parcours toutes les valeurs Y de la position X actuelle
			foreach ($value as $k => $val)
			{
				if($i == 0){ $chemin[$k] = 'M '; $cercles[$k] = ''; }
				else{ $chemin[$k] .= 'L '; }

				$x = (($this->ecartColonnes*$i)+$this->margeGauche);
				$y = $this->calculPositionY($val, $this->margeBas);

				//Décalage premier et dernier points :
				if($i == 0){ $x += 6; }
				if($i == count($this->data)-1){ $x -= 6; }

				$chemin[$k] .= $x.' '.$y.' ';

				$cc = '';
				if(isset($this->couleursChemins[$k])){ $cc = 'style="stroke:'.$this->couleursChemins[$k].';"'; }
				$cercles[$k] .= '<circle cx="'.$x.'" cy="'.$y.'" r="4" title="'.$key.' : '.$val.'" class="cercle" '.$cc.'/>';	
			}

			$i++;
		}

		foreach ($chemin as $key => $chem) {
			$cc = '';
			if(isset($this->couleursChemins[$key])){ $cc = 'style="stroke:'.$this->couleursChemins[$key].';"'; }

			$result .= '<path d="'.$chem.'" class="chemin" '.$cc.'/>';
		}
		
		foreach ($cercles as $cerc) {
			$result .= $cerc;
		}

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

	private function calculPositionY($valeur)
	{
		//Début axe y à zéro ou à la valeur min des données
		if($this->basZero){ return $this->hauteur - $this->margeBas - ((($this->hauteur - $this->margeBas)*$valeur)/$this->calculValeurYMax()); }
		else{ return $this->hauteur - $this->margeBas - (($valeur-$this->calculValeurYMin())*($this->hauteur - $this->margeBas))/($this->calculValeurYMax()-$this->calculValeurYMin()); }
	}

	private function calculValeurYMax()
	{
		$max = 0;

		foreach ($this->data as $value) {
			if(is_array($value))
			{
				foreach ($value as $val) {
					if($max < $val){ $max = $val; }
				}
			}
			else{ if($max < $value){ $max = $value; } }
		}
		return $max;
	}

	private function calculValeurYMin()
	{
		$min = 999999999999999999;

		foreach ($this->data as $value) {
			if(is_array($value))
			{
				foreach ($value as $val) {
					if($min > $val){ $min = $val; }
				}
			}
			else{ if($min > $value){ $min = $value; } }
		}
		return $min;
	}

	private function verifErreurNumerique()
	{
		foreach ($this->data as $value) {
			if(is_array($value))
			{
				foreach ($value as $val) {
					if(!is_numeric($val)){ return true; }
				}
			}
			else{ if(!is_numeric($value)){ return true; } }
		}

		return false;
	}
}
?>