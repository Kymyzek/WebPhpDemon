<?php
/**
 * Class for lock file
 *
 * @version 1.01
 * @package WebPhpDemon
 * @class FileLock
 * @author Alex Maximov <alex.maximov.freelance@gmail.com>
 *
 */
namespace Kymyzek\WebPhpDemon;


class FileLock
{
    protected static $file;

    /**
     * lock file
     * @param $file
     * @param $massage
     * @return bool
     */
    static function lock($file, $massage) {
        if (empty($file))
            return false;
        if ($fp = fopen($file, 'c'))
        {
            if (flock($fp, LOCK_EX | LOCK_NB))
            {
                ftruncate($fp, 0);
                fwrite($fp, $massage);
                self::$file = $fp;
                return true;
            }
        }
        return false;
    }

    /**
     * unlock file
     */
    static function unlock() {
        fflush(self::$file);
        flock(self::$file, LOCK_UN);
    }

}