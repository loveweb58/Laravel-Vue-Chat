<?php

if ( ! function_exists('formatNumber')) {
    function formatNumber($number)
    {
        $number = preg_replace('/[^0-9\.]/Uis', '', $number);

        if (substr($number, 0, 2) == '00' && strlen($number) > 10) {
            return '995' . substr($number, 2);
        } elseif (substr($number, 0, 1) == '0' && substr($number, 0, 2) != '00') {
            return '995' . substr($number, 1);
        } elseif ((substr($number, 0, 1) == '5' || substr($number, 0, 1) == "7") && strlen($number) == 9) {
            return '995' . $number;
        } elseif (substr($number, 0, 1) == '2' && strlen($number) == 7) {
            return '99532' . $number;
        }

        return $number;
    }
}

if ( ! function_exists('requestId')) {
    function requestId($prefix = 'api')
    {
        return sha1(uniqid($prefix, true));
    }
}

if ( ! function_exists('mb_range')) {

    /**
     * multibyte string compatible range('A', 'Z')
     *
     * @param string $start Character to start from (included)
     * @param string $end   Character to end with (included)
     *
     * @return array list of characters in unicode alphabet from $start to $end
     * @author Rodney Rehm
     */
    function mb_range($start, $end)
    {
        // if start and end are the same, well, there's nothing to do
        if ($start == $end) {
            return [$start];
        }

        $_result = [];
        // get unicodes of start and end
        list(, $_start, $_end) = unpack("N*", mb_convert_encoding($start . $end, "UTF-32BE", "UTF-8"));
        // determine movement direction
        $_offset  = $_start < $_end ? 1 : -1;
        $_current = $_start;
        while ($_current != $_end) {
            $_result[] = mb_convert_encoding(pack("N*", $_current), "UTF-8", "UTF-32BE");
            $_current  += $_offset;
        }
        $_result[] = $end;

        return $_result;
    }

}

if ( ! function_exists('jqxFilters')) {
    /**
     * @param $query \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     * @param $callback
     */
    function jqxFilters($query, $callback = null)
    {
        foreach (request('filterGroups', []) as $filters) {
            $query->where(function ($q) use ($filters, $callback) {
                /**
                 * @var $q \Illuminate\Database\Query\Builder
                 * @var $q \Illuminate\Database\Eloquent\Builder
                 */
                foreach ($filters['filters'] as $filter) {
                    if ($callback instanceof Closure) {
                        $filter = $callback($q, $filter);
                        if (is_null($filter)) {
                            continue;
                        }
                    }
                    switch ($filter['condition']) {
                        case "CONTAINS":
                            $filter['condition'] = "LIKE";
                            $filter['value']     = "%{$filter['value']}%";
                            break;
                        case "DOES_NOT_CONTAIN":
                            $filter['condition'] = "NOT LIKE";
                            $filter['value']     = "%{$filter['value']}%";
                            break;
                        case "EQUAL":
                            $filter['condition'] = "=";
                            break;
                        case "NOT_EQUAL":
                            $filter['condition'] = "<>";
                            break;
                        case "GREATER_THAN":
                            $filter['condition'] = ">";
                            break;
                        case "LESS_THAN":
                            $filter['condition'] = "<";
                            break;
                        case "GREATER_THAN_OR_EQUAL":
                            $filter['condition'] = ">=";
                            break;
                        case "LESS_THAN_OR_EQUAL":
                            $filter['condition'] = "<=";
                            break;
                        case "STARTS_WITH":
                            $filter['condition'] = "LIKE";
                            $filter['value']     = "{$filter['value']}%";
                            break;
                        case "ENDS_WITH":
                            $filter['condition'] = "LIKE";
                            $filter['value']     = "%{$filter['value']}";
                            break;
                        case "NULL":
                            $filter['condition'] = "IS NULL";
                            $filter['value']     = "%{$filter['value']}%";
                            break;
                        case "NOT_NULL":
                            $filter['condition'] = "IS NOT NULL";
                            $filter['value']     = "%{$filter['value']}%";
                            break;
                    }
                    if ($filter['operator'] == "and") {
                        $q->where($filter['field'], $filter['condition'], $filter['value']);
                    } else {
                        $q->orWhere($filter['field'], $filter['condition'], $filter['value']);
                    }
                }
            });
        }
    }
}
if ( ! function_exists('string_sanitize')) {
    function string_sanitize($s)
    {
        $result = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($s, ENT_QUOTES));

        return $result;
    }
}

if ( ! function_exists('conversation_hash')) {
    function conversation_hash($account_id, array $members)
    {
        sort($members);

        return sha1(strtolower($account_id . "|" . json_encode($members)));
    }
}

if ( ! function_exists('detectDelimiter')) {
    /**
     * @param string $csvFile Path to the CSV file
     *
     * @return string Delimiter
     */
    function detectDelimiter($csvFile)
    {
        $delimiters = [
            ';'  => 0,
            ','  => 0,
            "\t" => 0,
            "|"  => 0,
        ];

        $handle    = fopen($csvFile, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }
}