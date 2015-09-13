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
        $expectedResult = true;
        $db = Db::insert($vars);
        if($db) {
            $result = true;
        }else{
            $result = false;
        }
        $this->assertEquals($expectedResult, $result);
    }
    
    public function deleteCategoriesTest () {
        
        $expectedResult = true;
        $db = Categories::delete(1);
        if($db) {
            $result = true;
        }else{
            $result = false;
        }
        $this->assertEquals($expectedResult, $result);
    }
}
