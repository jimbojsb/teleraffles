<?php
namespace Piano;
class Config
{
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $val) {
            if (is_array($val)) {
                $this->$key = new self($val);
            } else {
                $this->$key = $val;
            }
        }
    }

    public static function fromFile($configFile, $env)
    {
        $configOptions = include $configFile;
        if (is_array($configOptions)) {
            if ($env && isset($configOptions[$env])) {
                $envConfigOptions = $configOptions[$env];
                if ($envConfigOptions["inherit"]) {
                    $configOptions = array_merge_recursive($configOptions[$envConfigOptions["inherit"]], $envConfigOptions);
                } else {
                    $configOptions = $envConfigOptions;
                }
            }
            return new self($configOptions);
        }
    }
}