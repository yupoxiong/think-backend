<?php
/**
 * 后台tree（左侧菜单，菜单列表，权限操作）相关
 */

namespace app\admin\traits;

use app\admin\model\AdminUser;
use think\Model;

trait AdminTreeTrait
{

    public $text, $html;
    /**
     * 生成树型结构所需要的2维数组
     * @var array
     */

    public $array;

    /**
     * 生成树型结构所需修饰符号，可以换成图片
     * @var array
     */
    public $icon = ['│', '├', '└'];

    /** @var string 空格 */
    public $space = '&nbsp;&nbsp;';

    /** @var string 结果 */
    public $result = '';

    /**
     * 构造函数，初始化类
     * @param array $arr
     */
    public function initTree($arr = []): void
    {
        $this->array  = $arr;
        $this->result = '';
        $this->html   = '';
    }

    /**
     * 得到树型结构
     * @param int $my_id ，表示获得这个ID下的所有子级
     * @param string $str 生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
     * @param int $sid 被选中的ID，比如在做树型下拉框的时候需要用到
     * @param string $adds
     * @param string $str_group
     * @return string
     */
    public function getTree($my_id, $str, $sid = 0, $adds = '', $str_group = '')
    {
        $parent_id = '';
        $n_str     = '';
        $number    = 1;
        $child     = $this->getChild($my_id);

        if (is_array($child)) {
            $total = count($child);
            foreach ($child as $id => $value) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->icon[2];
                } else {
                    $j .= $this->icon[1];
                    $k = $adds ? $this->icon[0] : '';
                }
                $spacer   = $adds ? $adds . $j : '';
                $selected = $id == $sid ? 'selected' : '';
                extract($value, null);
                if (0 === $parent_id && $str_group) {
                    eval("\$n_str = \"$str_group\";");
                } else {
                    eval("\$n_str = \"$str\";");
                }
                $this->result .= $n_str;
                $space        = $this->space;
                $this->getTree($id, $str, $sid, $adds . $k . $space, $str_group);
                $number++;
            }
        }
        return $this->result;
    }

    /**
     * 获取后台左侧菜单
     * @param $my_id
     * @param $current_id
     * @param $parent_ids
     * @return mixed
     */
    public function get_authTree($my_id, $current_id, $parent_ids)
    {

        $n_str = '';
        $child = $this->getChild($my_id);
        if (is_array($child)) {
            $menu = current($child);
            //获取当前等级的html
            $text = $this->text[$menu['level']] ?? end($this->text);

            foreach ($child as $k => $v) {
                extract($v, null);

                //如果有下级菜单
                if ($this->getChild($k)) {

                    if (in_array($k, $parent_ids, true)) {
                        //如果下级菜单是当前页面
                        eval("\$n_str = \"$text[1]\";");
                        $this->html .= $n_str;
                    } else {
                        //如果下级菜单不是当前页面
                        eval("\$n_str = \"$text[0]\";");
                        $this->html .= $n_str;
                    }


                    self::get_authTree($k, $current_id, $parent_ids);
                    eval("\$n_str = \"$text[2]\";");
                    $this->html .= $n_str;
                } else if ($k === $current_id) {
                    $a = $this->text['current'];
                    eval("\$n_str = \"$a\";");

                    $this->html .= $n_str;
                } else {
                    $a = $this->text['other'];
                    eval("\$n_str = \"$a\";");
                    $this->html .= $n_str;
                }
            }
        }

        return $this->html;
    }

    /**
     * 得到子级数组
     * @param int $pid
     * @return bool|array
     */
    public function getChild($pid)
    {
        $result = [];
        foreach ($this->array as $key => $value) {
            if ($value['parent_id'] === $pid) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * 递归获取级别
     * @param int $id ID
     * @param array $array 所有菜单
     * @param int $i 所在级别
     * @return int
     */
    public function getLevel($id, $array, $i = 0): int
    {
        if ($array[$id]['parent_id'] === 0 || empty($array[$array[$id]['parent_id']]) || $array[$id]['parent_id'] === $id) {
            return $i;
        }

        $i++;
        return self::getLevel($array[$id]['parent_id'], $array, $i);
    }

    //获取权限树
    public function getAuthTreeAccess($my_id): string
    {
        $id    = '';
        $n_str = '';
        $child = $this->getChild($my_id);

        if (is_array($child)) {
            $level = current($child);
            $text  = $this->text[$level['level']] ?? end($this->text);

            foreach ($child as $k => $v) {
                extract($v, null);

                if ($this->getChild($k)) {
                    eval("\$n_str = \"$text[0]\";");
                    $this->html .= $n_str;

                    self::getAuthTreeAccess($k);

                    eval("\$n_str = \"$text[1]\";");
                    $this->html .= $n_str;
                } else {
                    $a = $this->text['other'];
                    eval("\$n_str = \"$a\";");
                    $this->html .= $n_str;
                }
            }
        }
        return $this->html;
    }


    /**
     * 获取左侧菜单
     * @param $menu
     * @param int $current_id
     * @return mixed
     */
    protected function getLeftMenu($menu, $current_id = 1)
    {
        $max_level  = 0;
        $parent_ids = array(0 => 0);


        foreach ($menu as $k => $v) {
            if ($v['id'] === $current_id) {
                $parent_ids = $this->getMenuParent($menu, $v['id']);
                $current_id = $v['id'];
            }
        }

        if ($parent_ids === false) {
            $parent_ids = array(0 => 0);
        }

        foreach ($menu as $k => $v) {
            $url               = url($v['url'])->build();
            $menu[$k]['icon']  = $v['icon'];
            $menu[$k]['level'] = $this->getLevel($v['id'], $menu);
            $max_level         = $max_level <= $menu[$k]['level'] ? $menu[$k]['level'] : $max_level;
            $menu[$k]['url']   = $url;
        }

        $this->initTree($menu);

        $text_base_one   = "<li class='nav-item has-treeview";

        $text_base_two   = "'><a href='#' class='nav-link' >
<i class='\$icon'></i>
<p>\$name<i class='right fas fa-angle-left'></i></p>

                             </a>
                             <ul class='nav nav-treeview";
        $text_open       = ' menu-open';
        $text_base_three = "'>";

        $text_base_four = "<li class='nav-item'>
                            <a class='nav-link ";
        $text_base_five="' href='\$url'>
                            <i class='nav-icon \$icon'></i>
                            <p>\$name</p>
                            </a>
                         </li>";

        // 有子菜单，不打开
        $text_0       = $text_base_one . $text_base_two . $text_base_three;

        //echo $text_0.'<br/><br/><br/>';

        // 有子菜单，打开
        $text_1       = $text_base_one  .$text_open . $text_base_two  . $text_base_three;
        //echo $text_1.'<br/><br/><br/>';

        $text_2       = '</ul></li>';
        //echo $text_2.'<br/><br/><br/>';

        // 当前的子菜单
        $text_current = $text_base_four .' active ' . $text_base_five;
        //echo $text_current.'<br/><br/><br/>';

        //其他的子菜单
        $text_other   = $text_base_four . $text_base_five;
        //echo $text_other.'<br/><br/><br/>';

        for ($i = 0; $i <= $max_level; $i++) {
            $this->text[$i]['0'] = $text_0;
            $this->text[$i]['1'] = $text_1;
            $this->text[$i]['2'] = $text_2;
        }
        $this->text['current'] = $text_current;
        $this->text['other']   = $text_other;

        return $this->get_authTree(0, $current_id, $parent_ids);
    }

    //获取父级菜单
    protected function getMenuParent($arr, $my_id, $parent_ids = array())
    {
        $a = array();
        if (is_array($arr)) {
            foreach ($arr as $id => $a) {
                if (($a['id'] === $my_id) && $a['parent_id'] !== 0) {
                    $parent_ids[] = $a['parent_id'];
                    $parent_ids   = $this->getMenuParent($arr, $a['parent_id'], $parent_ids);
                }
            }
        }
        return !empty($parent_ids) ? $parent_ids : false;
    }

    //获取当前导航
    protected function getCurrentNav($arr, $my_id, $parent_ids = array(), $current_nav = '')
    {
        $a = array();
        if (is_array($arr)) {
            foreach ($arr as $id => $a) {
                if (($a['id'] === $my_id) && $a['parent_id'] !== 0) {
                    $parent_ids[] = $a['parent_id'];
                    $ru           = '<li><a><i class="' . $a['icon'] . '"></i> ' . $a['name'] . '</a></li>';
                    $current_nav  = $ru . $current_nav;
                    $temp_result  = $this->getCurrentNav($arr, $a['parent_id'], $parent_ids, $current_nav);
                    $parent_ids   = $temp_result[0];
                    $current_nav  = $temp_result[1];
                }
            }
        }
        return !empty([$parent_ids, $current_nav]) ? [$parent_ids, $current_nav] : false;
    }


    /**
     * 获取树形数据列表
     * @param Model $model
     * @return string
     */
    protected function getTreeList($model)
    {

        $data = $model->column('id,name,parent_id', 'id');

        foreach ($data as $key => $value) {
            //左侧选择
            $data[$key]['select_html'] = '<td>
                                <input type="checkbox" onclick="checkThis(this)" name="dataCheckbox"
                                       data-id="' . $value['id'] . '" class="checkbox dataListCheck" value="' . $value['id'] . '"
                                       placeholder="选择/取消">
                            </td>';

            //右侧操作
            $data[$key]['todo_html'] = '<td class="td-do">
                                <a href="' . url('edit', ['id' => $value['id']]) . '"
                                   class="btn btn-primary btn-xs" title="修改" data-toggle="tooltip">
                                    <i class="fas fa-pencil"></i>
                                </a>
                                <a class="btn btn-danger btn-xs AjaxButton" data-toggle="tooltip" title="删除"
                                   data-id="' . $value['id'] . '" data-confirm-title="删除确认"
                                   data-confirm-content=\'您确定要删除ID为 <span class="text-red">' . $value['id'] . '</span> 的数据吗\'
                                    data-url="' . url('del') . '" >
                                    <i class="fas fa-trash"></i>
                                </a>
                                 </td>';

        }


        $this->initTree($data);
        $html = "<tr>
                           \$select_html
                           
                            <td>\$id</td>
                            <td>\$spacer \$name</td>
                            
                            \$todo_html
                        </tr>";

        return $this->getTree(0, $html);
    }


    /**
     * 获取树形选择列表
     * @param Model $model
     * @param int $selected
     * @return string
     */
    protected function getSelectList($model, $selected = 0)
    {
        $data = $model->column('id,parent_id,name', 'id');

        $html = "<option value='\$id' \$selected >\$spacer \$name</option>";
        $this->initTree($data);
        return $this->getTree(0, $html, $selected);
    }




}