<?php

namespace Raneko\External\Twilio;

/**
 * Example to use Twilio SMS class.
 * @author Harry Lesmana
 */
class SMSExample
{

    public function test()
    {
        $sms = new \Raneko\External\Twilio\SMS();
        $sms->setCredAccountSID("AC571988d9436c077e4426e16f15398c68");
        $sms->setCredAuthToken("bddf423211eab868f3d4dc3fd95b3456");
        $sms->send("+14695188880", "+6584280760", "2 factors authentication/mobile number verification. Keyenn\n---\nsentrabayar-raneko");
    }
}
