<?php

/*
Cache whole database into system memory and share among other scripts & websites
WARNING: Please make sure your system have sufficient RAM to enable this feature
 */

// $db = new \IP2Location\Database('./databases/IP-COUNTRY-SAMPLE.BIN', \IP2Location\Database::SHARED_MEMORY);

/*
Cache the database into memory to accelerate lookup speed
WARNING: Please make sure your system have sufficient RAM to enable this feature
 */
// $db = new \IP2Location\Database('./databases/IP-COUNTRY-SAMPLE.BIN', \IP2Location\Database::MEMORY_CACHE);

/*
Default file I/O lookup
 */
class IPModel extends baseModel
{

    public $records = "";
    public $last_ip = "";
    public $db = "";
    public $type = 'FILE';

    public function __construct()
    {
    }
    public function init($type = "FILE_IO")
    {
        if ($type == 'FILE_IO') {
            require_once 'IP2Location.php';

            $this->db = new \IP2Location\Database(DIR_FRAMEWORK . DS . 'model/ip/databases/IP2LOCATION-LITE-DB11.BIN', \IP2Location\Database::FILE_IO);
            $this->type = 'FILE';
        }
        if ($type == 'SHARED_MEMORY') {
            require_once 'IP2Location.php';

            $this->db = new \IP2Location\Database(DIR_FRAMEWORK . DS . 'model/ip/databases/IP2LOCATION-LITE-DB11.BIN', \IP2Location\Database::SHARED_MEMORY);
            $this->type = 'FILE';
        }
        if ($type == 'MEMORY_CACHE') {
            require_once 'IP2Location.php';

            $this->db = new \IP2Location\Database(DIR_FRAMEWORK . DS . 'model/ip/databases/IP2LOCATION-LITE-DB11.BIN', \IP2Location\Database::MEMORY_CACHE);
            $this->type = 'FILE';
        }
        if ($type == 'DATABASE') {
            $this->db = framework::cm()->get('com.database');
            $this->type = 'DATABASE';
        }
    }

    public function findCountryCodeByIP($ip)
    {
        $ret = $this->find($ip);
        return $ret['countryCode'];
    }
    public function findCountryByIP($ip)
    {
        $ret = $this->find($ip);
        return $ret['countryName'];
    }

    public function findRegionByIP($ip)
    {
        $ret = $this->find($ip);
        return $ret['regionName'];
    }

    public function findCityByIP($ip)
    {
        $ret = $this->find($ip);
        return $ret['cityName'];
    }

    public function findTimeZoneByIP($ip)
    {
        $ret = $this->find($ip);
        return $ret['timeZonee'];
    }

    public function findIspByIP($ip)
    {
        $ret = $this->find($ip);
        return $ret['isp'];
    }

    public function findNumberByIP($ip)
    {
        $ret = $this->find($ip);
        return $ret['ipNumber'];
    }

    public function findAllByIP($ip)
    {
        $ret = $this->find($ip);
        return $ret;
    }

    /*
    echo '<pre>';
    echo 'IP Number             : ' . $records['ipNumber'] . "\n";
    echo 'IP Version            : ' . $records['ipVersion'] . "\n";
    echo 'IP Address            : ' . $records['ipAddress'] . "\n";
    echo 'Country Code          : ' . $records['countryCode'] . "\n";
    echo 'Country Name          : ' . $records['countryName'] . "\n";
    echo 'Region Name           : ' . $records['regionName'] . "\n";
    echo 'City Name             : ' . $records['cityName'] . "\n";
    echo 'Latitude              : ' . $records['latitude'] . "\n";
    echo 'Longitude             : ' . $records['longitude'] . "\n";
    echo 'Area Code             : ' . $records['areaCode'] . "\n";
    echo 'IDD Code              : ' . $records['iddCode'] . "\n";
    echo 'Weather Station Code  : ' . $records['weatherStationCode'] . "\n";
    echo 'Weather Station Name  : ' . $records['weatherStationName'] . "\n";
    echo 'MCC                   : ' . $records['mcc'] . "\n";
    echo 'MNC                   : ' . $records['mnc'] . "\n";
    echo 'Mobile Carrier        : ' . $records['mobileCarrierName'] . "\n";
    echo 'Usage Type            : ' . $records['usageType'] . "\n";
    echo 'Elevation             : ' . $records['elevation'] . "\n";
    echo 'Net Speed             : ' . $records['netSpeed'] . "\n";
    echo 'Time Zone             : ' . $records['timeZone'] . "\n";
    echo 'ZIP Code              : ' . $records['zipCode'] . "\n";
    echo 'Domain Name           : ' . $records['domainName'] . "\n";
    echo 'ISP Name              : ' . $records['isp'] . "\n";
    echo '</pre>';
     */

    public function find($ip, $findAs = false)
    {
        if ($this->last_ip != $ip) {
            if ($this->type == 'DATABASE') {
                $ipNum = $this->ip2num($ip);
                if ($findAs) {
                    $ip_sql = "SELECT * FROM slave_ip2asn WHERE $ipNum<ip_to LIMIT 1";
                    $this->db->sqlCommand->exec($ip_sql);
                    $records = $this->db->sqlCommand->getResultByOneArray();
                } else {
                    $records = [];
                }
                $ip_sql = "SELECT * FROM slave_ip2loc WHERE $ipNum<ip_to LIMIT 1";
                $this->db->sqlCommand->exec($ip_sql);
                $result = $this->db->sqlCommand->getResultByOneArray();
                foreach ($result as $key => $value) {
                    $records[$key] = $value;
                }

            } else {
                $records = $this->db->lookup($ip, \IP2Location\Database::ALL);
                if ($findAs) {
                    $ipNum = $this->ip2num($ip);
                    $ip_sql = "SELECT * FROM slave_ip2asn WHERE $ipNum<ip_to LIMIT 1";
                    framework::cm()->get('com.database')->sqlCommand->exec($ip_sql);
                    $result = framework::cm()->get('com.database')->sqlCommand->getResultByOneArray();
                    foreach ($result as $key => $value) {
                        $records[$key] = $value;
                    }
                }

            }
            //var_dump($records);
            $this->records = $records;
            $this->last_ip = $ip;
        }
        return $this->records;
    }
    public function ip2num($ip)
    {
        $ip2int = ip2long($ip);
        return sprintf('%u', $ip2int);
    }

}
