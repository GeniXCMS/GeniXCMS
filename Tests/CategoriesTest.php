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
            return true;
        }else{
            return false;
        }
        
    }
}
