<?php
/**
 * Created by PhpStorm.
 * User: arimis
 * Date: 18-3-6
 * Time: 上午11:33
 */

defined('INTELLIGENT_SYS') or exit('Access Invalid!');
class merchantControl extends SystemControl
{
    public function __construct()
    {
        Tpl::setDirquna("crm");
    }

    public function indexOp()
    {
        Tpl::output("");
        Tpl::showpage("merchant.index");
    }
}