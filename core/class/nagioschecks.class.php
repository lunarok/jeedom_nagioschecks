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
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class nagioschecks extends eqLogic {
  public static function dependancy_info() {
    $return = array();
    $return['log'] = 'nagios_plugins';
    $cmd = "dpkg -l | grep nagios-plugins";
    exec($cmd, $output, $return_var);
    if ($output[0] != "") {
      $return['state'] = 'ok';
    } else {
      $return['state'] = 'nok';
    }
    return $return;
  }

  public static function dependancy_install() {
    $cmd = 'sudo apt-get -y install nagios-plugins >> ' . log::getPathToLog('nagios_plugins') . ' 2>&1 &';
    exec($cmd);
  }


    public static function cron5() {
      foreach (eqLogic::byType('nagioschecks') as $nagioschecks) {
  			log::add('nagioschecks', 'debug', 'pull cron ' . $nagioschecks->getName());
        $sshhost = $nagioschecks->getConfiguration('sshhost');
        $sshuser = $nagioschecks->getConfiguration('sshuser');
        $sshport = $nagioschecks->getConfiguration('sshport');
        $sshkey = $nagioschecks->getConfiguration('sshkey');
        $sshpath = $nagioschecks->getConfiguration('sshpath');
        $alert = str_replace('#','',$nagioschecks->getConfiguration('alert'));
        if ($nagioschecks->getIsEnable()) {
    			$nagioschecks->getInformations($sshhost,$sshuser,$sshport,$sshkey,$sshpath,'5',$alert);
          $mc = cache::byKey('nagioschecksWidgetdashboard' . $nagioschecks->getId());
          $mc->remove();
          $nagioschecks->toHtml('dashboard');
          $mc = cache::byKey('nagioschecksWidgetmobile' . $nagioschecks->getId());
          $mc->remove();
          $nagioschecks->toHtml('mobile');
          $nagioschecks->refreshWidget();
      }
		}
    }

    public static function cron15() {
      foreach (eqLogic::byType('nagioschecks') as $nagioschecks) {
  			log::add('nagioschecks', 'debug', 'pull cron ' . $nagioschecks->getName());
        $sshhost = $nagioschecks->getConfiguration('sshhost');
        $sshuser = $nagioschecks->getConfiguration('sshuser');
        $sshport = $nagioschecks->getConfiguration('sshport');
        $sshkey = $nagioschecks->getConfiguration('sshkey');
        $sshpath = $nagioschecks->getConfiguration('sshpath');
        $alert = str_replace('#','',$nagioschecks->getConfiguration('alert'));
        if ($nagioschecks->getIsEnable()) {
    			$nagioschecks->getInformations($sshhost,$sshuser,$sshport,$sshkey,$sshpath,'15',$alert);
          $mc = cache::byKey('nagioschecksWidgetdashboard' . $nagioschecks->getId());
          $mc->remove();
          $nagioschecks->toHtml('dashboard');
          $mc = cache::byKey('nagioschecksWidgetmobile' . $nagioschecks->getId());
          $mc->remove();
          $nagioschecks->toHtml('mobile');
          $nagioschecks->refreshWidget();
      }
		}
    }

    public static function cron30() {
      foreach (eqLogic::byType('nagioschecks') as $nagioschecks) {
  			log::add('nagioschecks', 'debug', 'pull cron ' . $nagioschecks->getName());
        $sshhost = $nagioschecks->getConfiguration('sshhost');
        $sshuser = $nagioschecks->getConfiguration('sshuser');
        $sshport = $nagioschecks->getConfiguration('sshport');
        $sshkey = $nagioschecks->getConfiguration('sshkey');
        $sshpath = $nagioschecks->getConfiguration('sshpath');
        $alert = str_replace('#','',$nagioschecks->getConfiguration('alert'));
        if ($nagioschecks->getIsEnable()) {
    			$nagioschecks->getInformations($sshhost,$sshuser,$sshport,$sshkey,$sshpath,'30',$alert);
          $mc = cache::byKey('nagioschecksWidgetdashboard' . $nagioschecks->getId());
          $mc->remove();
          $nagioschecks->toHtml('dashboard');
          $mc = cache::byKey('nagioschecksWidgetmobile' . $nagioschecks->getId());
          $mc->remove();
          $nagioschecks->toHtml('mobile');
          $nagioschecks->refreshWidget();
      }
		}
    }

    public function postAjax() {
      log::add('nagioschecks', 'debug', 'pull update ' . $this->getName());
      $sshhost = $this->getConfiguration('sshhost');
      $sshuser = $this->getConfiguration('sshuser');
      $sshport = $this->getConfiguration('sshport');
      $sshkey = $this->getConfiguration('sshkey');
      $sshpath = $this->getConfiguration('sshpath');
      $this->getInformations($sshhost,$sshuser,$sshport,$sshkey,$sshpath);
      $this->refreshWidget();
    }


    /*     * *********************Methode d'instance************************* */

    public function getInformations($sshhost,$sshuser,$sshport,$sshkey,$sshpath,$cron,$cmdalert) {
      foreach ($this->getCmd() as $cmd) {
        $tempo = $cmd->getConfiguration('cron');
        if ($tempo == '') {
          $tempo = '15';
        }
        if ($cmd->getConfiguration('cron') == $cron) {
            $alert = $cmd->getConfiguration('alert');
    				$check = $cmd->getConfiguration('check');
            $options = $cmd->getConfiguration('options');
            $sudo = $cmd->getConfiguration('sudo');
            $ssh = $cmd->getConfiguration('ssh');
            $alert = $cmd->getConfiguration('alert');
            if ($alert == '') {
              $alert = 0;
            }
            $notifalert = $cmd->getConfiguration('notifalert');
            if ($ssh == '1') {
              $cline = $sshpath . $check . ' ' . $options;
            } else {
              $cline = '/usr/lib/nagios/plugins/' . $check . ' ' . $options;
            }
            if ($sudo == '1') {
              $cline = 'sudo ' . $cline;
            }
            if ($ssh == '1') {
              $cline = '/usr/lib/nagios/plugins/check_by_ssh -H ' . $sshhost . ' -l ' . $sshuser . ' -p ' . $sshport . ' -i ' . $sshkey . ' -C "' . $cline . '"';
            }
            log::add('nagioschecks', 'debug', 'Command : ' . $cline);
            unset($output);
            $output = array();
            exec($cline, $output, $return_var);
            //$return_var = '0';
            if ($return_var == '2' && $notifalert != '' && $cmdalert != '') {
              if ($alert > $notifalert) {
                log::add('nagioschecks','error','Erreur sur ' . $cmd->getName() . ' : ' . $output[0]);
                $cmd = cmd::byId($alert);
                $options['title'] = 'Alerte sur ' . $cmd->getName();
                $options['message'] = $cmd->getName() . " avec statut " . $output[0];
                $cmd->execCmd($options);
              } else {
                $newalerte = $alert + 1;
                $cmd->setConfiguration('alert', $newalerte);
              }
            } else {
              if ($alert != '0') {
                $cmd->setConfiguration('alert', '0');
              }
            }
            $cmd->setConfiguration('value', $return_var);
            $cmd->setConfiguration('status', $output[0]);
  					$cmd->save();
  					$cmd->event($return_var);
            log::add('nagioschecks', 'debug', 'Result : ' . $return_var . ' text ' . $output[0]);
            //log::add('nagioschecks', 'debug', print_r($cmd,true));

            //Traitement métriques
            if (strpos($output[0], '|') !== false) {
              $metric = substr($output[0], 0, strpos($output[0], '|'));
              $cmd->setConfiguration('hasMetric', '1');
              $cmd->save();
              log::add('nagioschecks', 'debug', $metric);
            }

    				}
        }
            //log::add('nagioschecks', 'debug', print_r($this,true));
      return ;
		}



    public function toHtml($_version = 'dashboard') {
      $mc = cache::byKey('nagioschecksWidget' . $_version . $this->getId());
      if ($mc->getValue() != '') {
        return $mc->getValue();
      }
      if ($this->getIsEnable() != 1) {
              return '';
          }
          if (!$this->hasRight('r')) {
              return '';
          }
          $_version = jeedom::versionAlias($_version);
          if ($this->getDisplay('hideOn' . $_version) == 1) {
              return '';
          }
          $vcolor = 'cmdColor';
          if ($_version == 'mobile') {
              $vcolor = 'mcmdColor';
          }
          $parameters = $this->getDisplay('parameters');
          $cmdColor = ($this->getPrimaryCategory() == '') ? '' : jeedom::getConfiguration('eqLogic:category:' . $this->getPrimaryCategory() . ':' . $vcolor);
          if (is_array($parameters) && isset($parameters['background_cmd_color'])) {
              $cmdColor = $parameters['background_cmd_color'];
          }

          if (($_version == 'dview' || $_version == 'mview') && $this->getDisplay('doNotShowNameOnView') == 1) {
              $replace['#name#'] = '';
              $replace['#object_name#'] = (is_object($object)) ? $object->getName() : '';
          }
          if (($_version == 'mobile' || $_version == 'dashboard') && $this->getDisplay('doNotShowNameOnDashboard') == 1) {
              $replace['#name#'] = '<br/>';
              $replace['#object_name#'] = (is_object($object)) ? $object->getName() : '';
          }

          if (is_array($parameters)) {
              foreach ($parameters as $key => $value) {
                  $replace['#' . $key . '#'] = $value;
              }
          }
      $background=$this->getBackgroundColor($_version);
      $replace = array(
        '#name#' => $this->getName(),
        '#id#' => $this->getId(),
        '#background_color#' => $background,
        '#height#' => $this->getDisplay('height', 'auto'),
        '#width#' => $this->getDisplay('width', '200px'),
        '#eqLink#' => ($this->hasRight('w')) ? $this->getLinkToConfiguration() : '#',
      );
    $checkList = '';

    foreach($this->getCmd() as $cmd){
      $value = $cmd->getConfiguration('value');
      $status = $cmd->getConfiguration('status');
      $update = $cmd->getConfiguration('updatetime');
      if (strpos($status, '|') !== false) {
        $status = substr($status, 0, strpos($status, '|'));
      }
      $status = str_replace('"','\'',$status);
      if ($value == '0') {
        $div = '<div class="btn btn-success center-block cmd" data-type="info" data-subtype="string" data-cmd_id="' . $cmd->getId() . '" title="' . $status . '" style="margin-left:5px;margin-right:5px;"><i class="fa fa-shield"></i> ';
        $text = 'OK';
      } elseif ($value == '1') {
        $div = '<div class="btn btn-warning center-block cmd" data-type="info" data-subtype="string" data-cmd_id="' . $cmd->getId() . '" title="' . $status . '" style="margin-left:5px;margin-right:5px;"><i class="fa fa-info"></i> ';
        $text = 'WARN';
      } else {
        $div = '<div class="btn btn-danger center-block cmd" data-type="info" data-subtype="string" data-cmd_id="' . $cmd->getId() . '" title="' . $status . '" style="margin-left:5px;margin-right:5px;"><i class="fa fa-exclamation"></i> ';
        $text = 'CRIT';
      }

      $checkList = $checkList . '<p>' . $div . $cmd->getName() . ' : ' . $text . '</div></p>';
    }


      $replace = array(
            '#name#' => $this->getName(),
              '#checks#' => $checkList,
              '#id#' => $this->getId(),
              '#collectDate#' => $update,
              '#background_color#' => $this->getBackgroundColor(jeedom::versionAlias($_version)),
              '#eqLink#' => $this->getLinkToConfiguration(),
          );

        $parameters = $this->getDisplay('parameters');
        if (is_array($parameters)) {
            foreach ($parameters as $key => $value) {
                $replace['#' . $key . '#'] = $value;
            }
        }

        $html = template_replace($replace, getTemplate('core', $_version, 'nagioschecks', 'nagioschecks'));
        cache::set('nagioschecksWidget' . $_version . $this->getId(), $html, 0);
        return $html;
    }

}

class nagioschecksCmd extends cmd {
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
    public function execute($_options = null) {
              log::add('nagioschecks', 'info', 'Commande recue');
              if ($_options['option'] == 'status') {
                return $this->getConfiguration('status');
              } else {
                return $this->getConfiguration('value');
              }
      }

}

?>
