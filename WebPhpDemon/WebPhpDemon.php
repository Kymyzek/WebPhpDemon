<?php
/**
 * Class for work of Demon
 *
 * @version 1.01
 * @package WebPhpDemon
 * @class WebPhpDemon
 * @author Alex Maximov <alex.maximov.freelance@gmail.com>
 *
 */
namespace Kymyzek\WebPhpDemon;

class WebPhpDemon
{
    // Конфигурация по умолчанию
    private $demon = array(
        'name' => 'controller.php',//имя демона
        'dir' => __DIR__,// рабочий каталог работы демона
        'wait' => 1, // собственная задержка работы демона, активна при отсутствии задач, 1 сек
    );

    /**
     * WebPhpDemon constructor.
     */
    function __construct() {
        $dirs = array(
            'params'    =>  WPD_WORK_DIR . DIRECTORY_SEPARATOR . 'params',  //Каталог сохранения параметров скриптов
            'logs'      =>  WPD_WORK_DIR . DIRECTORY_SEPARATOR . 'logs',    //Логи работы
            'states'    =>  WPD_WORK_DIR . DIRECTORY_SEPARATOR . 'states',  //Каталог сохранения текущих статусов работы скриптов
        );
        //Создаем структуру каталогов если они отсутствуют
        foreach($dirs as $key => $dir) {
            if(!is_dir($dir)){
                mkdir($dir, 0777);
            }
        }
        $this->demon = array_merge($this->demon, $dirs);
    }

    /**
     *  Запуск демона
     */
    public function run() {
        $set = array(
            'command' => 'work',
        );
        $this->demon = $this->setParam($set);

        chdir($this->demon['dir']);
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            /* Система Windows */
            pclose(popen('start /B cmd /C "php '. $this->demon['name'] .' >NUL 2>NUL"', 'r'));
        } else {
            /* Система *nix */
            $command = 'php -f ' . $this->demon['name'] . ' > /dev/null 2>&1 &';
            exec($command);
        }
    }

    /**
     *  Остановка демона
     */
    public function stop() {
        $set = array(
            'command' => 'stop',
        );
        $this->demon = $this->setParam($set);
    }

    /**
     * Установка параметров конфигруации
     * @param $params
     * @return array|mixed
     */
    public function setParam($params) {
        $this->demon = $this->loadConfig();
        $this->demon = array_merge($this->demon, $params);
        $this->__saveConfig();
        return $this->loadConfig();
    }

    /**
     * Чтение конфигурации
     * @return array|mixed
     */
    public function loadConfig() {
        $file = WPD_WORK_DIR . DIRECTORY_SEPARATOR . 'config.json';
        if (is_file($file))
            return json_decode(file_get_contents($file), true);
        return $this->demon;
    }

    /**
     * Сохранение конфигурации
     */
    private function __saveConfig() {
        $file = WPD_WORK_DIR . DIRECTORY_SEPARATOR . 'config.json';
        file_put_contents($file,json_encode($this->demon));
    }

}