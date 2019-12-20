<?

namespace Bxmaker\SmsNotice\Service;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bxmaker\SmsNotice\Error;
use Bxmaker\SmsNotice\Manager;
use Bxmaker\SmsNotice\ManagerTable;
use Bxmaker\SmsNotice\Result;
use Bxmaker\SmsNotice\Service;

Loc::loadMessages(__FILE__);


Class sms
{

//http://sms.ru/
//5 ��� �� ���� �����

    const REQUEST_SUCCESS = 'success';
    const REQUEST_ERROR = 'error';

    private $oHttp = null;

    private $arParams = array(
        'user'      => '',
        'pwd'       => '',
        'sadr'      => '',
        'test_mode' => ''
    );


    /**
     * �����������
     *
     * @param array $arParams
     * - string USER
     * - string PWD
     * - string SADR
     * - integer TEST_MODE
     */
    public function __construct($arParams = array())
    {
        if(is_null($this->oHttp))
        {
            $this->oHttp =  new \Bitrix\Main\Web\HttpClient();
        }

        if (array_key_exists('USER', $arParams))
            $this->arParams['user'] = trim($this->_getPreparedStr($arParams['USER']));

        if (array_key_exists('PWD', $arParams))
            $this->arParams['pwd'] = trim($this->_getPreparedStr($arParams['PWD']));

        if (array_key_exists('SADR', $arParams))
            $this->arParams['sadr'] = trim($this->_getPreparedStr($arParams['SADR']));
    }

    /**
     * ��������� �������� ���������
     *
     * @param      $name
     * @param null $default_value
     *
     * @return null
     */
    private function _getParam($name, $default_value = null)
    {
        return (isset($this->arParams[$name]) ? $this->arParams[$name] : $default_value);
    }


    /**
     * ���������
     *
     * @param $code
     *
     * @return mixed|string
     */
    private function _getMsg($code)
    {
        return GetMessage('bxmaker.smsnotice.sms.' . $code);
    }


    public function send($phone, $text, $arParams = array())
    {
        $result = new Result();
        $arParams['text'] = $this->_getPreparedStr($text);
        $arParams['to'] = $phone;

        $response = $this->_makeRequest('message/send', $arParams);
        $arRes = explode("\n", $response);

        if ($arRes[0] == $this->_getMsg('MSG_SEND_100')) {
            $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
            $result->setMore('params', array(
                'messageId' => (isset($arRes[1]) ? trim($arRes[1]) : '')
            ));
            $result->setMore('response', $response);
        } else {
            $result->setError(new Error($this->_getErrorDescription($arRes[0]), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array_merge($arParams, array(
                'response' => $response
            ))));
        }

        return $result;
    }

    /**
     * �������� �������
     * @return Result
     */
    public function getBalance()
    {
        $result = new Result();
        $response = $this->_makeRequest('balance', Array());

        $arRes = explode("\n", $response);
        if ($arRes[0] == $this->_getMsg('ERROR_DESCRIPTION_BALANCE_100')) {
            $result->setResult(floatval((isset($arRes[1]) ? $arRes[1] : 0)) . $this->_getMsg('SERVICE.CURRENCY'));
        } else {
            $result->setError(new Error($this->_getErrorDescription($response, 'balance'), \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array('response' => $response)));
        }
        return $result;
    }

    /**
     * ��������� ������ ��������� ������
     */
    public function notice()
    {
        return new Result();
    }


    /**
     * ������������ ����������� �����, ���������� ������� ��������� ������� ���� ���� �����
     *
     * @param $arSms
     *
     * @return Result
     */
    public function agent($arSms)
    {
        $result = new Result(true);
        $arResult = array();
        $arError = array();


        foreach ($arSms as $smsId => $arSmsMore) {
            if (isset($arSmsMore['PARAMS']['messageId'])) {

                $res = $this->_messageStatus($arSmsMore['PARAMS']['messageId']);

                if ($res->isSuccess()) {
                    $arResult[$smsId] = $res;
                } else {
                    $arError = array_merge($arError, $res->getErrors());
                    $arResult[$smsId] = $res;
                }
            } else {
                $arError[] = new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $arResult[$smsId] = new Result(new Error('UNKNOWN_MESSAGE_ID', \Bxmaker\SmsNotice\SMS_STATUS_ERROR));
            }
        }

        $result->setMore('results', $arResult);
        $result->setMore('errors', (!empty($arError) ? $arError : null)); // ������ ���

        return $result;
    }


    // ��������� ������ ���� UTF-8
    private function _getPreparedStr($str)
    {
        /** @global CMain $APPLICATION */
        global $APPLICATION;

        return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, LANG_CHARSET, "UTF-8") : $str);
    }

    // ����� � UTF-8 ��������, ������� ���������� ��� ����������� �����������
    private function _getFromUtf($str)
    {
        /** @global CMain $APPLICATION */
        global $APPLICATION;

        return (!Manager::isUTF() ? $APPLICATION->ConvertCharset($str, "UTF-8", LANG_CHARSET) : $str);
    }

    public function getParams()
    {

        return array(
            'USER' => array(
                'NAME'      => $this->_getMsg('PARAMS.USER'),
                'NAME_HINT' => $this->_getMsg('PARAMS.USER.HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'PWD'  => array(
                'NAME'      => $this->_getMsg('PARAMS.PWD'),
                'NAME_HINT' => $this->_getMsg('PARAMS.PWD.HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            ),
            'SADR' => array(
                'NAME'      => $this->_getMsg('PARAMS.SADR'),
                'NAME_HINT' => $this->_getMsg('PARAMS.SADR.HINT'),
                'TYPE'      => 'STRING',
                'VALUE'     => ''
            )
        );
    }

    /**
     * �������� ������� ��� ���� ������, ��� �������� ���������� �������
     * @return mixed|string
     */
    public function getDescription()
    {
        return $this->_getMsg('DESCRIPTION');
    }


    /**
     * ��������� ������
     *
     * @param string $function
     * @param array  $params
     *
     * @return array
     */
    protected function _makeRequest($method, array $params = array())
    {
        $p = array(
            'login'      => $this->_getParam('user'),
            'password'   => $this->_getParam('pwd'),
            'partner_id' => '129315'
        );

        $url = 'http://sms.ru/my/balance?';

        switch ($method) {
            case 'message/status': {
                $url = 'http://sms.ru/sms/status?';
                $p = array_merge(array(
                    'id' => $params['id']
                ), $p);
                break;
            }
            case 'message/send': {
                $url = 'http://sms.ru/sms/send?';
                $p = array_merge(array(
                    'text' => $params['text'],
                    'to'   => $params['to']
                ), $p);
                if ($this->_strlen($this->_getParam('sadr'))) {
                    $p['from'] = $this->_getParam('sadr');
                }
                break;
            }
            case 'balance': {
                break;
            }
            default: {
            return false;
            }
        }

        $url .= http_build_query($p);
        $response = $this->oHttp->get($url);

        return $this->response = $this->_getFromUtf($response);
    }

    private function _strlen($str)
    {
        return (Manager::isUTF() ? mb_strlen($str) : strlen($str));
    }


    /**
     * ��������� ������ �������� ���������
     *
     * @param string $messagesId
     *
     * @return boolean|array
     */
    private function _messageStatus($messagesId)
    {
//        http://bxmaker.sms.ru/?panel=api&subpanel=method&show=sms/status
        $result = new Result();

        $response = $this->_makeRequest('message/status', array(
            'id' => $messagesId
        ));
        $result->setMore('response', $response);

        $arRes = explode("\n", $response);

        switch ($arRes[0]) {
            case $this->_getMsg('MSG_STATUS_M1'): {
                //�� �������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_M1_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_100'): {
                //��������� ��������� � ����� �������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                break;
            }
            case $this->_getMsg('MSG_STATUS_101'): {
                //��������� ���������� ���������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                break;
            }
            case $this->_getMsg('MSG_STATUS_102'): {
                //��������� ���������� (� ����)
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_SENT);
                break;
            }
            case $this->_getMsg('MSG_STATUS_103'): {
                //��������� ����������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_DELIVERED);
                break;
            }
            case $this->_getMsg('MSG_STATUS_104'): {
                //����������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_104_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_105'): {
                //�� ����� ���� ����������: ������� ����������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_105_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_106'): {
                //�� ����� ���� ����������: ���� � ��������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_106_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_107'): {
                //�� ����� ���� ����������: ����������� �������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_107_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_108'): {
                //�� ����� ���� ����������: ���������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_108_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_200'): {
                //������������ api_id
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_200_VALUE'));
                break;
            }

            case $this->_getMsg('MSG_STATUS_201'): {
                //�� ������� ������� �� ������� �����
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_201_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_202'): {
                //����������� ������ ����������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_202_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_203'): {
                //��� ������ ���������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_203_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_204'): {
                //��� ����������� �� ����������� � ��������������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_204_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_205'): {
                //��������� ������� ������� (��������� 8 ���)
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_205_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_206'): {
                //����� �������� ��� ��� �������� ������� ����� �� �������� ���������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_206_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_207'): {
                //�� ���� ����� (��� ���� �� �������) ������ ���������� ���������, ���� ������� ����� 100 ������� � ������ �����������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_207_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_208'): {
                //�������� time ������ �����������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_208_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_209'): {
                //�� �������� ���� ����� (��� ���� �� �������) � ����-����
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_209_VALUE'));
                break;
            }


            case $this->_getMsg('MSG_STATUS_210'): {
                //������������ GET, ��� ���������� ������������ POST
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_210_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_211'): {
                //����� �� ������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_211_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_212'): {
                //����� ��������� ���������� �������� � ��������� UTF-8 (�� �������� � ������ ���������)
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_212_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_220'): {
                //������ �������� ����������, ���������� ���� �����.
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_220_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_230'): {
                //�������� ����� ����� ���������� ��������� �� ���� ����� � ����.
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_230_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_231'): {
                //�������� ����� ���������� ��������� �� ���� ����� � ������.
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_231_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_232'): {
                //�������� ����� ���������� ��������� �� ���� ����� � ����.
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_232_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_300'): {
                //������������ token (�������� ����� ���� ��������, ���� ��� IP ���������)
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_300_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_301'): {
                //������������ ������, ���� ������������ �� ������
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_301_VALUE'));
                break;
            }
            case $this->_getMsg('MSG_STATUS_302'): {
                //������������ �����������, �� ������� �� ����������� (������������ �� ���� ���, ���������� � ��������������� ���)
                $result->setResult(\Bxmaker\SmsNotice\SMS_STATUS_ERROR);
                $result->setMore('error_description', $this->_getMsg('MSG_STATUS_302_VALUE'));
                break;
            }
            default: {
            $result->setError(new Error($response, \Bxmaker\SmsNotice\ERROR_SERVICE_CUSTOM, array(
                'message_id' => $messagesId,
                'response'   => $response
            )));
            }
        }

        return $result;
    }

    /*
     * �������� ������, ������� ����� �����������
     */
    private function _getErrorDescription($error_msg, $error_type = '')
    {
        if ($error_type == 'balance') {
            switch ($error_msg) {
                case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_100'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_100_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_200'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_200_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_210'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_210_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_211'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_211_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_220'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_220_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_300'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_300_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_301'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_301_VALUE');
                    break;
                }
                case $this->_getMsg('ERROR_DESCRIPTION_BALANCE_302'): {
                    return $this->_getMsg('ERROR_DESCRIPTION_BALANCE_302_VALUE');
                    break;
                }
                default: {
                return $error_msg;
                }
            }
        }
        else {

            switch ($error_msg) {
                case $this->_getMsg('MSG_SEND_100'): {
                    return $this->_getMsg('MSG_SEND_100_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_200'): {
                    return $this->_getMsg('MSG_SEND_200_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_201'): {
                    return $this->_getMsg('MSG_SEND_201_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_202'): {
                    return $this->_getMsg('MSG_SEND_202_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_203'): {
                    return $this->_getMsg('MSG_SEND_203_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_204'): {
                    return $this->_getMsg('MSG_SEND_204_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_205'): {
                    return $this->_getMsg('MSG_SEND_205_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_206'): {
                    return $this->_getMsg('MSG_SEND_206_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_207'): {
                    return $this->_getMsg('MSG_SEND_207_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_208'): {
                    return $this->_getMsg('MSG_SEND_208_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_209'): {
                    return $this->_getMsg('MSG_SEND_209_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_210'): {
                    return $this->_getMsg('MSG_SEND_210_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_211'): {
                    return $this->_getMsg('MSG_SEND_211_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_212'): {
                    return $this->_getMsg('MSG_SEND_212_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_220'): {
                    return $this->_getMsg('MSG_SEND_220_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_230'): {
                    return $this->_getMsg('MSG_SEND_230_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_300'): {
                    return $this->_getMsg('MSG_SEND_300_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_301'): {
                    return $this->_getMsg('MSG_SEND_301_VALUE');
                    break;
                }
                case $this->_getMsg('MSG_SEND_302'): {
                    return $this->_getMsg('MSG_SEND_302_VALUE');
                    break;
                }

                default: {
                return $error_msg;
                }
            }
        }

    }


}
