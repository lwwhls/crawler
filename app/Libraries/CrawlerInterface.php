<?php
/**
 * Created by PhpStorm.
 * User: hudongbaike
 * Date: 2018/4/20
 * Time: 9:59
 */
namespace App\Libraries;

interface CrawlerInterface {
    public function handelList($html);
    public function handelContent($html);
    public function listRules();
}