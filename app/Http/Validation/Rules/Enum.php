<?php

namespace App\Http\Validation\Rules;

use DB;

class Enum
{

    public static function validate($attribute, $value, $parameters)
    {
        if (isset($parameters[0])) {
            $table      = $parameters[0];
            $column     = $parameters[1] ?? $attribute;
            $connection = $parameters[2] ?? null;

            return in_array($value, self::getEnumValues($table, $column, $connection));
        }

        return false;
    }


    public static function getEnumValues($table, $column, $connection = null)
    {
        try {
            $type = DB::connection($connection)->select(DB::connection($connection)
                                                          ->raw("SHOW COLUMNS FROM $table WHERE Field = '$column'"))[0]->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            $enum = [];
            foreach (explode(',', $matches[1]) as $value) {
                $v    = trim($value, "'");
                $enum = array_add($enum, $v, $v);
            }

            return $enum;
        } catch (\Exception $e) {
            return [];
        }
    }
}