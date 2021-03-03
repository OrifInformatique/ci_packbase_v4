<?php

namespace Welcome\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
	public function index()
	{
		$data['title'] = "Welcome";

		/**
         * @todo TEST DATASET, TO BE REMOVED
         */
		$data['list_title'] = "Test de la vue items_list";

        $data['items'] = [
            ['id' => '1', 'name' => 'Item 1', 'inventory_nb' => 'ITM0001', 'buying_date' => '01.01.2020', 'warranty_duration' => '12'],
            ['id' => '2', 'name' => 'Item 2', 'inventory_nb' => 'ITM0002', 'buying_date' => '01.02.2020', 'warranty_duration' => '12'],
            ['id' => '3', 'name' => 'Item 3', 'inventory_nb' => 'ITM0003', 'buying_date' => '01.03.2020', 'warranty_duration' => '12'],
            ['id' => '4', 'name' => 'Item 4', 'inventory_nb' => 'ITM0004', 'buying_date' => '01.04.2020', 'warranty_duration' => '12'],
            ['id' => '5', 'name' => 'Item 5', 'inventory_nb' => 'ITM0005', 'buying_date' => '01.05.2020', 'warranty_duration' => '12'],
            ['id' => '6', 'name' => 'Item 6', 'inventory_nb' => 'ITM0006', 'buying_date' => '01.06.2020', 'warranty_duration' => '12'],
            ['id' => '7', 'name' => 'Item 7', 'inventory_nb' => 'ITM0007', 'buying_date' => '01.07.2020', 'warranty_duration' => '12'],
        ];

		$this->display_view(['Common\Views\items_list','Welcome\welcome_message'], $data);
	}
}
