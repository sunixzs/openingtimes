<?php
abstract class Openingtimes
{
    /**
     * Path to textfile with definitions.
     *
     * @var string
     */
    protected $configurationFile = "/Test/Data/Oeffnungszeiten.txt";

    /**
     * @var array
     */
    protected $language = array(
        // today
        "today_oneTime_singletime" => 'Heute geöffnet bis <span class="value">%1$s Uhr</span>',
        "today_oneTime_doubletime" => 'Heute geöffnet <span class="value">%1$s–%2$s Uhr</span>',
        "today_oneTime_string" => 'Heute %1$s',
        "today_twoTimes_partOne_singletime" => 'Heute geöffnet <span class="value">%1$s Uhr</span>',
        "today_twoTimes_partOne_doubletime" => 'Heute geöffnet <span class="value">%1$s–%2$s</span>',
        "today_twoTimes_partOne_string" => 'Heute %1$s',
        "today_twoTimes_partTwo_singletime" => ' und <span class="value">%1$s Uhr</span>',
        "today_twoTimes_partTwo_doubletime" => ' und <span class="value">%1$s–%2$s Uhr</span>',
        "today_twoTimes_partTwo_string" => ' und %1$s',

        // weekdays
        "monday" => "Montag",
        "tuesday" => "Dienstag",
        "wednesday" => "Mittwoch",
        "thursday" => "Donnerstag",
        "friday" => "Freitag",
        "saturday" => "Samstag",
        "sunday" => "Sonntag",

        // values in weekdays
        "weekday_oneTime_singletime" => 'bis <span class="value">%1$s Uhr</span>',
        "weekday_oneTime_doubletime" => '<span class="value">%1$s–%2$s Uhr</span>',
        "weekday_oneTime_string" => '%1$s',
        "weekday_twoTimes_partOne_singletime" => '<span class="value">%1$s Uhr</span>',
        "weekday_twoTimes_partOne_doubletime" => '<span class="value">%1$s–%2$s Uhr</span>',
        "weekday_twoTimes_partOne_string" => '%1$s',
        "weekday_twoTimes_partTwo_singletime" => ' und <span class="value">%1$s Uhr</span>',
        "weekday_twoTimes_partTwo_doubletime" => ' und <span class="value">%1$s–%2$s Uhr</span>',
        "weekday_twoTimes_partTwo_string" => ' und %1$s',
    );

    /**
     * @param string $key
     * @param string $value
     * 
     * @return self
     * 
     * @throws \Exception
     */
    public function setLanguage($key, $value) {
        if (!isset($this->language[$key])) {
            throw new \Exception("Could not find a key " . $key . " in language configuration!");
        }

        $this->language[$key] = $value;

        return $this;
    }

    /**
     * Parsed configuration from file.
     *
     * @var array
     */
    protected $configuration = [
        "today" => "",
        "weekdays" => [
            "monday" => [],
            "tuesday" => [],
            "wednesday" => [],
            "thursday" => [],
            "friday" => [],
            "saturday" => [],
            "sunday" => []
        ],
        "tooltip" => "",
        "future" => []
    ];

    /**
     * @return array
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * @param string $configurationFile
     * @param array $language
     */
    public function __construct($configurationFile = null)
    {
        if (is_string($configurationFile)) {
            $this->configurationFile = $configurationFile;
        }

        $this->parseConfiguration();
        //print_r($this->configuration);
    }

    /**
     * Abstract method to render the output
     *
     * @return void
     */
    abstract public function render();

    /**
     * Parses the configuration file into an array.
     * 
     * @return array
     * @throws \Exception
     */
    public function parseConfiguration()
    {
        if (is_file($this->configurationFile) === false) {
            throw new \Exception("Could not find file '" . $this->configurationFile . "'!");
        }
        
        // read the file into an array
        $lines = file($this->configurationFile);
        
        // iterate the lines and build configuration
        
        $todayObj = new \DateTime();
        $todayObj->setTime(12, 0, 0);
        $weekday = strtolower($todayObj->format("l"));
        $datekey = strtolower($todayObj->format("Y-m-d"));
        $tooltips = [];
        
        foreach ($lines as $line) {
            // skip empty lines and configuration lines
            if (!trim($line) || substr(trim($line), 0, 1) == "#") {
                continue;
            }
            
            // get the key and the value (divided by =)
            $equalPos = strpos($line, "=");
            $key = strtolower(trim(substr($line, 0, $equalPos)));
            $value = trim(substr($line, $equalPos + 1));
            
            if (!($key && $value)) {
                continue;
            }
            
            if ($key === $datekey) {
                $this->configuration["today"] = $this->determineValue($value);
            } elseif ($key === $weekday) {
                $this->configuration["today"] = $this->determineValue($value);
            } elseif (preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/", $key)) {
                $dtObj = new \DateTime($key);
                $dtObj->setTime(12, 0, 0);
                if ($dtObj && $dtObj > $todayObj) {
                    $this->configuration["future"][$key]["values"] = $this->determineValue($value);
                    $this->configuration["future"][$key]["DateTime"] = $dtObj;
                }
            }
            
            if (in_array($key, [
                "monday",
                "tuesday",
                "wednesday",
                "thursday",
                "friday",
                "saturday",
                "sunday"
            ])) {
                $this->configuration["weekdays"][$key] = $this->determineValue($value);
            }

            if ($key === "tooltip") {
                $tooltips[] = $value;
            }
        }
        
        ksort($this->configuration["future"]);
        
        $this->configuration["tooltip"] = implode("", $tooltips);
        
        return $this->configuration;
    }
    
    /**
     * Interprets the value defined in configuration file.
     * 
     * @param string $value
     * @return array
     */
    protected function determineValue($value)
    {
        $retArr = [];
        $parts = array_map("trim", explode("|", $value));

        foreach ($parts as $num => $part) {
            if (preg_match("/^([0-9]|[0-1][0-9]|2[0-3]):([0-5][0-9])$/", $part)) {
                $retArr[ $num ] = array(
                    "type" => "singletime",
                    "value" => $part
                );
            } elseif (preg_match("/^([0-9]|[0-1][0-9]|2[0-3]):([0-5][0-9])-([0-9]|[0-1][0-9]|2[0-3]):([0-5][0-9])$/", $part)) {
                $timeParts = explode("-", $part);
                $retArr[ $num ] = [
                    "type" => "doubletime",
                    "value1" => $timeParts[ 0 ],
                    "value2" => $timeParts[ 1 ]
                ];
            } elseif ($part) {
                $retArr[ $num ] = [
                    "type" => "string",
                    "value" => $part
                ];
            }
        }
        
        return $retArr;
    }
}
