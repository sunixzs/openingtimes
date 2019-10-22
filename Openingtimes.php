<?php
declare(strict_types=1);
abstract class Openingtimes
{
    /**
     * @var array
     */
    protected const WEEKDAYS = [
        "monday",
        "tuesday",
        "wednesday",
        "thursday",
        "friday",
        "saturday",
        "sunday"
    ];

    /**
     * Path to textfile with definitions.
     *
     * @var string
     */
    protected $configurationFile = __DIR__ . "/Data/Openingtimes.txt";

    /**
     * Language patterns which will be parsed through sprintf() in child classes.
     * 
     * today_[...] is for OpeningtimesToday
     * weekday_[...] is for OpeningtimesWeek
     * future_[...] is for OpeningtimesFuture
     * 
     * [...]_oneTime_[...] means, that there is only one value for the day: "opened today from 09:00 to 19:00 o`clock" 
     * [...]_twoTimes_[...] means, that there are two values - p.e. for AM and PM: "opened today from 09:00 to 12:00 and from 13:00 to 18:00 o`clock" 
     * 
     * [...]_singletime means, that only one time is defined: "opened today until {time}"
     * [...]_doubletime means, there are two times to mark from/to: "opened today from {start} to {end}
     * [...]_string means, there is no time but a string: "today {string}"
     * 
     * @var array
     */
    public $language = [
        // today
        "today_oneTime_singletime" => 'Heute geöffnet bis <span class="openingtimes__value">%1$s Uhr</span>',
        "today_oneTime_doubletime" => 'Heute geöffnet <span class="value">%1$s–%2$s Uhr</span>',
        "today_oneTime_string" => 'Heute %1$s',
        "today_twoTimes_partOne_singletime" => 'Heute geöffnet <span class="openingtimes__value">%1$s Uhr</span>',
        "today_twoTimes_partOne_doubletime" => 'Heute geöffnet <span class="openingtimes__value">%1$s–%2$s</span>',
        "today_twoTimes_partOne_string" => 'Heute %1$s',
        "today_twoTimes_partTwo_singletime" => ' und <span class="openingtimes__value">%1$s Uhr</span>',
        "today_twoTimes_partTwo_doubletime" => ' und <span class="openingtimes__value">%1$s–%2$s Uhr</span>',
        "today_twoTimes_partTwo_string" => ' und %1$s',

        // weekday-labels
        "monday" => "Montag",
        "tuesday" => "Dienstag",
        "wednesday" => "Mittwoch",
        "thursday" => "Donnerstag",
        "friday" => "Freitag",
        "saturday" => "Samstag",
        "sunday" => "Sonntag",

        // values in weekdays
        "weekday_oneTime_singletime" => 'bis <span class="openingtimes__value">%1$s Uhr</span>',
        "weekday_oneTime_doubletime" => '<span class="openingtimes__value">%1$s–%2$s Uhr</span>',
        "weekday_oneTime_string" => '%1$s',
        "weekday_twoTimes_partOne_singletime" => '<span class="openingtimes__value">%1$s Uhr</span>',
        "weekday_twoTimes_partOne_doubletime" => '<span class="openingtimes__value">%1$s–%2$s Uhr</span>',
        "weekday_twoTimes_partOne_string" => '%1$s',
        "weekday_twoTimes_partTwo_singletime" => ' und <span class="openingtimes__value">%1$s Uhr</span>',
        "weekday_twoTimes_partTwo_doubletime" => ' und <span class="openingtimes__value">%1$s–%2$s Uhr</span>',
        "weekday_twoTimes_partTwo_string" => ' %1$s',

        // values in future entries
        "future_oneTime_singletime" => 'bis <span class="openingtimes__value">%1$s Uhr</span>',
        "future_oneTime_doubletime" => '<span class="openingtimes__value">%1$s–%2$s Uhr</span>',
        "future_oneTime_string" => '%1$s',
        "future_twoTimes_partOne_singletime" => '<span class="openingtimes__value">%1$s Uhr</span>',
        "future_twoTimes_partOne_doubletime" => '<span class="openingtimes__value">%1$s–%2$s Uhr</span>',
        "future_twoTimes_partOne_string" => '%1$s',
        "future_twoTimes_partTwo_singletime" => ' und <span class="openingtimes__value">%1$s Uhr</span>',
        "future_twoTimes_partTwo_doubletime" => ' und <span class="openingtimes__value">%1$s–%2$s Uhr</span>',
        "future_twoTimes_partTwo_string" => ' %1$s',
    ];

    

    /**
     * Parsed configuration from file.
     * Don't modify this array!
     *
     * @var array
     */
    protected $configuration = [
        // the value for today
        "today" => "", 

        // each weekday
        "weekdays" => [
            "monday" => [],
            "tuesday" => [],
            "wednesday" => [],
            "thursday" => [],
            "friday" => [],
            "saturday" => [],
            "sunday" => []
        ],

        // some html from configuration file
        "tooltip" => "",

        // dates in the future
        "future" => []
    ];

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
     * Abstract method to render the output.
     * The only method you've to define in child classes.
     * 
     * @see example classes
     * @return string HTML
     */
    abstract public function render();

    /**
     * Parses the configuration file into an array.
     * 
     * @return void
     * @throws \Exception if configuration file not found.
     */
    public function parseConfiguration()
    {
        if (!is_file($this->configurationFile)) {
            throw new \Exception("Could not find file '" . $this->configurationFile . "'!");
        }
        
        // read the file into an array
        $lines = file($this->configurationFile);
        
        // iterate the lines and build configuration
        $todayObj = new \DateTime();
        $todayObj->setTime(12, 0, 0);
        $weekday = strtolower($todayObj->format("l"));
        $datekey = $todayObj->format("Y-m-d");
        $tooltips = [];
        foreach ($lines as $line) {
            // skip empty lines and configuration lines
            if (!trim($line) || substr(trim($line), 0, 1) === "#") {
                continue;
            }
            
            // get the key and the value (divided by =)
            $equalPos = strpos($line, "=");
            $key = strtolower(trim(substr($line, 0, $equalPos)));
            $value = trim(substr($line, $equalPos + 1));
            
            // something went wrong: skip
            if (!($key && $value)) {
                continue;
            }
            
            if ($key === $datekey) {
                // today from YYYY-MM-DD
                $this->configuration["today"] = $this->determineValue($value);
            } elseif ($key === $weekday) {
                // today from weekday
                $this->configuration["today"] = $this->determineValue($value);
            } elseif (preg_match("/^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/", $key)) {
                // day by YYYY-MM-DD
                $dtObj = new \DateTime($key);
                $dtObj->setTime(12, 0, 0);
                if ($dtObj && $dtObj > $todayObj) {
                    $this->configuration["future"][$key]["values"] = $this->determineValue($value);
                    $this->configuration["future"][$key]["DateTime"] = $dtObj;
                }
            }
            
            if (in_array($key, self::WEEKDAYS)) {
                // weekday
                $this->configuration["weekdays"][$key] = $this->determineValue($value);
            }

            if ($key === "tooltip") {
                // tooltip
                $tooltips[] = $value;
            }
        }
        
        // sort future by date key
        ksort($this->configuration["future"]);
        
        // merge tooltip lines into one line
        $this->configuration["tooltip"] = implode("", $tooltips);
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
                $retArr[$num] = array(
                    "type" => "singletime",
                    "value" => $part
                );
            } elseif (preg_match("/^([0-9]|[0-1][0-9]|2[0-3]):([0-5][0-9])-([0-9]|[0-1][0-9]|2[0-3]):([0-5][0-9])$/", $part)) {
                $timeParts = explode("-", $part);
                $retArr[$num] = [
                    "type" => "doubletime",
                    "value1" => $timeParts[0],
                    "value2" => $timeParts[1]
                ];
            } elseif ($part) {
                $retArr[$num] = [
                    "type" => "string",
                    "value" => $part
                ];
            }
        }
        
        return $retArr;
    }
}
