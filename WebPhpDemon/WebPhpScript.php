<?php
/**
 * Class for scripts
 *
 * @version 1.01
 * @package WebPhpDemon
 * @class WebPhpScript
 * @extends WebPhpDemon
 * @require Kymyzek\Logger\Logger
 * @author Alex Maximov <alex.maximov.freelance@gmail.com>
 *
 */
namespace Kymyzek\WebPhpDemon;

use Kymyzek\Logger\Logger;

class WebPhpScript extends WebPhpDemon
{
    // Параметры по умолчанию
    private $param = array(
        'interface'     =>  '',//Основной идентификатор интерфейса
        'dir'           =>  '',  //Корневая директория скрипта
        'name'          =>  '',  //Имя скрипта
        'wait'          =>  60,  //Ожидание до следующего запуска в секундах
        'log'           =>  'error',  //если info - будет выводится лог запуска, иначе только ошибки
    );

    // Стартовые значения скрипта, wait - ожидание запуска
    private $states = array(
        'last_run'      =>  0,       //Последний запуск скрипта time
        'status'        =>  'wait',  //Текущее состояние
    );

    private $demon; // Массив параметров демона

    /**
     * WebPhpScript конструктор.
     * @param $interface
     */
    function __construct($interface) {  //Без интерфейса не работаем
        parent::__construct();
        $this->demon = parent::loadConfig();
        if (empty($interface)) {
            $set = array(
                'dir_logs'  =>  $this->demon['logs'],
                'initiator' =>  'WebPhpScript',
                'out_ip'    =>  false,
            );
            $logger = new Logger($set);
            $logger->fatal('Interface is empty');
            exit;
        }
        $this->param['interface'] = $interface;
    }

    /**
     * @param $param
     * Установка параметров запуска скрипта
     */
    public function params($param) {
        $this->param = $this->loadParams();
        $this->param = array_merge($this->param, $param);
        $this->__saveParams();
    }

    /**
     * @return array|mixed
     */
    public function loadParams() {
        $file = $this->demon['params'] . DIRECTORY_SEPARATOR . $this->param['interface'] . '.json';
        if (is_file($file))
            return json_decode(file_get_contents($file), true);
        return $this->param;
    }

    /**
     * Записываем параметры в файл
     */
    private function __saveParams() {
        $file = $this->demon['params'] . DIRECTORY_SEPARATOR . $this->param['interface'] . '.json';
        file_put_contents($file, json_encode($this->param));
    }

    /**
     * @param $status
     * Изменение статуса скрипта. Может принимать массив, либо данные для параметра 'status'
     */
    public function setStatus($status) {
        if (empty($status))
            return;
        if (is_array($status)) // Если переданные параметы являются массивом
            $this->states = array_merge($this->states, $status);
        else
            $this->states['status'] = $status;
        $this->__saveStates();
    }

    /**
     * @return array|mixed
     * Чтение файла статуса скрипта
     */
    public function loadStates() {
        $file = $this->demon['states'] . DIRECTORY_SEPARATOR . $this->param['interface'] . '.json';
        if (is_file($file))
            return json_decode(file_get_contents($file), true);
        /* Если файла нет, создаем его */
        $this->__saveStates();
        return $this->states;
    }

    /**
     *  Сохранение статуса скрипта
     */
    private function __saveStates() {
        $file = $this->demon['states'] . DIRECTORY_SEPARATOR . $this->param['interface'] . '.json';
        file_put_contents($file, json_encode($this->states));
    }

}