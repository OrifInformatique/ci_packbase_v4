<?php

namespace ListAuto\Controllers;

use App\Controllers\BaseController;

class ListAuto extends BaseController
{
    /**
     * Display a list of the items sent in parameter, with the options
     * desired by the caller.
     * 
     */
    public function getListView()
    {
        $data['title'] = "List auto module";
        $data['items'] = [
            ['id' => '1', 'name' => 'Item 1', 'inventory_nb' => 'ITM0001', 'buying_date' => '2020-01-01', 'warranty_duration' => '12']
        ];

        echo view('ListAuto\Views\list', $data);
    }
}