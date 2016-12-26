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

    public function postAjax() {
        foreach ($this->getCmd() as $cmd) {
            $cmd->setTemplate("mobile",$widget );
            $cmd->setTemplate("dashboard",$widget );
            $cmd->setSubType("binary");
            $cmd->save();
        }
        $this->getInformations('all');
    }

    public function alertCmd($titre, $message) {
        if ($this->getConfiguration('alert','') != '') {
            $cmd = cmd::byId(str_replace('#','',$this->getConfiguration('alert')));
            $options['title'] = 'Alerte sur ' . $titre;
            $options['message'] = $titre . " avec statut " . $message;
            $cmd->execCmd($options);
        }
    }

    public function getInformations($cron) {

        foreach ($this->getCmd() as $cmd) {
            $tempo = $cmd->getConfiguration('cron');
            if ($tempo == '') {
                $tempo = '15';
            }
            if ($cmd->getConfiguration('cron') == $cron || 'all' == $cron) {
                $alert = $cmd->getConfiguration('alert');
                if ($alert == '') {
                    $alert = 0;
                }
                $notifalert = $cmd->getConfiguration('notifalert');

                $cline = $cmd->getConfiguration('check') . ' ' . $cmd->getConfiguration('options');
                $cline = ($cmd->getConfiguration('ssh') == '1') ? $this->getConfiguration('sshpath') . $cline : '/usr/lib/nagios/plugins/' . $cline;
                $cline = ($cmd->getConfiguration('sudo') == '1') ? 'sudo ' . $cline : $cline;

                if ($ssh == '1') {
                    $cline = '/usr/lib/nagios/plugins/check_by_ssh -H ' . $this->getConfiguration('sshhost') . ' -l ' . $this->getConfiguration('sshuser') . ' -p ' . $this->getConfiguration('sshport') . ' -i ' . $this->getConfiguration('sshkey') . ' -C "' . $cline . '"';
                }
                log::add('nagioschecks', 'debug', 'Command : ' . $cline);
                unset($output);
                $output = array();
                exec($cline, $output, $return_var);
                //$return_var = '0';
                if ($return_var == '2' && $notifalert != '') {
                    if ($alert > $notifalert) {
                        $this->alertCmd($cmd->getName(), $output[0]);
                    } else {
                        $newalerte = $alert + 1;
                        $cmd->setConfiguration('alert', $newalerte);
                    }
                } else {
                    if ($alert != '0') {
                        $cmd->setConfiguration('alert', '0');
                    }
                }
                if ($return_var == '0') {
                    $value = 1;
                } else {
                    $value = 0;
                }
                $cmd->setConfiguration('value', $value);
                $cmd->setConfiguration('code', $return_var);
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

}

class nagioschecksCmd extends cmd {
    public function execute($_options = null) {
        log::add('nagioschecks', 'info', 'Commande recue');
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
