<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Version details
 *
 * @package    report
 * @copyright  Vas Vince
 */
defined('MOODLE_INTERNAL') || die;

$plugin->version   = 2023041701;         // The current plugin version (Date: YYYYMMDDXX)
$plugin->requires  = 2015051100;         // Requires this Moodle version (see https://docs.moodle.org/dev/Releases)
$plugin->component = 'local_modstats';  // Full name of the plugin
$plugin->maturity  = MATURITY_STABLE;         // Requires this Moodle version (see https://docs.moodle.org/dev/Releases)
$plugin->release  = 'v3.2';         // Requires this Moodle version (see https://docs.moodle.org/dev/Releases)
$plugin->cron     = 0;
