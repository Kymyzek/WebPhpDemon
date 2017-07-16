<?php
$self = 'test_1';
file_put_contents('test.txt',date('Y-m-d h:i:s').":$self\n", FILE_APPEND | LOCK_EX);