<?php
namespace Common\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class ItemsListTest extends CIUnitTestCase
{

    use ControllerTestTrait;
    public function testTitleIsVisible()
    {
        $data = self::getDefaultData();
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response();
        $result->assertSee($data['list_title'], 'h3');
    }

    public function testTitleIsHide()
    {
        $data = self::getDefaultData();
        unset($data['list_title']);
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response();
        $result->assertSee('', 'h3');
    }

    public function testCreateButtonVisible()
    {
        $data = self::getDefaultData();
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response();
        $result->assertSeeLink($data['btn_create_label']);
        #$result->assertSee($data['btn_create_label'], 'a .btn .btn-primary');
        #$result->assertSeeElement($data['url_create']);
        $result->assertSeeElement('.btn .btn-primary');

    }

    private function getDefaultData(): array
    {
        $data['list_title'] = "Test items_list view";
        $data['columns'] = [
            'name' => 'Name', 'inventory_nb' => 'Inventory nb',
            'buying_date' => 'Buying date',
            'warranty_duration' => 'Warranty duration'
        ];
        $data['items'] = [
            [
                'id' => '1', 'name' => 'Item 1', 'inventory_nb' => 'ITM0001',
                'buying_date' => '01/01/2020',
                'warranty_duration' => '12 months', 'deleted' => ''
            ],
            [
                'id' => '12', 'name' => 'Item 12', 'inventory_nb' => 'ITM0012',
                'buying_date' => '01/03/2020',
                'warranty_duration' => '12 months', 'deleted' => '2000-01-01'
            ]
        ];
        $data['primary_key_field']  = 'id';
        $data['btn_create_label']   = 'Add an item';
        $data['with_deleted']       = true;
        $data['deleted_field']      = 'deleted';
        $data = array_merge($data, $this->getDefaultUrlData());
        return $data;
    }

    private static function getDefaultUrlData(): array
    {
        $data['url_detail'] = "items_list/detail/";
        $data['url_update'] = "items_list/update/";
        $data['url_delete'] = "items_list/delete/";
        $data['url_create'] = "items_list/create/";
        $data['url_getView'] = "items_list/display_item/";
        $data['url_restore'] = "items_list/restore_item/";
        $data['url_duplicate'] = "items_list/duplicate_item/";
        return $data;
    }


}
