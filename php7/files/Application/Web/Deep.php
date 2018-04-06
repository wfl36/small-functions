<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6/006
 * Time: 13:26
 * http://php.net/manual/zh/language.generators.syntax.php
 */

namespace Application\Web;


class Deep
{
    protected $doamin;

    public function scan($url, $tag)
    {
        $vac = new Hoover();
        $scan = $vac->getAttribute($url, 'href', $this->getDomain($url));
        $reult = array();
        foreach ($scan as $subSite) {
            yield from $vac->getTags($subSite, $tag);
        }
        return $reult;
    }

    public function getDomain($url)
    {
        if (!$this->doamin) {
            $this->doamin = parse_url($url, PHP_URL_HOST);
        }
        return $this->doamin;
    }
}