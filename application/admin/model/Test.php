<?php 
namespace app\admin\model;

use think\Model;
/**
 * 
 */
class Test extends Model
{

    public function add($data)
    {
        $result = $this->validate('Test.add')->save($data);
        if ($result === false) {
            echo $this->getError();
        }
        exit;
    }
}

?>