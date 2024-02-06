<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class SubmitController extends Controller
{
    /**
     * Submit the custom Gutenberg block WT Contact Form.
     */
    public function sendWtContactFormXHR(Request $request)
    {
        // dd($request);
        $theme = app('wp.theme');

        $allFieldValues = [];
        $toValidate = [];
        $validationMessages = [];

        if(isset($request->fields) && count($request->fields)) {
            foreach($request->get('fields') as $field) {
                $allFieldValues[$field['name']] =  $field['value'];

                if($field['rules']) {
                    $aRules = explode(' ', $field['rules']);

                    $aRulesForValidation = array_map(function($v){
                        if($v == 'alpha') $v = 'alpha:ascii';
                        return $v;
                    }, $aRules);

                    $toValidate[$field['name']] = implode('|', $aRulesForValidation);

                    foreach($aRules as $rule) {
                        $custMessage = '';
                        if($rule == 'required')     $custMessage = '[:Attribute]Dit veld is verplicht';
                        if($rule == 'email')        $custMessage = '[:Attribute]Het e-mail adres is niet goed geformuleerd';
                        if($rule == 'numeric')      $custMessage = '[:Attribute]Vul alleen nummers in';
                        if($rule == 'alpha')        $custMessage = '[:Attribute]Vul alleen letters in';
                        if($rule == 'url')          $custMessage = '[:Attribute]Vul alleen letters in';
                        $validationMessages[$field['name'] . '.' . $rule] = $custMessage;
                    }
                }
            }
        }
// print_r($toValidate);
// print_r($validationMessages);

        $res = new \stdClass();
        $res->errors = [];
        $res->success = '';

        // if($request->get('valkuil') || $request->get('valstrik')) {
        //     $res->errors[] = 'Spam gedetecteerd';
        // }

/*
        $toValidate = array(
            // 'E-mail' => 'required|email',
            'Naam' => 'required|email',
        );
        $validationMessages = array(
            // 'E-mail.required'=> 'Vul een e-mail adres in2',
            // 'E-mail.email'=> 'Het e-mail adres is niet juist geformuleerd3',
            'Naam.required'=> 'Vul een naam in4',
            'Naam.email'=> 'e - mail !!5',
        );
*/
        // $validator = Validator::make($request->all(), $toValidate, $validationMessages);
        $validator = Validator::make($allFieldValues, $toValidate, $validationMessages);
        if($validator->fails()) {
            $errors = $validator->errors();
            foreach($errors->all() as $message) {
                $endingBracketPos = strpos($message, ']');
                $fieldName = substr($message, 1, ($endingBracketPos - 1));
                $fieldError = substr($message, ($endingBracketPos + 1));
                $res->errors[$fieldName] = $fieldError;
            }
        }

        if(!count($res->errors)) {

            $to_email = 'leon@wtmedia-events.nl';
            $subjectCompany = 'Ingevuld schedule-call-form vanaf wtmedia-events.nl';
            $subjectVisitor = 'Kopie van uw bericht aan wtmedia-events.nl';
            
            $messages = $this->getHtmlEmails($allFieldValues, $theme->getUrl() . '/dist/images/logo-wt-group.svg', 'De volgende gegevens zijn achtergelaten door de bezoeker.', 'Bedankt voor uw bericht. De volgende informatie hebben we ontvangen:');
            $headers = array(
                "MIME-Version: 1.0",
                "Content-Type: text/html; charset=ISO-8859-1",
                "From: WT Media & Events <aanmeld-formulier@wtmedia-events.nl>",
                "Reply-To: support@wtmedia-events.nl",
                // "X-Priority: 1",
            );
            $headers = implode("\r\n", $headers);
            mail($to_email, $subjectCompany, $messages[0], $headers);
            mail($allFieldValues['E-mail'], $subjectVisitor, $messages[1], $headers);
        }

        echo json_encode($res);

    }

    public function getHtmlEmails($values, $imgLocation, $introTextCompany, $introTextVisitor) {
        $message1 = '';
        $message2 = '';
        $topHtml = '
        <html><body>
        <!--[if mso]>
        <table cellpadding="0" cellspacing="0" border="0" style="padding:0px;margin:0px;width:100%;">
            <tr>
                <td style="padding:0px;margin:0px;">&nbsp;</td>
                <td style="padding:0px;margin:0px;" width="500">
        <![endif]-->
                    <div style="
                        max-width: 500px;
                        padding: 20px;
                        font-family: verdana, arial;
                        font-size: 14px;
                        margin-left: auto;
                        margin-right: auto;
                        background-color: #FFF;
                        border: 1px solid #CCC;
                    ">
                    <p style="text-align:center;"><img src="' . $imgLocation . '" alt="JusBros logo" /></p>
        ';

        $messageCompany = '';
        $messageVisitor = '';
        foreach($values as $i => $v) {
            if($i == '_token' || $i == 'g-recaptcha-response' || $i == 'method' || $i == 'valstrik' || $i == 'valkuil' || $i == 'success_text' || $i == 'email_to') continue;
            $messageCompany .= '
            <p>
                ' . str_replace('_', ' ', $i) . ':<br />
                <strong>' . (trim($v) == ''?'-':$v) . '</strong>
            </p>
            ';
            if($i == 'g-recaptcha-score') continue;
            $messageVisitor .= '
            <p>
                ' . str_replace('_', ' ', $i) . ':<br />
                <strong>' . (trim($v) == ''?'-':$v) . '</strong>
            </p>
            ';
        }
        $bottomHtml = '';
        $bottomHtml .= '
                    </div>
        <!--[if mso]>
                </td>
                <td style="padding:0px;margin:0px;">&nbsp;</td>
            </tr>
        </table>
        <![endif]-->
        </body></html>
        ';

        $message1 = $topHtml . '<p>' . $introTextCompany . '</p>' . $messageCompany . $bottomHtml;
        $message2 = $topHtml . '<p>' . $introTextVisitor . '</p>' . $messageVisitor . $bottomHtml;

        return array($message1, $message2);
    }

}
