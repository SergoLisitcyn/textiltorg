<?

namespace Bxmaker\SmsNotice;

class Result {

    private $arErrors = array();
    private $result = null;
    private $arMore = array();

    public function __construct($result = null){
        if($result instanceof Error )
        {
            $this->setError($result);
            return;
        }
        $this->result = $result;
    }

    /**
     * Проверка успешности операции
     * @return bool
     */
    public function isSuccess(){
        return empty($this->arErrors);
    }

    /**
     * ПОлучение массива ошибок
     * @return array
     */
    public function getErrors(){
        return $this->arErrors;
    }

    public function getErrorMessages(){
        $ar = array();
        /**
         * @var Error $error
         */
        foreach($this->arErrors as $error)
        {
            $ar[] = /*(!!$error->getCode() ? $error->getCode() . ' ' : '') .*/ $error->getMessage();
        }
        return $ar;
    }

    /**
     * Фиксирвоание ошибки в процессе выполнения операции
     * @param Error $error
     */
    public function setError(Error $error)
    {
        $this->arErrors[] = $error;
    }

    /**
     * Установка результата выполнения операции
     * @param $result
     */
    public function setResult($result){
        $this->result = $result;
    }

    /**
     * Возвращает результат выполнения операции
     * @return null
     */
    public function getResult(){
        return $this->result;
    }

    /**
     * Передача дополнительных данных
     * @param $name
     * @param $value
     */
    public function setMore($name, $value){
        $this->arMore[$name] = $value;
    }

    /**
     * Возвращает массив дополнительных данных или конкретного именованного элемента если он существует иначе null
     * @param null $name
     *
     * @return array|mixed|null
     */
    public function getMore($name = null)
    {
        if($name === null) return $this->arMore;

        return (isset($this->arMore[$name]) ? $this->arMore[$name] : null);
    }
}