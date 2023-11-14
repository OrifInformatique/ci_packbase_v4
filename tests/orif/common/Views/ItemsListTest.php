<?php
namespace Common\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;

class ItemsListTest extends CIUnitTestCase
{

    use ControllerTestTrait;
    public function testTitleIsShown(): void
    {
        $data = self::getDefaultData();
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertSee($data['list_title'], 'h3');
    }

    public function testDefaultNameId(): void
    {
        $data = self::getDefaultData();
        $data['primary_key_field'] = null;
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $keys = array_keys($data['columns']);
        $result->assertSee($data['items'][0][$keys[0]]);
    }

    public function testTitleIsHidden(): void
    {
        $data = self::getDefaultData();
        $list_title = $data['list_title'];
        $data['list_title'] = null;
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertDontSee($list_title, 'h3');
    }

    public function testCreateButtonShown(): void
    {
        $data = self::getDefaultData();
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertSee($data['btn_create_label'], 'a');
        $result->assertSeeLink($data['btn_create_label']);
        #$result->assertSee($data['btn_create_label'], 'a .btn .btn-primary');
        #$result->assertSeeElement($data['url_create']);
        #$result->assertSeeElement('.btn .btn-primary');

    }

    public function testCreateDefaultLabelButtonShown(): void
    {
        $data = self::getDefaultData();
        $data['btn_create_label'] = null;
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertSee(lang('common_lang.btn_add'), 'a');
        $result->assertSeeLink(lang('common_lang.btn_add'));
        #$result->assertSee($data['btn_create_label'], 'a .btn .btn-primary');
        #$result->assertSeeElement($data['url_create']);
        #$result->assertSeeElement('.btn .btn-primary');

    }

    
    public function testCreateButtonHidden(): void
    {
        $data = self::getDefaultData();
        $data['url_create'] = null;
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertDontSee($data['btn_create_label'], 'a');

    }

    public function testCheckboxShown(): void
    {
        $data = self::getDefaultData();
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertSee(lang('common_lang.btn_show_disabled'), 'label');
    }

    public function testCheckboxHidden(): void
    {
        $data = self::getDefaultData();
        $data['with_deleted'] = null;
        $data['url_getView'] = null;
        $data['deleted_field'] = null;
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertDontSee(lang('common_lang.btn_show_disabled'), 'label');
    }

    public function testDetailsIconShown(): void
    {
        $this->testIconShown('common_lang.btn_details');
    }

    public function testDetailsIconHidden(): void
    {
        $this->testIconHidden('common_lang.btn_details', 'url_detail');
    }

    public function testUpdateIconShown(): void
    {
        $this->testIconShown('common_lang.btn_edit');
    }

    public function testUpdateIconHidden(): void
    {
        $this->testIconHidden('common_lang.btn_edit', 'url_update');
    }

    public function testDuplicateIconShown(): void
    {
        $this->testIconShown('common_lang.btn_copy');
    }

    public function testDuplicateIconHidden(): void
    {
        $this->testIconHidden('common_lang.btn_copy', 'url_duplicate');
    }

    public function testDeleteIconShown(): void
    {
        $this->testIconShown('common_lang.btn_delete');
    }

    public function testDeleteIconHidden(): void
    {
        $this->testIconHidden('common_lang.btn_delete', 'url_delete');
    }

    public function testRestoreIconShown(): void
    {
        $this->testIconShown('common_lang.btn_restore');
    }

    public function testRestoreIconHidden(): void
    {
        $this->testIconHidden('common_lang.btn_restore', 'url_restore');
    }

    public function testRestoreIconHiddenWhenDateNull(): void
    {
        $data = $this->getDefaultData();
        $data['items'][1]['deleted'] = null;
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertDontSee(lang('common_lang.btn_restore'));
    }

    public function testRedDeleteIconShown(): void
    {
        $this->testIconShown('common_lang.btn_hard_delete');
    }

    public function testRedDeleteIconHidden(): void
    {
        $this->testIconHidden('common_lang.btn_hard_delete', 'url_delete');
    }

    public function testRedDeleteIconHiddenWhenDateNull(): void
    {
        $data = $this->getDefaultData();
        $data['items'][1]['deleted'] = null;
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertDontSee(lang('common_lang.btn_hard_delete'));
    }

    private function testIconShown(string $titleKey): void
    {
        $data = $this->getDefaultData();
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertSee(lang($titleKey));
    }

    private function testIconHidden(string $titleKey, string $urlKey): void
    {
        $data = $this->getDefaultData();
        $data[$urlKey] = null;
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertDontSee(lang($titleKey));
    }

    public function testArrangementColumnsName(): void
    {
        $data = $this->getDefaultData();
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $pattern = array_reduce($data['columns'], fn($carry, $name) =>
            "$carry$name.*", '');
        $this->assertEquals(1, preg_match("/$pattern/s", $response));
    }

    public function testArrangementValuesByColumns(): void
    {
        $data = $this->getDefaultData();
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $columnsValues = array_map(fn($key) => $data['items'][0][$key],
            array_keys($data['columns']));
        $pattern = array_reduce($columnsValues, fn($carry, $value) =>
            $carry.preg_quote($value, '/').'.*', '');
        $this->assertEquals(1, preg_match("/$pattern/s", $response));
    }

    public function testWhenColumnNotInItemData():void
    {
        $data = $this->getDefaultData();
        $data['columns']['fake'] = 'fakevalue';
        $result = $this->controller(Test::class)
                       ->execute('display_view', '\Common\items_list', $data);
        $response = $result->response()->getBody();
        $result->assertSee($data['columns']['fake']);
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
                'warranty_duration' => '12 months', 'deleted' => '',
                'buying_date' => '01/01/2020'
            ],
            [
                'id' => '12', 'name' => 'Item 12', 'inventory_nb' => 'ITM0012',
                'buying_date' => '01/03/2020',
                'warranty_duration' => '12 months', 'deleted' => '2000-01-01',
            ]
        ];
        $data['primary_key_field']  = 'id';
        $data['btn_create_label']   = 'Add an item';
        $data['with_deleted']       = true;
        $data['deleted_field']      = 'deleted';
        $data = array_merge($data, self::getDefaultUrlData());
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
