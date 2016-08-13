<?php
class Extra_Utilities
{
    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function objectToArray($obj)
    {
        $array = array();
        foreach ($obj as $key => $value)
        {
            $array[$key] = $value;
        }
        return $array;
    }
}