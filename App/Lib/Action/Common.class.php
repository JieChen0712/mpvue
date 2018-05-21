<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Common
 *
 * @author Administrator
 */
class Common {

    /**
     * 
     * @param type $data 要连表的数据
     * @param type $rel_tabel_name 要连表的表名
     * @param type $rel_field 关联字段
     * @param type $search_id   
     * @return string
     */
    public function get_related_data($data, $rel_tabel_name, $rel_field,$search_id='id') {
        if (!$data) {
            return;
        }
        $ids = [];
        $rel_data = [];
        if (!is_array($rel_field)) {
            foreach ($data as $v) {
                $ids[] = $v[$rel_field];
            }
        } else {
            foreach ($rel_field as $field) {
                foreach ($data as $v) {
                     $ids[] = $v[$field];
                }
            }
        }
        array_unique($ids);
        if ($ids) {
            $rel_info = M($rel_tabel_name)->where([$search_id => ['in', $ids]])->select();
            
            //因为代理表使用太多，增加判断用于显示总部
            if( $rel_tabel_name == 'distributor' ){
                $rel_info[] = [
                    'id'    =>  0,
                    'name'  =>  '总部',
                    'wechatnum' =>  '总部',
                    'levname'   =>  '总部',
                ];
            }
            
            foreach ($rel_info as $info) {
                $rel_data[$info[$search_id]] = $info;
            }
            if (!is_array($rel_field)) {
                foreach ($data as $k => $v) {
                    $data[$k][$rel_field.'_info'] = $rel_data[$v[$rel_field]];
                }
            } else {
                foreach ($rel_field as $field) {
                    foreach ($data as $k => $v) {
                         $data[$k][$field.'_info'] = $rel_data[$v[$field]];
                     } 
                }
            }
        }
        return $data;
    }
    
    /**
     * 数据根据pid排序
     * @param $data
     * @param int $pid
     * @param int $level
     * @return array
     * add by qjq
     */
    public function sortt($data,$pid=0,$level=0){
        static $arr=array();
        foreach ($data as $k => $v) {
            if($v['pid']==$pid){
                $v['level']=$level;
                $arr[]=$v;
                $this->sortt($data,$v['id'],$level+1);
            }
        }
        return $arr;

    }
}
