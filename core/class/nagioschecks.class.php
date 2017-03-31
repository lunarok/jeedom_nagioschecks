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
        $cmd = 'sudo chmod +x ' . dirname(__FILE__) . '/../../resources/*';
        exec($cmd);
    }

    public static function cron5() {
        foreach (eqLogic::byType('nagioschecks', true) as $nagioschecks) {
            $nagioschecks->getInformations('5');
        }
    }

    public static function cron15() {
        foreach (eqLogic::byType('nagioschecks', true) as $nagioschecks) {
            $nagioschecks->getInformations('15');
        }
    }

    public static function cron30() {
        foreach (eqLogic::byType('nagioschecks', true) as $nagioschecks) {
            $nagioschecks->getInformations('30');
        }
    }

    public static function cronHourly() {
        foreach (eqLogic::byType('nagioschecks', true) as $nagioschecks) {
            $nagioschecks->getInformations('60');
        }
    }

    public function postAjax() {
        foreach ($this->getCmd() as $cmd) {
            $cmd->setTemplate("mobile",'line' );
            $cmd->setTemplate("dashboard",'line' );
            $cmd->setSubType("binary");
            $cmd->save();
        }
        $this->getInformations('all');
    }

    public function getInformations($cron) {

        foreach ($this->getCmd() as $cmd) {
            if ($cmd->getLogicalId() == '') {
                $cmd->setLogicalId($cmd->getId());
                $cmd->save();
            }
            $tempo = $cmd->getConfiguration('cron');
            if ($tempo == '') {
                $tempo = '15';
            }
            if ($cmd->getConfiguration('cron') == $cron || 'all' == $cron) {
                $cline = $cmd->getConfiguration('check');
                if ($cmd->getConfiguration('ssh') == '1') {
                    $cline = $this->getConfiguration('sshpath') . $cline;
                }  else if (strrpos($cline,'/') !== false) {
                    $cline = dirname(__FILE__) . '/../../resources' . $cline;
                } else {
                    $cline = '/usr/lib/nagios/plugins/' . $cline;
                }
                $cline .= ' ' . $cmd->getConfiguration('options');
                $cline = ($cmd->getConfiguration('sudo') == '1') ? 'sudo ' . $cline : $cline;

                if ($cmd->getConfiguration('ssh') == '1') {
                    $cline = '/usr/lib/nagios/plugins/check_by_ssh -H ' . $this->getConfiguration('sshhost') . ' -l ' . $this->getConfiguration('sshuser') . ' -p ' . $this->getConfiguration('sshport') . ' -i ' . $this->getConfiguration('sshkey') . ' -C "' . $cline . '"';
                }
                log::add('nagioschecks', 'debug', 'Command : ' . $cline);
                unset($output);
                $output = array();
                exec($cline, $output, $return_var);
                if ($return_var > 3) {
                    return;
                }
                log::add('nagioschecks', 'debug', 'Result : ' . $return_var . ' label ' . $output[0]);
                $value = ($return_var == 0) ? 1 : 0;
                $cmd->setConfiguration('code', $return_var);
                $cmd->setConfiguration('status', $output[0]);
                $this->checkAndUpdateCmd($cmd->getLogicalId(), $value);
                //Traitement valeur texte si demandée
                if ($cmd->getConfiguration('cmdoutput') == 1) {
                    $nagiosCmd = nagioschecksCmd::byEqLogicIdAndLogicalId($this->getId(),$cmd->getLogicalId() . '_output');
                    if (!is_object($nagiosCmd)) {
                        $nagiosCmd = new nagioschecksCmd();
                        $nagiosCmd->setName(__($_name, __FILE__));
                        $nagiosCmd->setEqLogic_id($this->getId());
                        $nagiosCmd->setEqType('nagioschecks');
                        $nagiosCmd->setLogicalId($cmd->getLogicalId() . '_output');
                        $nagiosCmd->setType('info');
                        $nagiosCmd->setSubType('string');
                        $nagiosCmd->setTemplate("mobile",'line' );
                        $nagiosCmd->setTemplate("dashboard",'line' );
                        $nagiosCmd->setConfiguration("type",'output' );
                        $nagiosCmd->setConfiguration("cmdlink",$cmd->getLogicalId());
                        $nagiosCmd->save();
                    }
                    $this->checkAndUpdateCmd($cmd->getLogicalId() . '_output', $output[0]);
                }


                //Traitement métriques
                if (strpos($output[0], '|') !== false) {
                    $metric = substr($output[0], 0, strpos($output[0], '|'));
                    $cmd->setConfiguration('hasMetric', '1');
                    //log::add('nagioschecks', 'debug', $metric);
                }
                $cmd->save();

            }
        }
        return ;
    }

}

class nagioschecksCmd extends cmd {
    public function execute($_options = null) {
        if ($_options['option'] == 'status') {
            return $this->getConfiguration('status');
        } else if ($_options['option'] == 'code') {
            return $this->getConfiguration('code');
        } else {
            return $this->getConfiguration('value');
        }
    }

}

?>
