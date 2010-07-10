<?php

global $valid_fan;

## Supermicro Fanspeeds
if ($device['os'] == "linux") 
{
  $oids = snmp_walk($device, "1.3.6.1.4.1.10876.2.1.1.1.1.3", "-OsqnU", "SUPERMICRO-HEALTH-MIB");
  if ($debug) { echo($oids."\n"); }
  $oids = trim($oids);
  if ($oids) echo("Supermicro ");
  $type = "supermicro";
  foreach(explode("\n", $oids) as $data) 
  {
    $data = trim($data);
    if ($data) 
    {
      list($oid,$kind) = explode(" ", $data);
      $split_oid = explode('.',$oid);
      $index = $split_oid[count($split_oid)-1];
      if ($kind == 0)
      {
        $fan_oid       = "1.3.6.1.4.1.10876.2.1.1.1.1.4.$index";
        $descr_oid     = "1.3.6.1.4.1.10876.2.1.1.1.1.2.$index";
        $limit_oid     = "1.3.6.1.4.1.10876.2.1.1.1.1.6.$index";
        $precision_oid = "1.3.6.1.4.1.10876.2.1.1.1.1.9.$index";
        $monitor_oid   = "1.3.6.1.4.1.10876.2.1.1.1.1.10.$index";
        $descr         = snmp_get($device, $descr_oid, "-Oqv", "SUPERMICRO-HEALTH-MIB");
        $current       = snmp_get($device, $fan_oid, "-Oqv", "SUPERMICRO-HEALTH-MIB");
        $limit         = snmp_get($device, $limit_oid, "-Oqv", "SUPERMICRO-HEALTH-MIB");
#        $precision     = snmp_get($device, $precision_oid, "-Oqv", "SUPERMICRO-HEALTH-MIB");
# This returns an incorrect precision. At least using the raw value... I think. -TL
        $precision     = 1;
        $monitor       = snmp_get($device, $monitor_oid, "-Oqv", "SUPERMICRO-HEALTH-MIB");
        $descr         = str_replace(' Fan Speed','',$descr);
        $descr         = str_replace(' Speed','',$descr);
                
        if ($monitor == 'true')
        {
          echo discover_fan($valid_fan,$device, $fan_oid, $index, $type, $descr, $precision, $limit, NULL, $current);
        }
      }
    }
  }
}

?>