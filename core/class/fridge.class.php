<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class fridge extends eqLogic {
  /*     * *************************Attributs****************************** */
  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */

  /*     * ***********************Methode static*************************** */
  
  public static function pull($_options) {
		$fridge = fridge::byId($_options['fridge']);
		if (!is_object($fridge)) {
			$cron = cron::byClassAndFunction('fridge', 'pull', $_options);
			if (is_object($cron)) {
				$cron->remove();
			}
			throw new Exception('Fridge not found : ' . $_options['fridge'] . '. Task removed');
		}
		
		$temp = $fridge->getTemperature();
		$fridge->computeOutput($temp);
		
		$thermostat->getCmd(null, 'temperature')->event(jeedom::evaluateExpression($thermostat->getConfiguration('temperature_indoor')));
  }

  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom
  public static function cron() {}
  */
  public static function cron() {
    foreach (self::byType('fridge', true) as $fridge) { 
		$cmd = $fridge->getCmd(null, 'refresh');
		if (!is_object($cmd)) {
			continue; 
		}
		$cmd->execCmd();
    }
  }

  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  public static function cron5() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  public static function cron10() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert() {
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
        $this->setDisplay("width","200px");
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
        $temp = $this->getCmd(null, 'temperature');
        if (!is_object($temp)) {
        $temp = new fridgeCmd();
        $temp->setName(__('Temperature', __FILE__));
        }
        $temp->setLogicalId('temperature');
        $temp->setEqLogic_id($this->getId());
        $temp->setType('info');
        $temp->setUnite('°C');
        $temp->setSubType('numeric');
        $temp->setTemplate('dashboard','tile');
        $temp->save();

        $power = $this->getCmd(null, 'power');
        if (!is_object($power)) {
        $power = new fridgeCmd();
        $power->setName(__('Power', __FILE__));
        }
        $power->setLogicalId('power');
        $power->setEqLogic_id($this->getId());
        $power->setType('info');
        $power->setUnite('%');
        $power->setSubType('numeric');
        $power->setTemplate('dashboard','tile');
        $power->save();

        $output = $this->getCmd(null, 'output');
        if (!is_object($output)) {
        $output = new fridgeCmd();
        $output->setName(__('Output', __FILE__));
        }
        $output->setLogicalId('output');
        $output->setEqLogic_id($this->getId());
        $output->setType('info');
        $output->setSubType('binary');
        $temp->setTemplate('dashboard','tmplicon');
        $output->save();

        $temp = $this->getCmd(null, 'thermostat');
        if (!is_object($temp)) {
        $temp = new fridgeCmd();
        $temp->setName(__('Thermostat', __FILE__));
        }
        $temp->setLogicalId('thermostat');
        $temp->setEqLogic_id($this->getId());
        $temp->setType('action');
        $temp->setUnite('°C');
		$temp->setSubType('slider');
        $temp->setTemplate('dashboard','tile');
        $temp->save();
		
        $target = $this->getCmd(null, 'target');
        if (!is_object($target)) {
        $target = new fridgeCmd();
        $target->setName(__('Target', __FILE__));
        }
        $target->setLogicalId('target');
        $target->setEqLogic_id($this->getId());
        $target->setType('info');
        $target->setUnite('°C');
        $target->setSubType('numeric');
        $target->setTemplate('dashboard','tile');
        $target->save();
		
		$refresh = $this->getCmd(null, 'refresh');
		if (!is_object($refresh)) {
			$refresh = new fridgeCmd();
			$refresh->setName(__('Refresh', __FILE__));
		}
		$refresh->setEqLogic_id($this->getId());
		$refresh->setLogicalId('refresh');
		$refresh->setType('action');
		$refresh->setSubType('other');
		$refresh->save();
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */

  /*     * **********************Getteur Setteur*************************** */
  
  public function getTemperature() {
		$probe = $this->getConfiguration("probe", "");
		if( $probe == "") {
			return 0;
		}
		$probe = cmd::byString($probe);
		return $probe->execCmd();
	}
  
  public function computeOutput($temperature)
	{
		$this->checkAndUpdateCmd('temperature',$temperature);
		$thermostat = $this->getCmd(null, 'target');
		$target = $thermostat->execCmd();
		$power = $this->getCmd(null, 'power');
		$pw = $power->execCmd();
		$t=time();
		$t /= 60;
		$t %= 10;
		if(($t==0 || $pw==0) && $temperature >= ($target+0.1))
		{
			$t=0;//to force start
			if($temperature > ($target + 1))
			{	
				$pw = 80;					
			}
			elseif($temperature > ($target + 0.3))
			{
				$pw = 50;					
			}
			else
			{				
				$pw = 20;	
			}
			$this->checkAndUpdateCmd('power', $pw);
		}
			
		if($temperature < ($target-0.1))
		{
			$pw = 0;	
			$this->checkAndUpdateCmd('power', $pw);		
		}
		$enable = $t < ($pw * (10 / 100));
		$this->checkAndUpdateCmd('output', $enable);
		$out_relay = "";
		if($enable)
		{
			$out_relay = $this->getConfiguration("output_on", "");	
		}
		else
		{
			$out_relay = $this->getConfiguration("output_off", "");
		}
		if( $out_relay != "") {
			$out_relay = cmd::byString($out_relay);
			if (is_object($out_relay))
			{
				$out_relay->execCmd();			
			}
		}
	}
}

class fridgeCmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */
  public function dontRemoveCmd() {
    return true;
  }

  // Exécution d'une commande
  public function execute($_options = array()) {
	  $eqlogic = $this->getEqLogic();
	  switch ($this->getLogicalId()) {
		case 'refresh':
			$temp = $eqlogic->getTemperature();
			$eqlogic->computeOutput($temp);			  
			$this->event(jeedom::evaluateExpression($eqlogic->getConfiguration('probe')));
			break;
		case 'thermostat':
			  $eqlogic->checkAndUpdateCmd('target', $_options['slider']);
			  break;
		default:
			break;
	  }
  }

  /*     * **********************Getteur Setteur*************************** */

}
