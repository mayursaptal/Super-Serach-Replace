<?php
if (!defined('ABSPATH')) {
    die('DEAD END');
}
/**
 * Super Search Replace
 *
 * @package           SuperSearchReplace
 * @author            Mayur Saptal
 * @copyright         2020 Mayur Saptal
 * @license           GPL-2.0-or-later
 * 
 */


require_once  __DIR__ . DIRECTORY_SEPARATOR . 'SSRMysql.php';

if (!class_exists('SuperSearchReplace')) {

    class SuperSearchReplace
    {

        public $search_for;
        public $replace_with;
        public $file_filter = '/\.php$|\.css$|\.js$|\.html$|\.txt$/';
        public $log_file;
        public $log_dir_path;
        public $log_file_path;
        public $exclude_files = array();
        private $mysql;

        function __construct()
        {
            ignore_user_abort(true);
            set_time_limit(0);
            $this->mysql = new SSRMysql(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            $this->log_file = "Temp_log.txt";
            $this->exclude_files = array(__file__);
        }

        public  function setExcludeFiles($files)
        {
            $this->exclude_files =  array_merge($this->exclude_files, $files);
            return $this;
        }

        public function setLogPath($upload_dir)
        {
            $this->log_dir_path = $upload_dir . DIRECTORY_SEPARATOR . 'SuperSearchReplace';
            // $this->log_file = date('Y-m-d--H-i-s') . '-' . $this->randomString(12) . '.txt';
            $this->log_file_path = $this->log_dir_path . DIRECTORY_SEPARATOR . $this->log_file;
            $dir_exists = $this->makeDirs($this->log_dir_path);
            if ($dir_exists) {
                @unlink($this->log_file_path);
                file_put_contents($this->log_file_path, "\n Created " . date('Y-m-d H:i:s'), FILE_APPEND);
            }
            return $this;
        }

        public function setSearchFor($search_for)
        {
            $this->search_for = $search_for;
            return  $this;
        }

        public  function setReplaceWith($replace_with)
        {
            $this->replace_with = $replace_with;
            return  $this;
        }

        public  function setFileFilter($file_filter)
        {
            $this->file_filter = $file_filter;
            return $this;
        }

        private function setLog($msg)
        {
            file_put_contents($this->log_file_path, "\n " . date('Y-m-d--H-i-s') . ' - ' . $msg, FILE_APPEND);
            return  $this;
        }


        public  function searchReplace()
        {
            return $this->updateFiles()->updateMysqlTabels();
        }

        public function getLog()
        {
            return file_get_contents($this->log_file_path);
        }

        public function getLogEnd($upload_dir, $lines = 2)
        {
            $this->log_dir_path = $upload_dir . DIRECTORY_SEPARATOR . 'SuperSearchReplace';
            $this->log_file_path = $this->log_dir_path . DIRECTORY_SEPARATOR . $this->log_file;
            return join(PHP_EOL, array_slice(explode("\n", file_get_contents($this->log_file_path)), -$lines));
        }


        private function getMysqlTablesCloumns()
        {
            $result =  $this->mysql->query(" SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS ");
            return $result;
        }

        private  function getDirContents($dir, $filter = '', &$results = array())
        {
            $files = scandir($dir);
            foreach ($files as $key => $value) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
                if (!is_dir($path)) {
                    if (empty($filter) || preg_match($filter, $path)) $results[] = $path;
                } elseif ($value != "." && $value != "..") {
                    $this->getDirContents($path, $filter, $results);
                }
            }
            return $results;
        }

        public function updateMysqlTabels()
        {
            $this->setLog('Updating  table  Search ' . $this->search_for . ' replace with ' . $this->replace_with);
            $tables_with_columns = $this->getMysqlTablesCloumns();
            $Search =  $this->search_for;
            $replace =  $this->replace_with;
            foreach ($tables_with_columns  as $tables_with_column) {
                $table = $tables_with_column['TABLE_NAME'];
                $column = $tables_with_column['COLUMN_NAME'];
                $query = " update $table set $column = replace($column, '$search', '$replace') where $column LIKE '%$search%' ";
                $result = $this->mysql->query($query);
                $this->setLog('Updated  Table  ' . $table . ' column ' . $column);
            }
            return $this;
        }

        public  function updateFiles()
        {
            $this->setLog('Updating file Search ' . $this->search_for . ' replace with ' . $this->replace_with);
            $files = $this->getDirContents(ABSPATH, $this->file_filter);
            $this->setExcludeFiles(array(
                $this->log_file_path
            ));
            if (is_array($files) &&  !empty($files)) {
                foreach ($files as $file) {
                    if (in_array($file, $this->exclude_files)) {
                        continue;
                    }
                    file_put_contents($file, str_replace($this->search_for,  $this->replace_with, file_get_contents($file)));
                    $this->setLog('Updated   file ' . $file);
                }
            }
            return $this;
        }

        private   function randomString($length)
        {
            $key = '';
            $keys = array_merge(range(0, 9), range('a', 'z'));

            for ($i = 0; $i < $length; $i++) {
                $key .= $keys[array_rand($keys)];
            }

            return $key;
        }

        private  function makeDirs($dirpath, $mode = 0777)
        {
            return is_dir($dirpath) || mkdir($dirpath, $mode, true);
        }

        public function clearLog()
        {
            file_put_contents($this->log_file_path, "completed!");
        }
    }
}
