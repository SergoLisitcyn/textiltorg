<?

Class Bxmaker_SmsNotice_Manager_Demo {

    static private $instance = null;

    private $bDemo = null;
    private $bDemoExpired = null;


    private function __construct()
    {
    }

    private function __clone()
    {
    }


    /**
     * Проверка демо режима
     * @return bool
     */
    final public function isDemo()
    {
        if (is_null($this->bDemo)) {
            $this->_checkDemo();
        }
        return $this->bDemo;
    }

    /**
     * Проверка не истекло ли время демо режима
     * @return bool
     */
    final public function isExpired()
    {
        if (is_null($this->bDemoExpired)) {
            $this->_checkDemo();
        }
        return $this->bDemoExpired;
    }

    /**
     * Инициализация параметров работы модуля
     */
    final private function _checkDemo()
    {
        $module = new CModule();
        if ($module->IncludeModuleEx('bxmaker.smsnotice') == constant('M'.'O'.'D'.'U' . 'L'.'E' . '_'.'N'.'O' . 'T'.'_'.'F' . 'O'.'U'.'N'.'D')) {
            $this->bDemo = false;
            $this->bDemoExpired = false;
        } elseif ($module->IncludeModuleEx('bxmaker.smsnotice') == constant('M'.'O'.'D'.'U' . 'LE' . '_' . 'D'.'E' . 'M'.'O')) {
            $this->bDemo = true;
            $this->bDemoExpired = false;
        } elseif ($module->IncludeModuleEx('bxmaker.smsnotice') == constant('M'.'O'.'D'.'U' . 'L'.'E'.'_'.'D' . 'E'.'M'.'O'.'_'.'E' . 'X'.'P'.'I' . 'R'.'E'.'D')) {
            $this->bDemo = true;
            $this->bDemoExpired = true;
        }
    }


    /** Сообщение о Демо режиме */
    public function showDemoMessage()
    {
        if ($this->isDemo()) {
            if ($this->isExpired()) {
                echo '<div class="ap-bxmaker_smsnotice-notice-box expired" >' . $this->getMsg('DEMO_EXPIRED_NOTICE') . '</div>';
            } else {
                echo '<div class="ap-bxmaker_smsnotice-notice-box" >' . $this->getMsg('DEMO_NOTICE') . '</div>';
            }
        }
    }



    protected function _getPhoneRegExp()
    {

		// Россия  / Украина, франция / США / Великобритания / Германия / казахстан
		return '/^[78][\d]{10}$|^3[\d]{10,12}$|^1[\d]{10}$|^4[\d]{11}$|^4[\d]{14}$|^9[\d]{11}$/';
        /*return "/^([87]"
        ."(?!95[4-79]"."|99[^2457]"."|907"."|94[^0]"."|336)"
        ."([348]\d"."|9[0-689]"."|7[027])\d{8}"
        ."|[1246]\d{9,13}"
        ."|68\d{7}"
        ."|5[1-46-9]\d{8,12}"
        ."|55[1-9]\d{9}"
        ."|500[56]\d{4}"
        ."|5016\d{6}"
        ."|5068\d{7}"
        ."|502[45]\d{7}"
        ."|5037\d{7}"
        ."|50[457]\d{8}"
        ."|50855\d{4}"
        ."|509[34]\d{7}"
        ."|376\d{6}"
        ."|855\d{8}"
        ."|856\d{10}"
        ."|85[0-4789]\d{8,10}"
        ."|8[68]\d{10,11}"
        ."|8[14]\d{10}"
        ."|82\d{9,10}"
        ."|852\d{8}"
        ."|90\d{10}"
        ."|96(0[79]"
        ."|17[01]"
        ."|13)\d{6}"
        ."|96[23]\d{9}"
        ."|964\d{10}"
        ."|96(5[69]"
        ."|89)\d{7}"
        ."|96(65"
        ."|77)\d{8}"
        ."|92[023]\d{9}"
        ."|91[1879]\d{9}"
        ."|9[34]7\d{8}"
        ."|959\d{7}"
        ."|989\d{9}"
        ."|97\d{8,12}"
        ."|99[^456]\d{7,11}"
        ."|994\d{9}"
        ."|9955\d{8}"
        ."|996[57]\d{8}"
        ."|380[34569]\d{8}"
        ."|381\d{9}"
        ."|385\d{8,9}"
        ."|375[234]\d{8}"
        ."|372\d{7,8}"
        ."|37[0-4]\d{8}"
        ."|37[6-9]\d{7,11}"
        ."|30[69]\d{9}"
        ."|34[67]\d{8}"
        ."|3[12359]\d{8,12}"
        ."|36\d{9}"
        ."|38[1679]\d{8}"
        ."|382\d{8,9})$|^(38\d{10})$/";
		*/



    }

    /**
     * Проверка правильноси номера
     * @param $phone
     *
     * @return bool
     */
    public function isValidPhone($phone)
    {
        $phone = $this->getPreparePhone($phone);

        if(preg_match($this->_getPhoneRegExp(), $phone, $match))
        {
            return true;
        }
        return false;
    }

    /**
     * Подготовка номера телефона
     *
     * @param $phone
     *
     * @return mixed
     */
    public function getPreparePhone($phone)
    {
        $phone = preg_replace('/[^\d]+/', '', $phone);

        if(preg_match($this->_getPhoneRegExp(), $phone, $match))
        {
            return preg_replace('/^8/', '7', $match[0]);
        }
        return preg_replace('/^8/', '7', $phone);
    }


    /**
     * Единый формат ошибок для записи в базу, для быстрого анализа неполадок
     *
     * @param array $errors
     *
     * @return string
     */
    protected function getCommentFromErrors($errors = array())
    {
        $comment = '';
        foreach ($errors as $error) {
            $comment .= $error->getMessage() . "\r\n>>>>>>>>\r\n" . var_export($error->getMore(), true) . "\r\n<<<<<<<<<\r\n---------\r\n\r\n";
        }

        return $comment;
    }

    /**
     * Замена в шаблоне плэйсхолдеров на действительные значения
     *
     * @param       $arTemplate
     * @param array $arFields
     */
    protected function prepareTemplate(&$arTemplate, $arFields = array())
    {
        if ($arSiteData = $this->getSiteData()) {
            $arFields['SITE_NAME'] = $arSiteData['SITE_NAME'];
            $arFields['SERVER_NAME'] = $arSiteData['SERVER_NAME'];
        }
        foreach ($arFields as $find => $replacement) {
            $arTemplate['PHONE'] = trim(preg_replace('/#' . trim($find) . '#/', (string) $replacement, $arTemplate['PHONE']));
            $arTemplate['TEXT'] = trim(preg_replace('/#' . trim($find) . '#/', (string) $replacement, $arTemplate['TEXT']));
        }
    }

    /**
     * Используется агентом, для обновления статусов сообщений, для сервисов где есть задержки
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public function agentCheckSmsStatus()
    {

        $arSmsCheck = array();

        $oManagerTable = $this->_getObj('oManagerTable');
        $dbr = $oManagerTable->getList(array(
            'filter' => array(
                'STATUS' => $this->_getConst('SMS_STATUS_SENT')
            ),
            'order'  => array(
                'SITE_ID' => 'ASC',
                'CREATED' => 'ASC'
            ),
            'limit'  => '50'
        ));
        while ($ar = $dbr->fetch()) {
            $arSmsCheck[$ar['SITE_ID']][$ar['SERVICE_ID']][$ar['ID']] = $ar;
        }


        foreach ($arSmsCheck as $sid => $arSmsList) {
            foreach ($arSmsList as $serviceId => $arSms) {

                $resService = $this->initService($serviceId, $sid);
                if ($resService->isSuccess()) {

                    $result = $this->_getObj('oService')->agent($arSms);
                    $arResults = $result->getMore('results');

                    if ($arResults) {
                        foreach ($arResults as $smsId => $smsResult) {
                            //success
                            if ($smsResult->isSuccess()) {
                                switch ($smsResult->getResult()) {
                                    case $this->_getConst('SMS_STATUS_DELIVERED'): {

                                        if ($arSms[$smsId]["STATUS"] != $this->_getConst('SMS_STATUS_DELIVERED')) { // если статус не изменился
                                            $oManagerTable->update($smsId, array(
                                                'STATUS'  => $this->_getConst('SMS_STATUS_DELIVERED'),
                                                'COMMENT' => ''
                                            ));
                                        }

                                        break;
                                    }
                                    case $this->_getConst('SMS_STATUS_SENT'): {
                                        if ($arSms[$smsId]["STATUS"] != $this->_getConst('SMS_STATUS_SENT')) {// если статус не изменился
                                            $oManagerTable->update($smsId, array(
                                                'STATUS'  => $this->_getConst('SMS_STATUS_SENT'),
                                                'COMMENT' => ''
                                            ));
                                        }
                                        break;
                                    }
                                    case $this->_getConst('SMS_STATUS_ERROR'): {
                                        if ($arSms[$smsId]["STATUS"] != $this->_getConst('SMS_STATUS_ERROR')) {// если статус не изменился

                                            $arUpdateFields = array(
                                                'STATUS'  => $this->_getConst('SMS_STATUS_ERROR'),
                                                'COMMENT' => ''
                                            );
                                            // если есть описание ошикби, то добавим
                                            if ($smsResult->getMore('error_description')) {
                                                $arUpdateFields['PARAMS'] = array_merge($arSms[$smsId]['PARAMS'],
                                                    array('error_description' => $smsResult->getMore('error_description')));
                                            }
                                            $oManagerTable->update($smsId, $arUpdateFields);
                                        }
                                        break;
                                    }
                                }
                            } //error
                            else {
                                if ($arSms[$smsId]["STATUS"] != $this->_getConst('SMS_STATUS_ERROR')) {
                                    $oManagerTable->update($smsId, array(
                                        'STATUS'  => $this->_getConst('SMS_STATUS_ERROR'),
                                        'COMMENT' => $this->getCommentFromErrors($smsResult->getErrors())
                                    ));
                                }
                            }
                        }
                    }
                } else {
//                    echo '<pre>';
//                    print_r($resService);
//                    echo '</pre>';
                }
            }
        }


        return true;

    }


    /**
     * Отправка сообщения и добавление при успешном результате даных в базу
     *
     * @param      $phone
     * @param      $text
     * @param null $template
     *
     * @return Result
     */
    protected function sendSms($phone, $text, $site_id, $template = null)
    {
        if ($this->bDemoExpired) {
            return $this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.MODULE_DEMO_EXPIRED'), $this->_getConst('ERROR_SERVICE_INITIALIZATION')));
        }

        if (!$this->isValidPhone($phone)) {

            return $this->_getObjResult($this->_getObjError($this->getMsg('MANAGER.ERROR_INVALID_PHONE'), $this->_getConst('ERROR_INVALID_PHONE'), array(
                'PHONE'          => $phone,
                'PHONE_PREPARED' => $this->getPreparePhone($phone),
                'TEXT'           => $text,
                'SITE_ID'        => $site_id,
                'TEMPLATE'       => $template
            )));
        }

        $phone = $this->getPreparePhone($phone);

        $arSeviceTmp =$this->_getObj('arServiceCurrent');

        if ($this->_getObj('bDebug')) {
            $result = $this->_getObjResult($this->_getConst('SMS_STATUS_DELIVERED'));
            $result->setMore('phone', $phone);
            $result->setMore('text', $text);
            $result->setMore('template', $template);



            $resSaveSms = $this->_getObj('oManagerTable')->add(array(
                'PHONE'      => $this->getPreparePhone($phone),
                'TEXT'       => $text,
                'CREATED'    => $this->_getNewObj('\Bitrix\Main\Type\DateTime'),
                'STATUS'     => $result->getResult(),
                'TYPE_ID'    => $template,
                'COMMENT'    => $this->getMsg('MANAGER.SEND_SMS_DEBUG_MODE'),
                'SERVICE_ID' => (isset($arSeviceTmp['ID']) ? $arSeviceTmp['ID'] : null),
                'SITE_ID'    => $site_id
            ));
            if (!$resSaveSms->isSuccess()) {
                $result->setMore('save_sms_error', $resSaveSms->getErrorMessages());
            }

            return $result;
        }

        // отправка
        $arSms = array(
            'PHONE'      => $phone,
            'TEXT'       => $text,
            'CREATED'    => $this->_getNewObj('\Bitrix\Main\Type\DateTime'),
            'STATUS'     => $this->_getConst('SMS_STATUS_DELIVERED'), //значение поумолчанию
            'TYPE_ID'    => $template,
            'SERVICE_ID' => (isset($arSeviceTmp['ID']) ? $arSeviceTmp['ID'] : null),
            'SITE_ID'    => $site_id
        );
        $resSaveSms = $this->_getObj('oManagerTable')->add($arSms);
        if (!$resSaveSms->isSuccess()) {
            return $this->_getObjResult($this->_getObjError($resSaveSms->getErrorMessages(), 'save_sms_error', $arSms));
        }
        $smsId = $resSaveSms->getId();

        /**
         * @var Result $result
         */
        $result = $this->_getObj('oService')->send($phone, $text, array(
            'smsId'   => $smsId,
            'service' => $this->_getObj('arServiceCurrent')
        ));
        if ($result->isSuccess()) {

            // если после отправки получили статус отличный от доставлено, то обновляем
            if ($result->getResult() != $this->_getConst('SMS_STATUS_DELIVERED')) {
                $this->_getObj('oManagerTable')->update($smsId, array(
                    'STATUS' => $result->getResult(),
                    'PARAMS' => ($result->getMore('params') ? $result->getMore('params') : '')
                ));
            }

        } else {

            $this->_getObj('oManagerTable')->update($smsId, array(
                'STATUS'  => $this->_getConst('SMS_STATUS_ERROR'),
                'COMMENT' => $this->getCommentFromErrors($result->getErrors())
            ));
        }
        $result->setMore('smsId', $smsId);

        return $result;
    }

}

?>