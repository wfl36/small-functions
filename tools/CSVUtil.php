<?php

/**
 * author      : wangfenglei
 * createTime  : 2017/9/11 9:52
 * description : 提供csv读写
 */
class CSVUtil
{
    /**
     * 根据数组获取内容
     * @param $arr
     */
    public static function contentFromArray($arr)
    {
        $content = '';
        foreach ($arr as $a1) {
            foreach ($a1 as $k => $a2) {
                $a1[$k] = str_replace([',',"\n","\r"],[', ','。 ','。 '],$a2);
            }
            $content .= implode(',',$a1) . "\n";
        }
    }

    /**
     * 从文件中读取。如果指定了每列的格式，直接返回带key的数组
     * @param $src
     * @param array $rowKeys
     * @return array
     * @throws Exception
     */
    public static function read($src,$rowKeys = [])
    {
        if (!file_exists($src)) {
            throw new Exception("csv源文件不存在{$src}",1);
        }
        $ret = [];
        $content = file_get_contents($src);
        $row = explode("\n",$content);
        foreach ($row as $r) {
            if (trim($r) === '') {
                continue;
            }
            $row = explode(',',$r);
            if ($rowKeys) {
                $count = count($row);
                $keyCount = count($rowKeys);
                if ($count > $keyCount) {
                    for ($i = 0;$i < ($count - $keyCount);$i++) {
                        $rowKeys[] = $i;
                    }
                } else if ($count < $keyCount) {
                    $row = array_merge($row,array_fill(0,$keyCount - $count),'');
                }
                $row = array_combine($rowKeys,$row);
            }
            $ret[] = $row;
        }
        return $ret;
    }

    /**
     * 保存文件
     * @param $arr
     * @param $dir
     * @param $fileName
     * @return string
     */
    public static function save($arr,$dir,$fileName)
    {
        $content = self::contentFromArray($arr);
        if (!file_exists($dir)) {
            mkdir($dir,0777,true);
        }
        file_get_contents("{$dir}/{$fileName}",$content);
        return "{$dir}/{$fileName}";
    }
    /**
     * 读取CSV文件
     * @param string $csv_file csv文件路径
     * @param int $lines       读取行数
     * @param int $offset      起始行数
     * @return array|bool
     */
    public static function read_csv_lines($csv_file = '', $lines = 0, $offset = 0)
    {
        if (!$fp = fopen($csv_file, 'r')) {
            return false;
        }
        $i = $j = 0;
        while (false !== ($line = fgets($fp))) {
            if ($i++ < $offset) {
                continue;
            }
            break;
        }
        $data = array();
        while (($j++ < $lines) && !feof($fp)) {
            $data[] = fgetcsv($fp);
        }
        fclose($fp);
        return $data;
    }
    /**
     * 导出CSV文件
     * @param array $data        数据
     * @param array $header_data 首行数据
     * @param string $file_name  文件名称
     * @return string
     */
    public static function export_csv_1($data = [], $header_data = [], $file_name = '')
    {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $file_name);
        if (!empty($header_data)) {
            echo iconv('utf-8','gbk//TRANSLIT','"'.implode('","',$header_data).'"'."\n");
        }
        foreach ($data as $key => $value) {
            $output = array();
            $output[] = $value['id'];
            $output[] = $value['name'];
            echo iconv('utf-8','gbk//TRANSLIT','"'.implode('","', $output)."\"\n");
        }
    }
    /**
     * 导出CSV文件
     * @param array $data        数据
     * @param array $header_data 首行数据
     * @param string $file_name  文件名称
     * @return string
     */
    public static function export_csv_2($data = [], $header_data = [], $file_name = '')
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$file_name);
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a');
        if (!empty($header_data)) {
            foreach ($header_data as $key => $value) {
                $header_data[$key] = iconv('utf-8', 'gbk', $value);
            }
            fputcsv($fp, $header_data);
        }
        $num = 0;
        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;
        //逐行取出数据，不浪费内存
        $count = count($data);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $num++;
                //刷新一下输出buffer，防止由于数据过多造成问题
                if ($limit == $num) {
                    ob_flush();
                    flush();
                    $num = 0;
                }
                $row = $data[$i];
                foreach ($row as $key => $value) {
                    $row[$key] = iconv('utf-8', 'gbk', $value);
                }
                fputcsv($fp, $row);
            }
        }
        fclose($fp);
    }
}