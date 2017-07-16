<?php
require_once 'config.php';
use Kymyzek\WebPhpDemon\FileLock;

define('LOCK_FILE', WPD_WORK_DIR . DIRECTORY_SEPARATOR . 'pid.lock');

if (! FileLock::lock(LOCK_FILE, getmypid()))    //Блокировка, иначе выход
    exit;

use Kymyzek\WebPhpDemon\WebPhpDemon;
use Kymyzek\WebPhpDemon\WebPhpScript;
use Kymyzek\Logger\Logger;

$wpd = new WebPhpDemon();
$demon = $wpd->loadConfig();
while ('work' == $demon['command'])
{
    $set = array(
        'dir_logs'  =>  $demon['logs'],
        'initiator' =>  'controller',
        'out_ip'    =>  false,
    );
    $logger = new Logger($set);

    $start = time();
    $demon = $wpd->loadConfig();

    /* Получаем список скриптов */
    if ($handle = opendir($demon['params']))
    {
        $scripts = array();
        while (false !== ($process = readdir($handle))) {
            if (preg_match('/(.+)\.json$/', $process, $match))
                $scripts[$match[1]] = new WebPhpScript($match[1]);
        }
        closedir($handle);
    } else {
        $logger->error('Не создана рабочая среда');
        exit;
    }

    /* Запуск скриптов */
    foreach ($scripts as $interface => $wps)
    {
        /* Получаем параметры скрипта */
        $script_params = $wps->loadParams();

        /* Получаем статус скрипта */
        $script_states = $wps->loadStates();

        // Если скрипт ожидает запуска
        if ('wait' == $script_states['status'])
        {
            $time = time();
            // Если прошло не меньше чем wait
            if ($time >= $script_states['last_run'] + $script_params['wait'])
            {
                // Устанавливаем статус work и время последнего запуска скрипта
                $set = array(
                    'last_run'  => $time,   //Последний запуск скрипта time
                    'status'    => 'work',  //Текущее состояние
                );
                $wps->setStatus($set);

                // Запускаем скрипт
                chdir($script_params['dir']);
                if (is_file($script_params['name'])) {
                    exec('php ' . $script_params['name']);
                    if ('info' == $script_params['log'])
                        $logger->info($interface . ' -> запущен');
                } else
                    $logger->error('Нет файла ' . $script_params['name']);

                // Устанавливаем статус в wait
                $wps->setStatus('wait');
            }
        }
    }

    if (time() == $start) // Если скриптов нет, то собственная задержка
        sleep($demon['wait']);

}
FileLock::unlock();





