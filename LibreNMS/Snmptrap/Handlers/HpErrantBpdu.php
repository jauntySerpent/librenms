<?php
/**
 * HpErrantBpdu.php
 *
 * Handles the HP-ICF-OID::hpicfRpvstMIB trap //TODO: Fix trap name
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * @package    LibreNMS
 * @link       https://www.librenms.org
 * @copyright  2021 Caleb Seekell
 * @author     Caleb Seekell <cseekell@narragansettri.gov>
 */

namespace LibreNMS\Snmptrap\Handlers;

use App\Models\Device;
use LibreNMS\Interfaces\SnmptrapHandler;
use LibreNMS\Snmptrap\Trap;
use Log;

class HpErrantBpdu implements SnmptrapHandler
{
    /**
     * Handle snmptrap.
     * Data is pre-parsed and delivered as a Trap.
     * This trap handler handles the hpicfRpvstErrantBpduReceived trap
     * as defined in the HP-ICF-RPVST-MIB mib.
     *
     * @param Device $device
     * @param Trap $trap
     * @return void
     */
    public function handle(Device $device, Trap $trap)
    {
        // Get information from the raw trap data and store it in a variable.
        $VlanIndex = $trap->getOidData('HP-ICF-RPVST-MIB::hpicfRpvstVlanIndex');
        $PortNumber = $trap->getOidData('HP-ICF-RPVST-MIB::hpicfRpvstPortNumber');
        $PortVlanErrantBpduRxCount = $trap->getOidData("HP-ICF-RPVST-MIB::hpicfRpvstPortVlanErrantBpduRxCount.1.$PortNumber");        
        $PortVlanState = $trap->getOidData("HP-ICF-RPVST-MIB::hpicfRpvstPortVlanState.1.$PortNumber");
        $PortVlanDesigBridge = $trap->getOidData("HP-ICF-RPVST-MIB::hpicfRpvstPortVlanDesigBridge.1.$PortNumber");
        $DesignatedPort = $trap->getOidData('HP-ICF-RPVST-MIB::hpicfRpvstDesignatedPort');
        $ErrantBpduSrcMac = $trap->getOidData('HP-ICF-RPVST-MIB::hpicfRpvstErrantBpduSrcMac');
        $ErrantBpduDetector = $trap->getOidData('HP-ICF-RPVST-MIB::hpicfRpvstErrantBpduDetector');

        // Get the device name
        $DisplayName = $device->displayName();

        // Generate a log message with information from the trap
        Log::event("SNMP Trap: Device $DisplayName received an unexpected BPDU on port $PortNumber from $ErrantBpduSrcMac. The port has been disabled.", $device->device_id, 'trap', 5);
    }
}
