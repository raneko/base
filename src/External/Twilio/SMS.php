<?php

namespace Raneko\External\Twilio;

/**
 * Handling SMS functionality.
 * @author Harry Lesmana <harry@raneko.com>
 * @since 2014-03-31
 */
class SMS extends \Raneko\External\TwilioAbstract
{

    public function __construct()
    {
        parent::__construct();
        $this->_setModule("Messages");
    }

    /**
     * Send message.
     * @param string $from Twilio number you want to use to send SMS. Example: +14568888888
     * @param string $to Destination number with international prefix. Example: +6588889999
     * @param string $message
     * @result \Raneko\Common\Result
     * @example SMSExample.php Example to send SMS.
     */
    public function send($from, $to, $message)
    {
        $params = array(
            "From" => $from,
            "To" => $to,
            "Body" => $message
        );
        return $this->_invoke($params);
    }
}
