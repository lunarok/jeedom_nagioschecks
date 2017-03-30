#!/usr/bin/env php
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
require_once dirname(__FILE__) . "../../../core/php/core.inc.php";
define('STATE_OK', 0);
define('STATE_WARNING', 1);
define('STATE_CRITICAL', 2);
define('STATE_UNKNOWN', 3);

$backup = repo_market::listeBackup();
if (strpos($backup[0], date('Y-m-d', time() - 60 * 60 * 24)) !== false || strpos($backup[0], date('Y-m-d')) !== false) {
    echo "OK backup récent présent sur le market\n";
    exit(STATE_OK);
} else {
    echo "KO pas de backup récent sur le market\n";
    exit(STATE_CRITICAL);
}
?>
