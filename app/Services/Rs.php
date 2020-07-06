<?php

namespace App\Services;

use DB;
use SoapClient;

class Rs
{

    protected $url = "http://services.rs.ge/WayBillService/WayBillService.asmx?wsdl";

    protected $user = "USER";

    protected $key = "PASSWORD";

    protected $client;


    public function __construct()
    {
        $this->client = new SoapClient($this->url, ['encoding' => 'UTF-8']);
    }


    public function getNameFromTin($tin)
    {
        $name = DB::table('rs_data')->where('tin', $tin)->first(['name']);
        if ($name) {
            return $name->name;
        }
        $rsName = $this->request('get_name_from_tin', ['tin' => $tin])->get_name_from_tinResult;
        if ($rsName != "") {
            DB::table('rs_data')->insert(['tin' => $tin, 'name' => $rsName]);
        }

        return $rsName;
    }


    public function request($method, $data)
    {
        $data = array_merge($data, ['su' => $this->user, 'sp' => $this->key]);

        return $this->client->$method($data);
    }

}