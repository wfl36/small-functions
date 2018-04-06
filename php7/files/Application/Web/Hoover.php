<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/6/006
 * Time: 12:03
 * http://php.net/manual/zh/class.domdocument.php
 */
namespace Application\Web;

class Hoover
{
    public function getContent($url)
    {
        if (!$this->content) {
            if (stripos($url, 'http') !== 0) {
                $url = 'http://' . $url;
            }
            $this->content = new \DOMDocument('1.0', 'utf-8');
            $this->content->preserveWhiteSpace = false;
            //@符号用于过滤掉配置错误的网页生成的警告
            @$this->content->loadHTMLFile($url);
        }
        return $this->content;
    }

    public function getTags($url, $tag)
    {
        $count = 0;
        $result = array();
        $elements = $this->getContent($url)->getElementsByTagName($tag);
        foreach ($elements as $node) {
            $result[$count]['value'] = trim(preg_replace('/\s+/',' ',$node->nodeValue));
            if ($node->hasAttributes()) {
                foreach ($node->attributes as $name => $attr) {
                    $result[$count]['attributes'][$name] = $attr->value;
                }
            }
            $count++;
        }
        return $result;

    }

    public function getAttribute($url, $attr, $domain = NULL)
    {
        $retult = array();
        $elements = $this->getContent($url)->getElementsByTagName('*');
        foreach ($elements as $node) {
            if ($node->hasAttributes($attr)) {
                $value = $node->getAttribute($attr);
                if ($domain) {
                    if (stripos($value, $domain) !== false) {
                        $retult[] = trim($value);
                    }
                } else {
                    $retult[] = trim($value);
                }
            }
        }
        return $retult;
    }
}