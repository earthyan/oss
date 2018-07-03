<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/28
 * Time: 14:26
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class SqlConfig extends Model
{
    protected $table = 'oss_sql_config';
    protected $connection = '';

}