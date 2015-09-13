class CategoriesTest extends PHPUnit_Framework_TestCase 
{
    public function addCategoriesTest () {
        $vars = array(
            'table' => 'cat',
            'key' => array(
                'name' => 'Categories',
                'parent' => '0',
                'type' => 'post'
            )
        );

        $db = Db::insert($vars);
        if($db) {
            $result = true;
        }else{
            $result = false;
        }
        $this->assertTrue($result);
    }
    
    public function deleteCategoriesTest () {
        $db = Categories::delete(1);
        if($db) {
            $result = true;
        }else{
            $result = false;
        }
        $this->assertTrue($result);
    }
}
