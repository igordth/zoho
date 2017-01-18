<?php
/**
 * BaseZoHo
 */

namespace api;

use api\fields\Field;

abstract class BaseZoHo
{
    protected $auth_token = '________set_your_token_________';
    protected $base_url = 'https://crm.zoho.com/crm/private';
    protected $type = 'xml';

    protected function getUrl($section, $action)
    {
        return implode('/', [
            $this->base_url,
            $this->type,
            $section,
            $action
        ]);
    }

    protected function appendBaseFields(&$fields)
    {
        $fields['authtoken'] = $this->auth_token;
        $fields['scope'] = "crmapi";
        $fields['newFormat'] = '2';
    }

    protected function sendGet($url, array $fields = [])
    {
        $this->appendBaseFields($fields);
        $url= $url . "?" . http_build_query($fields, '', "&");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch,CURLOPT_TIMEOUT, 60);
        curl_setopt($ch,CURLOPT_POST,false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    protected function sendPost($url, array $fields = [])
    {
        $this->appendBaseFields($fields);
        $fields_string = http_build_query($fields, '', "&");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    protected function makeXml($section, array $data)
    {
        $xml_writer = new \XMLWriter();
        $xml_writer->openMemory();
        $xml_writer->startElement($section);
        $xml_writer->startElement('row');
        $xml_writer->writeAttribute('no', 1);
        foreach ($data as $key => $val) {
            $xml_writer->startElement('FL');
            $xml_writer->writeAttribute('val', $key);
            if ($val instanceof Field) {
                $val->render($xml_writer);
            }
            else {
                $xml_writer->text($val);
            }
            $xml_writer->endElement();
        }
        $xml_writer->endElement();
        $xml_writer->endElement();
        return $xml_writer->outputMemory();
    }

    public static function getIdFromRequest($xml_string)
    {
        $xml = new \SimpleXMLElement($xml_string);
        $result = (string) $xml->result->recorddetail->FL[0];
        return $result ? : null;
    }
}