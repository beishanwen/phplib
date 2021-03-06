<?php
/***************************************************************************
 *
 * Copyright (c) 2018 beishanwen.com, Inc. All Rights Reserved
 *
 **************************************************************************/

/**
 * @file ArrayUtils.php
 * @author strick@beishanwen.com
 * @date 2018/05/01 00:00:00
 * @brief 数组工具包
 */

namespace beishanwen\phplib\dt;

use beishanwen\phplib\math\GcdUtils;

class ArrayUtils
{
    /**
     * @brief 将数组里的某些元素，元素值为逗号隔开的字符串，转成数组格式
     * @author strick@beishanwen.com
     * @param $arr
     * @param $keys
     * @return array
     */
    public static function String2Array($arr = array(), $keys = array())
    {
        foreach ($arr as &$data) {
            foreach ($keys as $key) {
                if (isset($data[$key])) {
                    if ($data[$key] != '') {
                        $data[$key] = explode(",", $data[$key]);
                    } else {
                        $data[$key] = array();
                    }

                }
            }
        }
        return $arr;
    }

    /**
     * @brief 检查数组里的某些元素是否为数字格式
     * @author strick@beishanwen.com
     * @param $arr
     * @param $keys
     * @param $ignore_values
     * @return boolean
     */
    public static function isNumeric($arr = array(), $keys = array(), $ignore_values = array())
    {
        foreach ($keys as $key) {
            if (in_array($arr[$key], $ignore_values)) {
                continue;
            }
            if (!is_numeric($arr[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @brief 检查数组里的某些元素是否为数组格式
     * @author strick@beishanwen.com
     * @param $arr
     * @param $keys
     * @return boolean
     */
    public static function isArray($arr = array(), $keys = array())
    {
        foreach ($keys as $key) {
            if (!is_array($arr[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @brief 检查数组B是否包含 A
     * @author strick@beishanwen.com
     * @param $arr_a
     * @param $arr_b
     * @return boolean
     */
    public static function arrayInArray($arr_a = array(), $arr_b = array())
    {
        foreach ($arr_a as $val) {
            if (!in_array($val, $arr_b)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @brief 二维数组排序
     * @author strick@beishanwen.com
     * @param $targetArr
     * @param $key
     * @param bool|false $isDesc
     * @return mixed
     */
    public static function doubleDimensionalArraySort($targetArr, $key, $isDesc = false)
    {
        if ($isDesc) {
            $sortType = 'SORT_DESC';
        } else {
            $sortType = 'SORT_ASC';
        }
        $sort = array(
            'direction' => $sortType, // 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
            'field' => $key,       // 排序字段
        );

        $arrSort = array();
        foreach ($targetArr as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }

        if ($sort['direction']) {
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $targetArr);
        }

        return $targetArr;
    }

    /**
     * @brief 根据权重随机抽样
     * @author strick@beishanwen.com
     * @param $data array 待抽样的数组
     * @param $col_name string 权重字段名，字段值为整数
     * @return array | boolean
     */
    public static function randomSelection($data, $col_name = 'weight')
    {
        $weight = 0;
        $temp_data = array();

        // 得到最大公约数
        $arr_weight = array_column($data, $col_name);
        $max_common_divisor = GcdUtils::gcd_array($arr_weight);

        foreach ($data as $index => $one) {
            $one['random_selection_index'] = $index; // 存储索引位置，以便删除该随机数组使用
            if ($max_common_divisor) {
                // 对 weight 除以最大公约数，减小数组$temp_data大小
                $one_weight = intval(intval($one[$col_name]) / $max_common_divisor);
            } else {
                $one_weight = intval($one[$col_name]);
            }
            if (isset($one_weight)) {
                $weight += $one_weight;
                for ($i = 0; $i < $one_weight; $i++) {
                    $temp_data[] = $one;
                }
            } else {
                $weight += 1;
                $temp_data[] = $one;
            }
        }
        if (empty($temp_data)) {
            return false;
        }
        $use = rand(0, $weight - 1);
        $one = $temp_data[$use];
        unset($temp_data);
        return $one;
    }
}
