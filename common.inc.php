<?php

function filter($data) {
    $data = trim(htmlentities(strip_tags($data)));

    if (get_magic_quotes_gpc())
        $data = stripslashes($data);

    return $data;
}

// mege two arrays
function array_deep_merge() {
    switch (func_num_args()) {
        case 0 : return false;
            break;
        case 1 : return func_get_arg(0);
            break;
        case 2 :
            $args = func_get_args();
            $args[2] = array();
            if (is_array($args[0]) and is_array($args[1])) {
                foreach (array_unique(array_merge(array_keys($args[0]), array_keys($args[1]))) as $key)
                    if (is_string($key) and is_array($args[0][$key]) and is_array($args[1][$key]))
                        $args[2][$key] = array_deep_merge($args[0][$key], $args[1][$key]);
                    elseif (is_string($key) and isset($args[0][$key]) and isset($args[1][$key]))
                        $args[2][$key] = $args[1][$key];
                    elseif (is_integer($key) and isset($args[0][$key]) and isset($args[1][$key])) {
                        $args[2][] = $args[0][$key];
                        $args[2][] = $args[1][$key];
                    } elseif (is_integer($key) and isset($args[0][$key]))
                        $args[2][] = $args[0][$key];
                    elseif (is_integer($key) and isset($args[1][$key]))
                        $args[2][] = $args[1][$key];
                    elseif (!isset($args[1][$key]))
                        $args[2][$key] = $args[0][$key];
                    elseif (!isset($args[0][$key]))
                        $args[2][$key] = $args[1][$key];
                return $args[2];
            }
            else
                return $args[1]; break;
        default :
            $args = func_get_args();
            $args[1] = array_deep_merge($args[0], $args[1]);
            array_shift($args);
            return call_user_func_array('array_deep_merge', $args);
            break;
    }
}

// check table exist
//function checkTable($table) {
//    global $connection;
//    $check_table = $connection->prepare("DESC $table");
//    try {
//        $check_table->execute();
//    } catch (Exception $e) {
//        return false;
//    }
//    return true;
//}
//
//// create table
//function create_table($table) {
//    global $connection;
//    $create_table = $connection->prepare("CREATE TABLE `" . $table . "` (
//  `header` blob NOT NULL,
//  `deleted` tinyint(1) NOT NULL DEFAULT '0',
//  `last_served` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//  `file_size` bigint(20) NOT NULL,
//  `file_name` varchar(255) NOT NULL,
//  `original_url` varchar(255) NOT NULL,
//  `mime_type` text,
//  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//  PRIMARY KEY (`id`)
//) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1");
//
//    $create_table->execute();
//}

?>