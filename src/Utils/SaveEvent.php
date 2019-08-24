<?php

namespace App\Utils;

/**
 * Class VarExportArray
 * @package App\Utils
 */
class SaveEvent
{
    const DELIMITER = "||";

    private $filename;
    private $resource;

    /**
     * @param string $filename
     * @return VarExportArray
     */
    public function setFile(string $filename): self
    {
        $this->filename = $filename;
        $this->resource = fopen($filename, 'a+');

        return $this;
    }

    /**
     * Put data to file
     *
     * @param $data
     */
    private function PutContents(string $data)
    {
        $data .= "\n";
        return fwrite($this->resource, $data, mb_strlen($data));
    }

    /**
     * For get data from file
     *
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        if ($this->resource) {
            while (($buffer = fgets($this->resource, 4096)) !== false) {
                $explode = explode(self::DELIMITER, $buffer);
                if($explode[0] == $key){
                    return true;
                }
            }
            if (!feof($this->resource)) {
                echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
            }
        }
        return null;
    }

    /**
     * For add key with data to file
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public function add(string $key, $value): bool
    {
        $data = $key . self::DELIMITER . serialize($value);
        return $this->PutContents($data);
    }
}