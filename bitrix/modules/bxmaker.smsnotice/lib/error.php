<?

namespace Bxmaker\SmsNotice;

class Error {

    private $error_code = 0;
    private $error_msg = '';
    private $error_more = array();

    public function __construct($err_msg, $err_code = 0, $err_more = array()){
        $this->error_msg = $err_msg;
        $this->error_code = $err_code;
        $this->error_more = $err_more;
    }

    public function getMessage(){
        return $this->error_msg;
    }

    public function getCode(){
        return $this->error_code;
    }

    public function getMore(){
        return $this->error_more;
    }
}