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
     * �������� ���������� ��������
     * @return bool
     */
    public function isSuccess(){
        return empty($this->arErrors);
    }

    /**
     * ��������� ������� ������
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
     * ������������ ������ � �������� ���������� ��������
     * @param Error $error
     */
    public function setError(Error $error)
    {
        $this->arErrors[] = $error;
    }

    /**
     * ��������� ���������� ���������� ��������
     * @param $result
     */
    public function setResult($result){
        $this->result = $result;
    }

    /**
     * ���������� ��������� ���������� ��������
     * @return null
     */
    public function getResult(){
        return $this->result;
    }

    /**
     * �������� �������������� ������
     * @param $name
     * @param $value
     */
    public function setMore($name, $value){
        $this->arMore[$name] = $value;
    }

    /**
     * ���������� ������ �������������� ������ ��� ����������� ������������ �������� ���� �� ���������� ����� null
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